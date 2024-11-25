<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHeader;
use App\Models\DeliveryVoucher;
use Illuminate\Http\Request;

class VoucherPairingController extends Controller
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
      'title' => 'Voucher Pairing',
      'assets' => $assets
    ];

    return view('sap.voucher_pairing.index')->with($data);
	}

	public function search(Request $request)
	{
		$cardCode = $request->cardCode;

		$rowSap = $this->generateCn($cardCode);
		$rowUsed = $this->generateCnLocal($cardCode);

		$alreadyItem = collect($rowUsed)->pluck('DocNum');
		$notAlreadyItem = collect($rowSap)->filter(static function (array $item) use ($alreadyItem){
			return !$alreadyItem->contains($item['DocNum']);
		});

		// dd($notAlreadyItem);

		$data = [
			'title' => 'Generate - Voucher Pairing',
			'row' => $notAlreadyItem
		];

		return view('sap.voucher_pairing.view')->with($data);
	}

	public function generate(Request $request)
	{
		$check = $request->check;

		// dd($check);

		$DocEntry = $request->DocEntry;
		$DocNum = $request->DocNum;
		$DocDate = $request->DocDate;
		$DocDueDate = $request->DocDueDate;
		$CardCode = $request->CardCode;
		$CardName = $request->CardName;
		$Comments = $request->Comments;
		$NumAtCard = $request->NumAtCard;
		$DocTotal = $request->DocTotal;
		$PaidToDate = $request->PaidToDate;
		$BalanceDue = $request->BalanceDue;
		$OcrCode2 = $request->OcrCode2;
		$DocNumDelivery = $request->DocNumDelivery;

		foreach ($check as $key => $value) 
		{
			$data[] = [
				'DocEntry' => $DocEntry[$key],
				'DocNum' => $DocNum[$key],
				'DocDate' => $DocDate[$key],
				'DocDueDate' => $DocDueDate[$key],
				'CardCode' => $CardCode[$key],
				'CardName' => $CardName[$key],
				'Comments' => $Comments[$key],
				'NumAtCard' => $NumAtCard[$key],
				'DocTotal' => $DocTotal[$key],
				'PaidToDate' => $PaidToDate[$key],
				'BalanceDue' => $BalanceDue[$key],
				'OcrCode2' => $OcrCode2[$key],
				'DocNumDelivery' => $DocNumDelivery,
			];
		}

		// dd($data);

		DeliveryVoucher::insert($data);

		$alert = array(
			'type' => 'info',
			'message' => 'Data berhasil di input'
		);

		return redirect()->back()->with($alert);
	}

	public function generateCn($cardCode)
  {
    $data = [];

    $post_customer = [
      'CardCode' => $cardCode
    ];

    $customer = getCustomerId(json_encode($post_customer));

    if ($customer['GroupCode']=='100') 
    {
      $api = 'generateCollectorCN4';

      $post = [
        'CardCode' => $cardCode
      ];

      $row = callSapApiLtaWithPost($api,json_encode($post));

      foreach ($row as $value) 
    	{
				$date = explode(' ',$value['DocDate']);
				$dueDate = explode(' ',$value['DocDueDate']);

				$data[] = [
					'DocEntry' => $value['DocEntry'],
					'DocNum' => $value['DocNum'],
					'DocDate' => $date[0],
					'DocDueDate' => $dueDate[0],
					'CardCode' => $value['CardCode'],
					'CardName' => $value['CardName'],
					'Comments' => $value['Comments'],
					'NumAtCard' => $value['NumAtCard'],
					'DocTotal' => $value['DocTotal'],
					'PaidToDate' => $value['PaidToDate'],
					'BalanceDue' => $value['DocTotal'] - $value['PaidToDate'],
					'OcrCode2' => $value['OcrCode2']
				];
			}
    }
    else
    {
      $data = [];
    }
    
    return $data;
  }

	public function generateCnLocal($cardCode)
  {
    $data = [];

		$row = DeliveryVoucher::where('CardCode',$cardCode)->get();

		foreach ($row as $value) 
		{
			$data[] = [
				'DocEntry' => $value->DocEntry,
				'DocNum' => $value->DocNum,
				'DocDate' => $value->DocDate,
				'DocDueDate' => $value->DocDueDate,
				'CardCode' => $value->CardCode,
				'CardName' => $value->CardName,
				'Comments' => $value->Comments,
				'NumAtCard' => $value->NumAtCard,
				'DocTotal' => $value->DocTotal,
				'PaidToDate' => $value->PaidToDate,
				'BalanceDue' => $value->BalanceDue,
				'OcrCode2' => $value->OcrCode2
			];
		}
    
    return $data;
  }
}
