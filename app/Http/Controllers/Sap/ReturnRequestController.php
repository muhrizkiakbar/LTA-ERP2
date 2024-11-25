<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ClosingDate;
use App\Models\History;
use App\Models\Item;
use App\Models\ReturnDetail;
use App\Models\ReturnDetailLines;
use App\Models\ReturnDetailTemp;
use App\Models\Warehouse;
use App\Models\WarehouseList;
use App\Services\ApiServices;
use App\Services\ReturnRequestServices;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
  public function index()
	{
		$users_id = auth()->user()->id;

		$assets = [
      'style' => array(
        'assets/plugins/datatables/custom.css',
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/datatables/datatables.min.js',
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js',
        'assets/plugins/select2/select2.min.js'
      )
    ];

		$sales = getSalesEmployee();

		$alasan = [
			'1' => 'CACAT PRODUK / EXPIRED',
			'2' => 'TOLAKAN TOKO (SPV HUB TOKO)',
			'3' => 'TERLAMBAT KIRIM / PO EXPIRED'
		];

    $date_closing = ClosingDate::where('status',1)
                               ->orderBy('id','DESC')
                               ->limit(1)
                               ->first();

		$pg_closing = ClosingDate::where('pg_backdate',1)->limit(1)->first();

		if (isset($date_closing)) 
		{
			$closing = $date_closing->date;
		} 
		else if (isset($pg_closing))
		{
			$closing = $pg_closing->date;
		}
		else
		{
			$closing = NULL;
		}

    $date = date('Y-m-d');
    $dueDate = date('Y-m-d',strtotime($date . "+1 days"));

		$exp = explode('-',$date);

		$minDate = $exp[0].'-'.$exp[1].'-01';

    $local_currency = [
      'Local Currency' => 'Local Currency'
    ];

    $data = [
			'title' => 'Return Request',
      'local_currency' => $local_currency,
      'sales' => $sales,
      'remarks' => "Return from ERP",
      'date' => $date,
      'dueDate' => $dueDate,
      'closing' => isset($closing) ? $closing : NULL,
			'assets' => $assets,
			'users_id' => $users_id,
			'alasan' => $alasan,
			'minDate' => $minDate
    ];

		return view('sap.return.req.index')->with($data);
	}

	public function search_customer(Request $request)
	{
		$cust = $request->cardName;

    $service = new ApiServices;

    $post = [
      'CardName' => $cust . "%"
    ];

    $get = $service->getCustomer(json_encode($post));

    $data = [
      'row' => $get
    ];

    return view('sap.return.req.customer')->with($data);
	}

	public function select_customer(Request $request)
	{
		$cust = $request->id;

    $service = new ApiServices;

    $post = [
      'CardCode' => $cust
    ];

    $get = $service->getCustomerId(json_encode($post));
    // dd($get);
    $CardCode = $get['CardCode'];
    $CardName = $get['CardName'];
    $SalesPersonCode = $get['U_SALESCODE'];
    $segment = $get['cseg4'];

    $post_sales = [
      'SlpCode' => $SalesPersonCode
    ];

    $sales = getSalesDetail(json_encode($post_sales));

    $callback = array(
      'customer' => $CardCode,
      'name' => $CardName,
      'segment' => $segment,
      'nopol1' => $get['NopolMix'],
      'nopol2' => $get['NopolPng'],
      'bplid' => getWarehouseDetail2($get['U_CLASS'])->BPLId,
      'sales' => $sales['SlpCode'],
			'price_list' => $get['PriceList'],
    );

    echo json_encode($callback);
	}

	public function search_item(Request $request)
	{
		$cust = $request->itemName;
    $code = $request->cardCode;

    $service = new ApiServices;

    $post = [
      'a.ItemName' => "%" . $cust . "%"
    ];

    // dd($code);

    if (empty($code)) {
      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    } else {
      $get = $service->getItem(json_encode($post));

      $data = [
        'row' => $get
      ];

      return view('sap.return.req.item')->with($data);
    }
	}

	public function select_item(Request $request)
	{
		$service = new ApiServices;

    $row = $request->all();
    $post = [
      'ItemCode' => $request->id
    ];

    $get = $service->getItemId(json_encode($post));
    // dd($get);

    $cek = Item::where('code', $request->id)->first();
    if (empty($cek)) {
      $item = [
        'code' => $get['ItemCode'],
        'title' => $get['ItemName'],
        'INIT1' => $get['INIT1'],
        'INIT2' => $get['INIT2'],
        'INIT3' => $get['INIT3'],
        'INIT4' => $get['INIT4'],
        'INIT5' => $get['INIT5'],
        'INIT6' => $get['INIT6'],
        'INIT7' => $get['INIT7'],
        'CDB' => $get['CDB']
      ];

      Item::create($item);
    } else {
      $item = [
        'INIT1' => $get['INIT1'],
        'INIT2' => $get['INIT2'],
        'INIT3' => $get['INIT3'],
        'INIT4' => $get['INIT4'],
        'INIT5' => $get['INIT5'],
        'INIT6' => $get['INIT6'],
        'INIT7' => $get['INIT7'],
        'CDB' => $get['CDB']
      ];

      Item::where('code', $request->id)->update($item);
    }

    $post_cust = [
      'CardCode' => $request->cardcode
    ];
    $cust = $service->getCustomerId(json_encode($post_cust));

    // $cust = Customer::where('code',$request->cardcode)->first();
    $class = $cust['U_CLASS'];
    $whs = Warehouse::where('code', $class)->first();
    $warehouse = $whs->title;

    $post2 = [
      'ItemNo' => $request->id,
      'CardCode' => $request->cardcode,
      'WhsCode' => $warehouse
    ];

    $UomData = $service->getUomDetail(json_encode($post2));

    $satuan = [
      'nisik' => $UomData['satuan_kecil'],
      'nisib' => $UomData['satuan_besar']
    ];

		$tax = [
			'PPNO11' => 'PPNO11',
			'OUT0%' => 'OUT0%'
		];

		$whsList = WarehouseList::where('code', $class)->pluck('title','title');

    $data = [
      'ItemCode' => $get['ItemCode'],
      'ItemName' => $get['ItemName'],
      'satuan' => $satuan,
      'warehouse' => $warehouse,
			'whsList' => $whsList,
      'CardCode' => $request->cardcode,
			'tax' => $tax
    ];
    return view('sap.return.req.item_insert')->with($data);
	}

	public function temp_load()
	{
		$services = new ReturnRequestServices;

		$users_id = auth()->user()->id;

    $row = $services->temp_table($users_id);
    
    $data = [
      'row' => $row
    ];

    return view('sap.return.req.table')->with($data);
	}

	public function temp_store(Request $request)
	{
		// dd($request->all());

		$service = new ApiServices;

		$servicesReturn = new ReturnRequestServices;

    $satuan = $request->Satuan;
    $jml_order = $request->Quantity;

    $post_cust = [
      'CardCode' => $request->CardCode
    ];
    $cust = $service->getCustomerId(json_encode($post_cust));
    // $cust = Customer::where('code',$request->CardCode)->first();

    $post2 = [
      'ItemCode' => $request->ItemCode,
      'CardCode' => $request->CardCode,
      'WhsCode' => $request->Warehouse
    ];

    $UomData = getUomDetail(json_encode($post2));

    if($satuan=="nisik")
    {
      $UnitMsr = $UomData['satuan_kecil'];
      $NumPerMsr = 1;
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_pcs'];
    }
    else
    {
      $UnitMsr = $UomData['satuan_besar'];
      $NumPerMsr = $UomData['nisib'];
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_ktn'];
    }

    $CostingCode2 = $UomData['item_group'];

    if($CostingCode2 != 'P&G')
    {
      $CostingCode2x = 'MIX';
    }
    else
    {
      $CostingCode2x = $CostingCode2;
    }

		$lineTotal = $Quantity * $UnitPrice;

		$users_id = auth()->user()->id;

		$post3 = ['UomEntry'=>$UnitMsr];
    $getUomEntry = $service->getUomEntry(json_encode($post3));
    $UomEntry = $getUomEntry['uom_entry'];

		$data2 = [
			'ItemCode' => $request->ItemCode,
			'UnitMsr' => $UnitMsr,
			'NumPerMsr' => $NumPerMsr,
			'TaxCode' => $request->TaxCode,
			'Quantity' => $Quantity,
			'Price' => $UnitPrice,
			'OcrCode' => $cust['U_CLASS'],
			'OcrCode2' => $CostingCode2x,
			'OcrCode3' => 'SAL',
			'WhsCode' => $request->Warehouse,
			'LineTotal' => $lineTotal,
			'users_id' => $users_id,
			'UomEntry' => $UomEntry
		]; 

		$cek = ReturnDetailTemp::where('ItemCode',$request->ItemCode)->where('users_id',$users_id)->get();
		if(count($cek) == 0)
		{
			$post = ReturnDetailTemp::create($data2);

			if ($post) 
			{
				$temp = $servicesReturn->temp_table($users_id);
				$totalBefore = array_sum(array_column($temp,'docTotal'));
				if ($temp[0]['TaxCode']=='OUT0%') 
				{
					$vatSum = 0;
				}
				else
				{
					$vatSum = $totalBefore * 0.11;
				}
				
				$total = $totalBefore + $vatSum;
			}

			$callback = array(
				'message' => 'sukses',
				'totalBefore' => rupiah($totalBefore),
				'vatSum' => rupiah($vatSum),
				'total' => rupiah($total)
			);

			echo json_encode($callback);
		}
		else
		{
			$callback = array(
				'message' => 'already'
			);

			echo json_encode($callback);
		}
	}

	public function temp_delete(Request $request)
	{
		$services = new ReturnRequestServices;

		$id = $request->id;
    $users_id = auth()->user()->id;

    $post = ReturnDetailTemp::find($id)->delete();
    if ($post) 
    {
      $temp = $services->temp_table($users_id);
      $totalBefore = array_sum(array_column($temp,'docTotal'));
      $vatSum = $totalBefore * 0.11;
      $total = $totalBefore + $vatSum;

      $callback = array(
        'message' => 'sukses',
        'totalBefore' => rupiah($totalBefore),
        'vatSum' => rupiah($vatSum),
        'total' => rupiah($total)
      );

      echo json_encode($callback);
    }
    else
    {
      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
	}

	public function discount(Request $request)
	{
		$services = new ReturnRequestServices;

		$id = $request->id;
		$cardCode = $request->cardCode;

    $lines = $services->getDataLinesDisc($id, $cardCode);

    $data = [
      'title' => 'Discount Calculation',
      'lines' => $lines,
      'id' => $id,
			'cardCode' => $cardCode
    ];

    return view('sap.return.req.discount')->with($data);
	}

	public function discount_update(Request $request)
	{
		// dd($request->all());

		$services = new ReturnRequestServices;
		
		$numAtCard = $request->numAtCard;
		$cardCode = $request->cardCode;

    $post_customer = [
      'CardCode' => $cardCode
    ];
    $customerx = getCustomerId(json_encode($post_customer));

    $subsegment = $customerx['cseg4'];

    $platinum = array('WS PLATINUM','WS PLATINUM COS');
    $diamond = array('WS DIAMOND','WS DIAMOND COS');
    $gold = array('WS GOLD','WS GOLD COS');
    $silver = array('WS SILVER','WS SILVER COS');

    $idx = $request->idx;

    $id = $request->id;
    $disc1 = $request->disc1;
    $disc2 = $request->disc2;
    $disc3 = $request->disc3;
    $disc4 = $request->disc4;
    $disc5 = $request->disc5;
    $disc6 = $request->disc6;
    $disc7 = $request->disc7;
    $disc8 = $request->disc8;

		// $disc1_rp = $request->disc1_rp;
    // $disc2_rp = $request->disc2_rp;
    // $disc3_rp = $request->disc3_rp;
    // $disc4_rp = $request->disc4_rp;
    // $disc5_rp = $request->disc5_rp;
    // $disc6_rp = $request->disc6_rp;
    // $disc7_rp = $request->disc7_rp;
    // $disc8_rp = $request->disc8_rp;

    $total = $request->total;

    $totalx2 = 0;
    $totalx22 = 0;

		foreach ($idx as $key) 
		{
			$totalx = $total[$key];
			$discx1 = ($disc1[$key] / 100) * $totalx;
			$discx2 = ($disc2[$key] / 100) * ($totalx - $discx1);
			$discx3 = ($disc3[$key] / 100) * ($totalx - $discx1 - $discx2);
			$discx4 = ($disc4[$key] / 100) * ($totalx - $discx1 - $discx2 - $discx3);
			$discx5 = ($disc5[$key] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4);
			$discx6 = ($disc6[$key] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);
			$discx7 = ($disc7[$key] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);
			$discx8 = ($disc8[$key] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

			$disc_cal = $disc1[$key] + $disc2[$key] + $disc3[$key] + $disc4[$key] + $disc5[$key] + $disc6[$key] + $disc7[$key] + $disc8[$key];

			$disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

			$totalxx = $totalx - $disc_calx;

			$dataxx[] = [
			  'totalBefore' => $totalx,
			  'discvalue_calc' => $disc_calx,
			  'total' => round($totalxx,2)
			];

			$data = [
				'U_DISC1' => $disc1[$key],
				'U_DISCVALUE1' => $discx1,
				'U_DISC2' => $disc2[$key],
				'U_DISCVALUE2' => $discx2,
				'U_DISC3' => $disc3[$key],
				'U_DISCVALUE3' => $discx3,
				'U_DISC4' => $disc4[$key],
				'U_DISCVALUE4' => $discx4,
				'U_DISC5' => $disc5[$key],
				'U_DISCVALUE5' => $discx5,
				'U_DISC6' => $disc6[$key],
				'U_DISCVALUE6' => $discx6,
				'U_DISC7' => $disc7[$key],
				'U_DISCVALUE7' => $discx7,
				'U_DISC8' => $disc8[$key],
				'U_DISCVALUE8' => $discx8,
				'DiscountPercent' => $disc_cal,
				'LineTotal' => $totalxx
			];

			ReturnDetailTemp::find($id[$key])->update($data);
		}
		
		$users_id = auth()->user()->id;

		$temp = $services->temp_table($users_id);

		$totalBefore = array_sum(array_column($dataxx,'total'));

		if ($temp[0]['TaxCode']=='OUT0%') 
		{
			$vatSum = 0;
		}
		else
		{
			$vatSum = $totalBefore * 0.11;
		}
		
		$total = $totalBefore + $vatSum;

		$callback = array(
			'message' => 'sukses',
			'totalBefore' => rupiah($totalBefore),
			'vatSum' => rupiah($vatSum),
			'total' => rupiah($total)
		);

		echo json_encode($callback);
  }

	public function store(Request $request)
	{
		// dd($request->all());

		$apiService = new ApiServices;

		$service = new ReturnRequestServices;

		$users_id = auth()->user()->id;
    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

		$cardcode = $request->cardCode;

    $date = $request->docDate;

		$docDate = $date;

		$top = "+1 days";
		$docDueDate = date('Y-m-d', strtotime($top, strtotime($docDate)));

		$push = [
      'CardCode' => $request->cardCode,
      'DocDueDate' => $docDueDate,
      'DocDate' => $docDate,
      'BPL_IDAssignedToInvoice' => $request->BplId,
      'SalesPersonCode'=> $request->SalesPersonCode,
      'NumAtCard' => $request->numAtCard,
      'Comments' => $request->Comments.' - '.$request->cardCode,
      'U_NOPOLISI' => $request->Nopol1,
      'U_NOPOLISI2' => $request->Nopol2,
			'U_ALASANRETUR' => $request->U_ALASANRETUR,
      'DocumentLines' => $this->temp_lines($users_id)
    ];

		// dd($push);

		$db = 'LTALIVE2020';
    $url = 'https://192.168.1.81:50000/b1s/v1/Login';

    $body = [
      'CompanyDB' => $db,
      'UserName' => $user,
      'Password' => $pass
    ];
    
    $api = $apiService->callApiLogin($body,$url);

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

      echo json_encode($callback);
    }
		else
		{
			$header = [
				"Cookie: ".$headers,
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			];
	
			$url_sales = 'https://192.168.1.81:50000/b1s/v1/Returns';
			$api_sales = $service->postReturn($header,$url_sales,json_encode($push));
	
			// dd($api_sales);
	
			if(isset($api_sales['DocNum']))
			{
				$lines = $this->decode_temp_lines($api_sales['DocumentLines'],$api_sales['DocEntry'],$api_sales['NumAtCard']);
	
				$branch = Branch::where('BPLid',$api_sales['BPL_IDAssignedToInvoice'])->first();
	
				$Bruto = $api_sales['DocTotal'] - $api_sales['VatSum'];
	
				$post_sales = [
					'SlpCode' => $api_sales['SalesPersonCode']
				];
	
				$sales = getSalesEmployeeId(json_encode($post_sales));
	
				$post_users = [
					'USERID' => $api_sales['UserSign']
				];
	
				$users_sign = getUserId(json_encode($post_users));
	
				$header = [
					'Series' => $branch->snrdn,
					'DocNum' => $api_sales['DocNum'],
					'DocEntry' => $api_sales['DocEntry'],
					'DocDate' => $api_sales['DocDate'],
					'DocDueDate' => $api_sales['DocDueDate'],
					'CardCode' => $api_sales['CardCode'],
					'CardName' => $api_sales['CardName'],
					'Address' => $api_sales['Address'],
					'SlpName' => $sales,
					'SalesPersonCode' => $api_sales['SalesPersonCode'],
					'NumAtCard' => $api_sales['NumAtCard'],
					'Bruto' => $Bruto,
					'VatSum' => $api_sales['VatSum'],
					'Netto' => $api_sales['DocTotal'],
					'Branch' => $branch->id,
					'BPLId' => $api_sales['BPL_IDAssignedToInvoice'],
					'U_NOPOLISI' => $api_sales['U_NOPOLISI'],
					'U_NOPOLISI2' => $api_sales['U_NOPOLISI2'],
					'Comments' => $api_sales['Comments'],
					'OcrCode' => $lines[0]['OcrCode'],
					'OcrCode2' => $lines[0]['OcrCode2'],
					'WhsCode' => $lines[0]['WhsCode'],
					'U_NAME' => $users_sign['Username'],
					'Printed' => 'N'
				];
				
				$post = ReturnDetail::create($header);
	
				if($post)
				{
					ReturnDetailLines::insert($lines);
					ReturnDetailTemp::where('users_id',$users_id)->delete();
				}
	
				$history = [
					'title' => $username,
					'history_category_id' => 3,
					'card_code' => $api_sales['CardCode'],
					'desc' => 'Sukses push data <strong>'.$api_sales['CardCode'].'</strong> Return ke SAP dengan Document Number <strong>'.$api_sales['DocNum'].'</strong>'
				];
	
				History::create($history);
	
				$callback = array(
					'message' => 'sukses',
					'docnum' => $api_sales['DocNum']
				);
	
				echo json_encode($callback);
			}
			else
			{
				$error = $api_sales['error']['message']['value'];
	
				$history = [
					'title' => $username,
					'history_category_id' => 3,
					'card_code' => $cardcode,
					'desc' => 'Error push data Return ke SAP dengan pesan <strong>'.$error.'</strong>'
				];
	
				History::create($history);
	
				$callback = array(
					'message' => 'error'
				);
	
				echo json_encode($callback);
			}
		}
	}

	public function temp_lines($users_id)
	{
		$data= [];
    $service = new ApiServices;

    $get = ReturnDetailTemp::where('users_id',$users_id)->get();

    // dd($get);

    foreach ($get as $value) 
    {
      $wareHouse = $value['WhsCode'];

      $Quantity = $value['Quantity'];
      $NumPerMsr = $value['NumPerMsr'];
      $UnitMsr = $value['UnitMsr'];


			$qty = $Quantity >= 1 ? $Quantity * $NumPerMsr : $Quantity;
			$UnitMsr = $value['UnitMsr'];
			$NumPerMsr = $value['NumPerMsr'];
			$UnitPrice = $value['Price'];
			$UomEntry = $value['UomEntry'];

			$total = $value['Quantity'] * $value['Price'];

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

      $disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
      $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $totalx = $total - round($disc_calx,2);

			if ($value['TaxCode']=='OUT0%') 
			{
				$taxCode = NULL;
			}
			else
			{
				$taxCode = $value['TaxCode'];
			}

      $data[] = [
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $qty,
				'VatGroup' => $value['TaxCode'],
        'TaxCode' => $taxCode,
        'UnitPrice' => $UnitPrice,
        'CostingCode' => $value['OcrCode'],
        'CostingCode2' => $value['OcrCode2'],
        'CostingCode3' => $value['OcrCode3'],
        'MeasureUnit' => $UnitMsr,
        'UoMCode' => $UnitMsr,
        'UoMEntry' => $UomEntry,
        'UnitsOfMeasurment' => $NumPerMsr,
        'WarehouseCode' => $wareHouse,
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
      ];
    }
    return $data;
	}

	public function decode_temp_lines($row,$docentry)
  {
    $data = [];
    $no = 0;
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
				'Dscription' => $value['ItemDescription'],
        'Quantity' => $value['Quantity'],
        'TaxCode' => $value['VatGroup'],
        'Price' => $value['UnitPrice'],
        'OcrCode' => $value['CostingCode'],
        'OcrCode2' => $value['CostingCode2'],
        'OcrCode3' => $value['CostingCode3'],
        'UnitMsr' => $value['MeasureUnit'],
        'UomCode' => $value['UoMCode'],
        'UomEntry' => $value['UoMEntry'],
        'NumPerMsr' => $value['UnitsOfMeasurment'],
        'WhsCode' => $value['WarehouseCode'],
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
        'DocEntry' => $docentry,
        'LineNum' => $value['LineNum']
      ];
    }

    return $data;
  }
}
