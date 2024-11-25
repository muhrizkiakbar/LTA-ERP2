<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\ClosingDate;
use App\Models\Customer;
use App\Models\DeliveryHeader;
use App\Models\DeliveryLines;
use App\Models\History;
use App\Models\Item;
use App\Models\Sales;
use App\Models\VdistHeader;
use App\Models\VdistLines;
use App\Models\VdistTemp;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Str;

class InterfacingServices
{
	public function kino_data()
	{
		$role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    if ($role==1) 
    {
      $get = VdistHeader::whereNull('DocNum')->where('flag_label','KINO')->orderBy('id','DESC')->get();
    }
    else
    {
      $branch = Branch::find($branchx);
      $get = VdistHeader::whereNull('DocNum')->where('flag_label','KINO')->where('BPLId',$branch->BPLid)->orderBy('id','DESC')->get();
    }

    return $get;
	}

	public function kino_import($data, $branch)
	{
		$users_id = auth()->user()->id;
  
    $whs = Warehouse::where('id',$branch)->first();

		$val = [];

		foreach ($data as $key => $value) 
    {
			$slp = $this->getSlpDetail($value['SLSMAN_ID']);

			if ($value['FLAG_BONUS']=='Y') 
			{
				$item = $this->getItemDetailBonus($value['PRD_ID'],$whs->title);
			}
			else
			{
				$item = $this->getItemDetail($value['PRD_ID'],$whs->title);
			}
			

			$customer = $this->getCustomerDetail($value['PO_CUST_CODE']);

			$unitPrice = $value['PRICE'];

			if ($value['SO_STS']=='SHIPMENT') 
			{
				if (isset($slp) && isset($customer['CardName']))
				{
					if (isset($item)) 
					{
						$cekStok = $this->cekStokSap($item['code'], $whs->title);
						if ($cekStok < $value['QTY']) 
						{
							$qty_real = 0;
							$total = 0 * $unitPrice;
						}
						else
						{
							$qty_real = $value['QTY'];
							$total = $value['QTY'] * $unitPrice;
						}

						$qty_real = ($cekStok < $value['QTY']) ? 0 : $value['QTY'];

						if($value['FLAG_BONUS']=='Y')
						{
							$total = 0;
							$unitPrice = 0;
						}
						else
						{
							$total = $qty_real * $unitPrice;
						}

						if ($total == 0) 
						{
							$disc1 = 0;
							$disc2 = 0;
							$disc3 = 0;
							$disc4 = 0;
							$disc5 = 0;
							$disc6 = 0;
							$disc7 = 0;
							$disc8 = 0;

							$discx1 = 0;
							$discx2 = 0;
							$discx3 = 0;
							$discx4 = 0;
							$discx5 = 0;
							$discx6 = 0;
							$discx7 = 0;
							$discx8 = 0;
						}
						else
						{
							$disc1 = $value['DISC_1'];
							$disc2 = $value['DISC_2'];
							$disc3 = $value['DISC_3'];
							$disc4 = $value['DISC_4'];
							

							$discx1 = ($disc1 / 100) * $total;
							$discx2 = ($disc2 / 100) * ($total - $discx1);
							$discx3 = ($disc3 / 100) * ($total - $discx1 - $discx2);
							$discx4 = ($disc4 / 100) * ($total - $discx1 - $discx2 - $discx3);

							if ($value['DISC_5']!=0) 
							{
								// Logic PAK GUN
								$sum4 = $total - $discx1 - $discx2 - $discx3 - $discx4;
								$disc5 = ($value['DISC_5'] / $sum4) * 100;
								// $discx5 = $value['DISC_5'];
								$discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);

								// Logic KINO
								// $disc5 = ($value['DISC_5'] / $total) * 100;
								// $discx5 = $value['DISC_5'];
							}
							else
							{
								$disc5 = 0;
								$discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
							}

							if ($value['DISC_6']!=0) 
							{
								// Logic PAK GUN
								$sum5 = $total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5;
								$disc6 = ($value['DISC_6'] / $sum5) * 100;
								// $discx6 = $value['DISC_6'];
								$discx6 = ($disc6 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);

								// Logic KINO
								// $disc6 = ($value['DISC_6'] / $total) * 100;
								// $discx6 = $value['DISC_6'];
							}
							else
							{
								$disc6 = 0;
								$discx6 = ($disc6 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);
							}

							if ($value['DISC_7']!=0) 
							{
								// Logic PAK GUN
								$sum6 = $total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6;
								$disc7 = ($value['DISC_7'] / $sum6) * 100;
								// $discx7 = $value['DISC_7'];
								$discx7 = ($disc7 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);

								// Logic KINO
								// $disc7 = ($value['DISC_7'] / $total) * 100;
								// $discx7 = $value['DISC_7'];
							}
							else
							{
								$disc7 = 0;
								$discx7 = ($disc7 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);
							}

							$disc8 = $value['DISC_8'];
							$discx8 = ($disc8 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);
						}					

						$disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
						$disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

						$totalx = $total - round($disc_calx, 2);

						$itemName = strval($item['title']);

						$val[] = [
							'users_id' => $users_id,
							'NumAtCard' => $value['SO_NO'],
							'ItemCode' => $item['code'],
							'ItemCodeVdist' => $value['PRD_ID'],
							'ItemName' => $itemName,
							'Quantity' => $qty_real,
							'QuantityVdist' => $value['QTY'],
							'TaxCode' => 'PPNO11',
							'Price' => $unitPrice,
							'UnitPrice' => $unitPrice,
							'CostingCode' => $whs->code,
							'CostingCode2' => 'MIX',
							'CostingCode3' => 'SAL',
							'UoMCode' => $item['UomCode'],
							'UoMEntry' => $item['UomEntry'],
							'UnitsOfMeasurment' => 1,
							'LineTotal' => round($totalx, 2),
							'DiscountPercent' => round($disc_cal, 2),
							'U_DISC1' => $disc1 ?? "0",
							'U_DISCVALUE1' => $discx1,
							'U_DISC2' => $disc2 ?? "0",
							'U_DISCVALUE2' => $discx2,
							'U_DISC3' => $disc3 ?? "0",
							'U_DISCVALUE3' => $discx3,
							'U_DISC4' => $disc4 ?? "0",
							'U_DISCVALUE4' => $discx4,
							'U_DISC5' => $disc5 ?? "0",
							'U_DISCVALUE5' => $discx5,
							'U_DISC6' => $disc6 ?? "0",
							'U_DISCVALUE6' => $discx6,
							'U_DISC7' => $disc7 ?? "0",
							'U_DISCVALUE7' => $discx7,
							'U_DISC8' => $disc8 ?? "0",
							'U_DISCVALUE8' => $discx8,
							'WarehouseCode' => $whs->title,
							'CardCode' => $value['PO_CUST_CODE'],
							'SlpCodeSfa' => $slp->code,
							'flag_label' => 'KINO',
							'DocDate' => $value['SO_DATE'],
						];
					}
				}
			}
		}

		// dd($val);

		// return $val;

		$chunkSize = count($val) < 50 ? 25 : 50;
		$dataChunks = array_chunk($val, $chunkSize);

		foreach ($dataChunks as $chunk) 
		{
			DB::table('vdist_temp')->insert($chunk);
		}

		$cek = $this->importGenerateOrder($users_id, $whs->title);

		// dd($cek);

    VdistTemp::where('users_id',$users_id)->delete();

		$callback = array(
			'message' => 'success'
		);

		return $callback;

	}

	public function importGenerateOrder($users_id, $whs)
  {
    $get_group = VdistTemp::where('users_id',$users_id)
													->where('flag_label','KINO')
                          ->groupBy('NumAtCard')
                          ->get(); 

    $branch = Warehouse::where('title',$whs)->first();

    foreach ($get_group as $value) 
    {
      $cek = $this->cekData($value->NumAtCard);

      if (count($cek)==0)
      {
        $sales = $this->getSalesDetail($value->SlpCodeSfa);
        if (!empty($sales)) 
        {
          $cardCode = trim($value->CardCode);
          $customer = $this->getCustomerDetail($cardCode);

          if ($cardCode!='') 
          {
            $getTop = $this->getTopCustomer($value->CardCode);
            $top = "+".$getTop." days";
            $docDueDate= date('Y-m-d', strtotime($top, strtotime($value->DocDate)));

            $slug = Str::slug($value->NumAtCard);

            $lines = $this->importGenerateOrderLines($value->NumAtCard);

            $header = [
              'Branch' => $branch->id,
              'NumAtCard' => $value->NumAtCard,
              'DocDate' => $value->DocDate,
              'DocDueDate' => $docDueDate,
              'CardCode' => $value->CardCode,
              'CardName' => isset($customer['CardName']) ? $customer['CardName'] : '-',
              'Address' => isset($customer['Address']) ? $customer['Address'] : '-' ,
              'SalesPersonCode' => $sales->code_sap,
              'SalesPersonName' => $sales->title,
              'BPLId' => $branch->BPLId,
              'U_NOPOLISI' => isset($customer['NopolMix']) ? $customer['NopolMix'] : '-',
              'U_NOLOLISI2' => isset($customer['NopolPng']) ? $customer['NopolPng'] : '-',
              'Comments' => 'From ERP KINO Sync - '.$value->NumAtCard,
              'slug' => $slug,
							'flag_label' => 'KINO',
							'DocTotal' => $lines['DocTotal'],
            ];

            VdistHeader::create($header);
          }
        }
      }
    }

		// return $header;
  }

	public function importGenerateOrderLines($numAtCard)
  {
    $get = VdistTemp::where('NumAtCard',$numAtCard)->get();

		foreach ($get as $key => $value) 
		{
			$data[] = [
				'NumAtCard' => $value->NumAtCard,
				'ItemCode' =>$value->ItemCode,
				'ItemCodeVdist' => $value->ItemCodeVdist,
				'ItemName' =>$value->ItemName,
				'Quantity' => $value->Quantity,
				'QuantityVdist' => $value->QuantityVdist,
				'TaxCode' => 'PPNO11',
				'Price' => $value->UnitPrice,
				'UnitPrice' => $value->UnitPrice,
				'CostingCode' => $value->CostingCode,
				'CostingCode2' => 'MIX',
				'CostingCode3' => 'SAL',
				'UoMCode' =>$value->UoMCode,
				'UoMEntry' =>$value->UoMEntry,
				'UnitsOfMeasurment' => 1,
				'LineTotal' => round($value->LineTotal,2),
				'DiscountPercent' => round($value->DiscountPercent,2),
				'U_DISC1' => isset($value->U_DISC1) ? $value->U_DISC1 : "0",
				'U_DISCVALUE1' => $value->U_DISCVALUE1,
				'U_DISC3' => isset($value->U_DISC3) ? $value->U_DISC3 : "0",
				'U_DISCVALUE3' => $value->U_DISCVALUE3,
				'U_DISC4' => isset($value->U_DISC4) ? $value->U_DISC4 : "0",
				'U_DISCVALUE4' => $value->U_DISCVALUE4,
				'U_DISC2' => isset($value->U_DISC2) ? $value->U_DISC2 : "0",
				'U_DISCVALUE2' => $value->U_DISCVALUE2,
				'U_DISC5' => isset($value->U_DISC5) ? $value->U_DISC5 : "0",
				'U_DISCVALUE5' => $value->U_DISCVALUE5,
				'U_DISC6' => isset($value->U_DISC6) ? $value->U_DISC6 : "0",
				'U_DISCVALUE6' => $value->U_DISCVALUE6,
				'U_DISC7' => isset($value->U_DISC7) ? $value->U_DISC7 : "0",
				'U_DISCVALUE7' => $value->U_DISCVALUE7,
				'U_DISC8' => isset($value->U_DISC8) ? $value->U_DISC8 : "0",
				'U_DISCVALUE8' => $value->U_DISCVALUE8,
				'WarehouseCode' => $value->WarehouseCode
			];
		}

		VdistLines::insert($data);

		$DocTotal = array_sum(array_column($data,'LineTotal'));
    
    $result = [
      'DocTotal' => $DocTotal
    ];

    return $result;
  }

	public function pushKino($id)
  {
    // $get = VdistHeader::where('slug',$id)->first();
		$get = DB::table('view_kino_header_active')
						 ->where('slug',$id)
						 ->first();

    $date_closing = ClosingDate::where('status',1)->get();

		if (count($date_closing) > 0) 
		{
			$docDate = $get->DocDate;
		}
		else
		{
			$docDate = date('Y-m-d');
		}
    

    $getTop = $this->getTopCustomer($get->CardCode);
    $top = "+".$getTop." days";

    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

		$numAtCard = 'KINO-'.$get->NumAtCard;
      
    $data = [
      'CardCode' => $get->CardCode,
      'DocDueDate' => $docDueDate,
      'DocDate' => $docDate,
      'BPL_IDAssignedToInvoice' => $get->BPLId,
      'SalesPersonCode'=> $get->SalesPersonCode,
      'NumAtCard' => $numAtCard,
      'Comments' => $get->Comments,
      'U_NOPOLISI' => $get->U_NOPOLISI,
      'U_NOPOLISI2' => $get->U_NOPOLISI2,
      'DocumentLines' => $this->jsonKinoLines($get->NumAtCard)
    ];

		// dd($data);

    $push = $this->pushDelivery($data, $docDate);

    return $push;
  }

  public function jsonKinoLines($numAtCard)
  {

		$get = DB::table('view_kino_lines_active')
						 ->where('NumAtCard',$numAtCard)
						 ->get();

    // $get = VdistLines::where('NumAtCard',$numAtCard)->get();

    $no = 0;
    foreach ($get as $value) 
    {
      if ($value->Quantity > 0 && $value->ItemCode != NULL) 
      {
        $total = $value->Quantity * $value->UnitPrice;

        $disc1 = $value->U_DISC1;
				$disc2 = $value->U_DISC2;
				$disc3 = $value->U_DISC3;
				$disc4 = $value->U_DISC4;
				$disc5 = $value->U_DISC5;
				$disc6 = $value->U_DISC6;
				$disc7 = $value->U_DISC7;
				$disc8 = $value->U_DISC8;

        $discx1 = $value->U_DISCVALUE1;
				$discx2 = $value->U_DISCVALUE2;
        $discx3 = $value->U_DISCVALUE3;
        $discx4 = $value->U_DISCVALUE4;
				$discx5 = $value->U_DISCVALUE5;
				$discx6 = $value->U_DISCVALUE7;
				$discx7 = $value->U_DISCVALUE6;
				$discx8 = $value->U_DISCVALUE8;

        $disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
        $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

        $totalx = $total - round($disc_calx,2);

        $data[] = [
          'ItemCode' => $value->ItemCode,
          'Quantity' => $value->Quantity,
          'TaxCode' => $value->TaxCode,
          'UnitPrice' => $value->UnitPrice,
          'Price' => $value->UnitPrice,
          'CostingCode' => $value->CostingCode,
          'CostingCode2' => $value->CostingCode2,
          'CostingCode3' => $value->CostingCode3,
          'MeasureUnit' => $value->UoMCode,
          'UoMCode' => $value->UoMCode,
          'UoMEntry' => $value->UoMEntry,
          'UnitsOfMeasurment' => $value->UnitsOfMeasurment,
          'LineTotal' => round($totalx,2),
          'DiscountPercent' => round($disc_cal,2),
          'U_DISC1' => isset($disc1) ? $disc1 : "0",
          'U_DISCVALUE1' => $discx1,
					'U_DISC2' => isset($disc2) ? $disc2 : "0",
          'U_DISCVALUE2' => $discx2,
          'U_DISC3' => isset($disc3) ? $disc3 : "0",
          'U_DISCVALUE3' => $discx3,
          'U_DISC4' => isset($disc4) ? $disc4 : "0",
          'U_DISCVALUE4' => $discx4,
					'U_DISC5' => isset($disc5) ? $disc5 : "0",
          'U_DISCVALUE5' => $discx5,
					'U_DISC6' => isset($disc6) ? $disc6 : "0",
          'U_DISCVALUE6' => $discx6,
					'U_DISC7' => isset($disc7) ? $disc7 : "0",
          'U_DISCVALUE7' => $discx7,
					'U_DISC8' => isset($disc8) ? $disc8 : "0",
          'U_DISCVALUE8' => $discx8,
          'WarehouseCode' => $value->WarehouseCode,
          'BaseLine' => $no
        ];

        $no++;
      }      
    }

    return $data;
  }

  public function pushDelivery($json, $date)
  {
    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

    $db = 'LTALIVE2020';
    $url = 'https://192.168.1.81:50000/b1s/v1/Login';

    $body = [
        'CompanyDB' => $db,
        'UserName' => $user,
        'Password' => $pass
    ];

    $api = callApiLogin($body,$url);

    $sessionId = $api['SessionId'];
    $routeId = ".node1";
    $headers = "B1SESSION=" . $sessionId . "; ROUTEID=" . $routeId;

    $cek_period = [
      'DocDate' => $date
    ];
    $period = checkPeriod(json_encode($cek_period));

    if($period=="Y" || $period=="C")
    {
      $callback = array(
        'message' => 'period',
        'text' => 'Maaf, Period sudah di lock'
      );
    }
    else
    {
      if (empty($sessionId)) 
      {
        $error = $api['error']['message']['value'];

        $history = [
          'title' => $username,
          'action' => '<label class="badge badge-danger">LOGIN ERROR SAP</label>',
          'desc' => 'Error login ke SAP dengan pesan <strong>'.$error.'</strong>'
        ];

        History::create($history);

        $callback = array(
          'message' => 'sap-error',
          'text' => $error
        );
      }
      else
      {
        $header = [
          "B1S-ReplaceCollectionsOnPatch: True",
          "Cookie: ".$headers,
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json"
        ];
    
        $url_sales = 'https://192.168.1.81:50000/b1s/v1/DeliveryNotes';
        $api_sales = $this->pushDeliverySap($header,$url_sales,json_encode($json));
    
        // dd($api_sales);
        if(isset($api_sales['DocNum']))
        {
          $lines = $this->decodeJsonLines($api_sales['DocumentLines'],$api_sales['DocEntry']);
          $branch = Branch::where('BPLid',$api_sales['BPL_IDAssignedToInvoice'])->first();
    
          $DocTotal = $api_sales['DocTotal'] - $api_sales['VatSum'];
    
          $header = [
            'CardCode' => $api_sales['CardCode'],
            'NumAtCard' => $api_sales['NumAtCard'],
            'DocNum' => $api_sales['DocNum'],
            'DocEntry' => $api_sales['DocEntry'],
            'VatSum' => $api_sales['VatSum'],
            'DocTotal' => $DocTotal,
            'DocStatus' => "O",
            'DocDate' => $api_sales['DocDate'],
            'DocDueDate' => $api_sales['DocDueDate'],
            'BPLId' => $api_sales['BPL_IDAssignedToInvoice'],
            'SalesPersonCode' => $api_sales['SalesPersonCode'],
            'U_NOPOLISI' => $api_sales['U_NOPOLISI'],
            'U_NOPOLISI2' => $api_sales['U_NOPOLISI2'],
            'Comments' => $api_sales['Comments'],
            'Branch' => $branch->id
          ];
    
          $post = DeliveryHeader::create($header);
          if($post)
          {
						$numAtCard = str_replace('KINO-','',$json['NumAtCard']);

            $data2 = [
              'DocNum'=>$api_sales['DocNum']
            ];
            VdistHeader::where('NumAtCard',$numAtCard)->update($data2);
            
            DeliveryLines::insert($lines);
          }
    
          $history = [
            'title' => $username,
            'history_category_id' => 2,
            'card_code' => $api_sales['CardCode'],
            'desc' => 'Sukses push data <strong>'.$api_sales['CardCode'].'</strong> Delivery Order ke SAP dengan Document Number <strong>'.$api_sales['DocNum'].'</strong>'
          ];
    
          History::create($history);
    
          $callback = array(
            'message' => 'sukses',
            'docnum' => $api_sales['DocNum']
          );
        }
        else
        {
          $error = $api_sales['error']['message']['value'];
    
          $history = [
            'title' => $username,
            'history_category_id' => 2,
            'card_code' => $json['NumAtCard'],
            'desc' => 'Error push data Delivery Order ke SAP dengan pesan <strong>'.$error.'</strong>'
          ];
    
          History::create($history);
    
          $callback = array(
            'message' => 'error'
          );
        }
      }
    }

    return $callback;
  }

	public function getTotal($code)
  {
    // $get = PngLines::where('NumAtCard',$code)->groupBy('ItemCode')->get();

		$get = DB::table('view_kino_lines_active')
						 ->where('NumAtCard',$code)
						 ->sum('LineTotal');

    $total = isset($get) ? $get : 0;

    // foreach ($get as $value) 
    // {
    //   $linesTotal = $value->Quantity * $value->UnitPrice;

    //   $total += $linesTotal;
    // }

    return $total;
  }

  public function decodeJsonLines($row,$docentry)
  {
    $data = [];
    foreach ($row as $value) 
    {
      $disc1 = $value['U_DISC1'];
      $disc2 = $value['U_DISC2'];
      $disc3 = $value['U_DISC3'];
      $disc4 = $value['U_DISC4'];
      $disc5 = $value['U_DISC5'];
      $disc6 = $value['U_DISC6'];
      $disc7 = $value['U_DISC7'];
      $disc8 = $value['U_DISC8'];

      $discx1 = $value['U_DISCVALUE1'];
      $discx2 = $value['U_DISCVALUE2'];
      $discx3 = $value['U_DISCVALUE3'];
      $discx4 = $value['U_DISCVALUE4'];
      $discx5 = $value['U_DISCVALUE5'];
      $discx6 = $value['U_DISCVALUE6'];
      $discx7 = $value['U_DISCVALUE7'];
      $discx8 = $value['U_DISCVALUE8'];

      $data[] = [
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $value['Quantity'],
        'TaxCode' => $value['TaxCode'],
        'UnitPrice' => $value['UnitPrice'],
        'CostingCode' => $value['CostingCode'],
        'CostingCode2' => $value['CostingCode2'],
        'CostingCode3' => $value['CostingCode3'],
        'UnitMsr' => $value['MeasureUnit'],
        'UomCode' => $value['UoMCode'],
        'UomEntry' => $value['UoMEntry'],
        'NumPerMsr' => $value['UnitsOfMeasurment'],
        'WarehouseCode' => $value['WarehouseCode'],
        'U_DISC1' => isset($disc1) ? $disc1 : "0",
        'U_DISCVALUE1' => $discx1,
        'U_DISC2' => isset($disc2) ? $disc2 : "0",
        'U_DISCVALUE2' => $discx2,
        'U_DISC3' => isset($disc3) ? $disc3 : "0",
        'U_DISCVALUE3' => $discx3,
        'U_DISC4' => isset($disc4) ? $disc4 : "0",
        'U_DISCVALUE4' => $discx4,
        'U_DISC5' => isset($disc5) ? $disc5 : "0",
        'U_DISCVALUE5' => $discx5,
        'U_DISC6' => isset($disc6) ? $disc6 : "0",
        'U_DISCVALUE6' => $discx6,
        'U_DISC7' => isset($disc7) ? $disc7 : "0",
        'U_DISCVALUE7' => $discx7,
        'U_DISC8' => isset($disc8) ? $disc8 : "0",
        'U_DISCVALUE8' => $discx8,
        'BaseEntry' => $value['BaseEntry'],
        'BaseType' => $value['BaseType'],
        'BaseLine' => $value['BaseLine'],
        'DocEntry' => $docentry
      ];
    }

    return $data;
  }

  public function pushDeliverySap($header,$url,$post)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => $header,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

	public function cekData($numAtCard)
  {
    return VdistHeader::where('NumAtCard',$numAtCard)->get();
  }

	public function getItemDetail($val, $whsCode)
  {
		$item = Item::where('csn',$val)
								->where('flag_active','Y')
								->whereNotNull('barcode')
								->where('WhsCode', $whsCode)
								->limit(1)
								->first();
    return $item; 
  }

	public function getItemDetailBonus($val, $whsCode)
  {
		$item = Item::where('csn',$val)
								->where('flag_bonus','Y')
								->where('WhsCode', $whsCode)
								->first();

    return $item; 
  }

  public function getSlpDetail($code_kino)
	{
		return Sales::where('code_kino',$code_kino)->first();
	}

	public function getSalesDetail($code)
	{
		return Sales::where('code',$code)->first();
	}

	public function getCustomerDetail($cardCode)
  {
    $post = [
      'CardCode' => $cardCode
    ];

    $function = 'getCustomerId';

		$get = callSapApiLtaWithPost($function,json_encode($post));		

		return $get;
  }

  public function getTopCustomer($post)
  {
    $function = 'getTopCustomer';

    $post = [
      'CardCode' => $post
    ];

    $get = callSapApiLtaWithPost($function,json_encode($post));

    return $get;
  }

  public function cekStokSap($itemCode, $whsCode)
  {
    $post_available = [
      'ItemCode' => $itemCode,
      'WhsCode' => $whsCode
    ];
    
    $available = getAvailable(json_encode($post_available));
    return isset($available['available']) ? $available['available'] : 0;
  }
}