<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Models\ArcmDetail;
use App\Models\ArcmDetailLines;
use App\Models\History;
use App\Models\ReturnApproval;
use App\Models\ReturnDetail;
use App\Models\ReturnDetailLines;
use App\Models\ReturnTempHeader;
use App\Models\ReturnTempLines;
use App\Models\Sales;
use App\Services\ApiServices;
use App\Services\ReturnRequestServices;
use App\Services\ReturnServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
  public function __construct(ReturnServices $services)
  {
    $this->service = $services;
  }

  public function index()
  {
    $assets = [
      'style' => array(
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

    $data = [
      'title' => 'Return',
      'assets' => $assets
    ];

    return view('sap.return.index')->with($data);
  }

  public function search(Request $request)
  {
    $docnum = $request->docnum;

    $sap = $this->service->detail($docnum);

    if ($sap['message']=='success') 
    {
      $callback = array(
        'message' => 'sukses',
        'docnum' => $sap['docnum']
      );
    }
    else
    {
      $callback = array(
        'message' => 'error'
      );
    }

    echo json_encode($callback);
  }

  public function detail(Request $request)
  {
    $docnum = $request->docnum;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css',
      ),
      'script' => array(
        'assets/plugins/sweetalert2/sweetalert2.min.js',
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js'
      )
    ];

    $row = $this->service->detail_data($docnum);

    $data = [
      'title' => "Detail - Return",
      'assets' => $assets,
      'local_currency' => 'Local Currency',
      'header' => $row['header'],
      'lines' => $row['lines']
    ];
    
    return view('sap.return.detail')->with($data);
  }

  public function print($docNum)
  {
    $row = $this->service->detail_data($docNum);

    $data = [
      'header' => $row['header'],
      'lines' => $row['lines']
    ];
    
    return view('sap.return.detail.print')->with($data);
  }

  public function temp(Request $request)
  {
    $docnum = $request->docnum;

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

		$alasan = [
			'1' => 'CACAT PRODUK / EXPIRED',
			'2' => 'TOLAKAN TOKO (SPV HUB TOKO)',
			'3' => 'TERLAMBAT KIRIM / PO EXPIRED'
		];

    $get = $this->service->getDataTempDetail($docnum);
    $branch = Branch::get();
    $series = Branch::where('BPLid',$get['BPLId'])->first();
    $branchx = Branch::find($get['Branch']);

    // $customer = Customer::where('code',$get['CardCode'])->first();

		$post_customer = [
			'CardCode' => $get['CardCode']
		];

		$customer = getCustomerId(json_encode($post_customer));

    $lines = $this->service->getDataTempLines($get['DocEntry']);

    $sales = Sales::find($get['SalesPersonCode']);

		$post_sales = [
			'SlpCode' => $get['SalesPersonCode']
		];

		$sales = getSalesEmployeeId(json_encode($post_sales));

		$docDate = date('Y-m-d');

		$post = [
			'CardCode' => $get['CardCode']
		];

		$getTop = $this->service->getTopCustomer(json_encode($post));
		$top = "+".$getTop." days";

		$docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $data = [
      'title' => "Detail - Return",
      'assets' => $assets,
      'row' => $get,
      'local_currency' => 'Local Currency',
      'series' => $series->sndo,
      'branch' => $branch,
      'branch_title' => $branchx->title,
      'branch_reg' => $branchx->VatRegNum,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'lines' => $lines,
      'sales' => $sales,
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiah($get['DocTotal']),
      'VatSum' => rupiah($get['VatSum']),
      'TotalSum' => rupiah($get['TotalSum']),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
			'docDate' => $docDate,
			'docDueDate' => $docDueDate,
			'alasan' => $alasan
    ];

    return view('sap.return.temp')->with($data);
  }

  public function temp_lines_edit(Request $request)
  {
    $id = $request->id;

    $get = ReturnTempLines::find($id);

    $data = [
      'title' => 'Edit - Return Lines',
      'row' => $get
    ];

    return view('sap.return.temp.edit')->with($data);
  }

  public function temp_lines_update(Request $request,$id)
  {
    $get = ReturnTempLines::find($id);
    $docentry = $get->DocEntry;
    

    $beforeDisc = $request->Quantity * $get->UnitPrice;

    $disc1 = $get->U_DISC1;
    $disc2 = $get->U_DISC2;
    $disc3 = $get->U_DISC3;
    $disc4 = $get->U_DISC4;
    $disc5 = $get->U_DISC5;
    $disc6 = $get->U_DISC6;
    $disc7 = $get->U_DISC7;
    $disc8 = $get->U_DISC8;

    $discx1 = ($disc1 / 100) * $beforeDisc;
    $discx2 = ($disc2 / 100) * ($beforeDisc - $discx1);
    $discx3 = ($disc3 / 100) * ($beforeDisc - $discx1 - $discx2);
    $discx4 = ($disc4 / 100) * ($beforeDisc - $discx1 - $discx2 - $discx3);
    $discx5 = ($disc5 / 100) * ($beforeDisc - $discx1 - $discx2 - $discx3 - $discx4);
    $discx6 = ($disc6 / 100) * ($beforeDisc - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);
    $discx7 = ($disc7 / 100) * ($beforeDisc - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);
    $discx8 = ($disc8 / 100) * ($beforeDisc - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);
    
    $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;
    $total = $beforeDisc - $disc_calx;
      
    $data = [
      'Quantity' => $request->Quantity,
      "U_DISCVALUE1" => $discx1,
      "U_DISCVALUE2" => $discx2,
      "U_DISCVALUE3" => $discx3,
      "U_DISCVALUE4" => $discx4,
      "U_DISCVALUE5" => $discx5,
      "U_DISCVALUE6" => $discx6,
      "U_DISCVALUE7" => $discx7,
      "U_DISCVALUE8" => $discx8,
      'LineTotal' => $total 
    ];

    $post = ReturnTempLines::find($id)->update($data);

    if($post)
    {
      $sum = DB::table('return_temp_lines')
               ->where('DocEntry',$docentry)
               ->sum('LineTotal');
      
      // dd($sum);

      $header = [
        'DocTotal' => $sum,
        'VatSum' => $sum * 0.11
      ];

      ReturnTempHeader::where('DocEntry',$docentry)->update($header);

      $alert = array(
        'type' => 'success',
        'message' => 'Return berhasil di update !'
      );
  
      return redirect()->back()->with($alert);
    }
  }

  public function temp_lines_delete($id)
  {
    $get = ReturnTempLines::find($id);
    $docentry = $get->DocEntry;

    $post = ReturnTempLines::find($id)->delete();
    if($post)
    {
      $sum = DB::table('return_temp_lines')
               ->where('DocEntry',$docentry)
               ->sum('LineTotal');

      $header = [
        'DocTotal' => $sum,
        'VatSum' => $sum * 0.11
      ];

      ReturnTempHeader::where('DocEntry',$docentry)->update($header);

      $alert = array(
        'type' => 'success',
        'message' => 'Return berhasil di delete !'
      );
  
      return redirect()->back()->with($alert);
    }
  }

  public function push(Request $request)
  {
    $apiService = new ApiServices;

		$service = new ReturnRequestServices;

    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

    $id = $request->id;
		$alasan = $request->U_ALASANRETUR;

    $get = ReturnTempHeader::where('DocNum',$id)->first();
		$docDate = date('Y-m-d');
    $docentry = $get['DocEntry'];
    $docnum = $get['DocNum'];
		$cardCode = $get['CardCode'];

    $json = $this->service->jsonHeader($id, $docDate, $alasan);

    // dd($json);

    $db = "LTALIVE2020";
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
      'DocDate' => $docDate
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
			$api_sales = $service->postReturn($header,$url_sales,json_encode($json));
	
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
	
					ReturnTempHeader::where('DocEntry',$docentry)->delete();
					ReturnTempLines::where('DocEntry',$docentry)->delete();
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
					'card_code' => $cardCode,
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

  public function update_printed(Request $request)
  {
    // dd($request->all());

    $id = $request->docnum;

    return $this->service->updatePrinted($id);
  }

  public function approval()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/datatables/custom.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/datatables/datatables.min.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

    $get = $this->service->getApprovalData();

    $data = [
      'title' => 'E-Return | Approval Return',
      'assets' => $assets,
      'row' => $get
    ];

    return view('sap.return.approval.index')->with($data);
  }

  public function approval_detail(Request $request)
  {
    $id = $request->id;

    $row = $this->service->approvalDetail($id);

    $data = [
      'title' => 'Approval Return - '.$id,
      'row' => $row,
      'total' => $row['header']['DocTotal'],
      'id' => $id
    ];

    return view('sap.return.approval.detail')->with($data);
  }

  public function approval_print($id)
  {
    $row = $this->service->approvalPrint($id);
    
    $data = [
      'header' => $row['header'],
      'lines' => $row['lines']
    ];

    return view('sap.return.approval.print')->with($data);
  }

	public function pushCreditNotes(Request $request)
	{
		// dd($request->all());
		$apiService = new ApiServices;
		$services = new ReturnServices;

		$user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;
    $user_id = auth()->user()->id;

		$id = $request->id;
    $date = $request->docDate;
		$numAtCardx = $request->numAtCard;

		$get = ReturnDetail::where('DocEntry',$id)->first();
    $docentry = $get['DocEntry'];
    $docnumx = $get['DocNum'];
		$cardCodex = $get['CardCode'];

    $json = $services->jsonCreditNotes($id,$date,$numAtCardx);

		// dd($json);

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
	
			$url_sales = 'https://192.168.1.81:50000/b1s/v1/CreditNotes';
			$api_sales = $services->pushCreditNotes($header,$url_sales,json_encode($json));
	
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
	
				$post_return = [
					'number' => $api_sales['NumAtCard'],
					'DocNumCN' => $api_sales['DocNum'],
					'DocDateCN' => $date
				];
	
				$services->trigger_return_erp(json_encode($post_return));
	
				$header = [
					'Series' => $branch->snrtr,
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
					'PLAT_MIX' => $api_sales['U_NOPOLISI'],
					'PLAT_PNG' => $api_sales['U_NOPOLISI2'],
					'Comments' => $api_sales['Comments'],
					'OcrCode' => $lines[0]['OcrCode'],
					'OcrCode2' => $lines[0]['OcrCode2'],
					'WhsCode' => $lines[0]['WhsCode'],
					'U_NAME' => $users_sign['Username'],
					'Printed' => 'N',
					'DocStatus' => 'O'
				];
				
				$post = ArcmDetail::create($header);
	
				if($post)
				{
					ArcmDetailLines::insert($lines);
	
					$post2 = [
						'DocStatus' => 'C'
					];
	
					ReturnDetail::where('DocNum',$docnumx)->update($post2);
				}
	
				// $history2 = [
				//   'title' => $username,
				//   'history_category_id' => 5,
				//   'card_code' => $api_sales['CardCode'],
				//   'desc' => $response_trigger
				// ];
	
				// History::create($history2);
	
				$history = [
					'title' => $username,
					'history_category_id' => 5,
					'card_code' => $api_sales['CardCode'],
					'desc' => 'Sukses push data <strong>'.$api_sales['CardCode'].'</strong> A/R Credit Notes ke SAP dengan Document Number <strong>'.$api_sales['DocNum'].'</strong>'
				];
	
				History::create($history);
	
				// dd($post_return);
	
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
					'history_category_id' => 5,
					'card_code' => $cardCodex,
					'desc' => 'Error push data A/R Credit Notes ke SAP dengan pesan <strong>'.$error.'</strong>'
				];
	
				History::create($history);
	
				$callback = array(
					'message' => 'error'
				);
	
				echo json_encode($callback);
			}
		}
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

	public function delete_lines_mark(Request $request)
	{
		// dd($request->all());	

		$data = $request->data;
		$kd = $request->kd;
		$docEntry = $request->docEntry;

		$selectedItems = json_decode($data);

		foreach ($selectedItems as $item) 
		{
			ReturnTempLines::find($item)->delete();
		}

		$sum = DB::table('return_temp_lines')
							->where('DocEntry',$docEntry)
							->sum('LineTotal');

		$header = [
			'DocTotal' => $sum,
			'VatSum' => $sum * 0.11
		];

		ReturnTempHeader::where('DocEntry',$docEntry)->update($header);

		$callback = array(
			'message' => 'sukses',
			'docnum' => $kd
		);

		echo json_encode($callback);
	}
}
