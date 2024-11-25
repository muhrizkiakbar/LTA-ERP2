<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Sales;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class MasterServices
{
	public function item_kino()
	{
		return Item::where('flag_label','KINO')->orderBy('title','ASC')->get();
	}

	public function item_kino_sync($branch)
	{
		$whs = Warehouse::find($branch);

		$body = [
			'WhsCode' => $whs->title
		];

		$json = callSapApiLtaWithPost('getItemKino',json_encode($body));

		$count = count($json['data']);

		if ($count > 0) 
		{
			DB::table('m_item')
				->where('WhsCode',$whs->title)
				->delete();
		}

		foreach ($json['data'] as $key => $value) 
		{
			$itemName = $value['ItemName'];

			$exp = explode(' ',$itemName);

			$str_first = $exp[0];

			if ($str_first=='BONUS' || $str_first=='BNS') 
			{
				$flag_bonus = 'Y';
			}
			else
			{
				$flag_bonus = 'N';
			}

			$data[] = [
				'code' => $value['ItemCode'],
				'title' => $itemName,
				'INIT1' => $value['INIT1'],
				'INIT2' => $value['INIT2'],
				'INIT3' => $value['INIT3'],
				'INIT4' => $value['INIT4'],
				'INIT5' => $value['INIT5'],
				'INIT6' => $value['INIT6'],
				'INIT7' => $value['INIT7'],
				'CDB' => $value['CDB'],
				'nisib' => $value['NISIB'],
				'barcode' => $value['Barcode'],
				'brand' => $value['Brand'],
				'category' => $value['Category'],
				'variant' => $value['Variant'],
				'satuan_besar' => $value['SATUAN_BESAR'],
				'satuan_kecil' => $value['SATUAN_KECIL'],
				'nisik' => $value['NISIK'],
				'nw' => $value['NW'],
				'csn' => $value['CSN'],
				'sku_status' => $value['SKU_STATUS'],
				'flag_label' => 'KINO',
				'flag_bonus' => $flag_bonus,
				'UomCode' => $value['UomCode'],
				'UomEntry' => $value['UomEntry'],
				'flag_active' => $value['stok'] > 0 ? 'Y' : 'N',
				'WhsCode' => $value['WhsCode']
			];
		}

		// dd($data);

		$chunkSize = count($data) < 100 ? 100 : 250;
		$dataChunks = array_chunk($data, $chunkSize);

		// dd($dataChunks);

		foreach ($dataChunks as $chunk) 
		{
			DB::table('m_item')->insert($chunk);
		}

		$callback = array(
			'message' => 'sukses'
		);

		return $callback;
	}

	public function kino_export()
	{
		
	}

	public function sales_employee()
	{
		return Sales::orderBy('title','ASC')->get();
	}

	public function sales_employee_sync()
	{
		$json = callSapApiLtaWithoutPost('getSales');

		foreach ($json as $value) 
		{
			$cek = Sales::where('code_sap',$value['SlpCode'])
									->whereNull('code_kino')
									->get();

			if (count($cek) > 0) 
			{
				Sales::where('code_sap',$value['SlpCode'])
						 ->delete();

				$data = [
					'code_sap' => $value['SlpCode'],
					'title' => $value['SlpName'],
					'code' => $value['U_SALESCODE'],
					'branch' => $value['U_BRANCHCODESFA'],
					'spv_code' => $value['U_SPVCODE'],
					'spv_title' => $value['U_SPVNM']
				];

				Sales::create($data);
			}
		}

		$callback = array(
			'message' => 'sukses'
		);

		return $callback;
	}
}