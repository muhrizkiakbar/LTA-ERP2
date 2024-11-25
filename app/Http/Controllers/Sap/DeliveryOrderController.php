<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryHeader;
use App\Models\DeliveryLines;
use App\Models\DeliveryVoucher;
use App\Models\History;
use App\Http\Controllers\Controller;
use App\Models\InvoiceHeader;
use App\Models\InvoiceLines;
use App\Models\OrderHeader;
use App\Models\OrderLines;
use App\Models\ReturnTempHeader;
use App\Models\ReturnTempLines;
use App\Models\Sales;
use App\Services\ApiServices;
use App\Services\DeliveryServices;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use PDF;

class DeliveryOrderController extends Controller
{
  public function __construct(DeliveryServices $services)
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
      'title' => 'Delivery Order',
      'assets' => $assets
    ];

    return view('sap.delivery.index')->with($data);
  }

  public function search_docnum(Request $request)
  {
    $service = new DeliveryServices;
    $docnum = $request->docnum;

    $order = DeliveryHeader::where('DocNum',$docnum)->first();
    
    if(!empty($order['DocNum']))
    {
      $docentry = $order['DocEntry'];

      $delete_header = DeliveryHeader::where('DocNum',$docnum)->delete();
      if ($delete_header) 
      {
        DeliveryLines::where('DocEntry',$docentry)->delete();
      }
    }

    $post_odln = [
      'DocNum' => $docnum
    ];
    $sap = $service->getOdlnDetail(json_encode($post_odln));

		// dd($sap);

    if (!empty($sap)) 
    {
      $branchxx = getBranchDetail2($sap['BPLId'])->id;

      $header = [
        'DocNum' => $sap['DocNum'],
        'DocEntry' => $sap['DocEntry'],
        'CardCode' => $sap['CardCode'],
        'Branch' => $branchxx,
        'DocDate' => dateExp2($sap['DocDate']),
        'DocDueDate' => dateExp2($sap['DocDueDate']),
        'NumAtCard' => $sap['NumAtCard'],
        'SalesPersonCode' => $sap['SalesPersonCode'],
        'U_NOPOLISI' => $sap['U_NOPOLISI'],
        'U_NOPOLISI2' => $sap['U_NOPOLISI2'],
        'Comments' => $sap['Comments'],
        'BPLId' => $sap['BPLId'],
        'VatSum' => $sap['VatSum'],
        'DocTotal' => $sap['DocTotal']-$sap['VatSum'],
        'DocStatus' => $sap['DocStatus'],
        'USER_CODE' => $sap['USER_CODE'],
        'U_NAME' => $sap['U_NAME'],
      ];

      // dd($header);

      $post_header = DeliveryHeader::create($header);
      if($post_header)
      {
        foreach ($sap['SalesLines'] as $linesx) 
        {
          $totalx = $linesx['Quantity'] * $linesx['PriceBefDi'];
          $discx1 = ($linesx['U_DISC1'] / 100) * $totalx;
          $discx2 = ($linesx['U_DISC2'] / 100) * ($totalx - $discx1);
          $discx3 = ($linesx['U_DISC3'] / 100) * ($totalx - $discx1 - $discx2);
          $discx4 = ($linesx['U_DISC4'] / 100) * ($totalx - $discx1 - $discx2 - $discx3);
          $discx5 = ($linesx['U_DISC5'] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4);
          $discx6 = ($linesx['U_DISC6'] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);
          $discx7 = ($linesx['U_DISC7'] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);
          $discx8 = ($linesx['U_DISC8'] / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

          $lines[] = [
            'DocEntry' => $linesx['DocEntry'],
            'BaseLine' => $linesx['BaseLine'],
            'BaseEntry' => $linesx['BaseEntry'],
            'BaseType' => $linesx['BaseType'],
            'ItemCode' => $linesx['ItemCode'],
            'Quantity' => $linesx['Quantity'],
            'TaxCode' => $linesx['VatGroup'],
            'UnitPrice' => $linesx['PriceBefDi'],
            'UnitMsr' => $linesx['unitMsr'],
            'UomCode' => $linesx['UomCode'],
            'UomEntry' => $linesx['UomEntry'],
            'NumPerMsr' => $linesx['NumPerMsr'],
            'CostingCode' => $linesx['OcrCode'],
            'CostingCode2' => $linesx['OcrCode2'],
            'CostingCode3' => $linesx['OcrCode3'],
            'WarehouseCode' => $linesx['WhsCode'],
            'DocStatus' => $linesx['LineStatus'],
            'U_DISC1' => $linesx['U_DISC1'],
            'U_DISCVALUE1' => $discx1,
            'U_DISC2' => $linesx['U_DISC2'],
            'U_DISCVALUE2' => $discx2,
            'U_DISC3' => $linesx['U_DISC3'],
            'U_DISCVALUE3' => $discx3,
            'U_DISC4' => $linesx['U_DISC4'],
            'U_DISCVALUE4' => $discx4,
            'U_DISC5' => $linesx['U_DISC5'],
            'U_DISCVALUE5' => $discx5,
            'U_DISC6' => $linesx['U_DISC6'],
            'U_DISCVALUE6' => $discx6,
            'U_DISC7' => $linesx['U_DISC7'],
            'U_DISCVALUE7' => $discx7,
            'U_DISC8' => $linesx['U_DISC8'],
            'U_DISCVALUE8' => $discx8,
            'LineNum' => $linesx['LineNum'],
            'Batch' => $linesx['Batch'],
            'ExpDate' => $linesx['ExpDate']
          ];
        }

        // dd($lines);

        DeliveryLines::insert($lines);
        
        $callback = array(
          'message' => 'sukses',
          'docnum' => $sap['DocNum']
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

  public function detail(Request $request)
  {
    $docnum = $request->docnum;
    $role = auth()->user()->users_role_id;

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

    $get = $this->service->getDataDetail($docnum);
    $branch = Branch::get();
    $series = Branch::where('BPLid',$get['BPLId'])->first();
    $branchx = Branch::find($get['Branch']);

    // $customer = Customer::where('code',$get['CardCode'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customerx = getCustomerId2(json_encode($post_customer));

    $lines = $this->service->getDataLines($get['DocEntry']);

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $exp = explode('*',$get['NumAtCard']);
    $obat = isset($exp[1]) ? $exp[1] : '' ;

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

		$salesx = $sales=='nan' ? '-' : $sales;

    $data = [
      'title' => "Detail - Delivery Order",
      'assets' => $assets,
      'row' => $get,
      'local_currency' => 'Local Currency',
      'series' => $series->sndo,
      'branch' => $branch,
      'branch_title' => $branchx->title,
      'branch_reg' => $branchx->VatRegNum,
      'docnum' => $get['DocNum'],
      // 'customer' => $customer,
      'custx' => $customerx,
      'lines' => $lines,
      'sales' => $salesx,
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiah($get['DocTotal']),
      'VatSum' => rupiah($get['VatSum']),
      'TotalSum' => rupiah($get['TotalSum']),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'obat' => isset($obat) ? $obat : '',
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'total2' => $voucher!=0 ? $get['TotalSum'] - $voucher : 0,
      'role' => $role
    ];

		// dd($data);

    return view('sap.delivery.detail')->with($data);
  }

  public function printx($docnum)
  {
    $get = $this->service->getDataDetail($docnum);
    $branch = Branch::get();
    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    // dd($customer);

    $lines = $this->service->getDataLines($get['DocEntry']);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    $data = [
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
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : ''
    ];

    return view('sap.delivery.detail.print')->with($data);
  }

  public function print($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);
    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 11;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 11 + 17;

          if ($i >= 3) 
          {
            $skip = 11 + (($i-2) * 17) + 17;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 11;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    $totalx2 = $get['TotalSum'];

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print2')->with($data);
  }

  public function print_obat($docnum)
  {  
    // dd($docnum);
    $get = $this->service->getDataDetail($docnum);
    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 11;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 11 + 17;

          if ($i >= 3) 
          {
            $skip = 11 + (($i-2) * 17) + 17;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 11;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    $totalx2 = $get['TotalSum'];

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'USER_CODE' => $get['USER_CODE'],
      'U_NAME' => $get['U_NAME'],
      'pbf' => $series->NoApoteker,
      'cdob' => $series->CODB,
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_obat')->with($data);
  }

  public function print_baru($docnum)
  {
    $get = $this->service->getDataDetail($docnum);
    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 11;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 11 + 17;

          if ($i >= 3) 
          {
            $skip = 11 + (($i-2) * 17) + 17;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 11;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($get['TotalSum']-$voucher,0),
    ];

    return view('sap.delivery.detail.print3')->with($data);
  }

  public function print_png($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);
    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 9;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate2($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 9 + 14;

          if ($i >= 3) 
          {
            $skip = 9 + (($i-2) * 14) + 14;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 9;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($get['TotalSum']-$voucher,0),
    ];

    return view('sap.delivery.detail.print_png')->with($data);
  }

  public function print_png2($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);
    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 10;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate2($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 10 + 16;

          if ($i >= 3) 
          {
            $skip = 10 + (($i-2) * 16) + 16;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 10;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    $totalx2 = $get['TotalSum'];

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_png2')->with($data);
  }

  public function print_png3($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);

    $totalx2 = $get['TotalSum'];

    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 10;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate3($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 10 + 16;

          if ($i >= 3) 
          {
            $skip = 10 + (($i-2) * 16) + 16;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 10;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_png3')->with($data);
  }

  public function print_png4($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);

    $totalx2 = $get['TotalSum'];

    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 10;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate4($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 10 + 16;

          if ($i >= 3) 
          {
            $skip = 10 + (($i-2) * 16) + 16;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 10;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_png4')->with($data);
  }

  public function print_png5($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);

    $totalx2 = $get['TotalSum'];

    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 10;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate4($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 10 + 16;

          if ($i >= 3) 
          {
            $skip = 10 + (($i-2) * 16) + 16;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 10;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_png5')->with($data);
  }

  public function print_png_dev($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);

    $totalx2 = $get['TotalSum'];

    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 10;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate4($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 10 + 16;

          if ($i >= 3) 
          {
            $skip = 10 + (($i-2) * 16) + 16;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 10;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_png_dev')->with($data);
  }

  public function print_png6($docnum)
  {  
    $get = $this->service->getDataDetail($docnum);

    $totalx2 = $get['TotalSum'];

    $series = Branch::where('BPLid',$get['BPLId'])->first();

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId(json_encode($post_customer));

    $branchx = Warehouse::where('code',$customer['U_CLASS'])->first();

    $lines = $this->service->getDataLines($get['DocEntry']);

    $voucher = $this->service->getVoucher($get['DocNum']);
    $voucherList = $this->service->voucherList2($get['DocNum']);

    $count = count($lines);

    $bagi = $count / 10;

    $datax = [];

    if ($bagi > 0) 
    {
      for ($i=0; $i <= $bagi ; $i++) 
      {  
        $lines = $this->service->getDataLinesSeparate4($get['DocEntry'],$i);

        if ($i > 1) 
        {
          $skipx = 10 + 16;

          if ($i >= 3) 
          {
            $skip = 10 + (($i-2) * 16) + 16;
          }
          else
          {
            $skip = $skipx;
          }
        }
        else
        {
          $skip = $i * 10;
        }

        if (!empty($lines)) 
        {
          $datax[] = [
            'header' => $i,
            'lines' => $lines,
            'skip' => $skip
          ];
        }
      }
    }
    else
    {
      $datax[] = [
        'header' => 0,
        'lines' => $lines,
        'skip' => 0
      ];
    }

    // dd($data);

    $cogs = DeliveryLines::where('DocEntry',$get['DocEntry'])->first();
    $cogx = $cogs['CostingCode2'];

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    $sales = getSalesEmployeeId(json_encode($post_sales));

    $docNumSo = $this->service->getDocNumSO($get['DocEntry']);

    $post_printed = [
      'DocNum' => $docnum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    // dd($printed);

    if($printed=='N')
    {
      $update_print = [
        'DocNum' => $docnum
      ];

      $this->updatePrinted(json_encode($update_print));
    }

    $post_top = [
      'CardCode' => $get['CardCode']
    ];

    $getTop = $this->getTopCustomer(json_encode($post_top));
    $top = "+".$getTop." days";
    $docDate = $get['DocDate']; 
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

    $nopol_mix = isset($customer['NopolMix']) ? $customer['NopolMix'] : 'MIX' ;
    $nopol_png = isset($customer['NopolPng']) ? $customer['NopolPng'] : 'P&G' ;

    

    if ($totalx2 >= 5000000) 
    {
      $uang_materai = 10000;
    }
    else
    {
      $uang_materai = 0;
    }

    $totalx3 = $totalx2 + $uang_materai;

    $data = [
      'series' => $series->sndo,
      'docnum' => $get['DocNum'],
      'customer' => $customer,
      'sales' => $sales,
      'cardCode' => $get['CardCode'],
      'numAtCard' => $get['NumAtCard'],
      'remarks' => $get['Comments'],
      'DocTotal' => rupiahnon($get['DocTotal']),
      'VatSum' => rupiahnon($get['VatSum']),
      'TotalSum' => round($get['TotalSum'],0),
      'DocStatus' => $get['DocStatus'],
      'DocEntry' => $get['DocEntry'],
      'DocNumSO' => $docNumSo,
      'segment' => isset($customer['cseg4']) ? $customer['cseg4'] : '',
      'plat' => $cogx=='MIX' ? $nopol_mix : $nopol_png,
      'DocDate' => $get['DocDate'],
      'cabang' => getWarehouseDetail2($customer['U_CLASS'])->kota,
      'branch_norek' => $branchx->rek_no,
      'branch_norek_name' => $branchx->rek_title,
      'print' => $printed=='Y' ? 'Copy' : '',
      'DocDueDate' => $docDueDate,
      'top' => isset($getTop) ? $getTop : '',
      'separate' => $datax,
      'separate_num' => count($datax),
      'voucher' => $voucher,
      'voucherList' => $voucherList,
      'TotalSum2' => round($totalx3-$voucher,0),
      'uang_materai' => $uang_materai
    ];

    return view('sap.delivery.detail.print_png6')->with($data);
  }

  public function invoice(Request $request)
  {
    $apiService = new ApiServices;

    $user = auth()->user()->username_sap;
    $pass = auth()->user()->password_sap;
    $username = auth()->user()->username;

    $id = $request->id;
    $postingDate = $request->postingDate;
    $get = DeliveryHeader::where('DocNum',$id)->first();
    $docentry = $get['DocEntry'];
    $docnum = $get['DocNum'];
    $doctotal = $get['DocTotal'] + $get['VatSum'];

    if ($doctotal >= 5000000) 
    {
      $callback = array(
        'message' => 'error_nominal',
        'text' => 'Maaf, Transaksi >= 5.000.000 harap menggunakan SAP R'
      );

      echo json_encode($callback);
    }
    else
    {
      $json = $this->service->jsonInvoice($id,$postingDate);

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

      $header = [
        "Cookie: ".$headers,
        "accept: */*",
        "accept-language: en-US,en;q=0.8",
        "content-type: application/json"
      ];

      $cek_period = [
        'DocDate' => $postingDate
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
        $url_sales = 'https://192.168.1.81:50000/b1s/v1/Invoices';
        $api_sales = $this->service->pushInvoice($header,$url_sales,json_encode($json));
    
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
    
          $post = InvoiceHeader::create($header);
          if($post)
          {
            $header2 = [
              'DocStatus' => 'C'
            ];
    
            DeliveryHeader::where('DocNum',$docnum)->update($header2);
            InvoiceLines::insert($lines);
          }
    
          $history = [
            'title' => $username,
            'history_category_id' => 4,
            'card_code' => $api_sales['CardCode'],
            'desc' => 'Sukses push data <strong>'.$api_sales['CardCode'].'</strong> A/R Invoice ke SAP dengan Document Number <strong>'.$api_sales['DocNum'].'</strong>'
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
            'history_category_id' => 4,
            'card_code' => $id,
            'desc' => 'Error push data A/R Invoice ke SAP dengan pesan <strong>'.$error.'</strong>'
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

  public function return_temp(Request $request)
  {
    $id = $request->id;
    $get = DeliveryHeader::where('DocNum',$id)->first();
    $lines = $this->service->jsonReturnTempLines($get['DocEntry']);

    $header = [
      'DocEntry' => $get['DocEntry'],
      'DocNum' => $get['DocNum'],
      'CardCode' => $get['CardCode'],
      'DocDueDate' => $get['DocDueDate'],
      'DocDate' => $get['DocDate'],
      'BPLId' => $get['BPLId'],
      'SalesPersonCode'=> $get['SalesPersonCode'],
      'NumAtCard' => $get['DocNum'],
      'Comments' => $get['Comments'],
      'U_NOPOLISI' => $get['U_NOPOLISI'],
      'U_NOPOLISI2' => $get['U_NOPOLISI2'],
      'Branch' => $get['Branch'],
      'VatSum' => $get['VatSum'],
      'DocTotal' => $get['DocTotal']
    ];

    // dd($lines);

    $post = ReturnTempHeader::create($header);
    if($post)
    {
      ReturnTempLines::insert($lines);

      $callback = array(
        'message' => 'sukses',
        'docnum' => $get['DocNum']
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

  public function return_check(Request $request)
  {
    $id = $request->id;

    $cek = DeliveryHeader::where('DocEntry',$id)->first();
    $cek_return = $cek->return_check;
    $docnum = $cek->DocNum;

    if($cek_return == 0)
    {
      $lines = DeliveryLines::where('DocEntry',$id)->get();
      $no = 0;
      $nox= 0;
      foreach ($lines as $value) 
      {
        $id = $value->id;
        $docentry = $value->DocEntry;
        $itemcode = $value->ItemCode;
        $qty = $value->Quantity;
        $price = $value->UnitPrice;
        $baseentry = $value->BaseEntry;

        $post = [
          'DocEntry' => $docentry,
          'ItemCode' => $itemcode
        ];

        $cekQty = $this->service->cekQtyReturn(json_encode($post));
        $qtyR = $cekQty['Quantity'];
        if ($cekQty != 0) 
        {
          $nox = $no++;
          $sisa = $qty - $qtyR;

          if($sisa == 0)
          {
            $post_deliv = [
              'DocStatus' => 'C'
            ];
            
            DeliveryLines::find($id)->update($post_deliv);

            $post_sales = [
              'DocStatus' => 'O'
            ];

            OrderLines::where('DocEntry',$baseentry)
                      ->where('ItemCode',$itemcode)
                      ->update($post_sales);

          }
          else
          {
            $beforeDisc = $sisa * $price;

            $disc1 = $value->U_DISC1;
            $disc2 = $value->U_DISC2;
            $disc3 = $value->U_DISC3;
            $disc4 = $value->U_DISC4;
            $disc5 = $value->U_DISC5;
            $disc6 = $value->U_DISC6;
            $disc7 = $value->U_DISC7;
            $disc8 = $value->U_DISC8;

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

            $post_deliv = [
              'Quantity' => $sisa,
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
            
            DeliveryLines::find($id)->update($post_deliv);
          }
        }
      }

      // dd($nox);

      if($nox > 0)
      {
        $post_header2 = [
          'return_check' => 1
        ];

        DeliveryHeader::where('DocNum',$docnum)->update($post_header2);

        $post_header3 = [
          'DocStatus' => 'O'
        ];

        OrderHeader::where('DocEntry',$baseentry)->update($post_header3);

        $callback = array(
          'message' => 'sukses',
          'docnum' => $docnum
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
    else
    {
      $callback = array(
        'message' => 'already'
      );

      echo json_encode($callback);
    }
  }

  public function cekPrinted($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/cekPrinted';
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

  public function updatePrinted($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/updatePrinted';
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

  public function discount(Request $request)
  {
    $id = $request->id;

    $lines = $this->service->getDataLinesDisc($id);

    $data = [
      'title' => 'Discount Calculation',
      'lines' => $lines,
      'id' => $id
    ];

    return view('sap.delivery.detail.discount')->with($data);
  }

  public function discount_update(Request $request)
  {
    $numAtCard = $request->numAtCard;
    $header = DeliveryHeader::where('DocEntry',$numAtCard)->first();
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

      DeliveryLines::find($id[$key])->update($data);

      $totalx2 += round($totalxx,2);
    }

    // dd($totalx2);

    $data2 = [
      'DocTotal' => $totalx2,
      'VatSum' => ($totalx2 * 0.11)
    ];
    
    DeliveryHeader::where('DocNum',$docnum)->update($data2);

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
    $get = DeliveryHeader::where('DocEntry',$id)->first();
    $docentry = $get['DocEntry'];
    $docnum = $get['DocNum'];

    $json = $this->service->jsonUpdate($id);
    // dd($json);
    // dd($docentry);

    $db = 'LTALIVE2020';
    $url = 'https://192.168.1.81:50000/b1s/v1/Login';

    $body = [
        'CompanyDB' => $db,
        'UserName' => $user,
        'Password' => $pass
    ];

    // dd($body);

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

    $url_sales = 'https://192.168.1.81:50000/b1s/v1/DeliveryNotes('.$docentry.')';
    // dd($url_sales);
    $api_sales = $this->service->updateDelivery($header,$url_sales,json_encode($json));

    // dd($url_sales);

    if(isset($api_sales['error']))
    {
      $error = $api_sales['error']['message']['value'];

      $history = [
        'title' => $username,
        'history_category_id' => 2,
        'desc' => 'Error update data Delivery Order dengan Document Number <strong>'.$docnum.'</strong> ke SAP dengan pesan <strong>'.$error.'</strong>'
      ];

      History::create($history);

      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    }
    else
    {
      $history = [
        'title' => $username,
        'history_category_id' => 2,
        'desc' => 'Berhasil update data Delivery Order dengan Document Number <strong>'.$docnum.'</strong> ke SAP'
      ];

      History::create($history);

      $callback = array(
        'message' => 'sukses',
        'docnum' => $docnum
      );

      echo json_encode($callback);
    }
  }

  public function getTopCustomer($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getTopCustomer';
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

  public function voucher_generate(Request $request)
  {
    $id = $request->id;

    $header = DeliveryHeader::where('DocEntry',$id)->first();
    $cogs = DeliveryLines::where('DocEntry',$id)->first();
    $cogx = $cogs['CostingCode2'];

    $post_printed = [
      'DocNum' => $header->DocNum
    ];

    $printed = $this->cekPrinted(json_encode($post_printed));

    if($printed=='N')
    {
      $get = $this->service->generateCn($header->CardCode,$id,$cogx);

      if ($get['message']=='success') 
      {
        $callback = array(
          'message' => 'sukses',
          'kd' => $get['kd']
        );
      }
      elseif ($get['message']=='error_val') 
      {
        $callback = array(
          'message' => 'error_val',
          'kd' => $get['kd']
        );
      }
      else
      {
        $callback = array(
          'message' => 'error',
          'kd' => $get['kd']
        );
      }
    }
    else
    {
      $callback = array(
        'message' => 'error_print_already',
        'kd' => $id
      );
    }

    echo json_encode($callback);
  }

  public function voucher(Request $request)
  {
    $id = $request->id;

    $header = DeliveryHeader::where('DocEntry',$id)->first();
    $cogs = DeliveryLines::where('DocEntry',$id)->first();
    $cogx = $cogs['CostingCode2'];

    $row = $this->service->voucherList($header->CardCode, $cogx);

    $docTotal = array_sum(array_column($row,'BalanceDue'));

    $data = [
      'title' => 'Add Voucher - '.$header->cardCode,
      'row' => $row,
      'kd' => $header->DocNum,
      'docTotal' => $docTotal
    ];

    // dd($data);

    return view('sap.delivery.detail.voucher')->with($data);
  }

  public function voucher_update(Request $request)
  {
    $data = [];

    $kd = $request->kd;
    $docTotalCN = $request->docTotalCN;

    $generate = $this->service->generateVoucher($kd,$docTotalCN);

    if ($generate['message']=='error') 
    {
      $alert = array(
        'type' => 'danger',
        'message' => 'Voucher yg dipilih tidak boleh lebih dari total Invoice !'
      );
    }
    else
    {
      $alert = array(
        'type' => 'success',
        'message' => 'Voucher berhasil di update !'
      );
    }
   
    return redirect()->back()->with($alert);
  }

  public function voucher_release(Request $request)
  {
    // dd($request->all());
    
    $username = auth()->user()->username;

    $id = $request->id;

    $cek = DeliveryVoucher::where('DocNumDelivery',$id)->get();

    if (count($cek) > 0) 
    {
      DeliveryVoucher::where('DocNumDelivery',$id)->delete();

      $history = [
        'title' => $username,
        'history_category_id' => 2,
        'card_code' => $id,
        'desc' => 'Sukses release voucher dengan Document Number <strong>'.$id.'</strong>'
      ];

      History::create($history);

      $callback = array(
        'message' => 'sukses',
        'kd' => $id
      );
    }
    else
    {
      $history = [
        'title' => $username,
        'history_category_id' => 2,
        'card_code' => $id,
        'desc' => 'Error release voucher, Voucher tidak ditemukan'
      ];

      History::create($history);

      $callback = array(
        'message' => 'error'
      );
    }

    echo json_encode($callback);
  }

  public function relation_maps(Request $request)
  {
    $delivery = DeliveryHeader::where('DocNum',$request->id)->first();

    $numAtCard = explode('/',$delivery->NumAtCard);

    $check = $numAtCard[0];

		$numAtCard2 = explode('-',$delivery->NumAtCard);

    $check2 = $numAtCard2[0];

    if ($check!='FK' || $check2!='KINO') 
    {
      $data = [
        'DocNum' => $request->id
      ];
  
      $check = callSapApiLtaWithPost('relationMapsDelivery',json_encode($data));
  
      // dd($check);
  
      $data = [
        'title' => 'Relation Maps',
        'check' => $check
      ];
  
      return view('sap.relation')->with($data);
    }
    else
    {
			$data = [
        'DocNum' => $request->id
      ];
  
      $check = callSapApiLtaWithPost('relationMapsDeliveryNon',json_encode($data));
  
      // dd($check);
  
      $data = [
        'title' => 'Relation Maps',
        'check' => $check
      ];
  
      return view('sap.relation_non')->with($data);
    }
  }






}
