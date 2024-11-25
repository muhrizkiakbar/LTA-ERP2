<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\History;
use App\Models\Item;
use App\Models\MixHeader;
use App\Models\MixLines;
use App\Models\OrderHeader;
use App\Models\OrderLines;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class SfaMixServices
{
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

  public function getData()
  {  
    $role = auth()->user()->users_role_id;
    $branch = auth()->user()->branch_sap;

    $data = [];
    $sales = array('4753','4782','1100','4915','4909');

    if ($role==1) 
    {
      $get = MixHeader::whereNull('DocNum')
                      ->whereNotIn('SalesPersonCode',$sales)
                      ->OrderBy('id','DESC')
                      ->limit(300)->get();
    }
    else
    {
      $get = MixHeader::whereNull('DocNum')
											->where('Branch',$branch)
											->whereNotIn('SalesPersonCode',$sales)
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
        'Address' => isset($value['CardName']) ? $value['Address'] : '-',
        'DocDate' => $value['DocDate'],
        'SalesPersonCode' => $value['SalesPersonName'],
        'NumAtCard' => $value['NumAtCard'],
        'Total' => rupiah($value['DocTotal']),
        'Branch' => $this->getBranch($value['Branch'])->title,
      ]; 
    }

    return $data;
  }

  public function getDataDetail($numAtCard,$date)
  {
    $get = MixHeader::where('NumAtCard',$numAtCard)->get();

    foreach ($get as $value) 
    {
      if(isset($date))
      {
        $docDate = $date;
      }
      else
      {
        $docDate = date('Y-m-d');
      }
 
      $docDueDate= date('Y-m-d', strtotime("+1 days", strtotime($docDate)));

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
        'DocumentLines' => $this->getDocumentLines($numAtCard,$value['SalesPersonCode'])
      ];
    }

    return $data;
  }

  public function getDocumentLines($id,$sales)
  {
    $data= [];
    $service = new ApiServices;

    // $get = MixLines::where('NumAtCard',$id)->where('Quantity','>',0)->groupBy('ItemCode')->get();
		$get = DB::table('view_mix_lines_active')
						 ->where('NumAtCard',$id)
						 ->where('Quantity','>=',1)
						 ->get();

    // dd($get);

    foreach ($get as $value) 
    {
      if ($sales=='4801' || $sales=='4804' || $sales=='4808') 
      {
        $wareHouse = 'BTLWHFB';
      }
      else
      {
        $wareHouse = $value->WarehouseCode;
      }
      

      $post_available = [
        'ItemCode' => $value->ItemCode,
        'WhsCode' => $wareHouse
      ];
      
      $available = $service->getAvailable(json_encode($post_available));
      $availablex = $available['available'];

      $Quantity = $value->Quantity;
      $NumPerMsr = $value->NumPerMsr;
      $UnitMsr = $value->UnitMsr;

      // dd($availablex);

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

				if (isset($UomEntry) || $UomEntry != NULL) 
				{
					$UomEntryValid = $UomEntry;
				}
				else
				{
					$post4 = ['UomEntry' => $UnitMsr];
					$getUomEntry2 = getUomEntry(json_encode($post4));
					$UomEntryValid = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : NULL;
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
          'UoMEntry' => $UomEntryValid,
          'UnitsOfMeasurment' => $NumPerMsr,
          'WarehouseCode' => $wareHouse
        ];
      }
    }
    
    return $data;
  }

  public function getDataLines($numAtCard)
  {
    $data= [];
    // $get = MixLines::where('NumAtCard',$numAtCard)->groupBy('ItemCode')->get();
		$get = DB::table('view_mix_lines_active')
						 ->where('NumAtCard',$numAtCard)
						 ->get();

    foreach ($get as $value) 
    {
      if($value->Quantity >= 1)
      {
        $data[] = [
          'itemCode' => $value->ItemCode,
          'title' => isset($value->ItemName) ? $value->ItemName : '-',
          'quantity' => $value->Quantity,
          'price' => $value->UnitPrice,
          'total_price' => $value->TotalPrice,
          'unit' => $value->UnitMsr
        ];
      }
    }

    return $data;
  }

  public function getHistory()
  {
    $role = auth()->user()->users_role_id;
    $name = auth()->user()->title;

    $data = [];

    if ($role==1) 
    {
      $get = History::where('history_category_id',1)->OrderBy('id','DESC')->get();
    }
    else
    {
      $get = History::where('history_category_id',1)->where('title',$name)->OrderBy('id','DESC')->get();
    }

    foreach ($get as $value) 
    {
      $data[] = [
        'title' => $value['title'],
        'desc' => $value['desc'],
        'time' => $value['created_at']
      ];
    }

    return $data;
  }

	public function autoSyncMix($data)
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
					'Comments' => 'Auto Sync - From SFA MIX to ERP',
					'U_NOPOLISI' => $nopol_mix,
					'U_NOPOLISI2' => $nopol_png,
					'BPLId' => $BplId,
					'BLITZ' => $blitz==1 ? $blitz : 2
				];

				$post_header = MixHeader::create($data_header);
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

					$post = MixLines::insert($row);

					if ($post) 
					{
						$update['DocTotal'] = $this->getTotal($numAtCard);
						MixHeader::where('NumAtCard', $numAtCard)->update($update);
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

	public function cekOrderDetail($id)
  {
    $cek = MixHeader::where('NumAtCard',$id)->get();
    return $cek;
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

  public function getTotal($code)
  {
    // $get = MixLines::where('NumAtCard',$code)->groupBy('ItemCode')->get();

    // $total = 0;

    // foreach ($get as $value) 
    // {
    //   $linesTotal = $value->Quantity * $value->UnitPrice;

    //   $total += $linesTotal;
    // }

    // return $total;

		$get = DB::table('view_mix_lines_active')
						 ->where('NumAtCard',$code)
						 ->sum('TotalPrice');

    $total = isset($get) ? $get : 0;

		return $total;
  }

  public function getDataFixed($branch)
  {
    $data= [];

    $get = MixHeader::where('Branch',$branch)
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