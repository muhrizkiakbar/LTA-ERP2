<?php

namespace App\Services;

use App\Models\GpsCompliance;

class GpsComplianceServices
{
  public function sync_png($post)
  {
    $url = 'http://png.laut-timur.com/sfaerp/api/cekGpsCompliance';
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

  public function cek($hash)
  {
    return GpsCompliance::where('hash',$hash)->get();
  }

  public function insert($data)
  {
    return GpsCompliance::create($data);
  }

  public function data($tgl1, $tgl2, $spv)
  {
    $get = GpsCompliance::where('visit_date','>=',$tgl1)
                        ->where('visit_date','<=',$tgl2)
                        ->where('supervisor_code',$spv)
                        ->orderBy('visit_date','ASC')
                        ->get();
    return $get;
  }
}