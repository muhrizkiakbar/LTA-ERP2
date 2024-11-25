<?php

namespace App\Services;

use App\Models\ArCollectorCheckin;
use App\Models\ArCollectorCN;
use App\Models\ArCollectorHeader;
use App\Models\ArCollectorLines;
use App\Models\ArTitip;

class CollectorServices 
{
  public function generateCollector($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/generateCollector';
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

  public function getDataAll()
  {
    $data = [];
    
    $row = ArCollectorHeader::orderBy('id','DESC')
                            ->where('users_collector_st','!=',2)
                            ->where('users_admin_st','!=',2)
                            ->get();

    foreach ($row as $value) 
    {
      $data[] = [
        'id' => $value->id,
        'kd' => $value->kd,
        'user' => isset($value->user_collector) ? $value->user_collector->name : '-',
        'branch' => $value->branch_code,
        'category' => $value->category,
        'date' => $value->date,
        'users_admin_st' => $value->users_admin_st,
        'toko' => $this->getToko($value->kd),
        'status' => $this->cekStatusCollector($value->users_collector_st),
        'company' => isset($value->company) ? $value->company->title : '',
        'st' => $value->users_collector_st
      ];
    }

    return $data;
  }

  public function getDataByUser($users_id)
  {
    $data = [];
    
    $row = ArCollectorHeader::where('users_admin_id',$users_id)
                            ->where('users_collector_st','!=',2)
                            ->where('users_admin_st','!=',2)
                            ->orderBy('id','DESC')
                            ->get();

    foreach ($row as $value) 
    {

      $data[] = [
        'id' => $value->id,
        'kd' => $value->kd,
        'user' => isset($value->user_collector) ? $value->user_collector->name : '-',
        'branch' => $value->branch_code,
        'category' => $value->category,
        'date' => $value->date,
        'users_admin_st' => $value->users_admin_st,
        'toko' => $this->getToko($value->kd),
        'status' => $this->cekStatusCollector($value->users_collector_st),
        'company' => isset($value->company) ? $value->company->title : '-',
        'st' => $value->users_collector_st
      ];
    }

    return $data;
  }

  public function getDataByBranch($branch)
  {
    $data = [];
    
    $row = ArCollectorHeader::whereIn('branch_code',$branch)
                            ->where('users_collector_st','!=',2)
                            ->where('users_admin_st','!=',2)
                            ->orderBy('id','DESC')
                            ->get();

    foreach ($row as $value) 
    {

      $data[] = [
        'id' => $value->id,
        'kd' => $value->kd,
        'user' => $value->user_collector->name,
        'branch' => $value->branch_code,
        'category' => $value->category,
        'date' => $value->date,
        'users_admin_st' => $value->users_admin_st,
        'toko' => $this->getToko($value->kd),
        'status' => $this->cekStatusCollector($value->users_collector_st),
        'company' => $value->company->title,
        'st' => $value->users_collector_st
      ];
    }

    return $data;
  }

  public function getDataDetail($id)
  {
    $data = [];

    $row = ArCollectorLines::where('ar_collector_header_kd',$id)
                           ->groupBy('CardCode')
                           ->get();

    foreach ($row as $value) 
    {
      $lines = $this->getDataLines($value->ar_collector_header_kd,$value->CardCode);
      $cn = $this->getDataCNAll($value->CardCode);
      $titip = $this->getDataTitip($value->ar_collector_header_kd,$value->CardCode);
      $active = $this->getDataLinesPay($value->ar_collector_header_kd,$value->CardCode);

      $data[] = [
        'CardCode' => $value->CardCode,
        'CardCode2' => str_replace('.','-',$value->CardCode),
        'CardName' => $value->CardName,
        'Alamat' => $value->Alamat,
        'Lines' => $lines['lines'],
        'TotalInv' => $lines['total_inv'],
        'TotalPrice' => $lines['total_price'],
        'CN' => $cn,
        'titip' => $titip,
        'status' => $active!=0 ? '<span class="badge badge-info">On Progress</span>' : ''
      ];
    }

    return $data;
  }

  public function reportSerahTerima($branch,$user,$date)
  {
    $data = [];

    if (empty($user)) 
    {
      $row = ArCollectorHeader::where('branch_code',$branch)
                            ->where('date',$date)
                            ->get();
    }
    else
    {
      $row = ArCollectorHeader::where('branch_code',$branch)
                            ->where('users_collector_id',$user)
                            ->where('date',$date)
                            ->get();
    }

    

    foreach ($row as $key => $value) 
    {
      $detail = $this->getSerahTerimaDetail($value->kd);

      $tagihan_cash = $this->getTagihan($value->kd,'cash');
      $tagihan_bg = $this->getTagihan($value->kd,'bg');
      $tagihan_tf = $this->getTagihan($value->kd,'tf');
      $total = $tagihan_cash + $tagihan_bg + $tagihan_tf;
      $used_cn = $this->getTagihanCN($value->kd);
      $grand_total = $total - $used_cn;

      $data[] = [
        'kd' => $value->kd,
        'branch' => $value->branch_code,
        'collector' => $value->user_collector->name,
        'date' => $value->date,
        'customer_data' => $detail,
        'tagihan_cash' => $tagihan_cash,
        'tagihan_bg' => $tagihan_bg,
        'tagihan_tf' => $tagihan_tf,
        'used_cn' => $used_cn,
        'grand_total' => $grand_total,
        'kd' => $value->kd
      ];
    }

    return $data;
  }

  public function reportRekapPenagihan($branch,$date)
  {
    $data = [];

    $row = ArCollectorHeader::where('branch_code',$branch)
                          ->where('date',$date)
                          ->get();

    foreach ($row as $key => $value) 
    {
      $tagihan_cash = $this->getTagihan($value->kd,'cash');
      $tagihan_bg = $this->getTagihan($value->kd,'bg');
      $tagihan_tf = $this->getTagihan($value->kd,'tf');
      $total = $tagihan_cash + $tagihan_bg + $tagihan_tf;
      $used_cn = $this->getTagihanCN($value->kd);
      $grand_total = $total - $used_cn;

      $data[] = [
        'branch' => $value->branch_code,
        'collector' => $value->user_collector->name,
        'date' => $value->date,
        'tagihan_cash' => $tagihan_cash,
        'tagihan_bg' => $tagihan_bg,
        'tagihan_tf' => $tagihan_tf,
        'used_cn' => $used_cn,
        'grand_total' => $grand_total
      ];
    }

    return $data;
  }

  public function reportComplianceCollector($branch,$date)
  {
    $data = [];

    $row = ArCollectorHeader::where('branch_code',$branch)
                          ->where('date',$date)
                          ->get();

    foreach ($row as $key => $value) 
    {
      $progress = $this->getProgress($value->kd);
      $tagihan_cash = $this->getTagihan($value->kd,'cash');
      $tagihan_bg = $this->getTagihan($value->kd,'bg');
      $tagihan_tf = $this->getTagihan($value->kd,'tf');
      $total = $tagihan_cash + $tagihan_bg + $tagihan_tf;
      $used_cn = $this->getTagihanCN($value->kd);
      $grand_total = $total - $used_cn;
      $invoiceDone = $this->getInvoiceDone($value->kd);

      $total_tagihan = $this->getTagihanTotal($value->kd);
      $pending = $total_tagihan['payment_total'] - $progress['total_done'];
      $pending_invoice = $total_tagihan['invoice_total'] - $invoiceDone;

      $data[] = [
        'branch' => $value->branch_code,
        'collector' => $value->user_collector->name,
        'date' => $value->date,
        'tagihan_cash' => $tagihan_cash,
        'tagihan_bg' => $tagihan_bg,
        'tagihan_tf' => $tagihan_tf,
        'used_cn' => $used_cn,
        'grand_total' => $grand_total,
        'progress' => $progress,
        'pending' => $pending,
        'pending_invoice' => $pending_invoice,
        'total_tagihan' => $total_tagihan['payment_total'],
        'total_invoice' => $total_tagihan['invoice_total'],
        'invoice_done' => $invoiceDone,
        'company' => $value->company->title
      ];
    }

    return $data;
  }

  public function reportPerformance($user_collector,$date1,$date2)
  {
    $data = [];

    $row = ArCollectorHeader::where('users_collector_id',$user_collector)
                          ->where('date','>=',$date1)
                          ->where('date','<=',$date2)
                          ->get();
    // dd($row);

    foreach ($row as $key => $value) 
    {
      $progress = $this->getProgress($value->kd);
      $tagihan_cash = $this->getTagihan($value->kd,'cash');
      $tagihan_bg = $this->getTagihan($value->kd,'bg');
      $tagihan_tf = $this->getTagihan($value->kd,'tf');
      $total = $tagihan_cash + $tagihan_bg + $tagihan_tf;
      $used_cn = $this->getTagihanCN($value->kd);
      $grand_total = $total - $used_cn;
      $invoiceDone = $this->getInvoiceDone($value->kd);

      $total_tagihan = $this->getTagihanTotal($value->kd);
      $pending = $total_tagihan['payment_total'] - $progress['total_done'];
      $pending_invoice = $total_tagihan['invoice_total'] - $invoiceDone;

      $data[] = [
        'branch' => $value->branch_code,
        'collector' => $value->user_collector->name,
        'date' => $value->date,
        'tagihan_cash' => $tagihan_cash,
        'tagihan_bg' => $tagihan_bg,
        'tagihan_tf' => $tagihan_tf,
        'used_cn' => $used_cn,
        'grand_total' => $grand_total,
        'progress' => $progress,
        'progress_percent' => $progress['percent'],
        'pending' => $pending,
        'pending_invoice' => $pending_invoice,
        'total_tagihan' => $total_tagihan['payment_total'],
        'total_invoice' => $total_tagihan['invoice_total'],
        'invoice_done' => $invoiceDone,
        'company' => $value->company->title
      ];
    }

    $total_tagihan_all = array_sum(array_column($data,'total_tagihan'));
    $grand_total_all = array_sum(array_column($data,'grand_total'));

    $progress_all = array_sum(array_column($data,'progress_percent'));
    $count_all = count($data);

    $percent_avg = $progress_all / $count_all;

    $result = [
      'data' => $data,
      'percent_avg' => round($percent_avg,2),
      'total_tagihan_all' => $total_tagihan_all,
      'grand_total_all' => $grand_total_all
    ];

    return $result;
  }

  public function getSerahTerimaDetail($id)
  {
    $data = [];

    $row = ArCollectorLines::where('ar_collector_header_kd',$id)
                           ->groupBy('CardCode')
                           ->get();

    foreach ($row as $value) 
    {
      $lines = $this->getDataLines2($value->ar_collector_header_kd,$value->CardCode);
      $cn = $this->getDataCNAll2($value->ar_collector_header_kd,$value->CardCode);

      $data[] = [
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'Alamat' => $value->Alamat,
        'Lines' => $lines['lines'],
        'TotalInv' => $lines['total_inv'],
        'TotalPrice' => $lines['total_price'],
        'CN' => $cn,
      ];
    }

    return $data;
  }

  public function getDataLines($kd,$cardCode)
  {
    $data = [];

    $total = 0;

    $row = ArCollectorLines::where('ar_collector_header_kd',$kd)
                           ->where('CardCode',$cardCode)
                           ->orderBy('DocDueDate','ASC')
                           ->get();

    $header = ArCollectorHeader::where('kd',$kd)->first();

    foreach ($row as $value) 
    {
      $cekBg = $this->cekAlreadyPaymentByType($value->DocNum,$kd,'bg');
      $cekCash = $this->cekAlreadyPaymentByType($value->DocNum,$kd,'cash');

      if ($cekBg['count']==1) 
      {
        $sts = '1';
        $payment = 'Already Paid on BG '.$cekBg['date'];
      }
      else if ($cekCash['count']==1) 
      {
        if ($header->category=='SALES') 
        {
          $sts = '1';
          $payment = 'Already Paid on CASH '.$cekCash['date'];
        }
        else
        {
          $sts = $value->status;
          $pay = !empty($value->Payment) ? $value->Payment : 0;
          $payment = rupiahnon2($pay);
        }
      }
      else
      {
        $sts = $value->status;
        $pay = !empty($value->Payment) ? $value->Payment : 0;
        $payment = rupiahnon2($pay);
      }

      $total += $value->Netto;

      $data[] = [
        'DocNum' => $value->DocNum,
        'DocDueDate' => $value->DocDueDate,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'Price' => $value->Balance,
        'Payment' => $payment,
        'Balance' => $value->Balance - $value->Payment,
        'sts' => $sts,
        'type' => isset($value->Type) ? $value->Type : ''
      ];
    }

    $result = [
      'lines' => $data,
      'total_price' => $total,
      'total_inv' => count($data)
    ];

    return $result;
  }

  public function getDataLines2($kd,$cardCode)
  {
    $data = [];

    $total = 0;

    $row = ArCollectorLines::where('ar_collector_header_kd',$kd)
                           ->where('CardCode',$cardCode)
                           ->orderBy('DocDueDate','ASC')
                           ->get();

    $header = ArCollectorHeader::where('kd',$kd)->first();

    foreach ($row as $value) 
    {
      $cekBg = $this->cekAlreadyPaymentByType($value->DocNum,$kd,'bg');
      $cekCash = $this->cekAlreadyPaymentByType($value->DocNum,$kd,'cash');

      if ($cekBg['count']==1) 
      {
        $sts = 'BG ('.$cekBg['date'].')';
        $type = 'Already Paid';
        $partial = '';
      }
      elseif ($cekCash['count']==1) 
      {
        if ($header->category=='SALES') 
        {
          $sts = 'CASH ('.$cekCash['date'].')';
          $type = 'Already Paid';
          $partial = '';
        }
        else
        {
          $sts = $value->status==1 ? 'Paid' : 'Pending';
          $type = isset($value->Type) ? $value->Type : 'Pending';
          $partial = $value->partial==1 ? ' (Partial)' : '';
        }
      }
      else
      {
        $sts = $value->status==1 ? 'Paid' : 'Pending';
        $type = isset($value->Type) ? $value->Type : 'Pending';
        $partial = $value->partial==1 ? ' (Partial)' : '';
      }

      $total += $value->Netto;

      $data[] = [
        'DocNum' => $value->DocNum,
        'DocDueDate' => $value->DocDueDate,
        'Referensi' => $value->NumAtCard,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'Price' => $value->Balance,
        'Payment' => !empty($value->Payment) ? $value->Payment : 0,
        'Balance' => $value->Netto - $value->Payment,
        'sts' => $sts.$partial,
        'type' => $type
      ];
    }

    $result = [
      'lines' => $data,
      'total_price' => $total,
      'total_inv' => count($data)
    ];

    return $result;
  }

  public function getDataLinesPay($kd,$cardCode)
  {
    $row = ArCollectorLines::where('ar_collector_header_kd',$kd)
                           ->where('CardCode',$cardCode)
                           ->where('status','1')
                           ->orderBy('DocDueDate','ASC')
                           ->get();

    return count($row);
  }

  public function getDataCN($cardCode)
  {
    $data = [];

    $row = ArCollectorCN::where('CardCode',$cardCode)
                        ->where('status',0)
                        ->get();
    
    foreach ($row as $value) 
    {
      $data[] = [
        'DocEntry' => $value->DocEntry,
        'DocNum' => $value->DocNum,
        'DocDate' => $value->DocDate,
        'DocDueDate' => $value->DocDueDate,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'NumAtCard' => $value->NumAtCard,
        'Balance' => $value->DocTotal - $value->PaidToDate,
        'sts' => $value->status
      ];
    }

    return $data;
  }

  public function getDataCNAll($cardCode)
  {
    $data = [];

    $row = ArCollectorCN::where('CardCode',$cardCode)
                        ->orderBy('DocDate','ASC')
                        ->get();
    
    foreach ($row as $value) 
    {
      $data[] = [
        'DocEntry' => $value->DocEntry,
        'DocNum' => $value->DocNum,
        'DocDate' => $value->DocDate,
        'DocDueDate' => $value->DocDueDate,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'NumAtCard' => $value->NumAtCard,
        'Balance' => $value->DocTotal - $value->PaidToDate,
        'sts' => $value->status
      ];
    }

    return $data;
  }

  public function getDataCNAll2($kd,$cardCode)
  {
    $data = [];

    $row = ArCollectorCN::where('CardCode',$cardCode)
                        ->where('ar_collector_header_kd',$kd)
                        ->orderBy('DocDate','ASC')
                        ->get();
    
    foreach ($row as $value) 
    {
      $data[] = [
        'DocEntry' => $value->DocEntry,
        'DocNum' => $value->DocNum,
        'DocDate' => $value->DocDate,
        'DocDueDate' => $value->DocDueDate,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'NumAtCard' => $value->NumAtCard,
        'Balance' => $value->DocTotal - $value->PaidToDate,
        'sts' => $value->status
      ];
    }

    return $data;
  }

  public function getDataTitip($kd,$cardCode)
  {
    $data = [];

    $row = ArTitip::where('CardCode',$cardCode)
                        ->where('ar_collector_header_kd',$kd)
                        ->orderBy('DocDate','ASC')
                        ->get();
    
    foreach ($row as $value) 
    {
      $data[] = [
        'DocEntry' => $value->DocEntry,
        'DocNum' => $value->DocNum,
        'DocDate' => $value->DocDate,
        'DocDueDate' => $value->DocDueDate,
        'CardCode' => $value->CardCode,
        'CardName' => $value->CardName,
        'NumAtCard' => $value->NumAtCard,
        'Balance' => $value->Balance,
        'sts' => $value->status
      ];
    }

    return $data;
  }

  public function getProgress($id)
  {
    $tagihan_done = $this->getTagihanDone($id);
    $tagihan_total = $this->getTagihanTotal($id);
    
    if ($tagihan_done==0) 
    {
      $percent = 0;
    }
    else
    {
      $percent = ($tagihan_done / $tagihan_total['payment_total']) * 100;
    }
    
    $data = [
      'percent' => round($percent,2)." %",
      'total_done' => $tagihan_done,
      'total_invoice' => $tagihan_total['invoice_total'],
      'total_tagihan' => $tagihan_total['payment_total']
    ];

    return $data;
  }

  public function getTagihanDone($id)
  {
    $done = 0;
    $done = ArCollectorLines::where('ar_collector_header_kd',$id)
                            ->where('status',1)
                            ->sum('Payment');
    
    return $done;
  }

  public function getInvoiceDone($id)
  {
    $done = 0;
    $done = ArCollectorLines::where('ar_collector_header_kd',$id)
                            ->where('status',1)
                            ->count();
    
    return $done;
  }

  public function getTagihanTotal($id)
  {
    $total = 0;

    $done = ArCollectorLines::where('ar_collector_header_kd',$id)
                            ->get();

    foreach ($done as $value) 
    {
      $cekBg = $this->cekAlreadyPaymentByType($value->DocNum, $id, 'bg');
      
      if ($cekBg['count'] > 0)
      {
        $payment = 0;
        $invoice = 0;
      }
      else
      {
        $payment = $value->Balance;
        $invoice = 1;
      }

      $data[] = [
        'payment' => $payment,
        'invoice' => $invoice
      ];
    }

    $result = [
      'payment_total' => array_sum(array_column($data,'payment')),
      'invoice_total' => array_sum(array_column($data,'invoice'))
    ];

    return $result;
  }

  public function getToko($id)
  {
    $done = ArCollectorLines::where('ar_collector_header_kd',$id)
                            ->groupBy('CardCode')
                            ->get();
                            
    return count($done);
  }

  public function updateCollectorCN($company,$title_api,$cardCode)
  {
    $data = [];

    $post = [
      'CardCode' => $cardCode
    ];

    $row = callApiWithPost($company,$title_api,json_encode($post));

    $cek = ArCollectorCN::where('CardCode',$cardCode)
                        ->where('status',0)
                        ->get();

    if($cek->isEmpty())
    {
      foreach ($row as $value) 
      {
        $data[] = [
          'DocEntry' => $value['DocEntry'],
					'DocNum' => $value['DocNum'],
					'DocDate' => $value['DocDate'],
					'DocDueDate' => $value['DocDueDate'],
					'CardCode' => $value['CardCode'],
					'CardName' => $value['CardName'],
					'NumAtCard' => $value['NumAtCard'],
					'DocTotal' => $value['DocTotal'],
          'PaidToDate' => $value['PaidToDate'],
          'BalanceDue' => $value['DocTotal'] - $value['PaidToDate']
        ];
      }
    }
    else
    {
      foreach ($row as $value) 
      {
        if ($cek->where('DocEntry',$value['DocEntry'])->isEmpty()) 
        {
          $data[] = [
            'DocEntry' => $value['DocEntry'],
            'DocNum' => $value['DocNum'],
            'DocDate' => $value['DocDate'],
            'DocDueDate' => $value['DocDueDate'],
            'CardCode' => $value['CardCode'],
            'CardName' => $value['CardName'],
            'NumAtCard' => $value['NumAtCard'],
            'DocTotal' => $value['DocTotal'],
            'PaidToDate' => $value['PaidToDate'],
            'BalanceDue' => $value['DocTotal'] - $value['PaidToDate']
          ];
        }
      }
    }

    return ArCollectorCN::insert($data);
  }

  public function generateCollectorCN($post)
  {
    $url = 'https://saplta.laut-timur.tech/api/generateCollectorCN';
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

  public function notifikasiFirebase($post)
  {
    $url = 'https://fcm.googleapis.com/fcm/send';
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
          "authorization: key=AAAAZmJ9Kbo:APA91bFaXfyAGAo2LExPMZiaeCiPtybp16IQq8X7vjvFwlfIlVcbCHl4k9aJT21ZV3vbOHIeIebQNwqYzhoGlpW3a4aoHMzqygwaIIV3wjeQbkK4MyCrhhZWOsM3uI_usti8LwDuTj93"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
  }

  public function getTagihan($kd, $type)
  {
    $arr = ArCollectorLines::where('ar_collector_header_kd',$kd)
                           ->where('Type',$type)
                           ->sum('payment');

    return $arr;
  }

  public function getTagihanCN($kd)
  {
    $arr = ArCollectorCN::where('ar_collector_header_kd',$kd)
                           ->where('status',1)
                           ->sum('BalanceDue');

    return $arr;
  }

  public function cekAlreadyPaymentByType($docNum, $kd , $type)
  {
    $get = ArCollectorLines::where('DocNum',$docNum)
                           ->where('Type',$type)
                           ->where('ar_collector_header_kd','!=',$kd)
                           ->where('status',1)
                           ->get();
    
    if (count($get) > 0) 
    {
      foreach ($get as $key => $value) 
      {
        $date = $this->getPaymentDate($value->ar_collector_header_kd);
      }
    }
    else
    {
      $date = '';
    }

    $result = [
      'count' => count($get),
      'date' => $date
    ];

    return $result;
  }

  public function getPaymentDate($kd)
  {
    $get = ArCollectorHeader::where('kd',$kd)->first();
    $date = $get->date;

    return $date;
  }

  public function cekStatusCollector($value)
  {
    if ($value==0) 
    {
      return '<span class="badge badge-info">Open</span>';
    }
    else if ($value==1)
    {
      return '<span class="badge badge-primary">Progress</span>';
    }
    else
    {
      return '<span class="badge badge-danger">Close</span>';
    }
  }

  // public function closeProject($kd)
  // {
    
  // }

  public function trackCollector($collector, $date)
  {
    $data = [];
    $get = ArCollectorCheckin::whereDate('datetime',$date)
                             ->where('users_id',$collector)
                             ->where('type','IN')
                             ->get();
    
    foreach ($get as $key => $value) 
    {
      $out = $this->trackCollectorOut($value->ar_collector_header_kd,$value->users_id,$value->CardCode);
      $call = $this->trackCollectorCall($value->ar_collector_header_kd,$value->users_id,$value->CardCode);

      $time_in = date('H:i:s',strtotime($value->datetime));
      $time_out = $out['time_out'];
      if (!empty($time_out)) 
      {
        $selisih = durasi_waktu($time_in, $time_out);

        $filex = str_replace('public/','',$value->file);
        $file = 'http://36.93.82.10/erp-api-lta/storage/'.$filex;
        

        $data[] = [
          'CardCode' => $value->CardCode,
          'CardName' => $value->CardName,
          'time_in' => $time_in,
          'time_out' => $time_out,
          'file' => $file,
          'durasi' => $selisih,
          'call' => rupiah($call)
        ];
      }
      else
      {
        $data[] = [
          'CardCode' => $value->CardCode,
          'CardName' => $value->CardName,
          'time_in' => $time_in,
          'time_out' => '-',
          'file' => '',
          'durasi' => '-',
          'call' => ''
        ];
      }
    }

    return $data;
  }

  public function trackCollectorOut($kd, $user, $cardCode)
  {
    $time = '';

    $get = ArCollectorCheckin::where('ar_collector_header_kd',$kd)
                             ->where('users_id',$user)
                             ->where('CardCode',$cardCode)
                             ->where('type','OUT')
                             ->limit(1)
                             ->get();

    if (count($get) > 0) 
    {
      foreach ($get as $key => $value) 
      {
        $time = date('H:i:s',strtotime($value->datetime));
      }
    }

    $result = [
      'time_out' => $time,
    ];

    return $result;
  }

  public function trackCollectorCall($kd, $user, $cardCode)
  {
    $get = 0;
    $get = ArCollectorLines::where('ar_collector_header_kd',$kd)
                           ->where('users_collector_id',$user)
                           ->where('CardCode',$cardCode)
                           ->sum('Payment');
    return $get;
  }
}