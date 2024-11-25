<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\ClosingDate;
use App\Models\Customer;
use App\Models\DeliveryHeader;
use App\Models\DeliveryLines;
use App\Models\History;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\OrderHeader;
use App\Models\OrderLines;
use App\Models\OrderLinesBatch;
use App\Models\OrderTemp;
use App\Models\Sales;
use App\Services\ApiServices;
use App\Services\SalesServices;
use App\Services\SfaMixServices;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
  public function __construct(SalesServices $service)
  {
    $this->service = $service;
  }

  public function index()
  {
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

    // dd($sales);
    
    $status = [
      'O' => 'Open',
      'C' => 'Close'
    ];

    $data = [
      'title' => 'Sales Order',
      'assets' => $assets,
      'sales' => $sales,
      'status' => $status
    ];

    return view('sap.sales.index')->with($data);
  }

  public function create()
  {
    $sales = getSalesEmployee();

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

    $local_currency = [
      'Local Currency' => 'Local Currency'
    ];

    $data = [
      'local_currency' => $local_currency,
      'sales' => $sales,
      'remarks' => "From SFA Input Manual Sync",
      'date' => $date,
      'dueDate' => $dueDate,
      'closing' => isset($closing) ? $closing : NULL,
    ];

    return view('sap.sales.create')->with($data);
  }

  public function create_temp(Request $request)
  {
    $service = new ApiServices;

    $users_id = auth()->user()->id;

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

      $UnitMsr2 = $UomData['satuan_besar'];
      $NumPerMsr2 = $UomData['nisib'];
      $UnitPrice2 = $UomData['harga_jual_ktn'];
    }
    else
    {
      $UnitMsr = $UomData['satuan_besar'];
      $NumPerMsr = $UomData['nisib'];
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_ktn'];

      $UnitMsr2 = $UomData['satuan_kecil'];
      $NumPerMsr2 = 1;
      $UnitPrice2 = $UomData['harga_jual_pcs'];
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

    $post3 = ['UomEntry'=>$UnitMsr];
    $getUomEntry = $service->getUomEntry(json_encode($post3));
    $UomEntry = $getUomEntry['uom_entry'];

    $post4 = ['UomEntry'=>$UnitMsr2];
    $getUomEntry2 = $service->getUomEntry(json_encode($post4));
    $UomEntry2 = $getUomEntry2['uom_entry'];

    $post_available = [
      'ItemCode' => $request->ItemCode,
      'WhsCode' => $request->Warehouse,
    ];
    
    $available = getAvailable(json_encode($post_available));
    $availablex = $available['available'];

    if ($availablex >= 1) 
    {
      $qty_real = $Quantity * $NumPerMsr;

      if ($availablex > $qty_real) 
      {
        $qty = $qty_real / $NumPerMsr;
      }
      else 
      {
        if ($UnitMsr=="KTN") 
        {
          $qty = $availablex;
          $UnitMsr = $UomData['satuan_kecil'];
          $NumPerMsr = 1;
          $UnitPrice = $UomData['harga_jual_pcs'];

          $UnitMsr2 = $UomData['satuan_besar'];
          $NumPerMsr2 = $UomData['nisib'];
          $UnitPrice2 = $UomData['harga_jual_ktn'];
        }
        else
        {
          $qty = $availablex / $NumPerMsr;
        }
      }

      $data2 = [
        'ItemCode' => $request->ItemCode,
        'Quantity' => $qty,
        'TaxCode' => "PPNO11",
        'UnitPrice' => $UnitPrice,
        'UnitMsr' => $UnitMsr,
        'UomCode' => $UnitMsr,
        'UomEntry' => $UomEntry,
        'NumPerMsr' => $NumPerMsr,
        'UnitPrice2' => $UnitPrice2,
        'UnitMsr2' => $UnitMsr2,
        'UomCode2' => $UnitMsr2,
        'UomEntry2' => $UomEntry2,
        'NumPerMsr2' => $NumPerMsr2,
        'CostingCode' => $cust['U_CLASS'],
        'CostingCode2' => $CostingCode2x,
        'CostingCode3' => 'SAL',
        'WarehouseCode' => $request->Warehouse,
        'users_id' => $users_id
      ]; 

      $cek = OrderTemp::where('ItemCode',$request->ItemCode)->where('users_id',$users_id)->get();
      if(count($cek) == 0)
      {
        $post = OrderTemp::create($data2);

        if ($post) 
        {
          $temp = $this->service->getTempLines($users_id);
          $totalBefore = array_sum(array_column($temp,'docTotal'));
          $vatSum = $totalBefore * 0.11;
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
    else
    {
      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
  }

  public function create_temp_load()
  {
    $users_id = auth()->user()->id;

    $row = $this->service->getTempLines($users_id);
    
    $data = [
      'row' => $row,
    ];

    return view('sap.sales.create.tabs')->with($data);
  }

  public function create_temp_delete(Request $request)
  {
    $id = $request->id;
    $users_id = auth()->user()->id;

    $post = OrderTemp::find($id)->delete();
    if ($post) 
    {
      $temp = $this->service->getTempLines($users_id);
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

  public function detail($docnum)
  {
    $branch_sap = auth()->user()->branch_sap;

    $date_closing = ClosingDate::where('status',1)
                               ->orderBy('id','DESC')
                               ->limit(1)
                               ->first();

    $assets = [
      'style' => array(
        'assets/plugins/datatables/custom.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css',
        'assets/plugins/air-datepicker/css/datepicker.min.css'
      ),
      'script' => array(
        'assets/plugins/datatables/datatables.min.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js',
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js'
      )
    ];
    // dd($docnum);
    $get = $this->service->getDataDetail($docnum);

    // dd($get);

    if(empty($get))
    {
      $body_header = [
        'DocNum' => $docnum
      ];

      $get_header = $this->service->getSalesOrderDetail(json_encode($body_header));
      $branchxx = getBranchDetail2($get_header['BPLId'])->id;

      // dd($get_header);

      $header = [
        'DocNum' => $get_header['DocNum'],
        'DocEntry' => $get_header['DocEntry'],
        'CardCode' => $get_header['CardCode'],
        'Branch' => $branchxx,
        'DocDate' => dateExp2($get_header['DocDate']),
        'DocDueDate' => dateExp2($get_header['DocDueDate']),
        'NumAtCard' => $get_header['NumAtCard'],
        'SalesPersonCode' => $get_header['SlpCode'],
        'U_NOPOLISI' => $get_header['U_NOPOLISI'],
        'U_NOPOLISI2' => $get_header['U_NOPOLISI2'],
        'Comments' => $get_header['Comments'],
        'BPLId' => $get_header['BPLId'],
        'VatSum' => $get_header['VatSum'],
        'DocTotal' => $get_header['Bruto'],
        'DocStatus' => $get_header['DocStatus'],
        'OcrCode' => $get_header['OcrCode'],
        'OcrCode2' => $get_header['OcrCode2']
      ];

      $post_header = OrderHeader::create($header);
      if($post_header)
      {
        foreach ($get_header['Lines'] as $linesx) 
        {
          $lines[] = [
            'DocEntry' => $linesx['DocEntry'],
            'LineNum' => $linesx['LineNum'],
            'NumAtCard' => $get_header['NumAtCard'],
            'ItemCode' => $linesx['ItemCode'],
            'ItemDescription' => $linesx['Dscription'],
            'Quantity' => $linesx['Quantity'],
            'TaxCode' => $linesx['TaxCode'],
            'UnitPrice' => $linesx['UnitPrice'],
            'UnitMsr' => $linesx['UnitMsr'],
            'UomCode' => $linesx['UomCode'],
            'UomEntry' => $linesx['UomEntry'],
            'NumPerMsr' => $linesx['NumPerMsr'],
            'CostingCode' => $linesx['CostingCode'],
            'CostingCode2' => $linesx['CostingCode2'],
            'CostingCode3' => $linesx['CostingCode3'],
            'WarehouseCode' => $linesx['WarehouseCode'],
            'DocStatus' => $linesx['DocStatus']
          ];
        }

        OrderLines::insert($lines);
      }

      $series = Branch::where('BPLid',$get_header['BPLId'])->first();
      $branchx = Branch::find($branchxx);

      $post_customer = [
        'CardCode' => $get_header['CardCode']
      ];
      $customerx = getCustomerId2(json_encode($post_customer));

      $lines = $this->service->getDataLines($get_header['DocEntry']);

      $sales = $get_header['SlpCode'];
      $getx = $this->service->getDataDetail($docnum);
    }
    else
    {
      $getx = $get;
      $series = Branch::where('BPLid',$get['BPLId'])->first();
      $branchx = Branch::find($get['Branch']);

      $post_customer = [
        'CardCode' => $get['CardCode']
      ];
      $customerx = getCustomerId2(json_encode($post_customer));

      $lines = $this->service->getDataLines($get['DocEntry']);
      $sales = $get['SalesPersonCode'];
    }

    $salesx = getSalesEmployee();

    // dd($customerx);

    $data = [
      'title' => "Detail - Sales Order",
      'assets' => $assets,  
      'local_currency' => 'Local Currency',
      'series' => $series->snso,
      'branch_title' => $branchx->title,
      'branch_reg' => $branchx->VatRegNum,
      'docnum' => $getx['DocNum'],
      'lines' => $lines,
      'sales' => $sales,
      'salesx' => $salesx,
      'numAtCard' => $getx['NumAtCard'],
      'remarks' => $getx['Comments'],
      'DocTotal' => rupiah($getx['DocTotal']),
      'VatSum' => rupiah($getx['VatSum']),
      'TotalSum' => rupiah($getx['TotalSum']),
      'DocStatus' => $getx['DocStatus'],
      'DocEntry' => $getx['DocEntry'],
      'CardName' => $customerx['CardName'],
      'CardCode' => $getx['CardCode'],
      'cseg4' => $customerx['cseg4'],
			'PriceList' => $customerx['PriceList'],
      'DocDate' => $getx['DocDate'],
      'DocDueDate' => $getx['DocDueDate'],
      'closing' => isset($date_closing->date) ? $date_closing->date : '',
      'date' => $getx['DocDate'],
      'branch_sap' => $branch_sap,
      // 'DocStatus' => isset($getx['DocStatus']) ? $getx['DocStatus']=='O' ? 'Open' : 'Close' : '-'
    ];

    // dd($data);

    return view('sap.sales.detail')->with($data);
  }

  // public function detail2($docnum)
  // {
  //   $branch_sap = auth()->user()->branch_sap;

  //   $date_closing = ClosingDate::where('status',1)
  //                              ->orderBy('id','DESC')
  //                              ->limit(1)
  //                              ->first();

  //   $assets = [
  //     'style' => array(
  //       'assets/plugins/datatables/custom.css',
  //       'assets/plugins/sweetalert2/sweetalert2.min.css',
  //       'assets/css/loading.css',
  //       'assets/plugins/air-datepicker/css/datepicker.min.css'
  //     ),
  //     'script' => array(
  //       'assets/plugins/datatables/datatables.min.js',
  //       'assets/plugins/sweetalert2/sweetalert2.min.js',
  //       'assets/plugins/air-datepicker/js/datepicker.min.js',
	// 			'assets/plugins/air-datepicker/js/i18n/datepicker.en.js'
  //     )
  //   ];
  //   // dd($docnum);
  //   $get = $this->service->getDataDetail($docnum);

  //   // dd($get);

  //   if(empty($get))
  //   {
  //     $body_header = [
  //       'DocNum' => $docnum
  //     ];

  //     $get_header = $this->service->getSalesOrderDetail(json_encode($body_header));
  //     $branchxx = getBranchDetail2($get_header['BPLId'])->id;

  //     // dd($get_header);

  //     $header = [
  //       'DocNum' => $get_header['DocNum'],
  //       'DocEntry' => $get_header['DocEntry'],
  //       'CardCode' => $get_header['CardCode'],
  //       'Branch' => $branchxx,
  //       'DocDate' => dateExp2($get_header['DocDate']),
  //       'DocDueDate' => dateExp2($get_header['DocDueDate']),
  //       'NumAtCard' => $get_header['NumAtCard'],
  //       'SalesPersonCode' => $get_header['SalesPersonCode'],
  //       'U_NOPOLISI' => $get_header['U_NOPOLISI'],
  //       'U_NOPOLISI2' => $get_header['U_NOPOLISI2'],
  //       'Comments' => $get_header['Comments'],
  //       'BPLId' => $get_header['BPLId'],
  //       'VatSum' => $get_header['VatSum'],
  //       'DocTotal' => $get_header['DocTotal']-$get_header['VatSum'],
  //       'DocStatus' => $get_header['DocStatus']
  //     ];

  //     $post_header = OrderHeader::create($header);
  //     if($post_header)
  //     {
  //       foreach ($get_header['SalesLines'] as $linesx) 
  //       {
  //         $lines[] = [
  //           'DocEntry' => $linesx['DocEntry'],
  //           'LineNum' => $linesx['LineNum'],
  //           'NumAtCard' => $get_header['NumAtCard'],
  //           'ItemCode' => $linesx['ItemCode'],
  //           'ItemDescription' => $linesx['Dscription'],
  //           'Quantity' => $linesx['Quantity'],
  //           'TaxCode' => $linesx['VatGroup'],
  //           'UnitPrice' => $linesx['Price'],
  //           'UnitMsr' => $linesx['unitMsr'],
  //           'UomCode' => $linesx['UomCode'],
  //           'UomEntry' => $linesx['UomEntry'],
  //           'NumPerMsr' => $linesx['NumPerMsr'],
  //           'CostingCode' => $linesx['OcrCode'],
  //           'CostingCode2' => $linesx['OcrCode2'],
  //           'CostingCode3' => $linesx['OcrCode3'],
  //           'WarehouseCode' => $linesx['WhsCode'],
  //           'DocStatus' => $linesx['LineStatus']
  //         ];
  //       }

  //       OrderLines::insert($lines);
  //     }

  //     $series = Branch::where('BPLid',$get_header['BPLId'])->first();
  //     $branchx = Branch::find($branchxx);

  //     $post_customer = [
  //       'CardCode' => $get_header['CardCode']
  //     ];
  //     $customerx = getCustomerId(json_encode($post_customer));

  //     $lines = $this->service->getDataLines($get_header['DocEntry']);

  //     $sales = $get_header['SalesPersonCode'];
  //     $getx = $this->service->getDataDetail($docnum);
  //   }
  //   else
  //   {
  //     $getx = $get;
  //     $series = Branch::where('BPLid',$get['BPLId'])->first();
  //     $branchx = Branch::find($get['Branch']);

  //     $post_customer = [
  //       'CardCode' => $get['CardCode']
  //     ];
  //     $customerx = getCustomerId(json_encode($post_customer));

  //     dd($get['CardCode']);

  //     $lines = $this->service->getDataLines($get['DocEntry']);
  //     $sales = $get['SalesPersonCode'];
  //   }

  //   $salesx = getSalesEmployee();

  //   // dd($customerx);

  //   $data = [
  //     'title' => "Detail - Sales Order",
  //     'assets' => $assets,  
  //     'local_currency' => 'Local Currency',
  //     'series' => $series->snso,
  //     'branch_title' => $branchx->title,
  //     'branch_reg' => $branchx->VatRegNum,
  //     'docnum' => $getx['DocNum'],
  //     'lines' => $lines,
  //     'sales' => $sales,
  //     'salesx' => $salesx,
  //     'numAtCard' => $getx['NumAtCard'],
  //     'remarks' => $getx['Comments'],
  //     'DocTotal' => rupiah($getx['DocTotal']),
  //     'VatSum' => rupiah($getx['VatSum']),
  //     'TotalSum' => rupiah($getx['TotalSum']),
  //     'DocStatus' => $getx['DocStatus'],
  //     'DocEntry' => $getx['DocEntry'],
  //     'CardName' => $customerx['CardName'],
  //     'CardCode' => $getx['CardCode'],
  //     'cseg4' => $customerx['cseg4'],
  //     'DocDate' => $getx['DocDate'],
  //     'DocDueDate' => $getx['DocDueDate'],
  //     'closing' => isset($date_closing->date) ? $date_closing->date : '',
  //     'date' => date('Y-m-d'),
  //     'branch_sap' => $branch_sap
  //   ];

  //   // dd($data);

  //   return view('sap.sales.detail')->with($data);
  // }

  public function search_docnum(Request $request)
  {
    // dd($request->all());
    $post = [
      'SlpCode' => $request->sales,
      'DocStatus' => $request->status,
      'DocNum' => isset($request->docnum) ? $request->docnum : 'empty'
    ];

    $get = $this->service->getSalesOrder(json_encode($post));

    $data = [
      'title' => 'Sales Order',
      'row' => isset($get) ? $get : []
    ];

    return view('sap.sales.view')->with($data);
  }

  public function discount(Request $request)
  {
    $id = $request->id;

    $lines = $this->service->getDataLinesDisc($id);

    $data = [
      'title' => 'Discount Calculation',
      'lines' => $lines,
      'id' => $id
    ];

    return view('sap.sales.detail.disc')->with($data);
  }

  public function discount_update(Request $request)
  {
		$users_id = auth()->user()->id;

    $numAtCard = $request->numAtCard;
    $header = OrderHeader::where('DocEntry',$numAtCard)->first();
    
    $docnum = $header->DocNum;

    $ocrCode2 = $header->OcrCode2;

    $post_customer = [
      'CardCode' => $header->CardCode
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

		$disc1_rp = $request->disc1_rp;
    $disc2_rp = $request->disc2_rp;
    $disc3_rp = $request->disc3_rp;
    $disc4_rp = $request->disc4_rp;
    $disc5_rp = $request->disc5_rp;
    $disc6_rp = $request->disc6_rp;
    $disc7_rp = $request->disc7_rp;
    $disc8_rp = $request->disc8_rp;

		$qty_eko = $request->qty_eko;

    $total = $request->total;

    $totalx2 = 0;
    $totalx22 = 0;

    if($ocrCode2=='P&G')
    {
      if (in_array($subsegment, $platinum)) 
      {
        foreach ($idx as $key) 
        {
          $totalx = $total[$key];
          $discx1 = ($disc1[$key] / 100) * $totalx;
          // $discx2 = ($disc2[$key] / 100) * ($totalx - $discx1);
          $discx3 = ($disc3[$key] / 100) * ($totalx - $discx1);
          $discx4 = ($disc4[$key] / 100) * ($totalx - $discx1 - $discx3);
          $discx5 = ($disc5[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4);
          $discx6 = ($disc6[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5);
          $discx7 = ($disc7[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6);
          $discx8 = ($disc8[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

          $disc_calx = $discx1+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

          $totalxx = $totalx - $disc_calx;

          // $data[] = [
          //   'totalBefore' => $totalx,
          //   'discvalue_calc' => $disc_calx,
          //   'total' => round($totalxx,2)
          // ];

          $data = [
            'U_DISC1' => $disc1[$key],
            'U_DISCVALUE1' => $discx1,
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
            'U_DISCVALUE8' => $discx8
          ];

          OrderLines::find($id[$key])->update($data);

          $totalx2 += round($totalxx,2);
        }

        $moq = 2;

        $lines = OrderLines::where('DocEntry',$numAtCard)->get();

        foreach ($lines as $value) 
        {
          $itemCode = $value->ItemCode;
          $itemName = $value->ItemDescription;

          if (isset($itemName)) 
          {
            $itemName2 = $itemName;
          }
          else
          {
            $post_item = [
              'ItemCode' => $itemCode
            ];

            $item = getItemId(json_encode($post_item));

            $itemName2 = $item['ItemName'];
          }

          $unitPrice = $value->UnitPrice;

          $exp = explode(' ',$itemName2);

          $str_first = $exp[0];

          if ($str_first!="BONUS" || $unitPrice!='0.00' || $unitPrice!='0')
          {
            $total = $value->Quantity * $value->UnitPrice;

            $discx1 = $value->U_DISCVALUE1;
            $discx3 = $value->U_DISCVALUE3;
            $discx4 = $value->U_DISCVALUE4;
            $discx5 = $value->U_DISCVALUE5;
            $discx6 = $value->U_DISCVALUE6;
            $discx7 = $value->U_DISCVALUE7;
            $discx8 = $value->U_DISCVALUE8;

            $disc_reguler = $discx1;
            $disc_lotsell = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

            $total_reguler = $total - $disc_reguler;
            $total_lotsell = $total_reguler - $disc_lotsell;
            
            $disc_moq = $total_lotsell * ($moq / 100);
            $total_volume = $total_lotsell - $disc_moq;

            $totalxx2 = $total_volume;

            $data2 = [
              'U_DISC2' => $moq,
              'U_DISCVALUE2' => $disc_moq,
            ];
          }
          else
          {
            $totalxx2 = 0;

            $data2 = [
              'U_DISC2' => 0,
              'U_DISCVALUE2' => 0,
            ];
          }

          OrderLines::find($value->id)->update($data2);

          $totalx22 += round($totalxx2,2);
        }

        $totalx2 = $totalx22;
      }
      elseif (in_array($subsegment, $diamond)) 
      {
        foreach ($idx as $key) 
        {
          $totalx = $total[$key];
          $discx1 = ($disc1[$key] / 100) * $totalx;
          // $discx2 = ($disc2[$key] / 100) * ($totalx - $discx1);
          $discx3 = ($disc3[$key] / 100) * ($totalx - $discx1);
          $discx4 = ($disc4[$key] / 100) * ($totalx - $discx1 - $discx3);
          $discx5 = ($disc5[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4);
          $discx6 = ($disc6[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5);
          $discx7 = ($disc7[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6);
          $discx8 = ($disc8[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

          $disc_calx = $discx1+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

          $totalxx = $totalx - $disc_calx;

          // $data[] = [
          //   'totalBefore' => $totalx,
          //   'discvalue_calc' => $disc_calx,
          //   'total' => round($totalxx,2)
          // ];

          $data = [
            'U_DISC1' => $disc1[$key],
            'U_DISCVALUE1' => $discx1,
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
            'U_DISCVALUE8' => $discx8
          ];

          OrderLines::find($id[$key])->update($data);

          $totalx2 += round($totalxx,2);
        }

        $moq = $totalx2 >= 2500000 ? 1.5 : 0;

        $lines = OrderLines::where('DocEntry',$numAtCard)->get();

        foreach ($lines as $value) 
        {
          $itemCode = $value->ItemCode;
          $itemName = $value->ItemDescription;

          if (isset($itemName)) 
          {
            $itemName2 = $itemName;
          }
          else
          {
            $post_item = [
              'ItemCode' => $itemCode
            ];

            $item = getItemId(json_encode($post_item));

            $itemName2 = $item['ItemName'];
          }

          $unitPrice = $value->UnitPrice;

          $exp = explode(' ',$itemName2);

          $str_first = $exp[0];

          if ($str_first!="BONUS" || $unitPrice!='0.00' || $unitPrice!='0')
          {
            $total = $value->Quantity * $value->UnitPrice;

            $discx1 = $value->U_DISCVALUE1;
            $discx3 = $value->U_DISCVALUE3;
            $discx4 = $value->U_DISCVALUE4;
            $discx5 = $value->U_DISCVALUE5;
            $discx6 = $value->U_DISCVALUE6;
            $discx7 = $value->U_DISCVALUE7;
            $discx8 = $value->U_DISCVALUE8;

            $disc_reguler = $discx1;
            $disc_lotsell = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

            $total_reguler = $total - $disc_reguler;
            $total_lotsell = $total_reguler - $disc_lotsell;
            
            $disc_moq = $total_lotsell * ($moq / 100);
            $total_volume = $total_lotsell - $disc_moq;

            $totalxx2 = $total_volume;

            $data2 = [
              'U_DISC2' => $moq,
              'U_DISCVALUE2' => $disc_moq,
            ];
          }
          else
          {
            $totalxx2 = 0;

            $data2 = [
              'U_DISC2' => 0,
              'U_DISCVALUE2' => 0,
            ];
          }

          OrderLines::find($value->id)->update($data2);

          $totalx22 += round($totalxx2,2);
        }

        $totalx2 = $totalx22;
      }
      elseif (in_array($subsegment, $gold)) 
      {
        foreach ($idx as $key) 
        {
          $totalx = $total[$key];
          $discx1 = ($disc1[$key] / 100) * $totalx;
          // $discx2 = ($disc2[$key] / 100) * ($totalx - $discx1);
          $discx3 = ($disc3[$key] / 100) * ($totalx - $discx1);
          $discx4 = ($disc4[$key] / 100) * ($totalx - $discx1 - $discx3);
          $discx5 = ($disc5[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4);
          $discx6 = ($disc6[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5);
          $discx7 = ($disc7[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6);
          $discx8 = ($disc8[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

          $disc_calx = $discx1+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

          $totalxx = $totalx - $disc_calx;

          // $data[] = [
          //   'totalBefore' => $totalx,
          //   'discvalue_calc' => $disc_calx,
          //   'total' => round($totalxx,2)
          // ];

          $data = [
            'U_DISC1' => $disc1[$key],
            'U_DISCVALUE1' => $discx1,
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
            'U_DISCVALUE8' => $discx8
          ];

          OrderLines::find($id[$key])->update($data);

          $totalx2 += round($totalxx,2);
        }

        $moq = $totalx2 >= 1000000 ? 1.5 : 0;

        $lines = OrderLines::where('DocEntry',$numAtCard)->get();

        foreach ($lines as $value) 
        {
          $itemCode = $value->ItemCode;
          $itemName = $value->ItemDescription;

          if (isset($itemName)) 
          {
            $itemName2 = $itemName;
          }
          else
          {
            $post_item = [
              'ItemCode' => $itemCode
            ];

            $item = getItemId(json_encode($post_item));

            $itemName2 = $item['ItemName'];
          }

          $unitPrice = $value->UnitPrice;

          $exp = explode(' ',$itemName2);

          $str_first = $exp[0];

          if ($str_first!="BONUS" || $unitPrice!='0.00' || $unitPrice!='0')
          {
            $total = $value->Quantity * $value->UnitPrice;

            $discx1 = $value->U_DISCVALUE1;
            $discx3 = $value->U_DISCVALUE3;
            $discx4 = $value->U_DISCVALUE4;
            $discx5 = $value->U_DISCVALUE5;
            $discx6 = $value->U_DISCVALUE6;
            $discx7 = $value->U_DISCVALUE7;
            $discx8 = $value->U_DISCVALUE8;

            $disc_reguler = $discx1;
            $disc_lotsell = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

            $total_reguler = $total - $disc_reguler;
            $total_lotsell = $total_reguler - $disc_lotsell;
            
            $disc_moq = $total_lotsell * ($moq / 100);
            $total_volume = $total_lotsell - $disc_moq;

            $totalxx2 = $total_volume;

            $data2 = [
              'U_DISC2' => $moq,
              'U_DISCVALUE2' => $disc_moq,
            ];
          }
          else
          {
            $totalxx2 = 0;

            $data2 = [
              'U_DISC2' => 0,
              'U_DISCVALUE2' => 0,
            ];
          }

          OrderLines::find($value->id)->update($data2);

          $totalx22 += round($totalxx2,2);
        }

        $totalx2 = $totalx22;
      }
      elseif (in_array($subsegment, $silver)) 
      {
        foreach ($idx as $key) 
        {
          $totalx = $total[$key];
          $discx1 = ($disc1[$key] / 100) * $totalx;
          // $discx2 = ($disc2[$key] / 100) * ($totalx - $discx1);
          $discx3 = ($disc3[$key] / 100) * ($totalx - $discx1);
          $discx4 = ($disc4[$key] / 100) * ($totalx - $discx1 - $discx3);
          $discx5 = ($disc5[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4);
          $discx6 = ($disc6[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5);
          $discx7 = ($disc7[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6);
          $discx8 = ($disc8[$key] / 100) * ($totalx - $discx1 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

          $disc_calx = $discx1+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

          $totalxx = $totalx - $disc_calx;

          // $data[] = [
          //   'totalBefore' => $totalx,
          //   'discvalue_calc' => $disc_calx,
          //   'total' => round($totalxx,2)
          // ];

          $data = [
            'U_DISC1' => $disc1[$key],
            'U_DISCVALUE1' => $discx1,
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
						'U_EKO' => $qty_eko[$key]
          ];

          OrderLines::find($id[$key])->update($data);

          $totalx2 += round($totalxx,2);
        }

        $moq = $totalx2 >= 400000 ? 1.5 : 0;

        $lines = OrderLines::where('DocEntry',$numAtCard)->get();

        foreach ($lines as $value) 
        {
          $itemCode = $value->ItemCode;
          $itemName = $value->ItemDescription;

          if (isset($itemName)) 
          {
            $itemName2 = $itemName;
          }
          else
          {
            $post_item = [
              'ItemCode' => $itemCode
            ];

            $item = getItemId(json_encode($post_item));

            $itemName2 = $item['ItemName'];
          }

          $unitPrice = $value->UnitPrice;

          $exp = explode(' ',$itemName2);

          $str_first = $exp[0];

          if ($str_first!="BONUS" || $unitPrice!='0.00' || $unitPrice!='0')
          {
            $total = $value->Quantity * $value->UnitPrice;

            $discx1 = $value->U_DISCVALUE1;
            $discx3 = $value->U_DISCVALUE3;
            $discx4 = $value->U_DISCVALUE4;
            $discx5 = $value->U_DISCVALUE5;
            $discx6 = $value->U_DISCVALUE6;
            $discx7 = $value->U_DISCVALUE7;
            $discx8 = $value->U_DISCVALUE8;

            $disc_reguler = $discx1;
            $disc_lotsell = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

            $total_reguler = $total - $disc_reguler;
            $total_lotsell = $total_reguler - $disc_lotsell;
            
            $disc_moq = $total_lotsell * ($moq / 100);
            $total_volume = $total_lotsell - $disc_moq;

            $totalxx2 = $total_volume;

            $data2 = [
              'U_DISC2' => $moq,
              'U_DISCVALUE2' => $disc_moq,
            ];
          }
          else
          {
            $totalxx2 = 0;

            $data2 = [
              'U_DISC2' => 0,
              'U_DISCVALUE2' => 0,
            ];
          }

          OrderLines::find($value->id)->update($data2);

          $totalx22 += round($totalxx2,2);
        }

        $totalx2 = $totalx22;
      }
			elseif($subsegment=='HFS LARGE')
			{
				foreach ($idx as $key) 
        {
					$totalx = $total[$key];
					$discx1 = $disc1_rp[$key];
					$discx2 = $disc2_rp[$key];
					$discx3 = $disc3_rp[$key];
					$discx4 = $disc4_rp[$key];
					$discx5 = $disc5_rp[$key];
					$discx6 = $disc6_rp[$key];
					$discx7 = $disc7_rp[$key];
					// $discx8 = $disc8_rp[$key];
					$discx8 = ($disc8[$key] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

					$disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

					$totalxx = $totalx - $disc_calx;

					// $data[] = [
					// 	'totalBefore' => $totalx,
					// 	'discvalue_calc' => $disc_calx,
					// 	'total' => round($totalxx,2)
					// ];

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
						'U_EKO' => $qty_eko[$key]
          ];

          OrderLines::find($id[$key])->update($data);

          $totalx2 += round($totalxx,2);
				}
			}
      else
      {
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

          $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

          $totalxx = $totalx - $disc_calx;

          // $data[] = [
          //   'totalBefore' => $totalx,
          //   'discvalue_calc' => $disc_calx,
          //   'total' => round($totalxx,2)
          // ];

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
						'U_EKO' => $qty_eko[$key]
          ];

					// dd($data);

          OrderLines::find($id[$key])->update($data);

          $totalx2 += round($totalxx,2);
        }
      }
    }
    else
    {
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

        $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

        $totalxx = $totalx - $disc_calx;

        // $data[] = [
        //   'totalBefore' => $totalx,
        //   'discvalue_calc' => $disc_calx,
        //   'total' => round($totalxx,2)
        // ];

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
					'U_EKO' => $qty_eko[$key]
        ];

        OrderLines::find($id[$key])->update($data);

        $totalx2 += round($totalxx,2);
      }
    }

		// if($users_id==1)
		// {
		// 	dd($data);
		// }

		

    // dd($data2);

    // dd($totalx2);

    $data2 = [
      'DocTotal' => $totalx2,
      'VatSum' => ($totalx2 * 0.11)
    ];
    
    OrderHeader::where('DocNum',$docnum)->update($data2);

    $alert = array(
      'type' => 'success',
      'message' => 'Discount calculation berhasil di update !'
    );

    return redirect()->back()->with($alert);
  }

  public function discount_update_old(Request $request)
  {
    $numAtCard = $request->numAtCard;
    $header = OrderHeader::where('DocEntry',$numAtCard)->first();
    $docnum = $header->DocNum;

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

    $total = $request->total;

    $totalx2 = 0;

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

      $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $totalxx = $totalx - $disc_calx;

      // $data[] = [
      //   'totalBefore' => $totalx,
      //   'discvalue_calc' => $disc_calx,
      //   'total' => round($totalxx,2)
      // ];

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
        'U_DISCVALUE8' => $discx8
      ];

      OrderLines::find($id[$key])->update($data);

      $totalx2 += round($totalxx,2);
    }

    // dd($totalx2);

    $data2 = [
      'DocTotal' => $totalx2,
      'VatSum' => ($totalx2 * 0.11)
    ];
    
    OrderHeader::where('DocNum',$docnum)->update($data2);

    $alert = array(
      'type' => 'success',
      'message' => 'Discount calculation berhasil di update !'
    );

    return redirect()->back()->with($alert);
  }

  public function update(Request $request)
  {
    $apiService = new ApiServices;

    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

    $id = $request->id;
    $sales = $request->SalesPersonCode;
    $date = $request->DocDate;
    $get = OrderHeader::where('DocEntry',$id)->first();
    $docentry = $get['DocEntry'];
    $docnum = $get['DocNum'];

    $json = $this->service->jsonUpdate($id,$sales,$date);

    // dd($json);
    // dd($docentry);

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

    $header = [
      "B1S-ReplaceCollectionsOnPatch: True",
      "Cookie: ".$headers,
      "accept: */*",
      "accept-language: en-US,en;q=0.8",
      "content-type: application/json"
    ];

    $url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders('.$docentry.')';
    $api_sales = $this->service->updateSales($header,$url_sales,json_encode($json));

    // dd($api_sales);

    if(isset($api_sales['error']))
    {
      $error = $api_sales['error']['message']['value'];

      $history = [
        'title' => $username,
        'history_category_id' => 1,
        'desc' => 'Error update data Sales Order dengan Document Number <strong>'.$docnum.'</strong> ke SAP dengan pesan <strong>'.$error.'</strong>'
      ];

      History::create($history);

      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
    else
    {
      $lines = OrderLines::where('DocEntry',$docentry)->get();
      $no = 0;
      foreach ($lines as $value) 
      {
        $data_lines = [
          'LineNum' => $no
        ];
        OrderLines::find($value->id)->update($data_lines);

        $no++;
      }

      $history = [
        'title' => $username,
        'history_category_id' => 1,
        'desc' => 'Berhasil update data Sales Order dengan Document Number <strong>'.$docnum.'</strong> ke SAP'
      ];

      History::create($history);

      $callback = array(
        'message' => 'sukses',
        'docnum' => $docnum
      );

      echo json_encode($callback);
    }
  }

  public function close(Request $request)
  {
    $apiService = new ApiServices;

    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

    $id = $request->id;
    $get = OrderHeader::where('DocEntry',$id)->first();
    $docentry = $get['DocEntry'];
    $docnum = $get['DocNum'];

    $json = $this->service->jsonClose($docentry);

    // dd(json_encode($json));

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

    $header = [
      "Cookie: ".$headers,
      "accept: */*",
      "accept-language: en-US,en;q=0.8",
      "content-type: application/json"
    ];

    $url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders('.$docentry.')/Close';
    $api_sales = $this->service->closeSales($header,$url_sales,json_encode($json));

    if(isset($api_sales['error']))
    {
      $error = $api_sales['error']['message']['value'];

      $history = [
        'title' => $username,
        'history_category_id' => 1,
        'desc' => 'Error close data Sales Order dengan Document Number <strong>'.$docnum.'</strong> ke SAP dengan pesan <strong>'.$error.'</strong>'
      ];

      History::create($history);

      $callback = array(
        'message' => 'error',
      );

      echo json_encode($callback);
    }
    else
    {
      $history = [
        'title' => $username,
        'history_category_id' => 1,
        'desc' => 'Berhasil close data Sales Order dengan Document Number <strong>'.$docnum.'</strong> ke SAP'
      ];

      History::create($history);

      $data2 = [
        'DocStatus' => 'C'
      ];

      OrderHeader::where('DocNum',$docnum)->update($data2);

      $callback = array(
        'message' => 'sukses',
        'docnum' => $docnum
      );

      echo json_encode($callback);
    }
  }

  public function delivery(Request $request)
  {
    $apiService = new ApiServices;

    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;
    $user_id = auth()->user()->id;

    $id = $request->id;
    $date = $request->docDate;

    // dd($date);
    $get = OrderHeader::where('DocEntry',$id)->first();
    $docentry = $get['DocEntry'];
    $docnum = $get['DocNum'];

    $json = $this->service->jsonDelivery($id,$date);

    // if ($user_id==1) 
    // {
    //   dd($json);
    // }
    
    // dd($json);

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

      echo json_encode($callback);
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
        $api_sales = $this->service->pushDelivery($header,$url_sales,json_encode($json));
    
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
            $header2 = [
              'DocStatus' => 'C'
            ];
    
            OrderHeader::where('DocNum',$docnum)->update($header2);
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
    
          echo json_encode($callback);
        }
        else
        {
          $error = $api_sales['error']['message']['value'];

          if ($error=="(1) 2. Silahkan Periksa A/R Jatuh Tempo!" || $error=="(2) 2. Silahkan Periksa A/R Jatuh Tempo!") 
          {
            $this->service->order_failed($id, $error);
          }
    
          $history = [
            'title' => $username,
            'history_category_id' => 2,
            'card_code' => $id,
            'desc' => 'Error push data Delivery Order ke SAP dengan pesan <strong>'.$error.'</strong>'
          ];
    
          History::create($history);
    
          $callback = array(
            'message' => 'error'
          );
    
          echo json_encode($callback);
        }
      }
    }
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

      $post_available = [
        'ItemCode' => $value['ItemCode'],
        'WhsCode' => $value['WarehouseCode']
      ];
      
      $available = callSapApiLtaWithPost('getInStock',json_encode($post_available));
      $stock = $available['stok'];

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
        'DocEntry' => $docentry,
        'inStock' => $stock
      ];
    }

    return $data;
  }
  

  public function manual(Request $request)
  {
    $service = new SfaMixServices;
    $apiService = new ApiServices;

    $users_id = auth()->user()->id;
    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

    $cardcode = $request->cardCode;

    $date = $request->docDate;
    $date_closing = ClosingDate::where('status',1)->get();

    if(isset($date))
    {
      if (count($date_closing)==0) 
      {
        $docDate = date('Y-m-d');
      }
      else
      {
        $docDate = $date;
      }
    }
    else
    {
      $docDate = date('Y-m-d');
    }

    $top = "+1 days";

    // if(isset($date))
    // {
    //   $docDate = $date;
    // }
    // else
    // {
    //   $docDate = date('Y-m-d');
    // } 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $push = [
      'CardCode' => $request->cardCode,
      'DocDueDate' => $docDueDate,
      'DocDate' => $docDate,
      'BPL_IDAssignedToInvoice' => $request->BplId,
      'SalesPersonCode'=> $request->SalesPersonCode,
      'NumAtCard' => $request->numAtCard,
      'Comments' => $request->Comments,
      'U_NOPOLISI' => $request->Nopol1,
      'U_NOPOLISI2' => $request->Nopol2,
      'DocumentLines' => $this->getTempLines($users_id)
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

    $header = [
      "Cookie: ".$headers,
      "accept: */*",
      "accept-language: en-US,en;q=0.8",
      "content-type: application/json",
    ];

    $url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders';
    $api_sales = $service->postSales($header,$url_sales,json_encode($push));

    // dd($api_sales);

    if(isset($api_sales['DocNum']))
    {
      $lines = $this->decodeJsonTempLines($api_sales['DocumentLines'],$api_sales['DocEntry'],$api_sales['NumAtCard']);

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
        'OcrCode' => $lines[0]['CostingCode'],
        'OcrCode2' => $lines[0]['CostingCode2'],
        'Branch' => $branch->id
      ];
      
      $post = OrderHeader::create($header);
      if($post)
      {
        OrderLines::insert($lines);
        OrderTemp::where('users_id',$users_id)->delete();
      }

      $history = [
        'title' => $username,
        'history_category_id' => 1,
        'card_code' => $api_sales['CardCode'],
        'desc' => 'Sukses push data <strong>'.$api_sales['CardCode'].'</strong> Sales Order ke SAP dengan Document Number <strong>'.$api_sales['DocNum'].'</strong>'
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
        'history_category_id' => 1,
        'card_code' => $cardcode,
        'desc' => 'Error push data Sales Order ke SAP dengan pesan <strong>'.$error.'</strong>'
      ];

      History::create($history);

      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
  }

  public function getTempLines($user)
  {
    $data= [];
    $service = new ApiServices;

    $get = OrderTemp::where('users_id',$user)->get();

    // dd($get);

    foreach ($get as $value) 
    {
      $wareHouse = $value['WarehouseCode'];

      $post_available = [
        'ItemCode' => $value['ItemCode'],
        'WhsCode' => $wareHouse
      ];
      
      $available = $service->getAvailable(json_encode($post_available));
      $availablex = $available['available'];

      $Quantity = $value['Quantity'];
      $NumPerMsr = $value['NumPerMsr'];
      $UnitMsr = $value['UnitMsr'];

      if ($availablex >= 1) 
      {
        $qty_real = $Quantity * $NumPerMsr;

        if ($availablex >= $qty_real) 
        {
          $qty = $qty_real / $NumPerMsr;
          $UnitMsr = $value['UnitMsr'];
          $NumPerMsr = $value['NumPerMsr'];
          $UnitPrice = $value['UnitPrice'];
          $UomEntry = $value['UomEntry'];
        }
        else 
        {
          if ($UnitMsr=="KTN") 
          {
            $qty = $availablex;
            $UnitMsr = $value['UnitMsr2'];
            $NumPerMsr = 1;
            $UnitPrice = $value['UnitPrice2'];
            $UomEntry = $value['UomEntry2'];
          }
          else
          {
            $qty = $availablex / $NumPerMsr;
            $UnitMsr = $value['UnitMsr'];
            $NumPerMsr = $value['NumPerMsr'];
            $UnitPrice = $value['UnitPrice'];
            $UomEntry = $value['UomEntry'];
          }
        }
      }

      $data[] = [
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $qty,
        'TaxCode' => $value['TaxCode'],
        'UnitPrice' => $UnitPrice,
        'CostingCode' => $value['CostingCode'],
        'CostingCode2' => $value['CostingCode2'],
        'CostingCode3' => $value['CostingCode3'],
        'MeasureUnit' => $UnitMsr,
        'UoMCode' => $UnitMsr,
        'UoMEntry' => $UomEntry,
        'UnitsOfMeasurment' => $NumPerMsr,
        'WarehouseCode' => $wareHouse
      ];
    }
    return $data;
  }

  public function decodeJsonTempLines($row,$docentry)
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
        'DocEntry' => $docentry,
        'LineNum' => $no++
      ];
    }

    return $data;
  }

  public function lines_item(Request $request)
  {
    $cust = $request->itemName;
    $code = $request->cardCode;

    $service = new ApiServices;

    $post = [
      'a.ItemName' => "%".$cust."%"
    ];

    // dd($code);

    if(empty($code))
    {
      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
    else
    {
      $get = $service->getItem(json_encode($post));

      $data = [
        'row' => $get
      ];
      
      return view('sap.sales.detail.item')->with($data);
    }
  }

  public function lines_item_detail(Request $request)
  {
    $service = new ApiServices;

    $row = $request->all();
    $post = [
      'ItemCode' => $request->id
    ];

    $get = $service->getItemId(json_encode($post));
    // dd($get);

    $cek = Item::where('code',$request->id)->first();
    if (empty($cek)) 
    {
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
    }
    else
    {
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

      Item::where('code',$request->id)->update($item);
    }
    
    $post_cust = [
      'ItemCode' => $request->cardcode
    ];
    $cust = getCustomerId(json_encode($post_cust));

    // dd($cust);
    $class = $cust['U_CLASS'];

    $whs = Warehouse::where('code',$class)->first();
    $warehouse = $whs->title;

    $post2 = [
      'ItemNo' => $request->id,
      'CardCode' => $request->cardcode,
      'WhsCode' => $warehouse
    ];

    $UomData = $service->getUomDetail(json_encode($post2));

    $post_available = [
      'ItemCode' => $request->id,
      'WhsCode' => $warehouse
    ];
    
    $available = getAvailable(json_encode($post_available));
    $availablex = $available['available'];

    $satuan = [
      'nisik' => $UomData['satuan_kecil'],
      'nisib' => $UomData['satuan_besar']
    ];
    
    $data = [
      'ItemCode' => $get['ItemCode'],
      'ItemName' => $get['ItemName'],
      'satuan' => $satuan,
      'available' => number_format($availablex,0,'.',',').' '.$UomData['satuan_kecil'],
      'warehouse' => $warehouse,
      'CardCode' => $request->cardcode,
      'DocNum' => $request->docNum
    ];
    return view('sap.sales.detail.item_insert')->with($data);  
  }

  public function lines_item_store(Request $request)
  {
    $header = OrderHeader::where('DocNum',$request->DocNum)->first();
    $docEntry = $header->DocEntry;
    $numAtCard = $header->NumAtCard;

    $lines = OrderLines::where('DocEntry',$docEntry)->OrderBy('id','DESC')->limit(1)->first();
    $lineNum = $lines->LineNum + 1;

    $service = new ApiServices;

    $users_id = auth()->user()->id;

    $satuan = $request->Satuan;
    $jml_order = $request->Quantity;

    $post_cust = [
      'ItemCode' => $request->CardCode
    ];

    $cust = getCustomerId(json_encode($post_cust));

    // dd($cust);

    $post2 = [
      'ItemCode' => $request->ItemCode,
      'CardCode' => $request->CardCode,
      'WhsCode' => $request->Warehouse
    ];

    $UomData = $service->getUomDetail(json_encode($post2));

    if($satuan=="nisik")
    {
      $UnitMsr = $UomData['satuan_kecil'];
      $NumPerMsr = 1;
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_pcs'];

      $UnitMsr2 = $UomData['satuan_besar'];
      $NumPerMsr2 = $UomData['nisib'];
      $UnitPrice2 = $UomData['harga_jual_ktn'];
    }
    else
    {
      $UnitMsr = $UomData['satuan_besar'];
      $NumPerMsr = $UomData['nisib'];
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_ktn'];

      $UnitMsr2 = $UomData['satuan_kecil'];
      $NumPerMsr2 = 1;
      $UnitPrice2 = $UomData['harga_jual_pcs'];
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

    $post3 = ['UomEntry'=>$UnitMsr];
    $getUomEntry = $service->getUomEntry(json_encode($post3));
    $UomEntry = $getUomEntry['uom_entry'];

    $post4 = ['UomEntry'=>$UnitMsr2];
    $getUomEntry2 = $service->getUomEntry(json_encode($post4));
    $UomEntry2 = $getUomEntry2['uom_entry'];

    $post_available = [
      'ItemCode' => $request->ItemCode,
      'WhsCode' => $request->Warehouse,
    ];
    
    $available = getAvailable(json_encode($post_available));
    $availablex = $available['available'];

    if ($availablex >= 1) 
    {
      $qty_real = $Quantity * $NumPerMsr;

      if ($availablex > $qty_real) 
      {
        $qty = $qty_real / $NumPerMsr;
      }
      else 
      {
        if ($UnitMsr=="KTN") 
        {
          $qty = $availablex;
          $UnitMsr = $UomData['satuan_kecil'];
          $NumPerMsr = 1;
          $UnitPrice = $UomData['harga_jual_pcs'];

          $UnitMsr2 = $UomData['satuan_besar'];
          $NumPerMsr2 = $UomData['nisib'];
          $UnitPrice2 = $UomData['harga_jual_ktn'];
        }
        else
        {
          $qty = $availablex / $NumPerMsr;
        }
      }

      $data2 = [
        'DocEntry' => $docEntry,
        'LineNum' => $lineNum,
        'NumAtCard' => $numAtCard,
        'ItemCode' => $request->ItemCode,
        'Quantity' => $qty,
        'TaxCode' => "PPNO11",
        'UnitPrice' => $UnitPrice,
        'UnitMsr' => $UnitMsr,
        'UomCode' => $UnitMsr,
        'UomEntry' => $UomEntry,
        'NumPerMsr' => $NumPerMsr,
        'UnitPrice2' => $UnitPrice2,
        'UnitMsr2' => $UnitMsr2,
        'UomCode2' => $UnitMsr2,
        'UomEntry2' => $UomEntry2,
        'NumPerMsr2' => $NumPerMsr2,
        'CostingCode' => $cust['U_CLASS'],
        'CostingCode2' => $CostingCode2x,
        'CostingCode3' => 'SAL',
        'WarehouseCode' => $request->Warehouse
      ]; 

      // dd($data2);

      $cek = OrderLines::where('ItemCode',$request->ItemCode)->where('DocEntry',$docEntry)->get();
      // dd($cek);
      if(count($cek) == 0)
      {
        OrderLines::create($data2);

        $callback = array(
          'message' => 'sukses',
          'docnum' => $request->DocNum
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
    else
    {
      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
  }

  public function lines_item_edit(Request $request)
  {
    $service = new ApiServices;

    $id = $request->id;

    $lines = OrderLines::find($id);
    
    $post = [
      'ItemCode' => $lines->ItemCode
    ];

    $get = $service->getItemId(json_encode($post));

    $header = OrderHeader::where('DocEntry',$lines->DocEntry)->first();

    $post2 = [
      'ItemNo' => $lines->ItemCode,
      'CardCode' => $header->CardCode,
      'WhsCode' => $lines->WarehouseCode
    ];

    $UomData = $service->getUomDetail(json_encode($post2));

    // dd($UomData);

    $post_available = [
      'ItemCode' => $lines->ItemCode,
      'WhsCode' => $lines->WarehouseCode
    ];
    
    $available = getAvailable(json_encode($post_available));
    $availablex = $available['available'];

    $satuan = [
      'nisik' => $UomData['satuan_kecil'],
      'nisib' => $UomData['satuan_besar']
    ];
    
    $data = [
      'ItemCode' => $lines->ItemCode,
      'ItemName' => $UomData['desc'],
      'satuan' => $satuan,
      'available' => number_format($availablex,0,'.',',').' '.$UomData['satuan_kecil'],
      'warehouse' => $lines->WarehouseCode,
      'CardCode' => $header->CardCode,
      'Quantity' => $lines->Quantity,
      'DocNum' => $header->DocNum,
      'id' => $request->id
    ];

    // dd($data);

    return view('sap.sales.detail.item_edit')->with($data); 
  }

  public function lines_item_update(Request $request)
  {
    $header = OrderHeader::where('DocNum',$request->DocNum)->first();
    $docEntry = $header->DocEntry;

    $service = new ApiServices;

    $satuan = $request->Satuan;
    $jml_order = $request->Quantity;

    // $cust = Customer::where('code',$request->CardCode)->first();

    $post_cust = [
      'ItemCode' => $request->CardCode
    ];

    $cust = getCustomerId(json_encode($post_cust));

    $post2 = [
      'ItemCode' => $request->ItemCode,
      'CardCode' => $request->CardCode,
      'WhsCode' => $request->Warehouse
    ];

    $UomData = $service->getUomDetail(json_encode($post2));

    if($satuan=="nisik")
    {
      $UnitMsr = $UomData['satuan_kecil'];
      $NumPerMsr = 1;
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_pcs'];

      $UnitMsr2 = $UomData['satuan_besar'];
      $NumPerMsr2 = $UomData['nisib'];
      $UnitPrice2 = $UomData['harga_jual_ktn'];
    }
    else
    {
      $UnitMsr = $UomData['satuan_besar'];
      $NumPerMsr = $UomData['nisib'];
      $Quantity = $jml_order;
      $UnitPrice = $UomData['harga_jual_ktn'];

      $UnitMsr2 = $UomData['satuan_kecil'];
      $NumPerMsr2 = 1;
      $UnitPrice2 = $UomData['harga_jual_pcs'];
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

    $post3 = ['UomEntry'=>$UnitMsr];
    $getUomEntry = $service->getUomEntry(json_encode($post3));
    $UomEntry = $getUomEntry['uom_entry'];

    $post4 = ['UomEntry'=>$UnitMsr2];
    $getUomEntry2 = $service->getUomEntry(json_encode($post4));
    $UomEntry2 = $getUomEntry2['uom_entry'];

    $post_available = [
      'ItemCode' => $request->ItemCode,
      'WhsCode' => $request->Warehouse,
    ];
    
    $available = getAvailable(json_encode($post_available));
    $availablex = $available['available'];

    if ($availablex >= 1) 
    {
      $qty_real = $Quantity * $NumPerMsr;

      if ($availablex > $qty_real) 
      {
        $qty = $qty_real / $NumPerMsr;
      }
      else 
      {
        if ($UnitMsr=="KTN") 
        {
          $qty = $availablex;
          $UnitMsr = $UomData['satuan_kecil'];
          $NumPerMsr = 1;
          $UnitPrice = $UomData['harga_jual_pcs'];

          $UnitMsr2 = $UomData['satuan_besar'];
          $NumPerMsr2 = $UomData['nisib'];
          $UnitPrice2 = $UomData['harga_jual_ktn'];
        }
        else
        {
          $qty = $availablex / $NumPerMsr;
        }
      }

      $data2 = [
        'ItemCode' => $request->ItemCode,
        'Quantity' => $qty,
        'UnitPrice' => $UnitPrice,
        'UnitMsr' => $UnitMsr,
        'UomCode' => $UnitMsr,
        'UomEntry' => $UomEntry,
        'NumPerMsr' => $NumPerMsr,
        'UnitPrice2' => $UnitPrice2,
        'UnitMsr2' => $UnitMsr2,
        'UomCode2' => $UnitMsr2,
        'UomEntry2' => $UomEntry2,
        'NumPerMsr2' => $NumPerMsr2,
        'CostingCode' => isset($cust['U_CLASS']) ? $cust['U_CLASS'] : '',
        'CostingCode2' => $CostingCode2x,
        'CostingCode3' => 'SAL',
        'WarehouseCode' => $request->Warehouse
      ]; 

      // dd($data2);

      OrderLines::find($request->id)->update($data2);

      $callback = array(
        'message' => 'sukses',
        'docnum' => $request->DocNum
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

  public function lines_item_delete($id)
  {
    // $get = OrderLines::find($id);
    // $docentry = $get->DocEntry;

   
    // if($post)
    // {
    //   $sum = DB::table('order_lines')
    //            ->where('DocEntry',$docentry)
    //            ->sum('LineTotal');

    //   $header = [
    //     'DocTotal' => $sum,
    //     'VatSum' => $sum * 0.11
    //   ];

    //   OrderHeader::where('DocEntry',$docentry)->update($header);

      
  
      
    // }

    OrderLines::find($id)->delete();

    $alert = array(
      'type' => 'success',
      'message' => 'SO Lines berhasil di delete !'
    );

    return redirect()->back()->with($alert);
  }

  public function check_document(Request $request)
  {
    $get = OrderHeader::where('DocEntry',$request->id)->first();
    $docnum = $get['DocNum'];

    $post= [
      'DocEntry' => $request->id
    ];
    
    $DocStatus = $this->service->checkDocument(json_encode($post));

    if ($DocStatus=="O") 
    {
      $data = [
        'DocStatus' => "O"
      ];

      OrderHeader::where('DocEntry',$request->id)->update($data);

      $callback = array(
        'message' => 'sukses',
        'docnum' => $docnum
      );

      echo json_encode($callback);
    }
    else
    {
      $callback = array(
        'message' => 'error',
      );

      echo json_encode($callback);
    }
  }

  public function relation_maps(Request $request)
  {
    $data = [
      'DocNum' => $request->id
    ];

    $check = callSapApiLtaWithPost('relationMapsOrder',json_encode($data));

    // dd($check);

    $data = [
      'title' => 'Relation Maps',
      'check' => $check
    ];

    return view('sap.relation')->with($data);
  }

	public function fixbug(Request $request)
	{
		$docEntry = $request->id;

		$fix = $this->service->fixbug($docEntry);

		// dd($fix);

		$callback = array(
			'message' => 'sukses',
			'docnum' => $fix['docnum']
		);

		echo json_encode($callback);
	}

	public function lines_batch(Request $request)
	{
		$id = $request->id;

		$lines = OrderLines::find($id);

		$header = OrderHeader::where('DocEntry',$lines->DocEntry)->first();

		$post = [
			'ItemCode' => $lines->ItemCode,
			'WhsCode' => $lines->WarehouseCode
		];

		$check = callSapApiLtaWithPost('getBatchNumber',json_encode($post));

		$selectedBatch = OrderLinesBatch::where('order_lines_id',$id)->first();

		$post_item = [
			'ItemCode' => $lines->ItemCode
		];

		$item = getItemId(json_encode($post_item));

		$data = [
			'title' => 'Batch Number',
			'batch' => $check,
			'ItemCode' => $lines->ItemCode,
			'ItemName' => $item['ItemName'],
			'warehouse' => $lines->WarehouseCode,
			'DocEntry' => $lines->DocEntry,
			'Quantity' => $lines->Quantity,
			'id' => $id,
			'DocNum' => $header->DocNum,
			'BatchNumber' => isset($selectedBatch->BatchNumber) ? $selectedBatch->BatchNumber : NULL
		];

		return view('sap.sales.detail.item_batch')->with($data);
	}

	public function lines_batch_update(Request $request)
	{
		// dd($request->all());

		$cek = OrderLinesBatch::where('order_lines_id',$request->order_lines_id)->get();

		if(count($cek) > 0)
		{
			$data = [
				'BatchNumber' => $request->BatchNumber,
				'Quantity' => $request->Quantity,
				'ItemCode' => $request->ItemCode
			];

			OrderLinesBatch::where('order_lines_id',$request->order_lines_id)
										 ->where('DocEntry',$request->DocEntry)
										 ->update($data);
		}
		else
		{
			$data = [
				'DocEntry' => $request->DocEntry,
				'BatchNumber' => $request->BatchNumber,
				'Quantity' => $request->Quantity,
				'ItemCode' => $request->ItemCode,
				'order_lines_id' => $request->order_lines_id
			];

			OrderLinesBatch::create($data);
		}

		$callback = array(
			'message' => 'sukses',
			'docnum' => $request->DocNum
		);

		echo json_encode($callback);
	}
}