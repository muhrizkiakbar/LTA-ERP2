<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Http\Controllers\Controller;
use App\Models\PngHeader;
use App\Services\PerformanceServices;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
  public function __construct(PerformanceServices $service)
  {
    $this->service = $service;
  }

  public function order_png()
  {
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js'
      )
    ];

    if ($users_role==1) 
    {
      $branch = Branch::pluck('title','id');
    }
    else
    {
      $branch = Branch::where('id',$branchx)->pluck('title','id');
    }

    $data = [
      'title' => 'Order Balance P&G',
      'assets' => $assets,
      'branch' => $branch
    ];

    return view('sap.performance.png')->with($data);
  }

  public function order_png_search(Request $request)
  {
    $arr = [];

    $data = [
      'tgl_order' => $request->date,
      'kode_branch' => $request->branch_code
    ];

    $get = $this->service->getOrderDetail(json_encode($data));

    foreach ($get['data'] as $key => $value) 
    {
      $post_slp = [
        'code' => $value['SalesPersonCode']  
      ];
      $sales = getSalesDetail(json_encode($post_slp));
      $sfa_order = $value['count_lines'];
      $erp_order = $this->getErpPngOrder($sales['SlpCode'],$request->date);
      $deviasi = $sfa_order - $erp_order;

      $arr[] = [
        'sales_code_sfa' => $value['SalesPersonCode'],
        'sales_code_png' => $sales['SlpCode'],
        'sales_name' => $sales['SlpName'],
        'sfa_order' => $sfa_order,
        'erp_order' => $erp_order,
        'deviasi' => $deviasi
      ];
    }

    $view = [
      'row' => $arr
    ];

    return view('sap.performance.png_result')->with($view);
  }

  public function getErpPngOrder($slpCode,$date)
  {
    $get = PngHeader::where('SalesPersonCode',$slpCode)
                    ->where('DocDate',$date)
                    ->count();
    
    return $get;
  }
}
