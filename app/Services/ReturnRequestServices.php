<?php

namespace App\Services;

use App\Models\ReturnDetailTemp;

class ReturnRequestServices
{
	public function temp_table($users_id)
	{
		$get = ReturnDetailTemp::where('users_id',$users_id)->get();
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

      $total = $value['Quantity'] * $value['Price'];

			$disc = $value['U_DISC1'] + $value['U_DISC2'] + $value['U_DISC3'] + $value['U_DISC4'] + $value['U_DISC5'] + $value['U_DISC6'] + $value['U_DISC7'] + $value['U_DISC8'];

      $data[] = [
        'id' => $value['id'],
        'itemCode' => $value['ItemCode'],
        'itemDesc' => $item['ItemName'],
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $value['Price'],
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WhsCode'],
        'cogs' => $value['OcrCode'].';'.$value['OcrCode2'].';'.$value['OcrCode3'],
        'docTotal' => $value['LineTotal'],
				'disc' => $disc,
				'TaxCode' => $value['TaxCode']
      ];
    }

		// dd($data);

    return $data;
	}

	public function getDataLinesDisc($id, $cardCode)
  {
    $data = [];
    $get = ReturnDetailTemp::where('users_id',$id)->get();

    $post_customer = [
      'CardCode' => $cardCode
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

      $total = $value['Quantity'] * $value['Price'];

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
        'itemDesc' => $item['ItemName'],
				'nisib' => $item['NISIB'],
        'qty' => $value['Quantity'],
        'unitMsr' => $value['UnitMsr'],
        'unitPrice' => $value['Price'],
        'taxCode' => $value['TaxCode'],
        'whsCode' => $value['WhsCode'],
        'cogs' => $value['OcrCode'].';'.$value['OcrCode2'].';'.$value['OcrCode3'],
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
				'disc1_rp' => $discx1,
        'disc2_rp' => $discx2,
        'disc3_rp' => $discx3,
        'disc4_rp' => $discx4,
        'disc5_rp' => $discx5,
        'disc6_rp' => $discx6,
        'disc7_rp' => $discx7,
        'disc8_rp' => $discx8,
        'disc_total' => $disc_cal,
        // 'discountParameter' => $cek
      ];
    }

    // dd($data);

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

	public function postReturn($header,$url,$post)
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

}