<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Http\Controllers\Controller;
use App\Services\VdistServices;
use App\Models\VdistHeader;
use App\Models\Warehouse;
use App\Models\WarehouseVdist;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class VdistController extends Controller
{
  public function __construct(VdistServices $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    $role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

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

    $get = $this->service->getData();

    if ($role==1) 
    {
      $branch = WarehouseVdist::whereNotNull('vdist')->pluck('kota','vdist');
    }
    else
    {
      $branch = WarehouseVdist::where('Branch',$branchx)->whereNotNull('vdist')->pluck('kota','vdist');
    }
    
    // dd($branch);

    $data = [
      'title' => 'Data VDIST',
      'assets' => $assets,
      'row' => $get,
      'branch' => $branch,
      'role' => $role
    ];

    return view('sap.vdist.index')->with($data);
  }

  public function sync(Request $request)
  {
    $post = [
      'tgl_order' => $request->date,
      'branch' => $request->branch
    ];

    $sync = $this->service->sync($post);

    echo json_encode($sync);
  }

  public function detail(Request $request)
  {
    $id = $request->id;

    $header = VdistHeader::where('slug',$id)->first();

    $get = $this->service->getDataLines($header->NumAtCard);

    $data = [
      'title' => 'Data Detail',
      'row' => $get,
      'total' => $header->DocTotal,
      'id' => $id,
      'NumAtCard' => $header->NumAtCard
    ];

    return view('sap.vdist.detail')->with($data);
  }

  public function push(Request $request)
  {
    $id = $request->id;

    $push = $this->service->pushVdist($id);

    echo json_encode($push);
  }

  public function unserved()
  {
    $role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    if ($role==1) 
    {
      $branch = WarehouseVdist::whereNotNull('vdist')->pluck('kota','vdist');
    }
    else
    {
      $branch = WarehouseVdist::where('Branch',$branchx)->whereNotNull('vdist')->pluck('kota','vdist');
    }
    
    // dd($branch);

    $data = [
      'title' => 'Unserved Data VDIST',
      'assets' => $assets,
      'branch' => $branch,
      'role' => $role
    ];

    return view('sap.vdist.unserved.index')->with($data);
  }

  public function unserved_search(Request $request)
  {
    $post = [
      'tgl_order' => $request->date,
      'branch' => $request->branch
    ];

    $row = $this->service->unserved($post);

    $data = [
      'row' => $row
    ]; 

    return view('sap.vdist.unserved.view')->with($data);
  }

  public function delete($id)
  {
    $this->service->delete($id);

    return redirect()->back();
  }

  public function import(Request $request)
  {
    $role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    if ($role==1) 
    {
      $branch = WarehouseVdist::whereNotNull('vdist')->pluck('kota','vdist');
    }
    else
    {
      $branch = WarehouseVdist::where('Branch',$branchx)->whereNotNull('vdist')->pluck('kota','vdist');
    }

    $data = [
      'title' => 'Import CSV',
      'branch' => $branch
    ];

    return view('sap.vdist.import')->with($data);
  }

  public function import_store(Request $request)
  {
    $date = $request->date; 
    $branch = $request->branch;
    $image = $request->file('file');

    $filename = $image->getClientOriginalName();
    $path = public_path('/storage/upload/import/');

    $image->move($path, $filename);

    $pathx = public_path('/storage/upload/import/'.$filename);

    $data = (new FastExcel)->import($pathx);

		// dd($data);

    $proses = $this->service->import($data, $branch, $date);

    // dd($proses);

		$alert = array(
			'type' => 'info',
			'message' => 'Data berhasil di import'
		);

		return redirect()->back()->with($alert);
  }
}
