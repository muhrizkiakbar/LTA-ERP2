<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Services\ArcmServices;
use Illuminate\Http\Request;

class ArcmController extends Controller
{
  public function __construct(ArcmServices $services)
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
      'title' => 'A/R Credit Note',
      'assets' => $assets
    ];

    return view('sap.arcm.index')->with($data);
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
      'title' => "Detail - A/R Credit Note",
      'assets' => $assets,
      'local_currency' => 'Local Currency',
      'header' => $row['header'],
      'lines' => $row['lines']
    ];
    
    return view('sap.arcm.detail')->with($data);
  }

  public function print($docNum)
  {
    $row = $this->service->detail_data($docNum);

    $data = [
      'header' => $row['header'],
      'lines' => $row['lines']
    ];
    
    return view('sap.arcm.detail.print')->with($data);
  }

  public function print_kwitansi($docNum)
  {
    $row = $this->service->detail_data($docNum);

    $data = [
      'header' => $row['header'],
      'lines' => $row['lines']
    ];
    
    return view('sap.arcm.detail.print_kwitansi')->with($data);
  }

  public function print_tanda_terima($docNum)
  {
    $row = $this->service->detail_data($docNum);

    $data = [
      'header' => $row['header'],
      'lines' => $row['lines']
    ];
    
    return view('sap.arcm.detail.print_tanda_terima')->with($data);
  }

  public function print_bs($docNum)
  {
    $row = $this->service->detail_data($docNum);

    $data = [
      'header' => $row['header'],
      'lines' => $row['lines'],
      'docTotal' => array_sum(array_column($row['lines'],'lineTotal'))
    ];
    
    return view('sap.arcm.detail.print_bs')->with($data);
  }

  public function update_printed(Request $request)
  {
    $id = $request->docnum;

    return $this->service->updatePrinted($id);
  }
}
