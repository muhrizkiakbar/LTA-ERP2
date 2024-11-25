<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Sales;
use App\Models\Warehouse;
use App\Services\MasterServices;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class MasterController extends Controller
{
  public function item_kino()
	{
		$role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

		$service = new MasterServices;

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

		$get = $service->item_kino();

		if ($role==1) 
    {
      $branch = Warehouse::pluck('kota','id');
    }
    else
    {
      $branch = Warehouse::where('Branch',$branchx)->pluck('kota','id');
    }

		$data = [
      'title' => 'Item Master - KINO',
      'assets' => $assets,
      'row' => $get,
			'branch' => $branch
    ];

    return view('sap.master.item.kino')->with($data);
	}

	public function item_kino_sync(Request $request)
	{
		$services = new MasterServices;

		$branch = $request->branch;
		
		$sync = $services->item_kino_sync($branch);

		// dd($sync);

		return response()->json($sync);
	}

	public function item_kino_edit(Request $request)
	{
		$id = $request->id;

		$row = Item::find($id);

		$bool = [
			'Y' => 'Y',
			'N' => 'N'
		];

		$data = [
			'title' => 'Update Item Master - KINO',
			'row' => $row,
			'bool' => $bool
		];

		return view('sap.master.item.kino_edit')->with($data);
	}

	public function item_kino_update(Request $request, $id)
	{
		$data = $request->all();

		Item::find($id)->update($data);

		$alert = array(
			'type' => 'info',
			'message' => 'Data berhasil di update'
		);

		return redirect()->back()->with($alert);
	}

	public function item_kino_export(Request $request, $id)
	{
		$services = new MasterServices;
		
		$row = $services->kino_export();

		SimpleExcelWriter::streamDownload('Item_Master_Kino_'.time().'.xlsx')->addRows($row['data']);
	}

	public function sales_employee()
	{
		$service = new MasterServices;

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

		$get = $service->sales_employee();

		$data = [
      'title' => 'Sales Employee',
      'assets' => $assets,
      'row' => $get
    ];

    return view('sap.master.sales.index')->with($data);
	}

	public function sales_employee_sync(Request $request)
	{
		$services = new MasterServices;
		
		$sync = $services->sales_employee_sync();

		// dd($sync);

		return response()->json($sync);
	}

	public function sales_employee_edit(Request $request)
	{
		$id = $request->id;

		$row = Sales::find($id);

		$bool = [
			'Y' => 'Y',
			'N' => 'N'
		];

		$data = [
			'title' => 'Update Sales Employee',
			'row' => $row,
			'bool' => $bool
		];

		return view('sap.master.sales.edit')->with($data);
	}

	public function sales_employee_update(Request $request, $id)
	{
		$data = $request->all();

		Sales::find($id)->update($data);

		$alert = array(
			'type' => 'info',
			'message' => 'Data berhasil di update'
		);

		return redirect()->back()->with($alert);
	}
}
