<?php

namespace App\Services;

use App\Models\InvoiceHeader;
use App\Models\InvoiceLines;
use App\Models\Item;

class InvoiceServices
{
  public function getDataDetail($docnum)
  {
    $get = InvoiceHeader::where('DocNum',$docnum)->first();

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
      'NoSeriesPajak' => $get['NoSeriesPajak']
    ];

    return $data;
  }

  public function getDataLines($id)
  {
    $data = [];
    $get = InvoiceLines::where('DocEntry',$id)->get();
    
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
        'itemDesc' => isset($item['ItemName']) ? $item['ItemName'] : '-',
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

  public function getItem($code)
  {
    return Item::where('code',$code)->first();
  }

  public function getInvoice($docnum)
  {
    $cek = InvoiceHeader::where('DocNum',$docnum)->first();

    if (isset($cek)) 
    {
      InvoiceHeader::where('DocNum',$docnum)->delete();
      InvoiceLines::where('DocEntry',$cek->DocEntry)->delete();
    }

    $post = [
      'DocNum' => $docnum
    ];

    $row = callSapApiLtaWithPost('getInvoice',json_encode($post));

    if (isset($row)) 
    {
      $docDate = explode(' ',$row['DocDate']);
      $top = "+".$row['TOP']." days";

      $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate[0])));

      $post_header = [
        'DocNum' => $row['DocNum'],
        'DocEntry' => $row['DocEntry'],
        'Branch' => $row['Branch'],
        'CardCode' => $row['CardCode'],
        'DocDueDate' => $docDueDate,
        'NumAtCard' => $row['NumAtCard'],
        'DocDate' => $row['DocDate'],
        'BPLId' => $row['BPLId'],
        'SalesPersonCode' => $row['SalesPersonCode'],
        'U_NOPOLISI' => $row['U_NOPOLISI'],
        'U_NOPOLISI2' => $row['U_NOPOLISI2'],
        'Comments' => $row['Comments'],
        'VatSum' => $row['VatSum'],
        'DocTotal' => $row['DocTotal'],
        'DocStatus' => $row['DocStatus'],
        'NoSeriesPajak' => $row['NoSeriesPajak']
      ];

      InvoiceHeader::create($post_header);
      InvoiceLines::insert($row['Lines']);

      $callback = [
        'message' => 'success',
        'docnum' => $docnum
      ];
    }
    else
    {
      $callback = [
        'message' => 'error'
      ];
    }

    return $callback;
  }
}