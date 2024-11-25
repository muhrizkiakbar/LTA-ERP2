<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DeliveryHeader;
use App\Models\DeliveryLines;
use App\Models\History;
use App\Models\VdistHeader;
use App\Models\VdistLines;
use App\Models\VdistTemp;
use App\Models\Warehouse;
use App\Models\WarehouseVdist;
use Illuminate\Support\Facades\DB;
use Str;

class VdistServices
{
  public function getData()
  {
    $role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    if ($role==1) 
    {
      $get = VdistHeader::whereNull('DocNum')->where('flag_label','VDIST')->orderBy('id','DESC')->get();
    }
    else
    {
      $branch = Branch::find($branchx);
      $get = VdistHeader::whereNull('DocNum')->where('flag_label','VDIST')->where('BPLId',$branch->BPLid)->orderBy('id','DESC')->get();
    }

    return $get;
  }

  public function sync($body)
  {
    $branch = WarehouseVdist::where('vdist',$body['branch'])->first();

    $get = $this->getSyncData(json_encode($body));

    $no = 1;
    $nox = 0;

    // dd($get);

    if(isset($get['data']))
    {
      foreach ($get['data'] as $key => $value) 
      { 
        $cek = $this->cekData($value['NumAtCard']);

        if (count($cek)==0)
        {
          $sales = $this->getSalesDetail($value['SalesPersonCode']);
          if (!empty($sales)) 
          {
            $cardCode = trim($value['CardCode']);
            $customer = $this->getCustomerDetail($cardCode);
            $uclass = isset($customer['U_CLASS']) ? $customer['U_CLASS'] : '' ;
            $warehouse = $branch->title;

            if ($cardCode!='' || !empty($uclass)) 
            {
              $lines = $this->insertVdistLines($value['Lines'],$value['NumAtCard'],$warehouse,$uclass);
              $slug = Str::slug($value['NumAtCard']);
              
              $header = [
                'Branch' => $branch->id,
                'NumAtCard' => $value['NumAtCard'],
                'DocDate' => $value['DocDate'],
                'DocDueDate' => $value['DocDueDate'],
                'CardCode' => $value['CardCode'],
                'CardName' => isset($customer['CardName']) ? $customer['CardName'] : '-',
                'Address' => isset($customer['Address']) ? $customer['Address'] : '-' ,
                'SalesPersonCode' => $sales['SlpCode'],
                'SalesPersonName' => $sales['SlpName'],
                'BPLId' => $branch->BPLId,
                'U_NOPOLISI' => isset($customer['NopolMix']) ? $customer['NopolMix'] : '-',
                'U_NOLOLISI2' => isset($customer['NopolPng']) ? $customer['NopolPng'] : '-',
                'Comments' => 'From ERP Sync - '.$value['NumAtCard'],
                'DocTotal' => $lines['DocTotal'],
                'slug' => $slug
                // 'u_class' => isset($customer['U_CLASS']) ? $customer['U_CLASS'] : '-'
              ];

              VdistHeader::create($header);

              $nox = $no++;
            }
          }
        }
      }

      // dd($header);
    }
    else
    {
      $nox = 0;
    }

    if ($nox > 0) 
    {
      $callback = array(
        'message' => 'success'
      );
    }
    else
    {
      $callback = array(
        'message' => 'error'
      );
    }

    return $callback;
  }

  public function import($data, $branch, $date)
  {
		$users_id = auth()->user()->id;
  
    $whs = WarehouseVdist::where('vdist',$branch)->first();

    // $val = [];

		$val = [];
		// $itemDetails = [];
		// $cekStokCache = [];

		// dd($data->toArray());

		// $skuItems = array_column($data->toArray(), 'SKU ITEM');
		// $itemDetails = $this->getItemDetail($skuItems);
		// $cekStokCache[] = $this->cekStokSap($skuItems, $whs->title);

		// dd($cekStokCache);

    foreach ($data as $key => $value) 
    {
			if($branch!='EZ')
			{
				$exp_cust = substr($value['KODE CUSTOMER'],2);
				$cardCode = $exp_cust;

				$exp_sales = substr($value['KODE SALES'],2);
				$slpCode = $exp_sales;
			}
			else
			{
				$cardCode = $value['KODE CUSTOMER'];
				$slpCode = $value['KODE SALES'];
			}

      $item = $this->getItemDetail($value['SKU ITEM']);
			$unitPrice = $value['HARGA PER KARTON'] / $value['NISIB'];

      if (isset($item['ItemCode'])) 
      {
        $cekStok = $this->cekStokSap($item['ItemCode'], $whs->title);
        if ($cekStok < $value['QTY JUAL']) 
        {
          $qty_real = 0;
          $total = 0 * $unitPrice;
        }
        else
        {
          $qty_real = $value['QTY JUAL'];
          $total = $value['QTY JUAL'] * $unitPrice;
        }

				$qty_real = ($cekStok < $value['QTY JUAL']) ? 0 : $value['QTY JUAL'];
        $total = $qty_real * $unitPrice;

        $disc1 = (float) $value['DISC1'];
        $disc2 = (float) $value['DISC2'];
        $disc3 = (float) $value['DISC3'];
        $disc4 = $value['DISC4']!='' ? (float) $value['DISC4'] : 0;

        $discx1 = ($disc1 / 100) * $total;
        $discx2 = ($disc2 / 100) * ($total - $discx1);
        $discx3 = ($disc3 / 100) * ($total - $discx1 - $discx2);
        $discx4 = ($disc4 / 100) * ($total - $discx1 - $discx2 - $discx3);

        $disc_cal = $disc1+$disc2+$disc3+$disc4;
        $disc_calx = $discx1+$discx2+$discx3+$discx4;

        $totalx = $total - round($disc_calx, 2);

        $itemName = strval($item['ItemName']);

        $val[] = [
          'users_id' => $users_id,
					'NumAtCard' => $value['NOMOR FAKTUR'],
					'ItemCode' => $item['ItemCode'],
					'ItemCodeVdist' => $value['SKU ITEM'],
					'ItemName' => $itemName,
					'Quantity' => $qty_real,
					'QuantityVdist' => $value['QTY JUAL'],
					'TaxCode' => 'PPNO11',
					'Price' => $unitPrice,
					'UnitPrice' => $unitPrice,
					'CostingCode' => $whs->cogs,
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
					'WarehouseCode' => $whs->title,
					'CardCode' => $cardCode,
					'SlpCodeSfa' => $slpCode,
					'DocDate' => $date,
					'flag_label' => 'VDIST'
        ]; 
      }
    }

		// dd($val);

    $chunkSize = count($val) < 50 ? 25 : 50;
		$dataChunks = array_chunk($val, $chunkSize);

		// dd($dataChunks);

		// return $data;

    foreach ($dataChunks as $chunk) 
		{
			DB::table('vdist_temp')->insert($chunk);
		}

    $this->importGenerateOrder($users_id, $date, $whs->vdist);

    VdistTemp::where('users_id',$users_id)->delete();

		$callback = array(
			'message' => 'success'
		);

		return $callback;
  }

  public function importGenerateOrder($users_id, $date, $branch)
  {
    $get_group = VdistTemp::where('users_id',$users_id)
                          ->where('DocDate',$date)
                          ->groupBy('NumAtCard')
                          ->get(); 

    $branch = WarehouseVdist::where('vdist',$branch)->first();

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
            $docDueDate= date('Y-m-d', strtotime($top, strtotime($date)));

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
              'SalesPersonCode' => $sales['SlpCode'],
              'SalesPersonName' => $sales['SlpName'],
              'BPLId' => $branch->BPLId,
              'U_NOPOLISI' => isset($customer['NopolMix']) ? $customer['NopolMix'] : '-',
              'U_NOLOLISI2' => isset($customer['NopolPng']) ? $customer['NopolPng'] : '-',
              'Comments' => 'From ERP Sync - '.$value->NumAtCard,
              'DocTotal' => $lines['DocTotal'],
              'slug' => $slug,
							'flag_label' => 'VDIST'
            ];

            VdistHeader::create($header);
          }
        }
      }
    }
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

  public function cekData($numAtCard)
  {
    return VdistHeader::where('NumAtCard',$numAtCard)->get();
  }

  public function getDataLines($id)
  {
    // return VdistLines::where('NumAtCard',$id)->get();
		return DB::table('view_vdist_lines_active')->where('NumAtCard',$id)->get();
  }

  public function getSyncData($post)
  {
    $url = 'https://gdilta.laut-timur.tech/api/getSyncData';
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
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
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

  public function insertVdistLines($lines,$numAtCard,$whsCode,$uclass)
  {
    $data = [];

    foreach ($lines as $value) 
    {
      $item = $this->getItemDetail($value['ItemCodeVdist']);
      if (isset($item['ItemCode'])) 
      {
        $cekStok = $this->cekStokSap($item['ItemCode'], $whsCode);
        if ($cekStok < $value['Quantity']) 
        {
          $total = 0 * $value['UnitPrice'];

          $disc1 = $value['DISC1'];
          $disc3 = $value['DISC3'];
          $disc4 = $value['DISC4'];

          $discx1 = ($disc1 / 100) * $total;
          $discx3 = ($disc3 / 100) * ($total - $discx1);
          $discx4 = ($disc4 / 100) * ($total - $discx1 - $discx3);

          $disc_cal = $disc1+$disc3+$disc4;
          $disc_calx = $discx1+$discx3+$discx4;

          $totalx = $total - round($disc_calx,2);

          $data[] = [
            'NumAtCard' => $numAtCard,
            'ItemCode' => $item['ItemCode'],
            'ItemCodeVdist' => $value['ItemCodeVdist'],
            'ItemName' => $item['ItemName'],
            'Quantity' => 0,
            'QuantityVdist' => $value['Quantity'],
            'TaxCode' => 'PPNO11',
            'Price' => $value['UnitPrice'],
            'UnitPrice' => $value['UnitPrice'],
            'CostingCode' => $uclass,
            'CostingCode2' => 'MIX',
            'CostingCode3' => 'SAL',
            'UoMCode' => $item['UomCode'],
            'UoMEntry' => $item['UomEntry'],
            'UnitsOfMeasurment' => 1,
            'LineTotal' => round($totalx,2),
            'DiscountPercent' => round($disc_cal,2),
            'U_DISC1' => isset($disc1) ? $disc1 : "0",
            'U_DISCVALUE1' => $discx1,
            'U_DISC3' => isset($disc3) ? $disc3 : "0",
            'U_DISCVALUE3' => $discx3,
            'U_DISC4' => isset($disc4) ? $disc4 : "0",
            'U_DISCVALUE4' => $discx4,
            'WarehouseCode' => $whsCode
          ];
        }
        else
        {
          $total = $value['Quantity'] * $value['UnitPrice'];

          $disc1 = $value['DISC1'];
          $disc3 = $value['DISC3'];
          $disc4 = $value['DISC4'];

          $discx1 = ($disc1 / 100) * $total;
          $discx3 = ($disc3 / 100) * ($total - $discx1);
          $discx4 = ($disc4 / 100) * ($total - $discx1 - $discx3);

          $disc_cal = $disc1+$disc3+$disc4;
          $disc_calx = $discx1+$discx3+$discx4;

          $totalx = $total - round($disc_calx,2);

          $data[] = [
            'NumAtCard' => $numAtCard,
            'ItemCode' => $item['ItemCode'],
            'ItemCodeVdist' => $value['ItemCodeVdist'],
            'ItemName' => $item['ItemName'],
            'Quantity' => $value['Quantity'],
            'QuantityVdist' => $value['Quantity'],
            'TaxCode' => 'PPNO11',
            'Price' => $value['UnitPrice'],
            'UnitPrice' => $value['UnitPrice'],
            'CostingCode' => $uclass,
            'CostingCode2' => 'MIX',
            'CostingCode3' => 'SAL',
            'UoMCode' => $item['UomCode'],
            'UoMEntry' => $item['UomEntry'],
            'UnitsOfMeasurment' => 1,
            'LineTotal' => round($totalx,2),
            'DiscountPercent' => round($disc_cal,2),
            'U_DISC1' => isset($disc1) ? $disc1 : "0",
            'U_DISCVALUE1' => $discx1,
            'U_DISC3' => isset($disc3) ? $disc3 : "0",
            'U_DISCVALUE3' => $discx3,
            'U_DISC4' => isset($disc4) ? $disc4 : "0",
            'U_DISCVALUE4' => $discx4,
            'WarehouseCode' => $whsCode
          ];
        }        
      }
      else
      {
        $total = $value['Quantity'] * $value['UnitPrice'];

        $disc1 = $value['DISC1'];
        $disc3 = $value['DISC3'];
        $disc4 = $value['DISC4'];

        $discx1 = ($disc1 / 100) * $total;
        $discx3 = ($disc3 / 100) * ($total - $discx1);
        $discx4 = ($disc4 / 100) * ($total - $discx1 - $discx3);

        $disc_cal = $disc1+$disc3+$disc4;
        $disc_calx = $discx1+$discx3+$discx4;

        $totalx = $total - round($disc_calx,2);

        $data[] = [
          'NumAtCard' => $numAtCard,
          'ItemCode' => '',
          'ItemCodeVdist' => $value['ItemCodeVdist'],
          'ItemName' => '',
          'Quantity' => 0,
          'QuantityVdist' => $value['Quantity'],
          'TaxCode' => 'PPNO11',
          'Price' => $value['UnitPrice'],
          'UnitPrice' => $value['UnitPrice'],
          'CostingCode' => '',
          'CostingCode2' => '',
          'CostingCode3' => '',
          'UoMCode' => '',
          'UoMEntry' => '',
          'UnitsOfMeasurment' => 1,
          'LineTotal' => round($totalx,2),
          'DiscountPercent' => round($disc_cal,2),
          'U_DISC1' => isset($disc1) ? $disc1 : "0",
          'U_DISCVALUE1' => $discx1,
          'U_DISC3' => isset($disc3) ? $disc3 : "0",
          'U_DISCVALUE3' => $discx3,
          'U_DISC4' => isset($disc4) ? $disc4 : "0",
          'U_DISCVALUE4' => $discx4,
          'WarehouseCode' => ''
        ];
      }
    }

    VdistLines::insert($data);
    $DocTotal = array_sum(array_column($data,'LineTotal'));
    
    $result = [
      'DocTotal' => $DocTotal
    ];

    return $result;
  }

  public function pushVdist($id)
  {
    $get = VdistHeader::where('slug',$id)->first();
    // $date_closing = ClosingDate::where('status',1)->get();

    $docDate = $get->DocDate;

    $getTop = $this->getTopCustomer($get->CardCode);
    $top = "+".$getTop." days";

    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));
      
    $data = [
      'CardCode' => $get->CardCode,
      'DocDueDate' => $docDueDate,
      'DocDate' => $docDate,
      'BPL_IDAssignedToInvoice' => $get->BPLId,
      'SalesPersonCode'=> $get->SalesPersonCode,
      'NumAtCard' => $get->NumAtCard,
      'Comments' => $get->Comments,
      'U_NOPOLISI' => $get->U_NOPOLISI,
      'U_NOPOLISI2' => $get->U_NOPOLISI2,
      'DocumentLines' => $this->jsonVdistLines($get->NumAtCard)
    ];

    $push = $this->pushDelivery($data, $docDate, $get->NumAtCard);

    return $push;
  }

  public function jsonVdistLines($numAtCard)
  {
    // $get = VdistLines::where('NumAtCard',$numAtCard)->get();
		$get = DB::table('view_vdist_lines_active')->where('NumAtCard',$numAtCard)->get();

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

        $discx1 = $value->U_DISCVALUE1;
				$discx2 = $value->U_DISCVALUE2;
        $discx3 = $value->U_DISCVALUE3;
        $discx4 = $value->U_DISCVALUE4;

        $disc_cal = $disc1+$disc2+$disc3+$disc4;
        $disc_calx = $discx1+$discx2+$discx3+$discx4;

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
          'WarehouseCode' => $value->WarehouseCode,
          'BaseLine' => $no
        ];

        $no++;
      }      
    }

		// dd($data);

    return $data;
  }

  public function pushDelivery($json, $date, $numAtCard)
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
            $data2 = [
              'DocNum'=>$api_sales['DocNum']
            ];
            VdistHeader::where('NumAtCard',$json['NumAtCard'])->update($data2);
            
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

  public function getCustomerDetail($val)
  {
    $function = 'getCustomerId2';

    $post = [
      'CardCode' => $val
    ];

    $get = callSapApiLtaWithPost($function,json_encode($post));

    return $get;
  }

  public function getItemDetail($val)
  {
    $function = 'getItemVdistId';

    $post = [
      'ItemCode' => $val
    ];

    $get = callSapApiLtaWithPost($function,json_encode($post));

    return $get;
  }

  public function getSalesDetail($val)
  {
    $function = 'getSalesDetail';

    $post = [
      'U_SALESCODE' => $val
    ];

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
    
    $available = getInStock(json_encode($post_available));
    return isset($available['stok']) ? $available['stok'] : 0;
  }

	public function getTotal($code)
  {
    // $get = PngLines::where('NumAtCard',$code)->groupBy('ItemCode')->get();

		$get = DB::table('view_vdist_lines_active')
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

  public function unserved($post)
  {
    $branch_code = WarehouseVdist::where('vdist',$post['branch'])->first();

    $get = DB::table('vdist_lines')
             ->join('vdist_header', 'vdist_lines.NumAtCard', '=', 'vdist_header.NumAtCard')
             ->where('vdist_header.DocDate', $post['tgl_order'])
             ->where('vdist_lines.CostingCode',$branch_code->code)
             ->where('vdist_lines.Quantity',0)
						 ->where('vdist_header.flag_label','!=','KINO')
             ->select('vdist_header.NumAtCard', 'vdist_lines.ItemCode', 'vdist_lines.ItemCodeVdist', 'vdist_lines.ItemName', 'vdist_lines.QuantityVdist')
             ->get();
    
    return $get;
  }

  public function delete($id)
  {
    $get = VdistHeader::where('slug',$id)->first();

    VdistHeader::where('NumAtCard',$get->NumAtCard)->delete();
    VdistLines::where('NumAtCard',$get->NumAtCard)->delete();
  }

 
}