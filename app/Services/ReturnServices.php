<?php

namespace App\Services;

use App\Models\ClosingDate;
use App\Models\Item;
use App\Models\ReturnApproval;
use App\Models\ReturnApprovalLines;
use App\Models\ReturnDetail;
use App\Models\ReturnDetailLines;
use App\Models\ReturnTempHeader;
use App\Models\ReturnTempLines;
use Illuminate\Support\Facades\DB;

class ReturnServices 
{
  public function getDataTempDetail($docnum)
  {
    $get = ReturnTempHeader::where('DocNum',$docnum)->first();

		$sum = DB::table('return_temp_lines')
							->where('DocEntry',$get['DocEntry'])
							->sum('LineTotal');

		$DocTotal = $sum;;
		$VatSum = $DocTotal * 0.11;

		$TotalSum = $DocTotal + $VatSum;

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
      'DocTotal' => $DocTotal,
      'VatSum' => $VatSum,
      'TotalSum' => $TotalSum,
      'DocStatus' => $get['DocStatus']
    ];

    return $data;
  }

  public function getDataTempLines($id)
  {
    $data = [];
    $get = ReturnTempLines::where('DocEntry',$id)->groupBy('ItemCode')->get();
    
    foreach ($get as $value) 
    {
      $totalx = 0;
      $disc_cal = 0;
      $disc_calx = 0;

      // $item = $this->getItem($value['ItemCode']);

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

  public function getItem($code)
  {
    return Item::where('code',$code)->first();
  }

  public function jsonHeader($id, $docDate, $alasan)
  {
    $get = ReturnTempHeader::where('DocNum',$id)->get();

    foreach ($get as $value) 
    {
			$post = [
				'CardCode' => $value['CardCode']
			];
	
			$getTop = $this->getTopCustomer(json_encode($post));
			$top = "+".$getTop." days";
	
			$docDueDate= date('Y-m-d', strtotime($top, strtotime($docDate)));

      $data = [
        'CardCode' => $value['CardCode'],
        'DocDueDate' => $docDueDate,
        'DocDate' => $docDate,
        'BPL_IDAssignedToInvoice' => $value['BPLId'],
        'SalesPersonCode'=> $value['SalesPersonCode'],
        'NumAtCard' => $value['DocNum'],
        'Comments' => 'Return from ERP Based On Deliveries '.$id,
        'U_NOPOLISI' => $value['U_NOPOLISI'],
        'U_NOPOLISI2' => $value['U_NOPOLISI2'],
				'U_ALASANRETUR' => $alasan,
        'DocumentLines' => $this->jsonLines($value['DocEntry'])
      ];
    }

    return $data;
  }

  public function jsonLines($docentry)
  {
    $get = ReturnTempLines::where('DocEntry',$docentry)->get();

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
				'BaseType' => 15,
				'BaseEntry' => $docentry,
        'BaseLine' => $value['BaseLine'],
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $value['Quantity'],
        'TaxCode' => $value['TaxCode'],
        'UnitPrice' => $value['UnitPrice'],
        'Price' => $value['UnitPrice'],
        'CostingCode' => $value['CostingCode'],
        'CostingCode2' => $value['CostingCode2'],
        'CostingCode3' => $value['CostingCode3'],
        'UnitMsr' => $value['UnitMsr'],
        'UomCode' => $value['UnitMsr'],
        'UomEntry' => $value['UomEntry'],
        'NumPerMsr' => $value['NumPerMsr'],
        'WhsCode' => $value['WarehouseCode'],
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
      ];

      $no++;
    }

    return $data;
  }

  public function push($header,$url,$post)
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

  public function detail($docnum)
  {
    $cek = ReturnDetail::where('DocNum',$docnum)->first();

    if (isset($cek)) 
    {
      ReturnDetail::where('DocNum',$docnum)->delete();
      ReturnDetailLines::where('DocEntry',$cek->DocEntry)->delete();
    }

    $post = [
      'DocNum' => $docnum
    ];

    $function = 'getReturnDetail';

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
        'Bruto' => $row['Bruto'],
        'VatSum' => $row['VatSum'],
        'Netto' => $row['Netto'],
        'TOP' => $row['TOP'],
        'Printed' => $row['Printed']
      ];
  
      ReturnDetail::create($data);
      ReturnDetailLines::insert($row['Lines']);
  
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
    $header = ReturnDetail::where('DocNum',$docnum)->first();
    $get_lines = ReturnDetailLines::where('DocEntry',$header->DocEntry)->get();

    // $printed = $header->Printed;

    // if ($printed=='N') 
    // {
    //   $this->updatePrinted($docnum);
    // }

    $lines = [];
    foreach ($get_lines as $key => $lines_value) 
    {
      $lineTotal = $lines_value['Quantity'] * $lines_value['Price'];

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
        'U_DISC8' => $disc8
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

    ReturnDetail::where('DocNum',$docNum)->update($update_header);

    $post = [
      'DocNum' => $docNum
    ];

    return callSapApiLtaWithPost('updatePrintedReturn',json_encode($post));
  }

  public function getApprovalData()
  {
    $role = auth()->user()->users_role_id;
    $branch = auth()->user()->branch_sap;

    if ($role==1) 
    {
      $get = ReturnApproval::orderBy('id','DESC')->limit(100)->get();
    }
    else
    {
      $get = ReturnApproval::where('Branch',$branch)->get();
    }

    return $get;
  }

  public function approvalDetail($id)
  {
    $header = ReturnApproval::where('kd',$id)->first();
    $lines = ReturnApprovalLines::where('return_approval_kd',$id)
                                ->get();

    $result = [
      'header' => $header,
      'lines' => $lines
    ];

    return $result;
  }

  public function approvalView($kd)
  {
    $header = ReturnApproval::where('kd',$id)->first();
  }

  public function approvalViewLines($id)
  {
    $row = ReturnApprovalLines::where('return_approval_kd',$id)
                              ->get();

    foreach ($row as $value) 
    {
      $lines[] = [
        ''
      ];
    }
  }

  public function approvalPrint($id)
  {
    $header = ReturnApproval::where('kd',$id)->first();
    $lines = ReturnApprovalLines::where('return_approval_kd',$id)->get();

    $result = [
      'header' => $header,
      'lines' => $lines
    ];

    return $result;
  }

	public function jsonCreditNotes($numAtCard,$date,$numAtCardText)
  {
    $get = ReturnDetail::where('DocEntry',$numAtCard)->get();
    // $date_closing = ClosingDate::where('status',1)->get();

    foreach ($get as $value) 
    {
      $post = [
        'CardCode' => $value['CardCode']
      ];

      $getTop = $this->getTopCustomer(json_encode($post));
      $top = "+".$getTop." days";

      $docDueDate= date('Y-m-d', strtotime($top, strtotime($date)));
       
      $data = [
        'CardCode' => $value['CardCode'],
        'DocDueDate' => $docDueDate,
        'DocDate' => $date,
        'BPL_IDAssignedToInvoice' => $value['BPLId'],
        'SalesPersonCode'=> $value['SalesPersonCode'],
        'NumAtCard' => $numAtCardText,
        'Comments' => $value['Comments'],
        'U_NOPOLISI' => $value['U_NOPOLISI'],
        'U_NOPOLISI2' => $value['U_NOPOLISI2'],
        'DocumentLines' => $this->jsonCreditNotesLines($numAtCard,$value['DocEntry'], $value['DocNum'])
      ];
    }

    return $data;
  }

  public function jsonCreditNotesLines($numAtCard,$docentry, $docNum)
  {
    $get = ReturnDetailLines::where('DocEntry',$numAtCard)->get();

    $no = 0;
    foreach ($get as $value) 
    {
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

      $totalx = $total - round($disc_calx,2);

			if ($value['TaxCode']=='OUT0%') 
			{
				$taxCode = NULL;
			}
			else
			{
				$taxCode = $value['TaxCode'];
			}

      $data[] = [
        'ItemCode' => $value['ItemCode'],
        'Quantity' => $value['Quantity'],
        'VatGroup' => $value['TaxCode'],
        'TaxCode' => $taxCode,
        'UnitPrice' => $value['Price'],
        'Price' => $value['Price'],
        'CostingCode' => $value['OcrCode'],
        'CostingCode2' => $value['OcrCode2'],
        'CostingCode3' => $value['OcrCode3'],
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
        'BaseType' => 16,
        'BaseLine' => $value['LineNum']
      ];

      $no++;
    }

    return $data;
  }

  public function pushCreditNotes($header,$url,$post)
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

	public function trigger_return_erp($post)
  {
    $url = 'https://e-retur.laut-timur.tech/api/return/trigger_return';
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
}