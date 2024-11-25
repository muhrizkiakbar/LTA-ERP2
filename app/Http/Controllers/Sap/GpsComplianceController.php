<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\GpsCompliance;
use App\Http\Controllers\Controller;
use App\Services\GpsComplianceServices;
use Illuminate\Http\Request;

class GpsComplianceController extends Controller
{
  public function __construct(GpsComplianceServices $service)
  {
    $this->service = $service;
  }

  public function png()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/datatables/custom.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/plugins/simple-lightbox/simple-lightbox.min.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js',
        'assets/plugins/datatables/datatables.min.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js',
        'assets/plugins/simple-lightbox/simple-lightbox.jquery.min.js'
      )
    ];

    $branch = Branch::pluck('title','id');

    $data = [
      'title' => 'Sync GPS Compliance',
      'assets' => $assets,
      'branch' => $branch
    ];

    return view('sap.gps.png.index')->with($data);
  }

  public function spv_png(Request $request)
  {
    $post = [
      'kode_branch' => $request->branch
    ];

    $url = 'spvPng';
    $get = callApiWithPost(1,$url,json_encode($post));

    // dd($get);

    $list = "<option value=''>-- Pilih Supervisor --</option>";
    foreach ($get as $key) {
      $list .= "<option value='" . $key['kode_spv'] . "'>" . $key['nama_spv'] . "</option>";
    }
    $callback = array('listdoc' => $list);
    echo json_encode($callback);
  }

  public function sync_png(Request $request)
  {
    $post = [
      'dateFrom' => $request->dateFrom,
      'dateTo' => $request->dateTo,
      'spv' => $request->supervisor
    ];

    // dd($post);

    $url = 'syncPngGps';
    $get = callApiWithPost(1,$url,json_encode($post));

    if (count($get) > 0) 
    {
      $no = 1;
      $nox = 0;
      foreach ($get as $key => $value) 
      {
        $cek = $this->service->cek($value['hash']);
        if ($cek->count() == 0)
        {
          $data = [
            'distributor_name' => $value['distributor_name'],
            'branch_code' => $value['branch_code'],
            'branch_name' => $value['branch_name'],
            'visit_date' => date('Ymd',strtotime($value['visit_date'])),
            'supervisor_code' => $value['supervisor_code'],
            'supervisor_name' => $value['supervisor_name'],
            'seller_code' => $value['seller_code'],
            'seller_name' => $value['seller_name'],
            'seller_type' => $value['seller_type'],
            'store_code' => $value['store_code'],
            'store_name' => $value['store_name'],
            'store_chanel' => $value['store_chanel'],
            'visit_frequency' => $value['visit_frequency'],
            'planned_sequence' => $value['planned_sequence'],
            'visit_sequence' => $value['visit_sequence'],
            'off_route' => $value['off_route'],
            'sales_obj' => $value['sales_obj'],
            'sales_act' => $value['sales_act'],
            'master_coordinate' => $value['master_coordinate'],
            'actual_coordinate' => $value['actual_coordinate'],
            'compliance' => $value['compliance'],
            'distance_m' => $value['distance_m'],
            'time_in' => $value['time_in'],
            'time_out' => $value['time_out'],
            'duration' => $value['duration'],
            'file' => $value['file'],
            'hash' => $value['hash'],
            'principal' => 'P&G'
          ];

          $this->service->insert($data);
          $nox = $no++;
        }
      }

      if ($nox < 1) 
      {
        $callback = array(
          'message' => 'error'
        );
  
        echo json_encode($callback);
      } 
      else 
      {
        $callback = array(
          'message' => 'sukses'
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

    // dd($data);
  }

  public function view_png(Request $request)
  {
    $get = $this->service->data($request->dateFrom, $request->dateTo, $request->supervisor);
    // dd($get);
    $data = [
      'row' => $get
    ];

    return view('sap.gps.png.result')->with($data);
  }

  public function temuan(Request $request)
  {
    $post = [
      'temuan' => 1
    ];

    $update = GpsCompliance::find($request->id)->update($post);

    if ($update) 
    {
      $callback = array(
        'message' => 'sukses'
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
}
