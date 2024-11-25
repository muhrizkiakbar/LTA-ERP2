<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\BranchSap;
use App\Models\CardCode;
use App\Models\ClosingDate;
use App\Models\Customer;
use App\Models\Item;
use App\Models\OrderFailed;
use App\Models\OrderHeader;
use App\Models\OrderLines;
use App\Models\OrderLinesBatch;
use App\Models\OrderTemp;
use App\Models\Sales;
use App\Models\SalesCode;
use Ixudra\Curl\Facades\Curl;

class SalesServices
{
  public function getData()
  {
    $role = auth()->user()->users_role_id;
    $branch = auth()->user()->branch_sap;

    $data = [];

    if ($role==1) 
    {
      $get = OrderHeader::whereNotNull('DocNum')->orderBy('DocDate','DESC')->get();
    }
    else
    {
      $get = OrderHeader::whereNotNull('DocNum')->where('Branch',$branch)->orderBy('DocDate','DESC')->get();
    }

    foreach ($get as $value) 
    {
      $sales = $this->getSales($value['SalesPersonCode']);
      $cust = $this->getCustomer($value['CardCode']);

      $data[] = [
        'DocNum' => $value['DocNum'],
        'CardName' => isset($cust->title) ? $cust->title : '-',
        'Address' => isset($cust->address) ? $cust->address : '-',
        'DocDate' => $value['DocDate'],
        'SalesPersonCode' => isset($sales) ? $sales->title : '',
        'Branch' => $this->getBranch($value['Branch'])->title,
        'NumAtCard' => $value['NumAtCard']
      ];
    }

    return $data;
  }

  public function getDataDetail($docnum)
  {
    $get = OrderHeader::where('DocNum',$docnum)->first();

    if(empty($get))
    {
      $data = [];
    }
    else
    {
      $data = [
        'DocNum' => $get['DocNum'],
        'DocEntry' => $get['DocEntry'],
        'CardCode' => $get['CardCode'],
        'DocDueDate' => $get['DocDueDate'],
        'DocDate' => $get['DocDate'],
        'Branch' => $get['Branch'],
        'SalesPersonCode'=> $get['SalesPersonCode'],
        'NumAtCard' => $get['NumAtCard'],
        'Comments' => $get['Comments'],
        'U_NOPOLISI' => $get['U_NOPOLISI'],
        'U_NOPOLISI2' => $get['U_NOPOLISI2'],
        'BPLId' => $get['BPLId'],
        'DocTotal' => $get['DocTotal'],
        'VatSum' => $get['VatSum'],
        'TotalSum' => $get['DocTotal'] + $get['VatSum'],
        'DocStatus' => $get['DocStatus']
      ];
    }

    return $data;
  }

  public function getDataDetail2($docnum)
  {
    $get = OrderHeader::where('DocNum',$docnum)->first();

    if(empty($get))
    {
      $data = [];
    }
    else
    {
      $data = [
        'DocNum' => $get['DocNum'],
        'DocEntry' => $get['DocEntry'],
        'CardCode' => $get['CardCode'],
        'DocDueDate' => $get['DocDueDate'],
        'DocDate' => $get['DocDate'],
        'Branch' => $get['Branch'],
        'SalesPersonCode'=> $get['SalesPersonCode'],
        'NumAtCard' => $get['NumAtCard'],
        'Comments' => $get['Comments'],
        'U_NOPOLISI' => $get['U_NOPOLISI'],
        'U_NOPOLISI2' => $get['U_NOPOLISI2'],
        'BPLId' => $get['BPLId'],
        'DocTotal' => $get['DocTotal'],
        'VatSum' => $get['VatSum'],
        'TotalSum' => $get['DocTotal'] + $get['VatSum'],
        'DocStatus' => $get['DocStatus']
      ];
    }

    return $data;
  }

  public function getDataLines($id)
  {
    $data = [];
    $get = OrderLines::where('DocEntry',$id)->get();
    
    foreach ($get as $value) 
    {
      $total = 0;
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

      $itemName = isset($item['ItemName']) ? $item['ItemName'] : '-';

      $total = $value['Quantity'] * $value['UnitPrice'];

      $disc1 = $value['U_DISC1'];
      $disc2 = $value['U_DISC2'];
      $disc3 = $value['U_DISC3'];
      $disc4 = $value['U_DISC4'];
      $disc5 = $value['U_DISC5'];
      $disc6 = $value['U_DISC6'];
      $disc7 = $value['U_DISC7'];
      $disc8 = $value['U_DISC8'];

      $discx1 = $value['U_DISCVALUE1'];
      $discx2 = $value['U_DISCVALUE2'];
      $discx3 = $value['U_DISCVALUE3'];
      $discx4 = $value['U_DISCVALUE4'];
      $discx5 = $value['U_DISCVALUE5'];
      $discx6 = $value['U_DISCVALUE6'];
      $discx7 = $value['U_DISCVALUE7'];
      $discx8 = $value['U_DISCVALUE8'];

      $disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
      $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $totalx = $total - $disc_calx;

      // dd($cek);

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => isset($value['ItemDescription']) ? $value['ItemDescription'] : $itemName,
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $value['UnitPrice'],
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'disc1' => $disc1,
        'disc2' => $disc2,
        'disc3' => $disc3,
        'disc4' => $disc4,
        'disc5' => $disc5,
        'disc6' => $disc6,
        'disc7' => $disc7,
        'disc8' => $disc8,
        'disc_total' => $disc_cal,
        // 'discountParameter' => $cek
      ];
    }

    // dd($data);

    return $data;
  }

  public function getDataLinesDisc($id)
  {
		$users_id = auth()->user()->id;

    $data = [];
    $get = OrderLines::where('DocEntry',$id)->get();

    $header = OrderHeader::where('DocEntry',$id)->first();

    $post_customer = [
      'CardCode' => $header['CardCode']
    ];
    $customer = getCustomerId2(json_encode($post_customer));

    // dd($customer);
    
    foreach ($get as $value) 
    {
      $total = 0;
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

			$post_uom = [
        'ItemCode' => $value['ItemCode'],
				'CardCode' => $header['CardCode'],
				'WhsCode' => $value['WarehouseCode']
      ];

      $uom = getUomDetail(json_encode($post_uom));

      // dd($item);

      $total = $value['Quantity'] * $value['UnitPrice'];

			$qtyx = $value['Quantity'] * $value['NumPerMsr'];

      if($value['CostingCode2']=="P&G")
      {
				$full_cs = $item['NISIB'];
        $half_cs = $item['NISIB'] * 0.5;
        $init2 = $item['INIT2'];
				if($value['UomCode']=='KTN')
				{
					$unitPriceX = $uom['harga_jual_pcs'];
				}
				else
				{
					$unitPriceX = $value['UnitPrice'];
				}
       

        // $qtyx = $value['Quantity'] * $value['NumPerMsr'];

				$post_total_day = [
					'DocDate' => $header['DocDate'],
					'CardCode' => $header['CardCode'],
					'ItemCode' => $value['ItemCode']
				];

				$qtyTotalDay = $this->getQtyTotalDay(json_encode($post_total_day));

				$countQtyTotal = isset($qtyTotalDay) ? count($qtyTotalDay) : 0;

				if ($countQtyTotal > 0) 
				{
					$qtyTotal = $qtyTotalDay['Total'];
					$qtyTotalDayx = $qtyTotal + $qtyx;
				}
				else
				{
					$qtyTotalDayx = $qtyx;
				}

        $cek = [
          'ItemCode' => $value['ItemCode'],
          'CardCode' => $header['CardCode'],
          'Date' => $header['DocDate'],
          'Qty' => $qtyx,
          'DocEntry' => $value['DocEntry'],
          'SUBSEGMENT' => $customer['cseg4'],
          'INITIATIVE1' => $item['INIT1'],
          'INITIATIVE2' => $item['INIT2'],
          'INITIATIVE3' => $item['INIT3'],
          'INITIATIVE4' => $item['INIT4'],
          'INITIATIVE5' => $item['INIT5'],
          'INITIATIVE6' => $item['INIT6'],
          'INITIATIVE7' => $item['INIT7']
        ];
      
        $diskon = $this->getDiskonPng(json_encode($cek));

        $subsegment = $customer['cseg4'];

        // $disc1 = $customer['stat_disc1']=="Y" ? $value['U_DISC1'] == 0 ? isset($diskon) ? $diskon['disc1'] : $value['U_DISC1'] : 0 : 0;
        // $disc2 = $customer['stat_disc2']=="Y" ? $value['U_DISC2'] == 0 ? isset($diskon) ? $diskon['disc2'] : $value['U_DISC2'] : 0 : 0;
        // $disc3 = $customer['stat_disc3']=="Y" ? $value['U_DISC3'] == 0 ? isset($diskon) ? $diskon['disc3'] : $value['U_DISC3'] : 0 : 0;
        // $disc4 = $customer['stat_disc4']=="Y" ? $value['U_DISC4'] == 0 ? isset($diskon) ? $diskon['disc4'] : $value['U_DISC4'] : 0 : 0;
        // $disc5 = $customer['stat_disc5']=="Y" ? $value['U_DISC5'] == 0 ? isset($diskon) ? $diskon['disc5'] : $value['U_DISC5'] : 0 : 0;
        // $disc6 = $customer['stat_disc6']=="Y" ? $value['U_DISC6'] == 0 ? isset($diskon) ? $diskon['disc6'] : $value['U_DISC6'] : 0 : 0;
        // $disc7 = $customer['stat_disc7']=="Y" ? $value['U_DISC7'] == 0 ? isset($diskon) ? $diskon['disc7'] : $value['U_DISC7'] : 0 : 0;
        // $disc8 = $customer['stat_disc8']=="Y" ? $value['U_DISC8'] == 0 ? isset($diskon) ? $diskon['disc8'] : $value['U_DISC8'] : 0 : 0;

				// New Skema Discount
				$disc1 = $customer['stat_disc1']=="Y" ? isset($diskon) ? $diskon['disc1'] : 0 : 0;
				
				if ($value['U_DISC2'] != 0) 
				{
					$disc2 = $value['U_DISC2'];
				}
				else
				{
					$disc2 = $customer['stat_disc2']=="Y" ? isset($diskon) ? $diskon['disc2'] : 0 : 0;
				}
       
        $disc3 = $customer['stat_disc3']=="Y" ? isset($diskon) ? $diskon['disc3'] : 0 : 0;
        $disc4 = $customer['stat_disc4']=="Y" ? isset($diskon) ? $diskon['disc4'] : 0 : 0;
        $disc5 = $customer['stat_disc5']=="Y" ? isset($diskon) ? $diskon['disc5'] : 0 : 0;
        $disc6 = $customer['stat_disc6']=="Y" ? isset($diskon) ? $diskon['disc6'] : 0 : 0;
        $disc7 = $customer['stat_disc7']=="Y" ? isset($diskon) ? $diskon['disc7'] : 0 : 0;

				if ($value['U_DISC8'] != 0) 
				{
					$disc8 = $value['U_DISC8'];
				}
				else
				{
					$disc8 = $customer['stat_disc8']=="Y" ? isset($diskon) ? $diskon['disc8'] : 0 : 0;
				}

        // $disc8 = $customer['stat_disc8']=="Y" ? isset($diskon) ? $diskon['disc8'] : 0 : 0;

        $discx1 = ($disc1 / 100) * $total;
        $discx2 = ($disc2 / 100) * ($total - $discx1);
        $discx3 = ($disc3 / 100) * ($total - $discx1 - $discx2);
        $discx4 = ($disc4 / 100) * ($total - $discx1 - $discx2 - $discx3);

				$qty_eko = 0;

        if ($init2=='HCLS1')
        {
					if($subsegment=='HFS SMALL A' || $subsegment=='HFS MEDIUM' || $subsegment=='HFS SMALL B')
					{
						if (count($qtyTotalDay) == 0) 
						{
							if ($qtyx > $half_cs) 
							{
								$half_rp = $half_cs * $unitPriceX;
								$half_diskon = ($half_rp * $disc5) / 100;
								$totalx = $total - $half_diskon;

								$qty_eko = $half_cs;

								$discx5 = ($totalx - $discx1 - $discx2 - $discx3 - $discx4);
							}
							else
							{
								$qty_eko = $qtyx;
								$discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
							}
						}
						else
						{
							if ($qtyTotalDayx >= $half_cs) 
							{
								$qty_eko = 0;
								$disc5 = 0;
								$discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
							}
							else
							{
								if ($qtyx > $half_cs) 
								{
									$half_rp = $half_cs * $unitPriceX;
									$half_diskon = ($half_rp * $disc5) / 100;
									$totalx = $total - $half_diskon;

									$qty_eko = $half_cs;

									$discx5 = ($totalx - $discx1 - $discx2 - $discx3 - $discx4);
								}
								else
								{
									$qty_eko = $qtyx;
									$discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
								}
							}
						}	
					}
					else
					{
						if ($subsegment=='HFS LARGE') 
						{
							// Menentukan Discount Value
							if (count($qtyTotalDay) == 0) 
							{
								if ($qtyx >= 3) 
								{
									$disc5_val = $disc5;
								}
								else
								{
									$disc5_val = 0;
								}
							}
							else
							{
								if ($qtyTotalDayx >= $full_cs) 
								{
									$disc5_val = 0;
								}
								else
								{
									if ($qtyx >= 3) 
									{
										$disc5_val = $disc5;
									}
									else
									{
										$disc5_val = 0;
									}
								}
							}

							// Menentukan Discount Value Rupiah
							
							// if (count($qtyTotalDay) > 0) 
							// {
							// 	if ($qtyTotalDayx > $full_cs) 
							// 	{
							// 		$qty_eko = 0;
							// 		$discx5 = ($disc5_val / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
							// 	}
							// }
							// else
							// {
							// 	if ($qtyx > $full_cs) 
							// 	{
							// 		$full_rp = $full_cs * $unitPriceX;
							// 		$totalx = $full_rp;

							// 		$qty_eko = $full_cs;
							// 	}
							// 	else
							// 	{
							// 		$totalx = $total;
							// 		$qty_eko = $qtyx;
							// 	}

							// 	$discx5 = ($disc5_val / 100) * ($totalx - $discx1 - $discx2 - $discx3 - $discx4);
							// }	

							// if (count($qtyTotalDay) == 0) 
							// {
							// 	if ($qtyx > $full_cs) 
							// 	{
							// 		$full_rp = $full_cs * $unitPriceX;
							// 		$full_diskon = ($full_rp * $disc5_val) / 100;

							// 		$qty_eko = $full_cs;

							// 		$discx5 = $full_diskon - $discx1 - $discx2 - $discx3 - $discx4;
							// 	}
							// 	else
							// 	{
							// 		$qty_eko = $qtyx;
							// 		$discx5 = ($disc5_val / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
							// 	}
							// }
							// else
							// {
							// 	if ($qtyTotalDayx > $full_cs) 
							// 	{
							// 		$qty_eko = 0;
							// 		$discx5 = ($disc5_val / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
							// 	}
							// }
							
							// if (count($qtyTotalDay) == 0) 
							// {
							// 	if ($qtyx >= 3) 
							// 	{
							// 		$disc5_val = $disc5;
							// 	}
							// 	else
							// 	{
							// 		$disc5_val = 0;
							// 	}
							// }
							// else
							// {
							// 	if ($qtyTotalDayx >= $full_cs) 
							// 	{
							// 		$disc5_val = 0;
							// 	}
							// 	else
							// 	{
							// 		if ($qtyx >= 3) 
							// 		{
							// 			$disc5_val = $disc5;
							// 		}
							// 		else
							// 		{
							// 			$disc5_val = 0;
							// 		}
							// 	}
							// }

							
							if (count($qtyTotalDay) == 0) 
							{
								if ($qtyx > $full_cs) 
								{
									$full_rp = $full_cs * $unitPriceX;
									$full_diskon = ($full_rp * $disc5_val) / 100;

									$qty_eko = $full_cs;

									$discx5 = $full_diskon - $discx1 - $discx2 - $discx3 - $discx4;
								}
								else
								{
									$qty_eko = $qtyx;
									$discx5 = ($disc5_val / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
								}
							}
							else
							{
								if ($qtyTotalDayx >= $full_cs) 
								{
									$qty_eko = 0;
									$discx5 = ($disc5_val / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
								}
								else
								{
									if ($qtyx > $full_cs) 
									{
										$full_rp = $full_cs * $unitPriceX;
										$full_diskon = ($full_rp * $disc5_val) / 100;

										$qty_eko = $full_cs;

										$discx5 = $full_diskon - $discx1 - $discx2 - $discx3 - $discx4;
									}
									else
									{
										$qty_eko = $qtyx;
										$discx5 = ($disc5_val / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
									}
								}
							}
						}
						else
						{
							$discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
						}
					}
        }
        else
        {
          $discx5 = ($disc5 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4);
        }
        
        $discx6 = ($disc6 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);
        $discx7 = ($disc7 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);
        $discx8 = ($disc8 / 100) * ($total - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

        $disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
        $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

        $totalx = $total - $disc_calx;
      }
      else
      {
				$qtyTotalDayx = 0;
				$qty_eko = 0;
				
        $disc1 = $value['U_DISC1'];
        $disc2 = $value['U_DISC2'];
        $disc3 = $value['U_DISC3'];
        $disc4 = $value['U_DISC4'];
        $disc5 = $value['U_DISC5'];
        $disc6 = $value['U_DISC6'];
        $disc7 = $value['U_DISC7'];
        $disc8 = $value['U_DISC8'];

        $discx1 = $value['U_DISCVALUE1'];
        $discx2 = $value['U_DISCVALUE2'];
        $discx3 = $value['U_DISCVALUE3'];
        $discx4 = $value['U_DISCVALUE4'];
        $discx5 = $value['U_DISCVALUE5'];
        $discx6 = $value['U_DISCVALUE6'];
        $discx7 = $value['U_DISCVALUE7'];
        $discx8 = $value['U_DISCVALUE8'];

        $disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
        $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

        $totalx = $total - $disc_calx;
      }

      // dd($cek);

			if ($value['CostingCode2']=="P&G") 
			{
				if ($subsegment=='HFS LARGE') 
				{
					// if ($qtyx >= 3) 
					// {
					// 	$disc5_val = $disc5;
					// }
					// else
					// {
					// 	$disc5_val = 0;
					// }

					if (count($qtyTotalDay) == 0) 
					{
						if ($qtyx >= 3) 
						{
							$disc5_val = $disc5;
						}
						else
						{
							$disc5_val = 0;
						}
					}
					else
					{
						if ($qtyTotalDayx >= $full_cs) 
						{
							$disc5_val = 0;
						}
						else
						{
							if ($qtyx >= 3) 
							{
								$disc5_val = $disc5;
							}
							else
							{
								$disc5_val = 0;
							}
						}
					}
				}
				else
				{
					$disc5_val = $disc5;
				}
			}
			else
			{
				$disc5_val = $disc5;
			}

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => $item['ItemName'],
				'nisib' => $item['NISIB'],
        'qty' => $qtyx,
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $value['UnitPrice'],
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'disc1' => $disc1,
        'disc2' => $disc2,
        'disc3' => $disc3,
        'disc4' => $disc4,
        'disc5' => $disc5_val,
        'disc6' => $disc6,
        'disc7' => $disc7,
        'disc8' => $disc8,
				'disc1_rp' => $discx1,
        'disc2_rp' => $discx2,
        'disc3_rp' => $discx3,
        'disc4_rp' => $discx4,
        'disc5_rp' => $discx5,
        'disc6_rp' => $discx6,
        'disc7_rp' => $discx7,
        'disc8_rp' => $discx8,
        'disc_total' => $disc_cal,
				'qty_eko' => $qty_eko,
				'qty_total' => $qtyTotalDayx,
        // 'discountParameter' => $cek
      ];
    }

    // if($users_id==1)
		// {
		// 	dd($data);
		// }

    return $data;
  }


  public function getTempLines($id)
  {
    $get = OrderTemp::where('users_id',$id)->get();
    $data = [];
    
    foreach ($get as $value) 
    {
      $total = 0;
      $totalx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

      // $item = $this->getItem($value['ItemCode']);

      $total = $value['Quantity'] * $value['UnitPrice'];

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => $item['ItemName'],
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $value['UnitPrice'],
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'docTotal' => $total
      ];
    }

    // dd($data);

    return $data;
  }

  public function getSeriesName()
  {
    $branch = auth()->user()->branch_sap;
    $get = Branch::where('title',$branch)->first();
    return $get->snso;
  }

  public function getDocNum()
  {
    $series = $this->getSeriesName();

    $body = [
      'SeriesName' => $series
    ];

    $post = json_encode($body);

    $url = 'https://saplta.laut-timur.tech/api/sales/docNum';
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
      $get = json_decode($response,TRUE);
      foreach ($get['data'] as $key => $value) 
      {
        $data = $value['DocNum'];
      }
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

  public function jsonUpdate($id,$sales,$date)
  {

    $top = "+1 days";
    $docDueDate= date('Y-m-d', strtotime($top, strtotime($date)));
    $get = OrderHeader::where('DocEntry',$id)->first();

    $data_sales = [
      'SalesPersonCode' => $sales,
      'DocDate' => $date,
      'DocDueDate' => $docDueDate
    ];

    OrderHeader::find($get->id)->update($data_sales);
    // dd($data_lines);

    $data = [
      'DocDate' => $date,
      'DocDueDate' => $docDueDate,
      'SalesPersonCode' => $sales,
      'Comments' => 'From SFA Sync',
      'DocumentLines' => $this->jsonLines($id)
    ];

    return $data;
  }

  public function jsonLines($id)
  {
    $data = [];
    $get = OrderLines::where('DocEntry',$id)->get();

    foreach ($get as $value) 
    {
      $total = $value['Quantity'] * $value['UnitPrice'];

      $data[] = [
        'DocEntry' => $value['DocEntry'],
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $value['Quantity'],
        'Price' => $value['UnitPrice'],
        'UnitPrice' => $value['UnitPrice'],
        'CostingCode' => $value['CostingCode'],
        'CostingCode2' => $value['CostingCode2'],
        'CostingCode3' => $value['CostingCode3'],
        'LineTotal' => round($total,2),
        'WarehouseCode' => $value['WarehouseCode'],
        'MeasureUnit' => $value['UnitMsr'],
        'UoMCode' => $value['UomCode'],
        'UoMEntry' => $value['UomEntry'],
        'UnitsOfMeasurment' => $value['NumPerMsr'],
        'TaxCode' => $value['TaxCode'],
				'BatchNumbers' => $this->linesBatchNumber($value['id'])
      ];
    }

    return $data;
  }

  public function updateSales($header,$url,$post)
  {
    // $response = Curl::to($url)->withHeaders($header)
    //                           ->withData($post)
    //                           ->returnResponseObject()
    //                           ->patch();
    // return $response;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "PATCH",
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

  public function jsonClose($docentry)
  {
    $data = [
      'DocumentParams' => array('DocEntry' => $docentry)
    ];

    return $data;
  }

  public function closeSales($header,$url,$post)
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

  public function jsonDelivery($numAtCard,$date)
  {
    $get = OrderHeader::where('DocEntry',$numAtCard)->get();
    $date_closing = ClosingDate::where('status',1)->get();

    foreach ($get as $value) 
    {
      $post = [
        'CardCode' => $value['CardCode']
      ];

      $getTop = $this->getTopCustomer(json_encode($post));
      $top = "+".$getTop." days";
      if(isset($date))
      {
        if (empty($date_closing)) 
        {
          $docDate = date('Y-m-d');
        }
        else
        {
          $docDate = $date;
        }
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
        'DocumentLines' => $this->jsonDeliveryLines($numAtCard,$value['DocEntry'])
      ];
    }

    return $data;
  }

  public function jsonDeliveryLines($numAtCard,$docentry)
  {
    $get = OrderLines::where('DocEntry',$numAtCard)->get();

    $no = 0;
    foreach ($get as $value) 
    {
      $total = $value['Quantity'] * $value['UnitPrice'];

      $disc1 = $value['U_DISC1'];
      $disc2 = $value['U_DISC2'];
      $disc3 = $value['U_DISC3'];
      $disc4 = $value['U_DISC4'];
      $disc5 = $value['U_DISC5'];
      $disc6 = $value['U_DISC6'];
      $disc7 = $value['U_DISC7'];
      $disc8 = $value['U_DISC8'];

      $discx1 = $value['U_DISCVALUE1'];
      $discx2 = $value['U_DISCVALUE2'];
      $discx3 = $value['U_DISCVALUE3'];
      $discx4 = $value['U_DISCVALUE4'];
      $discx5 = $value['U_DISCVALUE5'];
      $discx6 = $value['U_DISCVALUE6'];
      $discx7 = $value['U_DISCVALUE7'];
      $discx8 = $value['U_DISCVALUE8'];

      $disc_cal = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
      $disc_calx = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $totalx = $total - round($disc_calx,2);

			$lines_batch = $this->linesBatchNumber($value['id']);

      $data[] = [
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $value['Quantity'],
        'TaxCode' => $value['TaxCode'],
        'UnitPrice' => $value['UnitPrice'],
        'Price' => $value['UnitPrice'],
        'CostingCode' => $value['CostingCode'],
        'CostingCode2' => $value['CostingCode2'],
        'CostingCode3' => $value['CostingCode3'],
        'MeasureUnit' => $value['UnitMsr'],
        'UoMCode' => $value['UnitMsr'],
        'UoMEntry' => $value['UomEntry'],
        'UnitsOfMeasurment' => $value['NumPerMsr'],
        'LineTotal' => round($totalx,2),
        'DiscountPercent' => round($disc_cal,2),
        'U_DISC1' => isset($disc1) ? $disc1 : "0",
        'U_DISCVALUE1' => $discx1,
        'U_DISC2' => isset($disc2) ? $disc2 : "0",
        'U_DISCVALUE2' => $discx2,
        'U_DISC3' => isset($disc3) ? $disc3 : "0",
        'U_DISCVALUE3' => $discx3,
        'U_DISC4' => isset($disc4) ? $disc4 : "0",
        'U_DISCVALUE4' => $discx4,
        'U_DISC5' => isset($disc5) ? $disc5 : "0",
        'U_DISCVALUE5' => $discx5,
        'U_DISC6' => isset($disc6) ? $disc6 : "0",
        'U_DISCVALUE6' => $discx6,
        'U_DISC7' => isset($disc7) ? $disc7 : "0",
        'U_DISCVALUE7' => $discx7,
        'U_DISC8' => isset($disc8) ? $disc8 : "0",
        'U_DISCVALUE8' => $discx8,
        'BaseEntry' => $docentry,
        'BaseType' => 17,
        'BaseLine' => $value['LineNum'],
				'U_NNILAI' => $value['U_EKO'],
				'BatchNumbers' => isset($lines_batch) ? $lines_batch : []
      ];

      $no++;
    }

    return $data;
  }

	public function linesBatchNumber($id)
	{
		$cek = OrderLinesBatch::where('order_lines_id',$id)->get();
		
		if (count($cek) > 0) 
		{
			foreach ($cek as $value) 
			{
				$data[] = [
					'BatchNumber' => $value->BatchNumber,
					'Quantity' => $value->Quantity
				];
			}
		}
		else
		{
			$data = [];
		}

		return $data;
	}

  public function pushDelivery($header,$url,$post)
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

  public function getDiskonPng($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getDiskonPng';
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

	public function getQtyTotalDay($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getQtyTotalDay';
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

  public function getSalesOrder($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getOrdr';
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

  public function getSalesOrderDetail($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/selectOrdr';
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

  public function getTopCustomer($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getTopCustomer';
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

  public function checkDocument($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/sales/checkDocument';
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

  public function order_failed($id, $error)
  {
    $header = OrderHeader::where('DocEntry',$id)->first();
    $lines = OrderLines::where('DocEntry',$id)->first();

    $data = [
      'DocNum' => $header->DocNum,
      'Branch' => $header->Branch,
      'CardCode' => $header->CardCode,
      'DocDueDate' => $header->DocDueDate,
      'NumAtCard' => $header->NumAtCard,
      'DocDate' => $header->DocDate,
      'VatSum' => $header->VatSum,
      'DocTotal' => $header->DocTotal,
      'remarks' => $error,
      'code' => $lines->CostingCode2
    ];

    return OrderFailed::create($data);
  }

	public function fixbug($docEntry)
	{
		$header = OrderHeader::where('DocEntry',$docEntry)->first();
		$get = OrderLines::where('DocEntry',$docEntry)->get();

		if (count($get) > 0) 
		{
			foreach ($get as $key => $value) 
			{
				$post = [
					'DocEntry' => $docEntry,
					'ItemCode' => $value->ItemCode
				];

				$json = callSapApiLtaWithPost('getLineNumSO',json_encode($post));

				$data = [
					'LineNum' => $json['LineNum']
				];

				OrderLines::where('DocEntry',$docEntry)
									->where('ItemCode',$value->ItemCode)
									->update($data);
			}
		}
		else
		{
			$numAtCard = $header->NumAtCard;

			$post = [
				'DocEntry' => $docEntry,
			];

			$json = callSapApiLtaWithPost('getOrdrDetailLines',json_encode($post));

			// dd($json);

			foreach ($json as $key => $value) 
			{
				$data = [
					'ItemCode' => $value['ItemCode'],
					'ItemDescription' => $value['Dscription'],
					'Quantity' => $value['Quantity'],
					'TaxCode' => $value['TaxCode'],
					'UnitPrice' => $value['UnitPrice'],
					'CostingCode' => $value['CostingCode'],
					'CostingCode2' => $value['CostingCode2'],
					'CostingCode3' => $value['CostingCode3'],
					'UnitMsr' => $value['UnitMsr'],
					'UomCode' => $value['UomCode'],
					'UomEntry' => $value['UomEntry'],
					'NumPerMsr' => $value['NumPerMsr'],
					'WarehouseCode' => $value['WarehouseCode'],
					'LineNum' => $value['LineNum'],
					'DocEntry' => $docEntry,
					'NumAtCard' => $numAtCard
				];

				OrderLines::create($data);
			}
		}

		$info = [
			'docnum' => $header->DocNum
		];

		return $info;
	}
}
