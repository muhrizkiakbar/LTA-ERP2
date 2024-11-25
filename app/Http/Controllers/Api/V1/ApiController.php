<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryVoucher;
use App\Models\Warehouse;
use App\Services\ApiServices;
use App\Services\SfaMixServices;
use App\Services\SfaPngServices;
use Illuminate\Http\Request;

class ApiController extends Controller
{
	public function generateInvoiceFP(Request $request)
  {
    $service = new ApiServices;

    $post = $request->all();

    $company = $post['CompanyId'];

    $body = [
      'DocDateFrom' => $post['DocDateFrom'],
      'DocDateTo' =>  $post['DocDateTo'],
      'Branch' =>  $post['Branch']
    ];
    
    $json = $service->generateInvoiceFP($company,json_encode($body));

    // dd($json);

    return response()->json([
      'success' => true,
      'data' => $json
    ]);
  }

  public function updateInvoiceFP(Request $request)
  {
    $service = new ApiServices;

    $post = $request->all();

    $company = $post['CompanyId'];
    
    $body = [
      'U_VATONO' => $post['NoSeriesPajak'],
      'U_VATODT' =>  $post['TanggalFakturPajak'],
      'U_VATADD' =>  $post['Address'],
      'DocNum' => $post['DocNum']
    ];

    // dd($post);
    
    $json = $service->updateInvoiceFP($company,json_encode($body));

    // dd($json);

    if ($json==1) 
    {
      return response()->json([
        'success' => true
      ]);
    }
    else
    {
      return response()->json([
        'success' => false
      ]);
    }
  }

	public function generatePAFile(Request $request)
  {
    $service = new ApiServices;

    $post = $request->all();

    $body = [
      'DocDate' => $post['DocDate']
    ];
    
    $json = $service->generatePAFile(json_encode($body));

    // dd($json);

    $date = date('dmY',strtotime($post['DocDate']));

    foreach ($json as $key => $value) 
    {
      $shipment = getShipmentCode($value['WhsCode']);
      $barcode = isset($value['Barcode']) ? $value['Barcode'] : '';
      $cost = isset($value['CostPerUnit']) ? round($value['CostPerUnit'],2) : 0;
      $onHand = isset($value['OnHandQty']) ? round($value['OnHandQty'],0) : 0;
      $onPurOrder = isset($value['OnPurOrder']) ? round($value['OnPurOrder'],0) : 0;
      $soldQty = isset($value['SoldQty']) ? round($value['SoldQty'],0) : 0;
      $commited = isset($value['CommitedQty']) ? round($value['CommitedQty'],0) : 0;

      $data[] = [
        'row' => $date.'|'.$shipment.'|'.$barcode.'|'.$value['ItemCode'].'|'.$cost.'|'.$onHand.'|'.$onPurOrder.'|'.$soldQty.'|A|'.$commited
      ];
    }

    return response()->json([
      'success' => true,
      'data' => $data
    ]);
  }

	public function generatePaNext(Request $request)
  {
    $service = new ApiServices;
    $post = $request->all();

    $body = [
      'DocDate' => $post['DocDate']
    ];
    
    $json = $service->generatePAFile(json_encode($body));

    return collect($json);
    

  }

	public function discountItem(Request $request)
  {
    $service = new ApiServices;
    $post = $request->all();

    $post_customer = [
      'CardCode' => $post['CardCode']
    ];
    $customer = getCustomerId(json_encode($post_customer));

    $post_item = [
      'ItemCode' => $post['ItemCode']
    ];
    $item = getItemId(json_encode($post_item));

    if (isset($customer) && isset($item)) 
    {
      $json = $service->getDiskonSfa($post['ItemCode'],$post['CardCode'],$post['Quantity']);

      if (count($json) > 0) 
      {
        return response()->json([
          'success' => true,
          'message' => 'Data found !',
          'data' => $json
        ]);
      }
      else
      {
        return response()->json([
          'success' => false,
          'message' => 'Data not found !',
          'data' => []
        ]);
      }
    }
    else
    {
      return response()->json([
        'success' => false,
        'message' => 'Store or Item code not found !',
        'data' => []
      ]);
    }
  }

  public function stokItem(Request $request)
  {
    $service = new ApiServices;
    $post = $request->all();

    $post_customer = [
      'CardCode' => $post['CardCode']
    ];
    $customer = getCustomerId(json_encode($post_customer));

    $itemCode = $post['ItemCode'];

    $warehouse = Warehouse::where('code',$customer['U_CLASS'])->first();

    $post_available = [
      'ItemCode' => $itemCode,
      'WhsCode' => $warehouse->title
    ];
    
    $json = $service->stokItem(json_encode($post_available));

    // $json = $service->getDiskonSfa($post['ItemCode'],$post['CardCode'],$post['Quantity']);

    if (count($json) > 0) 
    {
      return response()->json([
        'success' => true,
        'message' => 'Data found !',
        'stok' => $json['available']
      ]);
    }
    else
    {
      return response()->json([
        'success' => false,
        'message' => 'Data not found !',
        'stok' => 0
      ]);
    }
  }

  public function stokItemTaa(Request $request)
  {
    $service = new ApiServices;
    $post = $request->all();

		// dd($post);

    $json = $service->stokItemTaa($post['CardCode'], $post['ItemCode']);

    // $json = $service->getDiskonSfa($post['ItemCode'],$post['CardCode'],$post['Quantity']);

    if (count($json) > 0) 
    {
      return response()->json([
        'success' => true,
        'message' => 'Data found !',
        'stok' => $json['available']
      ]);
    }
    else
    {
      return response()->json([
        'success' => false,
        'message' => 'Data not found !',
        'stok' => 0
      ]);
    }
  }

	public function getVoucherDelivery(Request $request)
	{
		$DocNum = $request->input('DocNum');

		$sum = 0;
		$sum = DeliveryVoucher::where('DocNumDelivery',$DocNum)
													->sum('BalanceDue');

		$res = [
				'voucher' => $sum
		];
		
		return response()->json($res);
	}

	public function autoSyncPng(Request $request)
	{
		$service = new SfaPngServices;

		$body = $request->all();

		$data = $body['data'];

		$row = $service->autoSyncPng($data);

		if ($row['info']=='success') 
		{
			return response()->json([
        'success' => true,
        'message' => 'Order success !',
      ]);
		}
		else
		{
			return response()->json([
        'success' => false,
        'message' => 'Order already on ERP !'
      ]);
		}
	}

	public function autoSyncMix(Request $request)
	{
		$service = new SfaMixServices;

		$body = $request->all();

		$data = $body['data'];

		$row = $service->autoSyncMix($data);

		if ($row['info']=='success') 
		{
			return response()->json([
        'success' => true,
        'message' => 'Order success !',
      ]);
		}
		else
		{
			return response()->json([
        'success' => false,
        'message' => 'Order already on ERP !'
      ]);
		}
	}
}
