<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\DeliveryVoucher;
use Illuminate\Http\Request;

class VoucherReleaseController extends Controller
{
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
      'title' => 'Voucher Release',
      'assets' => $assets
    ];

    return view('sap.voucher_release.index')->with($data);
  }

	public function search(Request $request)
	{
		$docnum = $request->docnum;

		$cek = DeliveryVoucher::where('DocNum',$docnum)->get();

		if (isset($cek)) 
		{
			$response = [
				'message' => 'sukses',
				'docnum' => $docnum
			];
		}
		else
		{
			$response = [
				'message' => 'error'
			];
		}

		return response()->json($response);
	}

	public function view(Request $request)
	{
		$row = [];

		$docnum = $request->docnum;

		$cek = DeliveryVoucher::where('DocNum',$docnum)->get();

		foreach ($cek as $value) 
		{
			$status = isset($value->DocNumDelivery) ? '' : '<span class="badge badge-succes">Available</span>';

			$row[] = [
				'id' => $value->id,
				'DocNum' => $value->DocNum,
				'DocDate' => $value->DocDate,
				'DocDueDate' => $value->DocDueDate,
				'CardCode' => $value->CardCode,
				'CardName' => $value->CardName,
				'NumAtCard' => $value->NumAtCard,
				'Comments' => $value->Comments,
				'BalanceDue' => $value->BalanceDue,
				'DocNumDelivery' => $value->DocNumDelivery,
				'status' => $status
			];
		}

		$data = [
			'row' => $row
		];

		return view('sap.voucher_release.view')->with($data);
	}

	public function delete($id)
	{
		DeliveryVoucher::find($id)->delete();

		return redirect()->back();
	}
}
