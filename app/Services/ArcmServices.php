<?php

namespace App\Services;

use App\Models\ArcmDetail;
use App\Models\ArcmDetailLines;

class ArcmServices 
{
  public function detail($docnum)
  {
    $cek = ArcmDetail::where('DocNum',$docnum)->first();

    if (isset($cek)) 
    {
      ArcmDetail::where('DocNum',$docnum)->delete();
      ArcmDetailLines::where('DocEntry',$cek->DocEntry)->delete();
    }

    $post = [
      'DocNum' => $docnum
    ];

    $function = 'getArcmDetail';

    $row = callSapApiLtaWithPost($function,json_encode($post));

    if (isset($row)) 
    {
      $docDate = explode(' ',$row['DocDate']);
      $top = "+".$row['TOP']." days";

      $docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate[0])));
      
      $data = [
        'Series' => $row['Series'],
        'DocNum' => $row['DocNum'],
        'DocEntry' => $row['DocEntry'],
        'DocDate' => $row['DocDate'],
        'DocDueDate' => $docDueDate,
        'CardCode' => $row['CardCode'],
        'CardName' => $row['CardName'],
        'Address' => $row['Address'],
        'SlpName' => $row['SlpName'],
        'U_NAME' => $row['U_NAME'],
        'WhsCode' => $row['WhsCode'],
        'Bruto' => $row['Bruto'],
        'VatSum' => $row['VatSum'],
        'Netto' => $row['Netto'],
        'TOP' => $row['TOP'],
        'NumAtCard' => $row['NumAtCard'],
        'PLAT_MIX' => $row['PLAT_MIX'],
        'PLAT_PNG' => $row['PLAT_P&G'],
        'OcrCode2' => $row['OcrCode2'],
        'Printed' => $row['Printed']
      ];
  
      ArcmDetail::create($data);
      ArcmDetailLines::insert($row['Lines']);
  
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

  public function detail_data($docnum)
  {
    $header = ArcmDetail::where('DocNum',$docnum)->first();
    $get_lines = ArcmDetailLines::where('DocEntry',$header->DocEntry)->get();

    // $printed = $header->Printed;

    // if ($printed=='N') 
    // {
    //   $this->updatePrinted($docnum);
    // }

    $lines = [];
    foreach ($get_lines as $key => $lines_value) 
    {
      if (empty($lines_value['ItemCode']) && $lines_value['Quantity']==0 ) 
      {
        $lineTotal = $lines_value['Price'];
      }
      else
      {
        $lineTotal = $lines_value['Quantity'] * $lines_value['Price'];
      }
      

      $disc1 = isset($lines_value['U_DISC1']) ? $lines_value['U_DISC1'] : 0;
      $disc2 = isset($lines_value['U_DISC2']) ? $lines_value['U_DISC2'] : 0;
      $disc3 = isset($lines_value['U_DISC3']) ? $lines_value['U_DISC3'] : 0;
      $disc4 = isset($lines_value['U_DISC4']) ? $lines_value['U_DISC4'] : 0;
      $disc5 = isset($lines_value['U_DISC5']) ? $lines_value['U_DISC5'] : 0;
      $disc6 = isset($lines_value['U_DISC6']) ? $lines_value['U_DISC6'] : 0;
      $disc7 = isset($lines_value['U_DISC7']) ? $lines_value['U_DISC7'] : 0;
      $disc8 = isset($lines_value['U_DISC8']) ? $lines_value['U_DISC8'] : 0;

      $discx1 = ($disc1 / 100) * $lineTotal;
      $discx2 = ($disc2 / 100) * ($lineTotal - $discx1);
      $discx3 = ($disc3 / 100) * ($lineTotal - $discx1 - $discx2);
      $discx4 = ($disc4 / 100) * ($lineTotal - $discx1 - $discx2 - $discx3);
      $discx5 = ($disc5 / 100) * ($lineTotal - $discx1 - $discx2 - $discx3 - $discx4);
      $discx6 = ($disc6 / 100) * ($lineTotal - $discx1 - $discx2 - $discx3 - $discx4 - $discx5);
      $discx7 = ($disc7 / 100) * ($lineTotal - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6);
      $discx8 = ($disc8 / 100) * ($lineTotal - $discx1 - $discx2 - $discx3 - $discx4 - $discx5 - $discx6 - $discx7);

      $disc_total = $disc1+$disc2+$disc3+$disc4+$disc5+$disc6+$disc7+$disc8;
      $disc_total_rp = $discx1+$discx2+$discx3+$discx4+$discx5+$discx6+$discx7+$discx8;

      $lineTotalx = $lineTotal - $disc_total_rp;
      
      $cogs = $lines_value['OcrCode'].';'.$lines_value['OcrCode2'].';'.$lines_value['OcrCode3'];

      $disc = $disc1.'+'.$disc2.'+'.$disc3.'+'.$disc4.'+'.$disc5.'+'.$disc6;

      $lines[] = [
        'ItemCode' => $lines_value->ItemCode,
        'Dscription' => $lines_value->Dscription,
        'cogs' => $cogs,
        'disc_total' => $disc_total,
        'WhsCode' => $lines_value->WhsCode,
        'UnitMsr' => $lines_value->UnitMsr,
        'Price' => $lines_value->Price,
        'Quantity' => $lines_value->Quantity,
        'lineTotal' => $lineTotalx,
        'TaxCode' => $lines_value->TaxCode,
        'U_DISC1' => $disc1,
        'U_DISC2' => $disc2,
        'U_DISC3' => $disc3,
        'U_DISC4' => $disc4,
        'U_DISC5' => $disc5,
        'U_DISC6' => $disc6,
        'U_DISC7' => $disc7,
        'U_DISC8' => $disc8,
        'disc' => $disc,
        'AcctCode' => $lines_value->AcctCode
      ];
    }

    $data = [
      'header' => $header,
      'lines' => $lines,
    ];

    return $data;
  }

  public function updatePrinted($docNum)
  {
    $update_header = [
      'Printed' => 'Y'
    ];

    ArcmDetail::where('DocNum',$docNum)->update($update_header);

    $post = [
      'DocNum' => $docNum
    ];

    return callSapApiLtaWithPost('updatePrintedArcm',json_encode($post));
  }
}