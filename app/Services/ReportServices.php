<?php

namespace App\Services;

use App\Models\BosLtomset;
use App\Models\DeliveryVoucher;
use App\Models\MixLines;
use App\Models\PngLines;
use App\Models\Storemaster;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function App\Http\Controllers\Sap\mergeAndSumQuantities;

class ReportServices
{
  public function getPlat($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getPlat';
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

  public function getTipe()
  {
    $url = 'https://saplta.laut-timur.tech/api/getTipe';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
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

  public function getUser()
  {
    $url = 'https://saplta.laut-timur.tech/api/getUser';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
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

  public function getCabang()
  {
    $url = 'https://saplta.laut-timur.tech/api/getCabang';
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,// your preferred link
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
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

  public function reportPackingList($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/reportPackingList';
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

	public function reportPaketEko($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/reportPaketEko';
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

  public function reportDailyDeliveryBySales($post)
  {
    $data = [];
    $row = callSapApiLtaWithPost('reportDailyDeliveryBySales',$post);

    foreach ($row as $key => $value) 
    {
      $exp = explode('/',$value['DocNum']);
      $usedCn = $this->usedCn($exp[1]);
      $total = $value['Netto'] - $usedCn;

      if($value['Netto'] >= 5000000)
      {
        $bea_materai = 10000;
      }
      else
      {
        $bea_materai = 0;
      }

      $data[] = [
        'DocNum' => $value['DocNum'],
        'DocNumSO' => $value['DocNumSO'],
        'CardCode' => $value['CardCode'],
        'CardName' => $value['CardName'],
        'Netto' => $value['Netto'],
        'BeaMaterai' => $bea_materai,
        'Voucher' => $usedCn,
        'Total' => $total + $bea_materai, 
        'SlpName' => $value['SlpName']
      ];
    }

    return $data;
  }

  public function reportDailyDeliveryByPlat($post)
  {
    $data = [];
    $row = callSapApiLtaWithPost('reportDailyDeliveryByPlat',$post);

    // dd($row);
    
    foreach ($row as $key => $value) 
    {
      $exp = explode('/',$value['DocNum']);
      $usedCn = $this->usedCn($exp[1]);
      $total = $value['Netto'] - $usedCn;

      if($value['Netto'] >= 5000000)
      {
        $bea_materai = 10000;
      }
      else
      {
        $bea_materai = 0;
      }

      $data[] = [
        'DocNum' => $value['DocNum'],
        'DocNumSO' => $value['DocNumSO'],
        'CardCode' => $value['CardCode'],
        'CardName' => $value['CardName'],
        'Netto' => $value['Netto'],
        'BeaMaterai' => $bea_materai,
        'Voucher' => $usedCn,
        'Total' => $total + $bea_materai,  
        'SlpName' => $value['SlpName']
      ];
    }

    return $data;
  }

  public function reportRekapSalesOrder($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/reportRekapSalesOrder';
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

  public function reportOmset($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/reportOmsetNon';
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

  public function reportLTOmset($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/reportOmset';
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

  public function reportLTOmsetLokal($cabang,$dateFrom,$dateTo,$tipe)
  {
    if ($cabang=='ALL') 
    {
      $get = BosLtomset::where('TGL_JUAL','>=',$dateFrom)
                     ->where('TGL_JUAL','<=',$dateTo)
                     ->where('SUPP_NAME',$tipe)
                     ->cursor();
    }
    else
    {
      $get = BosLtomset::where('CABANG',$cabang)
                     ->where('TGL_JUAL','>=',$dateFrom)
                     ->where('TGL_JUAL','<=',$dateTo)
                     ->where('SUPP_NAME',$tipe)
                     ->cursor();
    }
    
    return $get;
  }

  public function reportRekapSalesOrderPlat($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/rekapSalesOrderPlat';
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

  public function rekapDeliveryByPlat($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/rekapDeliveryByPlat';
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

  public function unserved_png($branch, $dateF, $dateT)
  {
    $data = [];

    $get = DB::table('png_lines')
              ->join('png_header', 'png_header.NumAtCard', '=', 'png_lines.NumAtCard')
              ->select(
                  'png_lines.ItemCode', 
                  'png_lines.ItemName',
                  'png_header.DocDate',
                  'png_header.CardCode',
                  'png_header.CardName',
                  DB::raw('SUM(png_lines.Quantity) as Served'),
                  DB::raw('SUM(png_lines.QuantitySfa) as SfaQty'),
                  DB::raw('SUM(png_lines.QuantitySfaCases) as SfaQtyCases'),
                  DB::raw('SUM(png_lines.QuantitySfaTotal) as SfaQtyTotal'),
                  DB::raw('SUM(png_lines.QuantitySfaTotal) - SUM(png_lines.Quantity) as Unserve')

                )
              ->where('png_header.DocDate','>=',$dateF)
              ->where('png_header.DocDate','<=',$dateT)
              ->where('png_lines.CostingCode',$branch)
              // ->where('png_lines.Quantity',0)
              ->groupBy('png_lines.ItemCode', 'png_lines.ItemName')
              ->orderBy('png_header.DocDate')
              ->get();
    // dd($get);

    foreach ($get as $key => $value) 
    {
      $post_item = [
        'ItemCode' => $value->ItemCode
      ];

      $item = $this->getItemDetail(json_encode($post_item));
      $sfaSuccess = $value->Served;
      $percent = ($value->Served / $value->SfaQtyTotal) * 100;

      $totalOrderRp  = $value->SfaQtyTotal * $item['Harga'];
      $successOrderRp  = $value->Served * $item['Harga'];
      $unservedOrderRp  = $value->Unserve * $item['Harga'];

      $data[] = [
        'DocDate' => Carbon::parse($value->DocDate)->format('d.m.y'),
        'ItemCode' => $value->ItemCode,
        'ItemName' => $value->ItemName,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'NISIB' => $item['NISIB'],
        'SfaQty' => $value->SfaQty,
        'SfaQtyCases' => $value->SfaQtyCases,
        'SfaQtyTotal' => $value->SfaQtyTotal,
        'SfaQtySuccess' => $sfaSuccess,
        'SfaQtyUnserve' => $value->Unserve,
        'Harga' => $item['Harga'],
        'Total' => $totalOrderRp,
        'TotalSuccess' => $successOrderRp,
        'TotalUnserved' => $unservedOrderRp,
        'percentage' => $unservedOrderRp != 0 ? $percent : 0
      ];
    }

    // TOTAL BY QUANTITY
    $SfaTotal = array_sum(array_column($data,'SfaQtyTotal'));
    $SfaTotalSuccess = array_sum(array_column($data,'SfaQtySuccess'));
    $SfaTotalUnserved = array_sum(array_column($data,'SfaQtyUnserve'));
    // TOTAL BY RUPIAH
    $totalOrderRp = array_sum(array_column($data,'Total'));
    $totalSuccessRp = array_sum(array_column($data,'TotalSuccess'));
    $totalUnservedRp = array_sum(array_column($data,'TotalUnserved'));

    $percentage = $totalUnservedRp != 0 ? ($totalUnservedRp / $totalOrderRp) * 100 : 0;

    $result = [
      'data' => $data,
      'sfa_total_order' => $SfaTotal,
      'sfa_success_order' => $SfaTotalSuccess,
      'sfa_unserved_order' => $SfaTotalUnserved,
      'total_order_rp' => $totalOrderRp,
      'total_success_rp' => $totalSuccessRp,
      'total_unserved_rp' => $totalUnservedRp,
      'percentage' => round($percentage, 2)
    ];


    return $result;
  }

  // unserved png by table views
  public function unservedPngView($branch, $dateF, $dateT) {

    $data = DB::table('unservedpng')
      ->where('DocDate','>=',$dateF)
      ->where('DocDate','<=',$dateT)
      ->where('Branch',$branch)
      // ->groupBy('ItemCode')
      ->get();


    $res = collect($data)->transform(function($item) {

      $totalOrderRp = $item->unitPrice * $item->QtyRequestSfa;
      $totalSuccessRp = $item->unitPrice * $item->QtyProsesErp;
      $totalUnservedRp = $item->unitPrice * $item->Selisih;

      $percentage = $totalUnservedRp != 0 ? ($totalUnservedRp / $totalOrderRp) * 100 : 0;

      return [
          'DocDate' => Carbon::parse($item->DocDate)->format('d.m.y'),
          'RefSo' => $item->So,
          'RefNumber' => $item->RefNumber,
          'ItemCode' => $item->ItemCode,
          'ItemName' => $item->ItemName,
          'CardCode' => $item->CardCode,
          'CardName' => $item->CardName,
          'CostingCode' => $item->CostingCode,
          'UnitPrice' => $item->unitPrice,
          'SfaQty' => $item->QtyProsesErp,
          'SfaQtyCases' => "",
          'SfaQtyTotal' => $item->QtyRequestSfa,
          'SfaQtySuccess' => $item->QtyProsesErp,
          'SfaQtyUnserve' => $item->Selisih,
          'Harga' => $item->unitPrice,
          'Total' => $totalOrderRp,
          'TotalSuccess' =>  $totalSuccessRp,
          'TotalUnserved' =>  $totalUnservedRp,
          'percentage' => round($percentage, 2)
      ];
    });
   
     
    // TOTAL BY QUANTITY
    $SfaTotal = $res->sum("SfaQtyTotal");
    $SfaTotalSuccess =  $res->sum("SfaQtySuccess");
    $SfaTotalUnserved =  $res->sum("SfaQtyUnserve");
    // TOTAL BY RUPIAH
    $totalOrderRp = $res->sum("Total");
    $totalSuccessRp = $res->sum("TotalSuccess");
    $totalUnservedRp = $res->sum("TotalUnserved");

    $percentage = $totalUnservedRp != 0 ? ($totalUnservedRp / $totalOrderRp) * 100 : 0;

     $result = [
      'data' => $res,
      'sfa_total_order' => $SfaTotal,
      'sfa_success_order' => $SfaTotalSuccess,
      'sfa_unserved_order' => $SfaTotalUnserved,
      'total_order_rp' => $totalOrderRp,
      'total_success_rp' => $totalSuccessRp,
      'total_unserved_rp' => $totalUnservedRp,
      'percentage' => $percentage
    ];


    return $result;
  }

  public function unserved_mix($branch, $dateF, $dateT)
  {
    $data = [];

    $get = DB::table('mix_lines')
              ->join('mix_header', 'mix_header.NumAtCard', '=', 'mix_lines.NumAtCard')
              ->select(
                  DB::raw('mix_header.DocDate as OrderDate'), 
                  'mix_lines.ItemCode', 
                  'mix_lines.ItemName', 
                  'mix_header.CardCode', 
                  'mix_header.CardName', 
                  DB::raw('SUM(mix_lines.Quantity) as Serve'),
                  DB::raw('SUM(mix_lines.QuantitySfa) as SfaQty'),
                  DB::raw('SUM(mix_lines.QuantitySfaCases) as SfaQtyCases'),
                  DB::raw('SUM(mix_lines.QuantitySfaTotal) as SfaQtyTotal'),
                  DB::raw('SUM(mix_lines.QuantitySfaTotal) - SUM(mix_lines.Quantity) as Unserve')
                )
              ->where('mix_header.DocDate','>=',$dateF)
              ->where('mix_header.DocDate','<=',$dateT)
              ->where('mix_lines.CostingCode',$branch)
              ->groupBy('mix_lines.ItemCode', 'mix_lines.ItemName')
              ->orderBy('mix_lines.ItemName')
              ->get();

    foreach ($get as $key => $value) 
    {
      $post_item = [
        'ItemCode' => $value->ItemCode
      ];

      $item = $this->getItemDetail(json_encode($post_item));

      $sfaSuccess = $value->Serve;
      $percent1 = ($value->SfaQtyTotal / ($value->SfaQtyTotal + $sfaSuccess));
      $percent = $percent1 * 100;

      $totalHarga = $value->SfaQtyTotal * $item['Harga'];
      $totalSuccess = $sfaSuccess * $item['Harga'];
      $totalUnserve = $value->Unserve * $item['Harga'];

      $data[] = [
        'OrderDate' => Carbon::parse($value->OrderDate)->format('d.m.y'),
        'ItemCode' => $value->ItemCode,
        'ItemName' => $value->ItemName,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'NISIB' => $item['NISIB'],
        'SfaQtyTotal' => $value->SfaQtyTotal,
        'SfaQtySuccess' => $sfaSuccess,
        'SfaQtyUnserve' => $value->Unserve,
        'Harga' => $item['Harga'],
        'OrderTotal' => $totalHarga,
        'Total' => $totalUnserve,
        'TotalSuccess' => $totalSuccess,
        'percentage' => $totalUnserve != 0 ?($totalUnserve / $totalHarga) * 100 : 0
      ];
    }
    
    // TOTAL BY QUANTITY
    $SfaTotal = array_sum(array_column($data,'SfaQtyTotal'));
    $SfaTotalSuccess = array_sum(array_column($data,'SfaQtySuccess'));
    $SfaTotalUnserved = array_sum(array_column($data,'SfaQtyUnserve'));
    // TOTAL BY RUPIAH
    $totalOrderRp = array_sum(array_column($data,'OrderTotal'));
    $totalSuccessRp = array_sum(array_column($data,'TotalSuccess'));
    $totalUnservedRp = array_sum(array_column($data,'Total'));

    $percentage = $totalUnservedRp != 0 ? ($totalUnservedRp / $totalOrderRp) : 0;
    // $percentage = $percentage1 * 100;

    $result = [
      'data' => $data,
      'sfa_total_order' => $SfaTotal,
      'sfa_success_order' => $SfaTotalSuccess,
      'sfa_unserved_order' => $SfaTotalUnserved,
      'total_order_rp' => $totalOrderRp,
      'total_success_rp' => $totalSuccessRp,
      'total_unserved_rp' => $totalUnservedRp,
      'percentage' => round($percentage, 2)
    ];

    return $result;
  }

  public function unservedMixView($branch, $dateF, $dateT) {
    $data = DB::table('unservedmix')
      ->where('DocDate','>=',$dateF)
      ->where('DocDate','<=',$dateT)
      ->where('Branch',$branch)
      ->get();


    $res = collect($data)->transform(function($item) {

      $totalOrderRp = $item->unitPrice * $item->QtyRequestSfa;
      $totalSuccessRp = $item->unitPrice * $item->QtyProsesErp;
      $totalUnservedRp = $item->unitPrice * $item->Selisih;

      $percentage = $totalUnservedRp != 0 ? ($totalUnservedRp / $totalOrderRp) * 100 : 0;

      return [
          'DocDate' => Carbon::parse($item->DocDate)->format('d.m.y'),
          'ItemCode' => $item->ItemCode,
          'ItemName' => $item->ItemName,
          'CardCode' => $item->CardCode,
          'CardName' => $item->CardName,
          'UnitPrice' => $item->unitPrice,
          'SfaQty' => $item->QtyProsesErp,
          'SfaQtyCases' => "",
          'SfaQtyTotal' => $item->QtyRequestSfa,
          'SfaQtySuccess' => $item->QtyProsesErp,
          'SfaQtyUnserve' => $item->Selisih,
          'Harga' => $item->unitPrice,
          'Total' => $totalOrderRp,
          'TotalSuccess' =>  $totalSuccessRp,
          'TotalUnserved' =>  $totalUnservedRp,
          'percentage' => round($percentage, 2)
      ];
    });

     
    // TOTAL BY QUANTITY
    $SfaTotal = $res->sum("SfaQtyTotal");
    $SfaTotalSuccess =  $res->sum("SfaQtySuccess");
    $SfaTotalUnserved =  $res->sum("SfaQtyUnserve");
    // TOTAL BY RUPIAH
    $totalOrderRp = $res->sum("Total");
    $totalSuccessRp = $res->sum("TotalSuccess");
    $totalUnservedRp = $res->sum("TotalUnserved");

    $percentage = $totalUnservedRp != 0 ? ($totalUnservedRp / $totalOrderRp) * 100 : 0;

     $result = [
      'data' => $res,
      'sfa_total_order' => $SfaTotal,
      'sfa_success_order' => $SfaTotalSuccess,
      'sfa_unserved_order' => $SfaTotalUnserved,
      'total_order_rp' => $totalOrderRp,
      'total_success_rp' => $totalSuccessRp,
      'total_unserved_rp' => $totalUnservedRp,
      'percentage' => $percentage
    ];


    return $result;
  }

  public function served_png($itemCode)
  {
    $get = PngLines::where('ItemCode',$itemCode)
                   ->where('Quantity','!=',0)
                   ->sum('Quantity');
    
    return $get;
  }

  public function served_mix($itemCode)
  {
    $get = MixLines::where('ItemCode',$itemCode)
                   ->where('Quantity','!=',0)
                   ->sum('Quantity');
    
    return $get;
  }

  public function getItemDetail($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/getItemDetail';
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

  public function usedCn($docnum)
  {
    return DeliveryVoucher::where('DocNumDelivery',$docnum)
                          ->sum('BalanceDue');
  }

  public function sapUnServedPng($post) {
    $url = 'https://saplta.laut-timur.tech/api/getUnservedOrder';

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

    // return collect($data['data'])->transform(fn($item) => [
    //   'DocDate' => $item['Tgl Order'], 
    //   'RefSo' => $item['Document Number'], 
    //   'RefNumber' => $item['No SFA'], 
    //   'CardCode' => $item['Card Code'], 
    //   'CardName' => $item['Card Name'], 
    //   'DocDate' => $item['Tgl Order'], 
    // ]);

    return $data;
  }

  public function cek_penjualan_search($data)
  {
    $endPoint = 'getPenjualanData';
    $data = callSapApiLtaWithPost($endPoint, json_encode($data));

    $res = collect($data)->transform(function($item) {
      return [
        'DocNumSO'=> $item['DocNumSO'],
        'DocDateSO'=> $this->expDate($item['DocDateSO']),
        'DocNumDO'=> $item['DocNumDO'],
        'DocStatusDO'=> $item['DocStatusDO'],
        'DocNumAR'=> $item['DocNumAR'],
        'DocStatusAR'=> $item['DocStatusAR'],
        'SlpName'=> $item['SlpName'],
        'PaymentGroup'=> $item['PaymentGroup'],
        'TopDays'=> $item['TopDays'],
        'CardCode'=> $item['CardCode'],
        'CardName'=> $item['CardName'],
        'NumAtCardSO'=> $item['NumAtCardSO'],
        'DocStatusSO'=> $item['DocStatusSO'],
        'DppSO'=> rupiahnon2($item['DppSO']),
        'TaxSO'=> rupiahnon2($item['TaxSO']),
        'DocDateDO'=> $this->expDate($item['DocDateDO']),
        'DppDO'=> rupiahnon2($item['DppDO']),
        'TaxDO'=> rupiahnon2($item['TaxDO']),
        'DocDateAR'=> $this->expDate($item['DocDateAR']),
        'DppAR'=> rupiahnon2($item['DppAR']),
        'TaxAR'=> rupiahnon2($item['TaxAR']),
        'DocNumCN'=> $item['DocNumCN'],
        'DocDateCN'=> $this->expDate($item['DocDateCN']),
        'DocStatusCN'=> $item['DocStatusCN'],
        'DppCN'=> rupiahnon2($item['DppCN']),
        'TaxCN'=> rupiahnon2($item['TaxCN']),
        'DocNumIP'=> $item['DocNumIP'],
        'TotalIP'=> $item['TotalIP'],
        'PlatMix' => $item['PlatMix'],
        'PlatPng' => $item['PlatPng']
      ];
    });

    $result = [
      'data' => $res
    ];

    return $result;
  }

	public function cek_penjualan_do_search($data)
  {
    $endPoint = 'getPenjualanDo';
    $data = callSapApiLtaWithPost($endPoint, json_encode($data));

		// dd($data);

    $res = collect($data)->transform(function($item) {
      return [
        'DocDateDO'=> $this->expDate($item['DocDateDO']),
        'DocNumDO'=> $item['DocNumDO'],
        'DocStatusDO'=> $item['DocStatusDO'],
        'DocNumAR'=> $item['DocNumAR'],
        'DocStatusAR'=> $item['DocStatusAR'],
        'SlpName'=> $item['SlpName'],
        'PaymentGroup'=> $item['PaymentGroup'],
        'TopDays'=> $item['TopDays'],
        'CardCode'=> $item['CardCode'],
        'CardName'=> $item['CardName'],
        'NumAtCardDO'=> $item['NumAtCardDO'],
        'DppDO'=> rupiahnon2($item['DppDO']),
        'TaxDO'=> rupiahnon2($item['TaxDO']),
        'DocDateAR'=> $this->expDate($item['DocDateAR']),
        'DppAR'=> rupiahnon2($item['DppAR']),
        'TaxAR'=> rupiahnon2($item['TaxAR']),
        'DocNumCN'=> $item['DocNumCN'],
        'DocDateCN'=> $this->expDate($item['DocDateCN']),
        'DocStatusCN'=> $item['DocStatusCN'],
        'DppCN'=> rupiahnon2($item['DppCN']),
        'TaxCN'=> rupiahnon2($item['TaxCN']),
        'DocNumIP'=> $item['DocNumIP'],
        'TotalIP'=> $item['TotalIP'],
        'PlatMix' => $item['PlatMix'],
        'PlatPng' => $item['PlatPng']
      ];
    });

    $result = [
      'data' => $res
    ];

		// dd($result);

    return $result;
  }


  public function expDate($date)
  {
    if($date!='')
    {
      $exp = explode(' ',$date);

      $result = $exp[0];
    }
    else
    {
      $result = '';
    }

    return $result;
  }

	public function storemaster_export($branch)
  {
		if (isset($branch)) 
		{
			$data = Storemaster::where('Branch',$branch)
												 ->orderBy('id','DESC')
												 ->get();
		}
		else
		{
			$data = Storemaster::orderBy('id','DESC')->get();
		}

    $res = collect($data)->transform(function($item) {
      return [
        'SOURCE_SYS_ID'=> $item['SOURCE_SYS_ID'],
				'FACT_TYPE_CODE'=> $item['FACT_TYPE_CODE'],
				'SBMSN_TYPE_CODE'=> $item['SBMSN_TYPE_CODE'],
				'DUE_PERD'=> date('Ymd',strtotime($item['DUE_PERD'])),
				'TIME_PERD_TYPE_CODE'=> $item['TIME_PERD_TYPE_CODE'],
				'TIME_PERD_START_DATE'=> date('Ymd',strtotime($item['TIME_PERD_START_DATE'])),
				'TRADE_CHANNEL_ID'=> $item['TRADE_CHANNEL_ID'],
				'CUST_ID'=> NULL,
				'LGCY_STORE_ID'=> $item['LGCY_STORE_ID'],
				'DIST_BRANCH_ID'=> $item['DIST_BRANCH_ID'],
				'SUBDISTR_STORE_FLAG'=> $item['SUBDISTR_STORE_FLAG'],
				'IN_COVERAGE_FLAG'=> $item['IN_COVERAGE_FLAG'],
				'STORE_STATUS_FLAG'=> $item['STORE_STATUS_FLAG'],
				'GOLDEN_STORE_FLAG'=> $item['GOLDEN_STORE_FLAG'],
				'STORE_NAME'=> $item['STORE_NAME'],
				'Street'=> $item['Street'],
				'STORE_ADDRESS_2'=> $item['Block'],
				'STORE_ADDRESS_3'=> $item['County'],
				'ZIP_CODE'=> $item['ZIP_CODE'],
				'GENERIC_TEXT_FIELD_1'=> $item['GENERIC_TEXT_FIELD_1'],
				'GENERIC_TEXT_FIELD_2'=> $item['GENERIC_TEXT_FIELD_2'],
				'GENERIC_TEXT_FIELD_3'=> $item['GENERIC_TEXT_FIELD_3'],
				'GENERIC_TEXT_FIELD_4'=> NULL,
				'GENERIC_TEXT_FIELD_5'=> NULL,
				'GENERIC_NUM_FIELD_1'=> $item['GENERIC_NUM_FIELD_1'],
				'GENERIC_NUM_FIELD_2'=> NULL,
				'GENERIC_NUM_FIELD_3'=> NULL,
				'GENERIC_NUM_FIELD_4'=> NULL,
				'GENERIC_NUM_FIELD_5'=> NULL,
				'GENERIC_NUM_FIELD_6'=> $item['GENERIC_NUM_FIELD_6'],
				'U_TIERTOKO'=> $item['U_TIERTOKO'],
				'CITY_GEO'=> $item['CITY_GEO'],
				'SELLER_ID'=> $item['SELLER_ID'],
				'LATITUDE'=> $item['LATITUDE'],
				'LONGITUDE'=> $item['LONGITUDE'],
				'GENERIC_NUM_FIELD_12'=> NULL,
				'GENERIC_NUM_FIELD_13'=> NULL,
				'U_STOREATTRIBUTE'=> $item['U_STOREATTRIBUTE'],
				'GENERIC_NUM_FIELD_15'=> NULL,
				'GENERIC_NUM_FIELD_16'=> NULL,
				'GENERIC_NUM_FIELD_17'=> NULL,
				'GENERIC_NUM_FIELD_18'=> NULL,
				'GENERIC_NUM_FIELD_19'=> NULL,
				'GENERIC_NUM_FIELD_20'=> NULL,
				'U_PASAR'=> $item['U_PASAR'],
				'U_SELLERTYPE'=> $item['U_SELLERTYPE'],
				'U_NAMAPASAR'=> $item['U_NAMAPASAR'],
				'GENERIC_NUM_FIELD_24'=> NULL,
				'OUTER'=> $item['OUTER']
      ];
    });

    $result = [
      'data' => $res
    ];

    return $result;
  }

	public function getOrderCut($body)
	{
		$arrayErp = $this->orderCutErp($body);

		// dd($arrayErp);
		
		$arraySap = callSapApiLtaWithPost('getOrderCut',json_encode($body));

		$row = [];

		foreach ($arraySap as $key => $value) 
		{
			$whs = Warehouse::where('code',$value['Branch'])->first();

			$unitMsr = $value['U_CGROUP']=='0' ? 'CS' : 'IT';

			if ($unitMsr=='CS') 
			{
				$orderCut = $value['Selisih'] / $value['U_NISIB'];
				$harga = $orderCut * $value['HargaKtn'];
			}
			else
			{
				$orderCut = $value['Selisih'];
				$harga = $orderCut * $value['HargaPcs'];
			}

			$row[] = [
				'3069' => '3069',
				'OC' => 'OC',
				'N' => 'N',
				'DATE_1' => date('Ymd',strtotime($value['DocDate'])),
				'DAY' => 'DAY',
				'DATE_2' => date('Ymd',strtotime($value['DocDate'])),
				'STOREID' => $value['CardCode'],
				'GCAS' => $value['U_CSN'],
				'GENERIC_NUM_FIELD_1'=> NULL,
				'GENERIC_NUM_FIELD_2'=> NULL,
				'GENERIC_NUM_FIELD_3'=> NULL,
				'GENERIC_NUM_FIELD_4'=> NULL,
				'1' => '1',
				'GENERIC_NUM_FIELD_5'=> NULL,
				'IDR' => 'IDR',
				'GENERIC_NUM_FIELD_6'=> NULL,
				'GENERIC_NUM_FIELD_7'=> NULL,
				'GENERIC_NUM_FIELD_8'=> NULL,
				'GENERIC_NUM_FIELD_9'=> NULL,
				'GENERIC_NUM_FIELD_10'=> NULL,
				'11'=> '11',
				'STPCODE_1'=> $whs->shipment,
				'13' => '13',
				'STPCODE_2'=> $whs->shipment,
				'GENERIC_NUM_FIELD_11'=> NULL,
				'GENERIC_NUM_FIELD_12'=> NULL,
				'GENERIC_NUM_FIELD_13'=> NULL,
				'UnitMsr' => $unitMsr,
				'Harga' => $harga,
				'22' => '22',
				'OrderCut' => $orderCut,
				'0' => '0'
			];
		}

		// dd($row);

		$result = [
			'data' => $row
		];

		return $result;
	}

	public function orderCutErp($body)
	{
		if ($body['Branch']=='ALL') 
		{
			$row = DB::table('view_oc_png')
							 ->whereBetween('DocDate', [$body['DateFrom'], $body['DateTo']])
							 ->where('Selisih','!=',0)
							 ->get();
		}
		else
		{
			$row = DB::table('view_oc_png')
							 ->whereBetween('DocDate', [$body['DateFrom'], $body['DateTo']])
							 ->where('Branch',$body['Branch'])
							 ->where('Selisih','!=',0)
							 ->get();
		}

		if (count($row) > 0) 
		{
			foreach ($row as $key => $value) 
			{
				$data[] = [
					'DocDate' => $value->DocDate,
					'DocNum' => $value->DocNum,
					'Branch' => $value->Branch,
					'NumAtCard' => $value->NumAtCard,
					'CardCode' => $value->CardCode,
					'ItemCode' => $value->ItemCode,
					'ItemName' => $value->ItemName,
					'UnitMsr' => $value->UnitMsr,
					'Selisih' => $value->Selisih
				];
			}
		}
		else
		{
			$data = [];
		}

		return $data;
	}

	public function mergeAndSumQuantities($arr1, $arr2) 
	{
		$mergedArray = [];
    // Menggabungkan kedua array
    foreach ($arr1 as $item1) {
			$tempArray = $item1;
			foreach ($arr2 as $item2) {
				// Mengecek apakah kriteria yang diberikan sama
				if ($item1["ItemCode"] === $item2["ItemCode"] && $item1["CardCode"] === $item2["CardCode"] && $item1["DocDate"] === $item2["DocDate"] && $item1["UnitMsr"] === $item2["UnitMsr"] && $item1["NumAtCard"] === $item2["NumAtCard"]) 
				{
					// Menjumlahkan nilai Selisih
					$tempArray["Selisih"] += intval($item2["Selisih"]);
				}
			}

			$mergedArray[] = $tempArray;
    }

    return $mergedArray;
	}
}