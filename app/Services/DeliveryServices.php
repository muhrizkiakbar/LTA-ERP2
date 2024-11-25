<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryHeader;
use App\Models\DeliveryLines;
use App\Models\DeliveryVoucher;
use App\Models\InvoiceLines;
use App\Models\Item;
use App\Models\OrderHeader;
use App\Models\ReturnTempLines;
use App\Models\Sales;

class DeliveryServices
{
  public function getDataDetail($docnum)
  {
    $get = DeliveryHeader::where('DocNum',$docnum)->first();

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
      'DocStatus' => $get['DocStatus'],
      'USER_CODE' => $get['USER_CODE'],
      'U_NAME' => $get['U_NAME']
    ];

    return $data;
  }

  public function getDataLines($id)
  {
    $data = [];
    $get = DeliveryLines::where('DocEntry',$id)->groupBy('ItemCode')->get();
    
    foreach ($get as $value) 
    {
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

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

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => isset($item) ? $item['ItemName'] : '-',
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $value['UnitPrice'],
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'disc1' => isset($disc1) ? $disc1 : 0,
        'disc2' => isset($disc2) ? $disc2 : 0,
        'disc3' => isset($disc3) ? $disc3 : 0,
        'disc4' => isset($disc4) ? $disc4 : 0,
        'disc5' => isset($disc5) ? $disc5 : 0,
        'disc6' => isset($disc6) ? $disc6 : 0,
        'disc7' => isset($disc7) ? $disc7 : 0,
        'disc8' => isset($disc8) ? $disc8 : 0,
        'disc_total' => $disc_cal,
        'sts' => $value['DocStatus'],
        'CostingCode' => $value['CostingCode'],
        'Batch' => $value['Batch'],
        'ExpDate' => $value['ExpDate']
      ];
    }

    return $data;
  }

  public function getDataLinesSeparate($id,$i)
  {
    $data = [];

    if ($i==0) 
    {
      $get = DeliveryLines::where('DocEntry',$id)
													->groupBy('ItemCode')
													->orderBy('id','ASC')->take(11)->get();
    }
    else
    {
      if ($i > 1) 
      {
        $skipx = 11 + 17;

        if ($i >= 3) 
        {
          $skip = 11 + (($i-2) * 17) + 17;
        }
        else
        {
          $skip = $skipx;
        }
      }
      else
      {
        $skip = $i * 11;
      }
      
      $get = DeliveryLines::where('DocEntry',$id)
													->groupBy('ItemCode')
													->orderBy('id','ASC')->skip($skip)->take(17)->get();
    }
    
    
    foreach ($get as $value) 
    {
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

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

      $disc_reg = $discx1+$discx2;
      $disc_vol = 0;
      $disc_lot = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $totalx = $total - $disc_calx;

      $totalx2 = $totalx  + ($totalx * 0.11);

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
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'docTotal2' => $totalx2,
        'disc1' => isset($disc1) ? $disc1 : 0,
        'disc2' => isset($disc2) ? $disc2 : 0,
        'disc3' => isset($disc3) ? $disc3 : 0,
        'disc4' => isset($disc4) ? $disc4 : 0,
        'disc5' => isset($disc5) ? $disc5 : 0,
        'disc6' => isset($disc6) ? $disc6 : 0,
        'disc7' => isset($disc7) ? $disc7 : 0,
        'disc8' => isset($disc8) ? $disc8 : 0,
        'disc_total' => $disc_cal,
        'sts' => $value['DocStatus'],
        'CostingCode' => $value['CostingCode'],
        'Batch' => $value['Batch'],
        'ExpDate' => $value['ExpDate'],
        'disc_reg' => $disc_reg,
        'disc_vol' => $disc_vol,
        'disc_lot' => $disc_lot,
        'barcode' => $item['Barcode'],
      ];
    }

    return $data;
  }

  public function getDataLinesSeparate2($id,$i)
  {
    $data = [];

    if ($i==0) 
    {
      $get = DeliveryLines::where('DocEntry',$id)->orderBy('id','ASC')->take(10)->get();
    }
    else
    {
      if ($i > 1) 
      {
        $skipx = 10 + 16;

        if ($i >= 3) 
        {
          $skip = 10 + (($i-2) * 16) + 16;
        }
        else
        {
          $skip = $skipx;
        }
      }
      else
      {
        $skip = $i * 10;
      }
      
      $get = DeliveryLines::where('DocEntry',$id)->orderBy('id','ASC')->skip($skip)->take(16)->get();
    }
    
    
    foreach ($get as $value) 
    {
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

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

      $disc_reg = $discx1+$discx2;
      $disc_vol = 0;
      $disc_lot = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $totalx = $total - $disc_calx;

      $totalx2 = $totalx  + ($totalx * 0.11);

      $unitPrice = $value['UnitPrice'] + ($value['UnitPrice'] * 0.11);

      $disc_reg_ppn = $disc_reg + ($disc_reg * 0.11);
      $disc_vol_ppn = $disc_vol + ($disc_vol * 0.11);
      $disc_lot_ppn = $disc_lot + ($disc_lot * 0.11);

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => $item['ItemName'],
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $unitPrice,
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'docTotal2' => $totalx2,
        'disc1' => isset($disc1) ? $disc1 : 0,
        'disc2' => isset($disc2) ? $disc2 : 0,
        'disc3' => isset($disc3) ? $disc3 : 0,
        'disc4' => isset($disc4) ? $disc4 : 0,
        'disc5' => isset($disc5) ? $disc5 : 0,
        'disc6' => isset($disc6) ? $disc6 : 0,
        'disc7' => isset($disc7) ? $disc7 : 0,
        'disc8' => isset($disc8) ? $disc8 : 0,
        'disc_total' => $disc_cal,
        'sts' => $value['DocStatus'],
        'CostingCode' => $value['CostingCode'],
        'Batch' => $value['Batch'],
        'ExpDate' => $value['ExpDate'],
        'disc_reg' => $disc_reg_ppn,
        'disc_vol' => $disc_vol_ppn,
        'disc_lot' => $disc_lot_ppn,
        'barcode' => $item['Barcode'],
      ];
    }

    return $data;
  }

  public function getDataLinesSeparate3($id,$i)
  {
    $data = [];

    if ($i==0) 
    {
      $get = DeliveryLines::where('DocEntry',$id)->orderBy('id','ASC')->take(10)->get();
    }
    else
    {
      if ($i > 1) 
      {
        $skipx = 10 + 16;

        if ($i >= 3) 
        {
          $skip = 10 + (($i-2) * 16) + 16;
        }
        else
        {
          $skip = $skipx;
        }
      }
      else
      {
        $skip = $i * 10;
      }
      
      $get = DeliveryLines::where('DocEntry',$id)->orderBy('id','ASC')->skip($skip)->take(16)->get();
    }
    
    
    foreach ($get as $value) 
    {
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

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

      $disc_cal = $disc1+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8+$disc2;
      $disc_calx = $discx1+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8+$discx2;

      $disc_reg = $discx1;
      $disc_lot = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;
      $disc_vol = +$discx2;

      $totalx = $total - $disc_calx;

      $totalx2 = $totalx  + ($totalx * 0.11);

      $unitPrice = $value['UnitPrice'] + ($value['UnitPrice'] * 0.11);

      $disc_reg_ppn = $disc_reg + ($disc_reg * 0.11);
      $disc_vol_ppn = $disc_vol + ($disc_vol * 0.11);
      $disc_lot_ppn = $disc_lot + ($disc_lot * 0.11);

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => $item['ItemName'],
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $unitPrice,
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'docTotal2' => $totalx2,
        'disc1' => isset($disc1) ? $disc1 : 0,
        'disc2' => isset($disc2) ? $disc2 : 0,
        'disc3' => isset($disc3) ? $disc3 : 0,
        'disc4' => isset($disc4) ? $disc4 : 0,
        'disc5' => isset($disc5) ? $disc5 : 0,
        'disc6' => isset($disc6) ? $disc6 : 0,
        'disc7' => isset($disc7) ? $disc7 : 0,
        'disc8' => isset($disc8) ? $disc8 : 0,
        'disc_total' => $disc_cal,
        'sts' => $value['DocStatus'],
        'CostingCode' => $value['CostingCode'],
        'Batch' => $value['Batch'],
        'ExpDate' => $value['ExpDate'],
        'disc_reg' => $disc_reg_ppn,
        'disc_vol' => $disc_vol_ppn,
        'disc_lot' => $disc_lot_ppn,
        'barcode' => $item['Barcode'],
      ];
    }

    return $data;
  }

  public function getDataLinesSeparate4($id,$i)
  {
    $data = [];

    if ($i==0) 
    {
      $get = DeliveryLines::where('DocEntry',$id)
													->groupBy('ItemCode')
													->orderBy('id','ASC')
													->take(10)
													->get();
    }
    else
    {
      if ($i > 1) 
      {
        $skipx = 10 + 16;

        if ($i >= 3) 
        {
          $skip = 10 + (($i-2) * 16) + 16;
        }
        else
        {
          $skip = $skipx;
        }
      }
      else
      {
        $skip = $i * 10;
      }
      
      $get = DeliveryLines::where('DocEntry',$id)->groupBy('ItemCode')->orderBy('id','ASC')->skip($skip)->take(16)->get();
    }
    
    
    foreach ($get as $value) 
    {
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      $post_item = [
        'ItemCode' => $value['ItemCode']
      ];
      $item = getItemId(json_encode($post_item));

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

      $disc_cal = $disc1+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8+$disc2;
      $disc_calx = $discx1+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8+$discx2;

      $disc_reg = $discx1;
      $disc_lot = $discx3+$discx4+$discx5+$discx6+$discx7+$discx8;
      $disc_vol = +$discx2;

      $totalx = $total - $disc_calx;

      $totalx2 = $totalx  + ($totalx * 0.11);

      $unitPrice = $value['UnitPrice'] + ($value['UnitPrice'] * 0.11);

      $disc_reg_satuan = $disc_reg / $value['Quantity'];
      $disc_vol_satuan = $disc_vol / $value['Quantity'];
      $disc_lot_satuan = $disc_lot / $value['Quantity'];

      $disc_reg_ppn = ($disc_reg_satuan + ($disc_reg_satuan * 0.11));
      $disc_vol_ppn = ($disc_vol_satuan + ($disc_vol_satuan * 0.11));
      $disc_lot_ppn = ($disc_lot_satuan + ($disc_lot_satuan * 0.11));

      $harga_satuan_nett = $totalx2 / $value['Quantity'];

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => isset($item) ? $item['ItemName'] : '-',
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $unitPrice,
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WarehouseCode'],
        'cogs' => $value['CostingCode'].';'.$value['CostingCode2'].';'.$value['CostingCode3'],
        'beforeDiscount' => $total,
        'docTotal' => $totalx,
        'docTotal2' => $totalx2,
        'disc_total' => $disc_cal,
        'sts' => $value['DocStatus'],
        'CostingCode' => $value['CostingCode'],
        'Batch' => $value['Batch'],
        'ExpDate' => $value['ExpDate'],
        'disc_reg' => $disc_reg_ppn,
        'disc_vol' => $disc_vol_ppn,
        'disc_lot' => $disc_lot_ppn,
        'barcode' => $item['Barcode'],
        'harga_satuan_nett' => $harga_satuan_nett
      ];
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

  public function getDocNumSO($id)
  {
    $get = DeliveryLines::where('DocEntry',$id)->first();
    $docnum = OrderHeader::where('DocEntry',$get->BaseEntry)->first();
    if(!empty($docnum))
    {
      $data = $docnum->DocNum;
    }
    else
    {
      $push_doc = [
        'DocEntry' => $get->BaseEntry
      ];

      $docnum = getDocNumSO(json_encode($push_doc));
      $data = '';
    }

    return $data;
  }

  public function jsonInvoice($id,$postingDate)
  {
    $get = DeliveryHeader::where('DocNum',$id)->get();

    foreach ($get as $value) 
    {
      $post = [
        'CardCode' => $value['CardCode']
      ];

      $getTop = $this->getTopCustomer(json_encode($post));
      $top = "+".$getTop." days";
      $docDate = $postingDate; 
      $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

      $data = [
        'CardCode' => $value['CardCode'],
        'DocDueDate' => $docDueDate,
        'DocDate' => $docDate,
        'BPL_IDAssignedToInvoice' => $value['BPLId'],
        'SalesPersonCode'=> $value['SalesPersonCode'],
        'NumAtCard' => $value['DocNum'],
        'Comments' => $value['Comments'],
        'U_NOPOLISI' => $value['U_NOPOLISI'],
        'U_NOPOLISI2' => $value['U_NOPOLISI2'],
        'DocumentLines' => $this->jsonInvoiceLines($id,$value['DocEntry'])
      ];
    }

    return $data;
  }

  public function jsonInvoiceLines($docnum,$docentry)
  {
    $get = DeliveryLines::where('DocEntry',$docentry)->where('DocStatus','O')->groupBy('ItemCode')->orderBy('BaseLine','ASC')->get();

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
        'WarehouseCode' => $value['WhsCode'],
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
        'BaseRef' => $docnum,
        'BaseEntry' => $docentry,
        'BaseType' => 15,
        'BaseLine' => $value['BaseLine']
      ];

      $no++;
    }

    return $data;
  }

  public function pushInvoice($header,$url,$post)
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

  public function jsonReturnTempLines($docentry)
  {
		ReturnTempLines::where('DocEntry',$docentry)->delete();
		
    $get = DeliveryLines::where('DocEntry',$docentry)
												->groupBy('ItemCode')
												->get();

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

      $data[] = [
        'DocEntry' => $docentry,
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $value['Quantity'],
        'TaxCode' => $value['TaxCode'],
        'UnitPrice' => $value['UnitPrice'],
        'CostingCode' => $value['CostingCode'],
        'CostingCode2' => $value['CostingCode2'],
        'CostingCode3' => $value['CostingCode3'],
        'UnitMsr' => $value['UnitMsr'],
        'UomCode' => $value['UnitMsr'],
        'UomEntry' => $value['UomEntry'],
        'NumPerMsr' => $value['NumPerMsr'],
        'WarehouseCode' => $value['WarehouseCode'],
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
        'BaseType' => 15,
        'BaseLine' => $value['BaseLine'],
        'LineTotal' => round($totalx,2),
        'DiscountPercent' => round($disc_cal,2),
      ];

      $no++;
    }

    return $data;
  }

  public function cekQtyReturn($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/cekReturnLines';
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
        // Set here requred headersn
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

  public function getOdlnDetail($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getOdlnDetail';
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

  public function getDataLinesDisc($id)
  {
    $data = [];
    $get = DeliveryLines::where('DocEntry',$id)->groupBy('ItemCode')->get();

    $header = DeliveryHeader::where('DocEntry',$id)->first();

    $post_customer = [
      'CardCode' => $header['CardCode']
    ];
    $customer = getCustomerId(json_encode($post_customer));

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

      // dd($item);

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
        'itemDesc' => $item['ItemName'],
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
        'disc_total' => $disc_cal
      ];
    }

    return $data;
  }

  public function jsonUpdate($id)
  {
    $get = DeliveryHeader::where('DocEntry',$id)->first();
    $lines = DeliveryLines::where('DocEntry',$id)->get();
    $no = 0;
    foreach ($lines as $value) 
    {
      $data_lines = [
        'LineNum' => $no
      ];
      DeliveryLines::find($value->id)->update($data_lines);

      $no++;
    }

    // dd($data_lines);

    $data = [
      'Comments' => $get->Comments,
      'DocumentLines' => $this->jsonUpdateLines($id)
    ];

    return $data;
  }

  public function jsonUpdateLines($id)
  {
    $data = [];
    $get = DeliveryLines::where('DocEntry',$id)->get();

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

      $totalx = $total - $disc_calx;

      $data[] = [
        'DocEntry' => $value['DocEntry'],
        'ItemCode' => $value['ItemCode'],
        'DiscPrcnt' => round($disc_cal,2),
        'LineTotal' => round($totalx,2),
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
        'U_DISCVALUE8' => $discx8
      ];
    }

    return $data;
  }

  public function updateDelivery($header,$url,$post)
  {
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

  public function generateCn($cardCode,$id,$cogs)
  {
    $data = [];

    $post_customer = [
      'CardCode' => $cardCode
    ];

    $customer = getCustomerId(json_encode($post_customer));

    if ($customer['GroupCode']=='100' || $customer['GroupCode']=='113') 
    {
      $api = 'generateCollectorCN2';

      $post = [
        'CardCode' => $cardCode,
        'OcrCode2' => $cogs
      ];

      $row = callSapApiLtaWithPost($api,json_encode($post));

      // dd($row);

      // dd($post);

      DeliveryVoucher::whereNull('DocNumDelivery')
                      ->where('CardCode',$cardCode)
                      ->delete();

      if (count($row)==0) 
      {
        $response = [
          'message' => 'error',
          'kd' => $id
        ];
      }
      else
      {
        $DocTotalCn = array_sum(array_column($row,'DocTotal'));

        $delivery = DeliveryHeader::where('DocEntry',$id)->first();

        $DocTotalDelivery = $delivery->DocTotal + $delivery->VatSum;

        if ($DocTotalDelivery > $DocTotalCn) 
        {
          $no = 1;
          $nox = 0;

          foreach ($row as $value) 
          {
            $cek = DeliveryVoucher::where('DocEntry',$value['DocEntry'])
                                  ->where('CardCode',$cardCode)
                                  ->get();

            if (count($cek)==0) 
            {
              $data = [
                'DocEntry' => $value['DocEntry'],
                'DocNum' => $value['DocNum'],
                'DocDate' => $value['DocDate'],
                'DocDueDate' => $value['DocDueDate'],
                'CardCode' => $value['CardCode'],
                'CardName' => $value['CardName'],
                'Comments' => $value['Comments'],
                'NumAtCard' => $value['NumAtCard'],
                'DocTotal' => $value['DocTotal'],
                'PaidToDate' => $value['PaidToDate'],
                'BalanceDue' => $value['DocTotal'] - $value['PaidToDate'],
                'OcrCode2' => $value['OcrCode2']
              ];

              DeliveryVoucher::create($data);

              $nox = $no++;
            }        
          }

          if ($nox > 0) 
          {
            $response = [
              'message' => 'success',
              'kd' => $id
            ];
          } 
          else 
          {
            $response = [
              'message' => 'error',
              'kd' => $id
            ];
          }
        }
        else
        {
          $response = [
            'message' => 'error_val',
            'kd' => $id
          ];
        }
      }
    }
    else
    {
      $response = [
        'message' => 'error',
        'kd' => $id
      ];
    }
    
    return $response;
  }

  public function voucherList($cardCode,$cogs)
  {
    $data = [];

    $row = DeliveryVoucher::where('CardCode',$cardCode)
                          ->where('OcrCode2',$cogs)
                          ->whereNull('DocNumDelivery')
                          ->get();
    
    foreach ($row as $key => $value) 
    {
      $data[] = [
        'DocNum' => $value->DocNum,
        'NumAtCard' => $value->NumAtCard,
        'DocDate' => $value->DocDate,
        'DocTotal' => $value->DocTotal,
        'PaidToDate' => $value->PaidToDate,
        'BalanceDue' => $value->BalanceDue,
        'Comments' => $value->Comments
      ];
    }

    return $data;
  }

  public function voucherList2($DocNum)
  {
    $data = [];

    $row = DeliveryVoucher::where('DocNumDelivery',$DocNum)
                          ->get();
    
    foreach ($row as $key => $value) 
    {
      $data[] = [
        'DocNum' => $value->DocNum,
        'NumAtCard' => $value->NumAtCard,
        'DocDate' => $value->DocDate,
        'DocTotal' => $value->DocTotal,
        'PaidToDate' => $value->PaidToDate,
        'BalanceDue' => $value->BalanceDue,
        'Comments' => $value->Comments
      ];
    }

    return $data;
  }

  public function generateVoucher($kd,$docTotalCN)
  {
    $header = DeliveryHeader::where('DocNum',$kd)->first();
    $cogs = DeliveryLines::where('DocEntry',$header->DocEntry)->first();
    $cogx = $cogs['CostingCode2'];

    $docTotal = $header->VatSum + $header->DocTotal;

    $lines = DeliveryVoucher::where('CardCode',$header->CardCode)
                            ->where('OcrCode2',$cogx)
                            ->whereNull('DocNumDelivery')
                            ->get();

    if ($docTotalCN < $docTotal) 
    {
      foreach ($lines as $value) 
      {
        $update = [
          'DocNumDelivery' => $header->DocNum
        ];

        DeliveryVoucher::find($value->id)->update($update);
      }

      $response = [
        'message' => 'success'
      ];
    }
    else
    {
      $response = [
        'message' => 'error'
      ];
    }

    return $response;
  }

  public function getVoucher($docNum)
  {
    $get = DeliveryVoucher::where('DocNumDelivery',$docNum)
                          ->sum('BalanceDue');

    if ($get==0) 
    {
      return 0;
    }
    else
    {
      return $get;
    }
  }

  public function getVoucherLines($docNum)
  {
    $get = DeliveryVoucher::where('DocNumDelivery',$docNum)
                          ->get();

    return $get;
  }
}