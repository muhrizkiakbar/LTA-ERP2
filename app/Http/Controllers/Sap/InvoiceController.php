<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryHeader;
use App\Http\Controllers\Controller;
use App\Models\InvoiceHeader;
use App\Models\Sales;
use App\Services\InvoiceServices;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function __construct(InvoiceServices $services)
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
      'title' => 'Invoice Order',
      'assets' => $assets
    ];

    return view('sap.invoice.index')->with($data);
  }

  public function search_docnum(Request $request)
  {
    $service = new InvoiceServices;
    $docnum = $request->docnum;

    $order_sap = $service->getInvoice($docnum);

    if ($order_sap['message']=='success') 
    {
      $callback = array(
        'message' => 'sukses',
        'docnum' => $order_sap['docnum']
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
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css',
      ),
      'script' => array(
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

    $get = $this->service->getDataDetail($docnum);
    $branch = Branch::get();
    $series = Branch::where('BPLid',$get['BPLId'])->first();
    $branchx = Branch::find($get['Branch']);

    $post_customer = [
      'CardCode' => $get['CardCode']
    ];
    $customer = getCustomerId(json_encode($post_customer));

    $lines = $this->service->getDataLines($get['DocEntry']);

    $sales = Sales::find($get['SalesPersonCode']);

    $post_sales = [
      'SlpCode' => $get['SalesPersonCode']
    ];
    
    $sales = getSalesEmployeeId(json_encode($post_sales));

    // dd( $sales);

    $data = [
      'title' => "Detail - A/R Invoice",
      'assets' => $assets,
      'row' => $get,
      'local_currency' => 'Local Currency',
      'series' => $series->snar,
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
      'DocEntry' => $get['DocEntry']
    ];

    return view('sap.invoice.detail')->with($data);
  }

  public function relation_maps(Request $request)
  {
    $data = [
      'DocNum' => $request->id
    ];

    $check = callSapApiLtaWithPost('relationMapsInvoiceNon',json_encode($data));

    if (count($check) > 0) 
    {
      $data = [
        'title' => 'Relation Maps',
        'check' => $check
      ];
  
      return view('sap.relation_non')->with($data);
    }
    else
    {
      $check2 = callSapApiLtaWithPost('relationMapsInvoice',json_encode($data));

      // dd($check);
  
      $data = [
        'title' => 'Relation Maps',
        'check' => $check2
      ];
  
      return view('sap.relation')->with($data);
    }
  }
}
