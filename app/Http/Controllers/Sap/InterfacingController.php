<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\VdistHeader;
use App\Models\VdistLines;
use App\Models\Warehouse;
use App\Services\InterfacingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class InterfacingController extends Controller
{
  public function kino()
	{
		$service = new InterfacingServices;

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

		$get = $service->kino_data();

		$data = [
      'title' => 'Interfacing KINO',
      'assets' => $assets,
			'row' => $get,
    ];

		return view('sap.interfacing.kino')->with($data);
	}

	public function kino_import(Request $request) 
	{
		$role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

		if ($role==1) 
    {
      $branch = Warehouse::pluck('kota','id');
    }
    else
    {
      $branch = Warehouse::where('Branch',$branchx)->pluck('kota','id');
    }

		$data = [
      'title' => 'Import File',
      'branch' => $branch
    ];

		return view('sap.interfacing.kino_import')->with($data);
	}

	public function kino_upload(Request $request)
	{
		$service = new InterfacingServices;

		$branch = $request->branch;
    $image = $request->file('file');

    $filename = $image->getClientOriginalName();
    $path = public_path('/storage/upload/import/');

    $image->move($path, $filename);

    $pathx = public_path('/storage/upload/import/'.$filename);

    $data = (new FastExcel)->import($pathx);

		$proses = $service->kino_import($data, $branch);

		// dd($proses);

		$alert = array(
			'type' => 'info',
			'message' => 'Data berhasil di import'
		);

		return redirect()->back()->with($alert);
	}

	public function kino_detail(Request $request)
	{
		$id = $request->id;

    $header = DB::table('view_kino_header_active')->where('slug',$id)->first();

    $get = DB::table('view_kino_lines_active')
						 ->where('NumAtCard',$header->NumAtCard)
						 ->get();

		// dd($get);

    $data = [
      'title' => 'Data Detail',
      'row' => $get,
      'total' => $header->DocTotal,
      'id' => $id,
      'NumAtCard' => $header->NumAtCard
    ];

    return view('sap.interfacing.kino_detail')->with($data);
	}

	public function kino_push(Request $request)
  {
		$service = new InterfacingServices;

    $id = $request->id;

    $push = $service->pushKino($id);

    echo json_encode($push);
  }

	public function kino_delete($id)
	{
		$get = VdistHeader::where('slug',$id)->first();

    VdistHeader::where('NumAtCard',$get->NumAtCard)->delete();
    VdistLines::where('NumAtCard',$get->NumAtCard)->delete();

		return redirect()->back();
	}
}