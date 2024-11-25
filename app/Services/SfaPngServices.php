<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Item;
use App\Models\PngHeader;
use App\Models\PngLines;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class SfaPngServices
{
  public function getData()
  {  
    $role = auth()->user()->users_role_id;
    $branch = auth()->user()->branch_sap;

    $data = [];
    // $sales = array('4753','4782','1100');

    if ($role==1) 
    {
      $get = PngHeader::whereNull('DocNum')->OrderBy('id','DESC')->limit(200)->get();
    }
    else
    {
      $get = PngHeader::whereNull('DocNum')
                      ->where('Branch',$branch)
                      ->OrderBy('id','DESC')
                      ->limit(200)->get();
    }



    foreach ($get as $value) 
    {
			if ($value['BLITZ']==1) 
			{
				$blitz = ' <strong>[BLITZ]</strong>';
			}
			else if($value['BLITZ']==2)
			{
				$blitz = ' <strong>[AUTO SYNC]</strong>';
			}
			else
			{
				$blitz = '';
			}
      

      $data[] = [
        'CardCode' => $value['CardCode'].$blitz,
        'CardName' => isset($value['CardName']) ? $value['CardName'] : '-',
        'Address' => isset($value['Address']) ? $value['Address'] : '-',
        'DocDate' => $value['DocDate'],
        'SalesPersonCode' => isset($value['SalesPersonName']) ? $value['SalesPersonName'] : '-',
        'Branch' => $this->getBranch($value['Branch'])->title,
        'NumAtCard' => $value['NumAtCard'],
        'Total' => rupiah($value['DocTotal'])
      ];
    }

    return $data;
  }

  public function getDataLines($numAtCard)
  {
    $data= [];
    // $get = PngLines::where('NumAtCard',$numAtCard)->groupBy('ItemCode')->get();
		$get = DB::table('view_png_lines_active')
						 ->where('NumAtCard',$numAtCard)
						 ->groupBy('ItemCode')
						 ->get();

    foreach ($get as $value) 
    {
      $data[] = [
        'itemCode' => $value->ItemCode,
        'title' => isset($value->ItemName) ? $value->ItemName : '-',
        'quantity' => $value->Quantity,
        'quantitySfa' => $value->QuantitySfa,
        'price' => $value->UnitPrice,
        'total_price' => $value->Quantity * $value->UnitPrice,
        'unit' => $value->UnitMsr
      ];
    }

    return $data;
  }

	public function autoSyncPng($data)
	{
		$row = [];

		$cardCode = $data[0]['kode_retailer'];

		$post_cust = [
			'CardCode' => $cardCode
		];

		$customer = getCustomerId(json_encode($post_cust));

		$numAtCard = $data[0]['no_order'];

		$cek = $this->cekOrderDetail($numAtCard);

		if ($cek->count() == 0) 
		{
			if (isset($customer['U_CLASS'])) 
			{
				$nopol_mix = $customer['NopolMix'];
				$nopol_png = $customer['NopolPng'];
				$uclass = $customer['U_CLASS'];

				$warehouse = getWarehouseDetail($uclass, $data[0]['kode_sls_rep']);

				$post_slp = [
					'code' => $data[0]['kode_sls_rep']
				];

				$sales = getSalesDetail(json_encode($post_slp));

				$slpCode = $sales['SlpCode'];
				$slpName = $sales['SlpName'];

				// dd($sales);

				$BplId = getBranchDetail($data[0]['kode_branch'])->BPLid;

				$blitz = $data[0]['non_im'];

				$data_header = [
					'Branch' => $data[0]['kode_branch'],
					'SalesPersonCode' => $slpCode,
					'SalesPersonName' => $slpName,
					'CardCode' => $cardCode,
					'CardName' => $customer['CardName'],
					'Address' => $customer['Address'],
					'NumAtCard' => $data[0]['no_order'],
					'DocDate' => $data[0]['tgl_order'],
					'DocDueDate' => $data[0]['tgl_delivery'],
					'Comments' => 'Auto Sync - From SFA P&G to ERP',
					'U_NOPOLISI' => $nopol_mix,
					'U_NOPOLISI2' => $nopol_png,
					'BPLId' => $BplId,
					'BLITZ' => $blitz==1 ? $blitz : 2
				];

				$post_header = PngHeader::create($data_header);
				if ($post_header) 
				{
					foreach ($data as $value) 
					{
						$itemCode = $value['kode_child_sku'];

						$post_item = [
							'ItemCode' => $itemCode
						]; 

						$item = getItemId(json_encode($post_item));
						
						$post2 = [
							'ItemNo' => $value['kode_child_sku'],
							'CardCode' => $cardCode,
							'WhsCode' => $warehouse
						];

						$UomData = $this->getUomDetail(json_encode($post2));

						$jml_order = $value['jml_order'];
						$jml_order_cases = $value['jml_order_cases'];

						if (isset($UomData)) 
						{
							if ($jml_order > 0 && $jml_order_cases > 0) 
							{
								$UnitMsr = $UomData['satuan_kecil'];
								$NumPerMsr = 1;
								$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;
			
								$UnitMsr2 = $UomData['satuan_besar'];
								$NumPerMsr2 = $UomData['nisib'];
								$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;
			
								$Quantity1 = $jml_order;
								$Quantity2 = $jml_order_cases * $NumPerMsr2;
								$Quantity = $Quantity1 + $Quantity2;
								$QuantitySfaTotal = $Quantity;
							} 
							else if ($jml_order > 0) 
							{
								$UnitMsr = $UomData['satuan_kecil'];
								$NumPerMsr = 1;
								$Quantity = $jml_order;
								$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;
			
								$UnitMsr2 = $UomData['satuan_besar'];
								$NumPerMsr2 = $UomData['nisib'];
								$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;
								$QuantitySfaTotal = $Quantity;
							} 
							else 
							{
								$UnitMsr = $UomData['satuan_besar'];
								$NumPerMsr = $UomData['nisib'];
								$Quantity = $jml_order_cases;
								$UnitPrice = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;
			
								$UnitMsr2 = $UomData['satuan_kecil'];
								$NumPerMsr2 = 1;
								$UnitPrice2 = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;
								$QuantitySfaTotal = $Quantity * $NumPerMsr;
							}
			
							$CostingCode2 = $UomData['item_group'];
			
							$post3 = ['UomEntry' => $UnitMsr];
							$post4 = ['UomEntry' => $UnitMsr2];
			
							$post_available = [
								'ItemCode' => $itemCode,
								'WhsCode' => $warehouse
							];
			
							$available = getAvailable(json_encode($post_available));
							$availablex = isset($available['available']) ? $available['available'] : 0;

							if ($availablex > 0) 
							{
								$qty_real = $Quantity * $NumPerMsr;
			
								if ($availablex > $qty_real) 
								{
									$qty = $qty_real / $NumPerMsr;  //true
								} 
								else 
								{
									if ($UnitMsr == "KTN" || $UnitMsr == "CASE" || $UnitMsr == "LSN") 
									{
										$qty = $availablex;
										$UnitMsr = $UomData['satuan_kecil'];
										$NumPerMsr = 1;
										$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;
			
										$UnitMsr2 = $UomData['satuan_besar'];
										$NumPerMsr2 = $UomData['nisib'];
										$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;
			
										$post3 = ['UomEntry' => $UomData['satuan_kecil']];
										$post4 = ['UomEntry' => $UomData['satuan_besar']];
									} 
									else 
									{
										$qty = $availablex / $NumPerMsr;
									}
								}
							} 
							else 
							{
								$qty = 0;
							}

							$getUomEntry = getUomEntry(json_encode($post3));
							$UomEntry = isset($getUomEntry['uom_entry']) ? $getUomEntry['uom_entry'] : '';

							$getUomEntry2 = getUomEntry(json_encode($post4));
							$UomEntry2 = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : '';
						}
						else
						{
							$UnitMsr = '';
							$UomEntry = '';
							$UnitPrice = '';
							$NumPerMsr = '';
							$UnitMsr2 = '';
							$UomEntry2 = '';
							$UnitPrice2 = '';
							$NumPerMsr2 = '';
							$CostingCode2 = '';
							$qty = 0;
							$Quantity = 0;
							$QuantitySfaTotal = $Quantity;
						}

						$total = 0;
						$total += $qty * isset($UnitPrice) ? $UnitPrice : 0;

						$row[] = [
							'NumAtCard' => $value['no_order'],
							'ItemCode' => $itemCode,
							'ItemName' => $item['ItemName'],
							'Quantity' => $qty,
							'QuantitySfa' => $jml_order,
							'QuantitySfaCases' => $jml_order_cases,
							'QuantitySfaTotal' => $QuantitySfaTotal,
							'TaxCode' => "PPNO11",
							'UnitPrice' => isset($UnitPrice) ? $UnitPrice : 0,
							'UnitMsr' => $UnitMsr,
							'UomCode' => $UnitMsr,
							'UomEntry' => $UomEntry,
							'NumPerMsr' => $NumPerMsr,
							'UnitPrice2' => isset($UnitPrice2) ? $UnitPrice2 : 0,
							'UnitMsr2' => $UnitMsr2,
							'UomCode2' => $UnitMsr2,
							'UomEntry2' => $UomEntry2,
							'NumPerMsr2' => $NumPerMsr2,
							'CostingCode' => $uclass,
							'CostingCode2' => $CostingCode2,
							'CostingCode3' => 'SAL',
							'WarehouseCode' => $warehouse
						];
					}

					$post = PngLines::insert($row);

					if ($post) 
					{
						$update['DocTotal'] = $this->getTotal($numAtCard);
						PngHeader::where('NumAtCard', $numAtCard)->update($update);
					}
				}
			}

			$info = [
				'info' => 'success'
			];
		}
		else
		{
			$info = [
				'info' => 'already'
			];
		}

		return $info;
	}

  public function getSales($post)
  {
    return Sales::find($post);
  }

  public function getBranch($branch)
  {
    return Branch::find($branch);
  }

  public function getCustomer($post)
  {
    return Customer::where('code',$post)->first();
  }

  public function getItem($code)
  {
    return Item::where('code',$code)->first();
  }

  public function getOrderDetail($post)
  {
    $url = 'https://png.laut-timur.com/sfaerp/api/order_detail';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

	public function getOrderDetailBranch($post)
  {
    $url = 'https://png.laut-timur.com/sfaerp/api/order_detail_branch';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

  public function getOrderDetailLines($post)
  {
    $url = 'https://png.laut-timur.com/sfaerp/api/order_detail_lines';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

  public function getUomDetail($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getItemUomDetail';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

  public function getUomEntry($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getUomEntry';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

  public function getAvailable($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getAvailable';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
          // Set here requred headers
          "accept: */*",
          "accept-language: en-US,en;q=0.8",
          "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

  public function cekOrderDetail($id)
  {
    $cek = PngHeader::where('NumAtCard',$id)->get();
    return $cek;
  }

  public function getDataDetail($numAtCard,$date)
  {
    $get = PngHeader::where('NumAtCard',$numAtCard)->get();

    foreach ($get as $value) 
    {
      $top = "+1 days";
      if(isset($date))
      {
        $docDate = $date;
      }
      else
      {
        $docDate = date('Y-m-d');
      }
       
      $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

      $data = [
        'CardCode' => $value['CardCode'],
        'DocDueDate' => $docDueDate,
        'DocDate' => $docDate,
        'BPL_IDAssignedToInvoice' => $value['BPLId'],
        'SalesPersonCode'=> $value['SalesPersonCode'],
        'NumAtCard' => $value['NumAtCard'],
        'Comments' => $value['Comments'],
        'U_NOPOLISI' => $value['U_NOPOLISI'],
        'U_NOPOLISI2' => $value['U_NOPOLISI2'],
        'DocumentLines' => $this->getDocumentLines($numAtCard)
      ];
    }

    return $data;
  }

  public function getDocumentLines($id)
  {
    $data= [];
    $service = new ApiServices;

    // $get = PngLines::where('NumAtCard',$id)->where('Quantity','>',0)->groupBy('ItemCode')->get();
		$get = DB::table('view_png_lines_active')
						 ->where('NumAtCard',$id)
						 ->where('Quantity','>=',1)
						 ->groupBy('ItemCode')
						 ->get();

    // dd($get);

    foreach ($get as $value) 
    {
      $wareHouse = $value->WarehouseCode;

      $post_available = [
        'ItemCode' => $value->ItemCode,
        'WhsCode' => $wareHouse
      ];
      
      $available = $service->getAvailable(json_encode($post_available));
      $availablex = isset($available['available']) ? $available['available'] : 0;

      $Quantity = $value->Quantity;
      $NumPerMsr = $value->NumPerMsr;
      $UnitMsr = $value->UnitMsr;

      if ($availablex >= 1)  
      {
        $qty_real = $Quantity * $NumPerMsr;

        if ($availablex > $qty_real) 
        {
          $qty = $qty_real / $NumPerMsr;
          $UnitMsr = $value->UnitMsr;
          $NumPerMsr = $value->NumPerMsr;
          $UnitPrice = $value->UnitPrice;

					if ($value->UomEntry == "" || $value->UomEntry == NULL) 
					{
						$post4 = ['UomEntry' => $UnitMsr];
						$getUomEntry2 = getUomEntry(json_encode($post4));
						$UomEntry = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : NULL;
					}
					else
					{
						$UomEntry = $value->UomEntry;
					}
          
        }
        else 
        {
          if ($UnitMsr=="KTN" || $UnitMsr=="CASE" || $UnitMsr=="LSN") 
          {
            $qty = $availablex;
            $UnitMsr = $value->UnitMsr2;
            $NumPerMsr = 1;
            $UnitPrice = $value->UnitPrice2;
            // $UomEntry = $value->UomEntry2;

						if ($value->UomEntry2 == "" || $value->UomEntry2 == NULL) 
						{
							$post4 = ['UomEntry' => $UnitMsr];
							$getUomEntry2 = getUomEntry(json_encode($post4));
							$UomEntry = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : NULL;
						}
						else
						{
							$UomEntry = $value->UomEntry2;
						}
          }
          else
          {
            $qty = $availablex / $NumPerMsr;
            $UnitMsr = $value->UnitMsr;
            $NumPerMsr = $value->NumPerMsr;
            $UnitPrice = $value->UnitPrice;
            // $UomEntry = $value->UomEntry;

						if ($value->UomEntry == "" || $value->UomEntry == NULL) 
						{
							$post4 = ['UomEntry' => $UnitMsr];
							$getUomEntry2 = getUomEntry(json_encode($post4));
							$UomEntry = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : NULL;
						}
						else
						{
							$UomEntry = $value->UomEntry;
						}
          }
        }

        $data[] = [
          'ItemCode' => $value->ItemCode,
          'Quantity' => $qty,
          'TaxCode' => $value->TaxCode,
          'UnitPrice' => $UnitPrice,
          'CostingCode' => $value->CostingCode,
          'CostingCode2' => $value->CostingCode2,
          'CostingCode3' => $value->CostingCode3,
          'MeasureUnit' => $UnitMsr,
          'UoMCode' => $UnitMsr,
          'UoMEntry' => $UomEntry,
          'UnitsOfMeasurment' => $NumPerMsr,
          'WarehouseCode' => $wareHouse
        ];
      }
    }

		// dd($data);

    return $data;
  }

  public function postSales($header,$url,$post)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => $header,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // $data = [];

    if ($err) 
    {
      $data = [];
    } 
    else 
    {
      $data = json_decode($response,TRUE);
    }

    return $data;
  }

  public function getTotal($code)
  {
    // $get = PngLines::where('NumAtCard',$code)->groupBy('ItemCode')->get();

		$get = DB::table('view_png_lines_active')
						 ->where('NumAtCard',$code)
						 ->sum('TotalPrice');

    $total = isset($get) ? $get : 0;

    // foreach ($get as $value) 
    // {
    //   $linesTotal = $value->Quantity * $value->UnitPrice;

    //   $total += $linesTotal;
    // }

    return $total;
  }

  public function getDataFixed($branch)
  {
    $data= [];

    $get = PngHeader::where('Branch',$branch)
                    ->whereNull('DocNum')
                    ->whereNull('CardName')
                    ->orderBy('id','DESC')
                    ->limit(50)
                    ->get();
    
    foreach ($get as $key => $value) 
    {
      if(empty($value['CardName']) || empty($value['Address']) || empty($value['SalesPersonName']) || empty($value['DocTotal']))
      {
        $data[] = [
          'id' => $value['id'],
          'CardCode' => $value['CardCode'],
          'CardName' => $value['CardName'],
          'Address' => $value['Address'],
          'SalesPersonCode' => $value['SalesPersonCode'],
          'SalesPersonName' => $value['SalesPersonName'],
          'NumAtCard' => $value['NumAtCard'],
          'DocTotal' => $value['DocTotal']
        ];
      }
      else
      {
        $data = [];
      }
    }

    return $data;
  }
}