<?php

use App\Models\Branch;
use App\Models\CompanyApi;
use App\Models\Customer;
use App\Models\MixHeader;
use App\Models\OrderHeader;
use App\Models\Sales;
use App\Models\SalesWarehouse;
use App\Models\Warehouse;

function format_hari_indo($waktu)
{
    $hari_array = array(
        'MINGGU',
        'SENIN',
        'SELASA',
        'RABU',
        'KAMIS',
        'JUMAT',
        'SABTU'
    );
    $hr = date('w', strtotime($waktu));
    $hari = $hari_array[$hr];

    return $hari;
}

function countSfaSales()
{
  $role = auth()->user()->users_role_id;
  $branchx = auth()->user()->branch_sap;

  if ($role==1) 
  {
    $get = MixHeader::whereNull('DocNum')->get();
  }
  else
  {
    $get = MixHeader::whereNull('DocNum')->where('Branch',$branch)->get();
  }

  return count($get);
}

function getCustomerDetail($code)
{
  return Customer::where('code',$code)->first();
}

function getBranchDetail($id)
{
  return Branch::find($id);
}

function getBranchDetail2($id)
{
  return Branch::where('BPLId',$id)->first();
}

function getWarehouseDetail($code,$slp)
{
  // $user_id = auth()->user()->users_role_id;
  if ($code == "BJM") 
  {
    $cek = SalesWarehouse::where('SalesPersonCode',$slp)->first();
    if(isset($cek))
    {
      $warehouse = $cek->WarehouseCode;
    }
    else
    {
      $get = Warehouse::where('code',$code)->first();
      $warehouse = $get->title;
    }
  }
  else
  {
    $get = Warehouse::where('code',$code)->first();
    $warehouse = $get->title;
  }

  return $warehouse;
}

function getWarehouseDetail2($code)
{
  $get = Warehouse::where('code',$code)->first();
  
  return $get;
}

function rupiah($angka) {
  $hasil = 'IDR ' . number_format($angka, 2, ",", ".");
  return $hasil;
}

function rupiahnon($angka) {
  $hasil = number_format($angka, 0, ",", ".");
  return $hasil;
}

function rupiahnon2($angka) {
  $hasil = number_format($angka, 2, ",", ".");
  return $hasil;
}

function rupiahnon3($angka) {
  $hasil = number_format($angka, 2, ".", "");
  return $hasil;
}

function penyebut($nilai) {
  $nilai = abs($nilai);
  $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
  $temp = "";
  if ($nilai < 12) {
    $temp = " ". $huruf[$nilai];
  } else if ($nilai <20) {
    $temp = penyebut($nilai - 10). " belas";
  } else if ($nilai < 100) {
    $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
  } else if ($nilai < 200) {
    $temp = " seratus" . penyebut($nilai - 100);
  } else if ($nilai < 1000) {
    $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
  } else if ($nilai < 2000) {
    $temp = " seribu" . penyebut($nilai - 1000);
  } else if ($nilai < 1000000) {
    $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
  } else if ($nilai < 1000000000) {
    $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
  } else if ($nilai < 1000000000000) {
    $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
  } else if ($nilai < 1000000000000000) {
    $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
  }     
  return $temp;
}

function group_by($key, $data) {
  $result = array();

  foreach($data as $val) {
      if(array_key_exists($key, $val)){
          $result[$val[$key]][] = $val;
      }else{
          $result[""][] = $val;
      }
  }

  return $result;
}

function grupArray($key, $data)
{
  $grouped_array = array();
  foreach ($data as $element) 
  {
    $grouped_array[$element[$key]][] = $element;
  }

  return $grouped_array;
}

function dateExp($data)
{
  $exp = explode(' ',$data,);
  $date = date('d-M-Y',strtotime($exp[0]));
  return $date;
}

function dateExp2($data)
{
  $exp = explode(' ',$data,);
  $date = date('Y-m-d',strtotime($exp[0]));
  return $date;
}

function dateExp3($data)
{
  $exp = explode(' ',$data,);
  $date = date('Ym',strtotime($exp[0]));
  return $date;
}

function getAvailable($post)
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

function getInStock($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getInStock';
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

function getSalesEmployee()
{
  $url = 'https://saplta.laut-timur.tech/api/getSalesEmployee';
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

function getSalesEmployeeId($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getSalesEmployeeId';
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

function getUserId($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getUserId';
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

function getVendor()
{
  $url = 'https://saplta.laut-timur.tech/api/getVendor';
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

function getCustomerId($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getCustomerId2';
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

function getCustomerId2($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getCustomerId2';
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

function getCustomerName($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getCustomerName';
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

function getItemId($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getItemId';
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

function getDocNumSO($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getDocNumSO';
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

function getSalesDetail($post)
{
  $url = 'https://saplta.laut-timur.tech/api/getSalesDetail';
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

function getUomDetail($post)
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

function getUomEntry($post)
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

function callApiLogin($body,$url)
{
  $post = json_encode($body);

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,// your preferred link
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_TIMEOUT => 30000,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
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

function callApiLogout()
{
  $url = 'https://192.168.1.81:50000/b1s/v1/Logout';
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,// your preferred link
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_TIMEOUT => 30000,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
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

function checkPeriod($post)
{
  $url = 'https://saplta.laut-timur.tech/api/checkPeriod';
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


function getSfaSupervisorPng($post)
{
  $url = 'http://png.laut-timur.com/sfaerp/api/getSpv';
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

function callApiWithPost($company,$title,$post)
{
  $get = CompanyApi::where('company_id',$company)
                   ->where('title',$title)
                   ->first();

  $url = $get->url;
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

function callSapApiLtaWithPost($title,$post)
{
  $url = 'https://saplta.laut-timur.tech/api/'.$title;
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

function callSapApiLtaWithoutPost($title)
{
  $url = 'https://saplta.laut-timur.tech/api/'.$title;
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

function callSapApiTaaWithPost($title,$post)
{
  $url = 'https://saptaa.laut-timur.tech/api/'.$title;
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

function callApiWithoutPost($company,$title)
{
  $get = CompanyApi::where('company_id',$company)
                   ->where('title',$title)
                   ->first();

  $url = $get->url;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,// your preferred link
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_TIMEOUT => 30000,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
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

function selisih_waktu($jam_masuk, $jam_keluar) 
{ 
  $times = array($jam_masuk, $jam_keluar);
  $seconds = 0; 
  
  foreach($times as $time) 
  { 
    list( $g, $i, $s ) = explode(':', $time ); 
    $seconds += $g * 3600; 
    $seconds += $i * 60; 
    $seconds += $s; 
  } 
  
  $hours = floor( $seconds / 3600 ); 
  $seconds -= $hours * 3600; 
  $minutes = floor( $seconds / 60 ); 
  $seconds -= $minutes * 60; 

  return $hours.':'.$minutes.':'.$seconds;
}

function durasi_waktu($jam_masuk, $jam_keluar)
{
  list($h,$m,$s) = explode(':',$jam_masuk); 
  $dtAwal = mktime($h,$m,$s,'1','1','1'); 
  
  list($h,$m,$s) = explode(':',$jam_keluar); 
  $dtAkhir = mktime($h,$m,$s,'1','1','1'); 
  
  $dtSelisih = $dtAkhir-$dtAwal; 
  $totalmenit=$dtSelisih/60; 
  $jam =explode('.',$totalmenit/60); 
  $sisamenit=($totalmenit/60)-$jam[0]; 
  $sisamenit2=$sisamenit*60; 
  $jml_jam=$jam[0]; 

  if($jml_jam > 0)
  {
    $result = round($jml_jam,0).' jam '.round($sisamenit2,0).' menit';
  }
  else
  {
    $result = round($sisamenit2,0).' menit';
  }

  return $result;
}

function array_group_by($array, $key) 
{
  $result = [];
  foreach ($array as $item) 
  {
    $result[$item[$key]][] = $item;
  }
  return $result;
}

function getShipmentCode($whs)
{
  $get = Warehouse::where('title',$whs)->get();
  
  foreach ($get as $key => $value) 
  {
    $result = $value['shipment'];
  }

  return isset($result) ? $result : '-';
}

