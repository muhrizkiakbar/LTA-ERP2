<?php

namespace App\Http\Controllers\Sap;

use App\Models\BosLtomset;
use App\Exports\ExportReportLTOmset;
use App\Exports\ExportReportOmset;
use App\Exports\ReportUnservedMix;
use App\Exports\ReportUnservedPng;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Storemaster;
use App\Services\ReportServices;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Milon\Barcode\DNS1D;
use Rap2hpoutre\FastExcel\FastExcel;

use Spatie\SimpleExcel\SimpleExcelWriter;

class ReportController extends Controller
{
  public function __construct(ReportServices $service)
  {
    $this->service = $service;
  }

  public function globalan()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = [
      'P&G' => 'P&G',
      'MIX' => 'MIX'
    ];
    $cabang = $this->service->getCabang();

    // dd($plat);
    
    $data = [
      'title' => 'Report - Packing List Global',
      'assets' => $assets,
      'tipe' => $tipe,
      'cabang' => $cabang
    ];
    return view('sap.report.global')->with($data);
  }

  public function globalan_search(Request $request)
  {
    // dd($request->all());

    $post = [
      'DocDateFrom' => $request->dateFrom,
      'DocDateTo' => $request->dateTo,
      'NOPOLISI' => $request->plat,
      'OcrCode2' => $request->tipe
    ];

    $row = $this->service->reportPackingList(json_encode($post));

    $data = [
      'title' => 'Global SR',
      'dateFrom' => $request->dateFrom,
      'dateTo' => $request->dateTo,
      'plat' => $request->plat,
      'company' => 'PT Laut Timur Ardiprima',
      'row' => $row,
      'count' => count($row),
      'totalstn' => array_sum(array_column($row,'SATUAN')),
      'totalktn' => array_sum(array_column($row,'KARTON'))
    ];

    return view('sap.report.global_result')->with($data);
  }

  public function delivery_sales()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = $this->service->getTipe();
    $user = $this->service->getUser();
    $cabang = $this->service->getCabang();
    $sales = getSalesEmployee();

    $status = [
      'N' => 'No',
      'Y' => 'Yes'
    ];

    // dd($plat);
    
    $data = [
      'title' => 'Report - Daily Delivery By Sales',
      'assets' => $assets,
      'tipe' => $tipe,
      'user' => $user,
      'cabang' => $cabang,
      'status' => $status,
      'sales' => $sales
    ];
    return view('sap.report.delivery_sales')->with($data);
  }

  public function delivery_sales_search(Request $request)
  {
    $post = [
      'U_TIPEKIRIM' => $request->status,
      'DocDateFrom' => $request->dateFrom,
      'DocDateTo' => $request->dateTo,
      'DocTimeFrom' => $request->timeFrom,
      'DocTimeTo' => $request->timeTo,
      'SlpCode' => $request->sales,
      'OcrCode' => $request->cabang,
      'OcrCode2' => $request->tipe,
      'U_NAME' => $request->user
    ];

    // $post_sales = [
    //   'SlpCode' => $request->sales
    // ];

    // $sales = getSalesEmployeeId(json_encode($post_sales));

    // dd($sales);

    $row = $this->service->reportDailyDeliveryBySales(json_encode($post));

    // dd($row);

    $count = count($row);

    $data = [
      'title' => 'Laporan Delivery '.$request->tipe,
      'date' => $request->dateFrom,
      'plat' => $request->plat,
      'company' => 'PT Laut Timur Ardiprima',
      'sales' => $request->sales,
      'row' => $row,
      'count' => isset($count) ? $count : 0,
      'totalnetto' => array_sum(array_column($row,'Total'))
    ];

    return view('sap.report.delivery_sales_result')->with($data);
  }

  public function delivery_plat()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = [
      'P&G' => 'P&G',
      'MIX' => 'MIX'
    ];
    $user = $this->service->getUser();
    $cabang = $this->service->getCabang();

    // dd($plat);

    $status = [
      'N' => 'No',
      'Y' => 'Yes'
    ];

    // dd($plat);
    
    $data = [
      'title' => 'Report - Daily Delivery By Plat',
      'assets' => $assets,
      'tipe' => $tipe,
      'user' => $user,
      'cabang' => $cabang,
      'status' => $status
    ];
    
    return view('sap.report.delivery_plat')->with($data);
  }

  public function delivery_plat_search(Request $request)
  {
    $post = [
      'U_TIPEKIRIM' => $request->status,
      'DocDateFrom' => $request->dateFrom,
      'DocDateTo' => $request->dateTo,
      'DocTimeFrom' => $request->timeFrom,
      'DocTimeTo' => $request->timeTo,
      'U_NOPOLISI' => $request->plat,
      'OcrCode' => $request->cabang,
      'OcrCode2' => $request->tipe,
      'U_NAME' => $request->user
    ];

    // dd($post);
    // $sales = Sales::find($request->sales);

    $row = $this->service->reportDailyDeliveryByPlat(json_encode($post));

    // dd($row);

    $data = [
      'title' => 'Laporan Delivery '.$request->tipe,
      'date' => $request->dateFrom,
      'plat' => $request->plat,
      'company' => 'PT Laut Timur Ardiprima',
      'row' => $row,
      'count' => count($row),
      'totalnetto' => array_sum(array_column($row,'Total'))
    ];

    return view('sap.report.delivery_plat_result')->with($data);
  }

  public function rekap_so()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = [
      'P&G' => 'P&G',
      'MIX' => 'MIX'
    ];
    $user = $this->service->getUser();
    $cabang = $this->service->getCabang();
    $sales = getSalesEmployee();

    $status = [
      'N' => 'No',
      'Y' => 'Yes'
    ];

    // dd($plat);
    
    $data = [
      'title' => 'Report - Rekapan Sales Order',
      'assets' => $assets,
      'tipe' => $tipe,
      'user' => $user,
      'cabang' => $cabang,
      'status' => $status,
      'sales' => $sales
    ];
    return view('sap.report.rekap_so')->with($data);
  }

  public function rekap_so_search(Request $request)
  {
    $post = [
      'DocDate' => $request->dateFrom,
      'DocTimeFrom' => $request->timeFrom,
      'DocTimeTo' => $request->timeTo,
      'OcrCode' => $request->cabang,
      'OcrCode2' => $request->tipe,
      'SlpName' => $request->sales,
      'U_TIPEKIRIM' => $request->status,
      'U_NAME' => $request->user
    ];

    // dd($post);
    $warehouse = Warehouse::where('code',$request->cabang)->first();

    $row = callSapApiLtaWithPost('rekap_so_sales',json_encode($post));

    // $row = $this->service->reportRekapSalesOrder(json_encode($post));
    // $byGroup = grupArray('CardName', $row);
    // dd($row);

    $data = [
      'title' => 'Report Rekapan SO',
      'date' => $request->dateFrom,
      'plat' => $request->plat,
      'company' => 'PT Laut Timur Ardiprima',
      'sales' => $request->sales,
      'user' => $request->user,
      'cabang' => $request->cabang,
      'gudang' => $warehouse->title,
      'row' => $row
    ];

    return view('sap.report.rekap_so_result')->with($data);
  }

  public function omset()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

    $cabang = Warehouse::pluck('code','code');

    $tipe = [
      'MIX' => 'MIX',
      'P&G' => 'P&G'
    ];

    // dd($plat);
    
    $data = [
      'title' => 'Report - Report Omset',
      'assets' => $assets,
      'cabang' => $cabang,
      'tipe' => $tipe
    ];
    return view('sap.report.omset')->with($data);
  }

  public function omset_search(Request $request)
  {
    $post = [
      "OcrCode2" => $request->tipe,
      "DocDateFrom" => $request->dateFrom,
      "DocDateTo" => $request->dateTo,
      "OcrCode" => $request->cabang
    ];

    // dd($post);

    $row = $this->service->reportOmset(json_encode($post));

    // dd($row);

    $data = [
      'row' => $row
    ];

    return view('sap.report.omset_result')->with($data);
  }

  public function omset_export(Request $request)
  {
    $tipe = $request->tipe;
    $dateFrom = $request->dateFrom;
    $dateTo = $request->dateTo;
    $cabang = $request->cabang;

    $myFile =  Excel::raw(new ExportReportOmset($tipe,$dateFrom,$dateTo,$cabang), 'Xlsx');
    
    $response =  array(
      'name' => "Report_Omset_".$cabang."_".$tipe."_".time().".xlsx",
      'file' => "data:application/vnd.ms-excel;base64,".base64_encode($myFile)
    );
    return response()->json($response);

    // return Excel::download(new ExportReportOmset($tipe,$dateFrom,$dateTo,$cabang), 'Report_Omset_'.$tipe.'.xlsx');
  }

  public function ltomset()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

    
    $cabang = Warehouse::pluck('code','code');
    
    $tipe = getVendor();

    // dd($plat);
    
    $data = [
      'title' => 'Report - Report LT Omset',
      'assets' => $assets,
      'cabang' => $cabang,
      'tipe' => $tipe
    ];

    return view('sap.report.ltomset')->with($data);
  }

  public function ltomset_search(Request $request)
  {
    // dd($request->all());

    $post = [
      'Cabang' => isset($request->cabang) ? $request->cabang : 'ALL',
      'DateFrom' => $request->dateFrom,
      'DateTo' => $request->dateTo,
      'Sup_Name' => $request->tipe
    ];

    // dd($post);

    $data = [];

    $row = $this->service->reportLTOmset(json_encode($post));

    // $arr_docentry = array_column($row['data'],"DOCENTRY");
    // $arr_linenum = array_column($row['data'],"LINENUM");

    // dd($row);

    if ($row['count'] > 10000) 
    {
      $response =  array(
        'message' => 'bigger'
      );
    }
    else
    {
      $cek = BosLtomset::select('DOCENTRY','LINENUM')
                        ->where('TGL_JUAL','>=',$request->dateFrom)
                        ->where('TGL_JUAL','<=',$request->dateTo)
                        ->where('SUPP_NAME',$request->tipe)
                        ->get();
      
      if ($cek->isEmpty()) 
      {
        foreach ($row['data'] as $value) 
        {
          $data[] = [
            'DOCENTRY' => $value['DOCENTRY'],
            'LINENUM' => $value['LINENUM'],
            'TGL_JUAL' => $value['TGL_JUAL'],
            'PERIODE' => $value['PERIODE'],
            'CABANG' => $value['CABANG'],
            'NO_FAKTUR' => $value['NO_FAKTUR'],
            'CKET' => $value['CKET'],
            'CUST_CODE' => $value['CUST_CODE'],
            'CUST_NAME' => $value['CUST_NAME'],
            'PASAR' => $value['PASAR'],
            'KOTA' => $value['KOTA'],
            'SUPP_NAME' => $value['SUPP_NAME'],
            'BRAND_NAME' => $value['BRAND_NAME'],
            'CATEGORY_N' => $value['CATEGORY_N'],
            'VARIANT_NA' => $value['VARIANT_NA'],
            'CLASS_NAME' => $value['CLASS_NAME'],
            'ITEM_CODE' => $value['ITEM_CODE'],
            'CODE_BARCODE' => $value['CODE_BARCODE'],
            'ITEM_NAME' => $value['ITEM_NAME'],
            'ISI_BESAR' => $value['ISI_BESAR'],
            'SATUAN_BES' => $value['SATUAN_BES'],
            'SATUAN_KEC' => $value['SATUAN_KEC'],
            'NW' => $value['NW'],
            'HARGA_JUAL' => $value['HARGA_JUAL'],
            'QTY_JUAL' => $value['QTY_JUAL'],
            'KONVERSI' => $value['KONVERSI'],
            'DISC_BRG1' => $value['DISC_BRG1'],
            'DISC_BRG2' => $value['DISC_BRG2'],
            'DISC_BRG3' => $value['DISC_BRG3'],
            'DISC_BRG4' => $value['DISC_BRG4'],
            'DISC_BRG5' => $value['DISC_BRG5'],
            'DISC_BRG6' => $value['DISC_BRG6'],
            'DISC_BRG7' => $value['DISC_BRG7'],
            'DISC_BRG8' => $value['DISC_BRG8'],
            'VALUE_DISC' => $value['VALUE_DISC'],
            'DISC_FAKTU' => $value['DISC_FAKTU'],
            'SALES_CODE' => $value['SALES_CODE'],
            'SALES_NAME' => $value['SALES_NAME'],
            'SUPERVISOR' => $value['SUPERVISOR'],
            'BRUTTO' => $value['BRUTTO'],
            'TOT_DISCRP' => $value['TOT_DISCRP'],
            'NETTO' => $value['NETTO'],
            'PPN' => $value['PPN'],
            'SKU' => $value['SKU'],
            'SUB_SEGMEN' => $value['SUB_SEGMEN'],
            'NAMA_SBD' => $value['NAMA_SBD'],
            'STATUS_SKU' => $value['STATUS_SKU'],
            'KODE_INI1' => $value['KODE_INI1'],
            'KODE_INI2' => $value['KODE_INI2'],
            'KETERANGAN' => $value['KETERANGAN'],
            'CGUDANG' => $value['CGUDANG'],
            'CUSTREF' => $value['CUSTREF'],
            'GROUP' => $value['GROUP'],
          ];
        }
      }
      else
      {
        foreach ($row['data'] as $value) 
        {
          if ($cek->where('DOCENTRY',$value['DOCENTRY'])->where('LINENUM',$value['LINENUM'])->isEmpty()) 
          {
            $data[] = [
              'DOCENTRY' => $value['DOCENTRY'],
              'LINENUM' => $value['LINENUM'],
              'TGL_JUAL' => $value['TGL_JUAL'],
              'PERIODE' => $value['PERIODE'],
              'CABANG' => $value['CABANG'],
              'NO_FAKTUR' => $value['NO_FAKTUR'],
              'CKET' => $value['CKET'],
              'CUST_CODE' => $value['CUST_CODE'],
              'CUST_NAME' => $value['CUST_NAME'],
              'PASAR' => $value['PASAR'],
              'KOTA' => $value['KOTA'],
              'SUPP_NAME' => $value['SUPP_NAME'],
              'BRAND_NAME' => $value['BRAND_NAME'],
              'CATEGORY_N' => $value['CATEGORY_N'],
              'VARIANT_NA' => $value['VARIANT_NA'],
              'CLASS_NAME' => $value['CLASS_NAME'],
              'ITEM_CODE' => $value['ITEM_CODE'],
              'CODE_BARCODE' => $value['CODE_BARCODE'],
              'ITEM_NAME' => $value['ITEM_NAME'],
              'ISI_BESAR' => $value['ISI_BESAR'],
              'SATUAN_BES' => $value['SATUAN_BES'],
              'SATUAN_KEC' => $value['SATUAN_KEC'],
              'NW' => $value['NW'],
              'HARGA_JUAL' => $value['HARGA_JUAL'],
              'QTY_JUAL' => $value['QTY_JUAL'],
              'KONVERSI' => $value['KONVERSI'],
              'DISC_BRG1' => $value['DISC_BRG1'],
              'DISC_BRG2' => $value['DISC_BRG2'],
              'DISC_BRG3' => $value['DISC_BRG3'],
              'DISC_BRG4' => $value['DISC_BRG4'],
              'DISC_BRG5' => $value['DISC_BRG5'],
              'DISC_BRG6' => $value['DISC_BRG6'],
              'DISC_BRG7' => $value['DISC_BRG7'],
              'DISC_BRG8' => $value['DISC_BRG8'],
              'VALUE_DISC' => $value['VALUE_DISC'],
              'DISC_FAKTU' => $value['DISC_FAKTU'],
              'SALES_CODE' => $value['SALES_CODE'],
              'SALES_NAME' => $value['SALES_NAME'],
              'SUPERVISOR' => $value['SUPERVISOR'],
              'BRUTTO' => $value['BRUTTO'],
              'TOT_DISCRP' => $value['TOT_DISCRP'],
              'NETTO' => $value['NETTO'],
              'PPN' => $value['PPN'],
              'SKU' => $value['SKU'],
              'SUB_SEGMEN' => $value['SUB_SEGMEN'],
              'NAMA_SBD' => $value['NAMA_SBD'],
              'STATUS_SKU' => $value['STATUS_SKU'],
              'KODE_INI1' => $value['KODE_INI1'],
              'KODE_INI2' => $value['KODE_INI2'],
              'KETERANGAN' => $value['KETERANGAN'],
              'CGUDANG' => $value['CGUDANG'],
              'CUSTREF' => $value['CUSTREF'],
              'GROUP' => $value['GROUP'],
            ];
          }
        }
      }

      if (count($data) != 0) 
      {
        $count = count($data);
        
        if($count < 1000)
        {
          $data = collect($data);
          $chunks = $data->chunk(100);
        }
        else
        {
          $data = collect($data);
          $chunks = $data->chunk(500);
        }

        foreach ($chunks as $chunk)
        {
          DB::table('bos_ltomset')->insert($chunk->toArray());
        }

        // BosLtomset::insert($data);

        $response =  array(
          'message' => 'sukses'
        );
      }
      else
      {
        $response =  array(
          'message' => 'error'
        );
      }
    }

    return response()->json($response);
  }

  public function ltomset_export(Request $request)
  {
    $tipe = $request->tipe;
    $dateFrom = $request->dateFrom;
    $dateTo = $request->dateTo;
    $cabang = isset($request->cabang) ? $request->cabang : 'ALL';

    $myFile =  Excel::raw(new ExportReportLTOmset($tipe,$dateFrom,$dateTo,$cabang), 'Xlsx');

    // dd($myFile);
    
    $response =  array(
      'name' => "Report_LTOmset_".$cabang."_".$tipe."_".time().".xlsx",
      'file' => "data:application/vnd.ms-excel;base64,".base64_encode($myFile)
    );
    return response()->json($response);

    // return Excel::download(new ExportReportOmset($tipe,$dateFrom,$dateTo,$cabang), 'Report_Omset_'.$tipe.'.xlsx');
  }

  public function ltomset_export2(Request $request)
  {
    $tipe = $request->tipe;
    $dateFrom = $request->dateFrom;
    $dateTo = $request->dateTo;
    $cabang = isset($request->cabang) ? $request->cabang : 'ALL';

    $row = $this->generateLTOmsetLokal($cabang,$dateFrom,$dateTo,$tipe);
    $fileName = 'Report_LTOmset_'.$cabang.'_'.time().'.xlsx';
    $filePath = storage_path().'/app/public/upload/export/'.$fileName;
    $fileDown = 'http://36.93.82.10/sapweb'.Storage::url('upload/export/'.$fileName);

    (new FastExcel($row))->export($filePath, function($data){
      $netto = $data->NETTO + $data->PPN;
      $exp = explode('/',$data->NO_FAKTUR);

      return [
        'Tanggal Jual' => $data->TGL_JUAL,
        'Periode' => $data->PERIODE,
        'Cabang' => $data->CABANG,
        'No Faktur' => $exp[1],
        'CKET' => $data->CKET,
        'Cust Code' => $data->CUST_CODE,
        'Cust Name' => $data->CUST_NAME,
        'Pasar' => $data->PASAR,
        'Kota' => $data->KOTA,
        'Supp Name' => $data->SUPP_NAME,
        'Brand Name' => $data->BRAND_NAME,
        'Kategori Name' => $data->CATEGORY_N,
        'Variant Name' => $data->VARIANT_NA,
        'Class Name' => $data->CLASS_NAME,
        'Item Code' => $data->ITEM_CODE,
        'Barcode' => $data->CODE_BARCODE,
        'Item Name' => $data->ITEM_NAME,
        'Isi Besar' => $data->ISI_BESAR,
        'Satuan Besar' => $data->SATUAN_BES,
        'Satuan Kecil' => $data->SATUAN_KEC,
        'NW' => $data->NW,
        'Harga Jual' => $data->HARGA_JUAL,
        'Qty Jual' => $data->QTY_JUAL,
        'Konversi' => $data->KONVERSI,
        'Diskon 1' => $data->DISC_BRG1,
        'Diskon 2' => $data->DISC_BRG2,
        'Diskon 3' => $data->DISC_BRG3,
        'Diskon 4' => $data->DISC_BRG4,
        'Diskon 5' => $data->DISC_BRG5,
        'Diskon 6' => $data->DISC_BRG6,
        'Diskon 7' => $data->DISC_BRG7,
        'Diskon 8' => $data->DISC_BRG8,
        'Value Disc' => $data->VALUE_DISC,
        'Disc Faktur' => $data->DISC_FAKTU,
        'Sales Code' => $data->SALES_CODE,
        'Sales Name' => $data->SALES_NAME,
        'Supervisor' => $data->SUPERVISOR,
        'Bruto' => $data->BRUTTO,
        'Total Disc Rp' => $data->TOT_DISCRP,
        'Netto' => $netto,
        'SKU' => $data->SKU,
        'Sub Segment' => $data->SUB_SEGMEN,
        'Nama Sbd' => $data->NAMA_SBD,
        'STATUS_SKU'=> $data->STATUS_SKU,
        'Kode INI1' => $data->KODE_INI1,
        'Kode INI2' => $data->KODE_INI2,
        'Keterangan' => $data->KETERANGAN,
        'C. Gudang' => $data->CGUDANG,
        'CUSTREF' => $data->CUSTREF
      ];
    });

    $response =  array(
      'name' => $fileName,
      'file' => $fileDown
    );

    return response()->json($response);
  }

  public function generateLTOmsetLokal($cabang,$dateFrom,$dateTo,$tipe)
  {
    $get = $this->service->reportLTOmsetLokal($cabang,$dateFrom,$dateTo,$tipe);

    foreach ($get as $data) 
    {
      yield $data;
    }
  }

  public function rekap_so_plat()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = [
      'P&G' => 'P&G',
      'MIX' => 'MIX'
    ];
    $user = $this->service->getUser();
    $cabang = $this->service->getCabang();
    $sales = getSalesEmployee();

    $status = [
      'N' => 'No',
      'Y' => 'Yes'
    ];

    // dd($plat);
    
    $data = [
      'title' => 'Report - Rekapan Sales Order By Plat',
      'assets' => $assets,
      'tipe' => $tipe,
      'user' => $user,
      'cabang' => $cabang,
      'status' => $status,
      'sales' => $sales
    ];
    return view('sap.report.rekap_so_plat')->with($data);
  }

  public function rekap_so_plat_search(Request $request)
  {
    $post = [
      'DocDate' => $request->dateFrom,
      'DocTimeFrom' => $request->timeFrom,
      'DocTimeTo' => $request->timeTo,
      'OcrCode' => $request->cabang,
      'OcrCode2' => $request->tipe,
      'U_NOPOLISI' => $request->plat,
      'U_TIPEKIRIM' => $request->status,
      'U_NAME' => $request->user
    ];

    // dd($post);
    $warehouse = Warehouse::where('code',$request->cabang)->first();

    $row = callSapApiLtaWithPost('rekap_so_plat',json_encode($post));

    // dd($row);

    // $row = $this->service->reportRekapSalesOrderPlat(json_encode($post));
    // $byGroup = grupArray('CardName', $row);
    // dd($row);

    $data = [
      'title' => 'Report Rekapan SO',
      'date' => $request->dateFrom,
      'plat' => $request->plat,
      'company' => 'PT Laut Timur Ardiprima',
      'sales' => $request->plat,
      'user' => $request->user,
      'cabang' => $request->cabang,
      'gudang' => $warehouse->title,
      'row' => $row
    ];

    return view('sap.report.rekap_so_plat_result')->with($data);
  }

  public function rekap_do_plat()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = [
      'P&G' => 'P&G',
      'MIX' => 'MIX'
    ];
    $user = $this->service->getUser();
    $cabang = $this->service->getCabang();
    $sales = getSalesEmployee();

    $status = [
      'N' => 'No',
      'Y' => 'Yes'
    ];

    // dd($plat);
    
    $data = [
      'title' => 'Report - Rekapan Delivery Order By Plat',
      'assets' => $assets,
      'tipe' => $tipe,
      'user' => $user,
      'cabang' => $cabang,
      'status' => $status,
      'sales' => $sales
    ];
    return view('sap.report.rekap_do_plat')->with($data);
  }

  public function rekap_do_plat_search(Request $request)
  {
    $post = [
      'DocDate' => $request->dateFrom,
      'DocTimeFrom' => $request->timeFrom,
      'DocTimeTo' => $request->timeTo,
      'U_NOPOLISI' => $request->plat,
      'OcrCode' => $request->cabang,
      'OcrCode2' => $request->tipe,
      'U_TIPEKIRIM' => $request->status,
      'U_NAME' => $request->user
    ];

    // dd($post);
    $warehouse = Warehouse::where('code',$request->cabang)->first();

    $row = $this->service->rekapDeliveryByPlat(json_encode($post));
    
    // $byGroup = grupArray('CardName', $row);
    // dd($row);

    $data = [
      'title' => 'Rekap Delivery Order',
      'date' => $request->dateFrom,
      'plat' => $request->plat,
      'company' => 'PT Laut Timur Ardiprima',
      'sales' => $request->plat,
      'user' => $request->user,
      'cabang' => $request->cabang,
      'gudang' => $warehouse->title,
      'row' => $row
    ];

    return view('sap.report.rekap_do_plat_result')->with($data);
  }

  public function barcode()
  {
    $docnum = '001245259636';

    $data = [
      'barcode' => $docnum
    ];

    return view('sap.barcode')->with($data);
  }

  public function unserved_mix()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $branch = Warehouse::pluck('code','code');

    $data = [
      'title' => 'Unserved Order - MIX',
      'assets' => $assets,
      'branch' => $branch
    ];

    return view('sap.report.unserved_mix')->with($data);
  }

  public function unserved_mix_search(Request $request)
  {

    $branch = $request->branch_code;
    $dateT = $request->dateT;
    $dateF = $request->dateF;

    $row = $this->service->unservedMixView($branch, $dateF, $dateT);
    
    $collect = collect($row['data'])->groupBy('CardCode');

    $collect->transform(function($item, $key) {
      return [
        'CardCode' => $key,
        'CardName' => $item[0]['CardName'],
        'DocDate' => $item[0]['DocDate'],
        'TotalItem' => $item->groupBy('ItemCode')->count(),
        'SfaQtyOrder' => $item->sum('SfaQtyTotal'),
        'SfaQtySuccess' => $item->sum('SfaQtySuccess'),
        'SfaQtyUnserve' => $item->sum('SfaQtyUnserve'),
        'TotalOrderRp' => $item->sum('Total'),
        'TotalSuccessRp' => $item->sum('TotalSuccess'),
        'TotalUnservedRp' => $item->sum('TotalUnserved'),
        'UnservedPrecentage' => ($item->sum('TotalUnserved') / $item->sum('Total')) * 100,
      ];
    });

    return view('sap.report.unserved_mix_result', [
        'data' => $collect,
      'sfa_total_order' => $row['sfa_total_order'],
      'sfa_success_order' => $row['sfa_success_order'],
      'sfa_unserved_order' => $row['sfa_unserved_order'],
      'total_order_rp' => $row['total_order_rp'],
      'total_success_rp' => $row['total_success_rp'],
      'total_unserved_rp' => $row['total_unserved_rp'],
      'percentage' => $row['percentage']
    ]);
  }

  // export excel
  public function unserved_mix_export(Request $request) {
    $branch = $request->get('branch_code');
    $dateT = $request->get('dateT');
    $dateF = $request->get('dateF');

    // return Excel::download(new ReportUnservedMix($branch, $dateF, $dateT), 'ReportUnservedMix.xlsx');

    $row = $this->service->unservedMixView($branch, $dateF, $dateT);


    SimpleExcelWriter::streamDownload('mix_unserved.csv')  
      ->addRows($row['data']);
  }

  public function unserved_png()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $branch = Warehouse::pluck('code','code');

    $data = [
      'title' => 'Unserved Order - P&G',
      'assets' => $assets,
      'branch' => $branch
    ];

    return view('sap.report.unserved_png')->with($data);
  }

  public function unserved_png_search(Request $request)
  {
    $branch = $request->branch_code;
    $dateT  = $request->dateT;
    $dateF  = $request->dateF;

    $row = $this->service->unservedPngView($branch, $dateF, $dateT);

    $collect = collect($row['data'])->groupBy('CardCode');

    $collect->transform(function($item, $key) {
      return [
        'CardCode' => $key,
        'CardName' => $item[0]['CardName'],
        'DocDate' => $item[0]['DocDate'],
        'TotalItem' => $item->groupBy('ItemCode')->count(),
        'SfaQtyOrder' => $item->sum('SfaQtyTotal'),
        'SfaQtySuccess' => $item->sum('SfaQtySuccess'),
        'SfaQtyUnserve' => $item->sum('SfaQtyUnserve'),
        'TotalOrderRp' => $item->sum('Total'),
        'TotalSuccessRp' => $item->sum('TotalSuccess'),
        'TotalUnservedRp' => $item->sum('TotalUnserved'),
        'UnservedPrecentage' => ($item->sum('TotalUnserved') / $item->sum('Total')) * 100,
      ];
    });


    return view('sap.report.unserved_png_result')->with([
      'data' => $collect,
      'sfa_total_order' => $row['sfa_total_order'],
      'sfa_success_order' => $row['sfa_success_order'],
      'sfa_unserved_order' => $row['sfa_unserved_order'],
      'total_order_rp' => $row['total_order_rp'],
      'total_success_rp' => $row['total_success_rp'],
      'total_unserved_rp' => $row['total_unserved_rp'],
      'percentage' => $row['percentage']
    ]);
  }

  public function unserved_png_export(Request $request) {
    
    $branch = $request->get('branch_code');
    $dateT = $request->get('dateT');
    $dateF = $request->get('dateF');
    $row = $this->service->unservedPngView($branch, $dateF, $dateT);


    SimpleExcelWriter::streamDownload('png_unserved.csv')  
      ->addRows($row['data']);
  }

  public function unserved_png_sap(Request $request) {

    $body = [
      "DateFrom" => $request['dateF'],
      "DateTo" => $request['dateT'],
      "Branch" => $request['branch_code']
    ];

    $res = $this->service->sapUnServedPng(json_encode($body));

    // $query = collect($res['data']);
    // $group = $query->groupBy('Code Item');

    return $res;
    // return $body;
  } 

  public function cek_penjualan()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = [
      'PNG' => 'PNG',
      'MIX' => 'MIX'
    ];
    $cabang = $this->service->getCabang();

    // dd($plat);
    
    $data = [
      'title' => 'Report - Cek Penjualan',
      'assets' => $assets,
      'tipe' => $tipe,
      'cabang' => $cabang
    ];
    return view('sap.report.cek_penjualan')->with($data);
  }

  public function cek_penjualan_search(Request $request)
  {
    $tipe = str_replace('N','&',$request->tipe);

    $data = [
      'dateFrom' => $request->dateFrom,
      'dateTo' => $request->dateTo,
      'branchCode' => $request->cabang,
      'ocrCode2' => $tipe
    ];

    $row = $this->service->cek_penjualan_search($data);

    $view = [
      'row' => $row['data']
    ];

    return view('sap.report.cek_penjualan_result')->with($view);
  }

  public function cek_penjualan_export(Request $request)
  {
    $tipe = str_replace('N','&',$request->get('tipe'));

    $data = [
      'dateFrom' => $request->get('dateFrom'),
      'dateTo' => $request->get('dateTo'),
      'branchCode' => $request->get('cabang'),
      'ocrCode2' => $tipe
    ];

    $row = $this->service->cek_penjualan_search($data);

    SimpleExcelWriter::streamDownload('Penjualan_ID_LTA_'.$request->get('cabang').'_'.time().'.csv')  
      ->addRows($row['data']);
  }

	public function storemaster()
	{
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/datatables/custom.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/datatables/datatables.min.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

		$branch = Branch::pluck('title','id');

		$data = [
      'title' => 'RTDX - Storemaster',
      'assets' => $assets,
      'branch' => $branch
    ];

    return view('sap.report.storemaster')->with($data);
	}

	public function storemaster_view(Request $request)
	{
		$get = Storemaster::orderBy('id','DESC')->get();

		$data = [
			'row' => $get
		];

		return view('sap.report.storemaster_view')->with($data);
	}

	public function storemaster_sync(Request $request)
	{
		$branch = $request->branch;

		$body = [
			'Branch' => $branch
		];

		$cek = Storemaster::where('Branch',$branch)->get();

		if (isset($cek)) 
		{
			Storemaster::where('Branch',$branch)->delete();
		}

		$post = callSapApiLtaWithPost('getRtdxStore2',json_encode($body));

		if(isset($post['data']))
		{
			$count = count($post['data']);

			if($count < 1000)
			{
				$insert = collect($post['data']);
				$chunks = $insert->chunk(500);
			}
			else
			{
				$insert = collect($post['data']);
				$chunks = $insert->chunk(1000);
			}	

			foreach ($chunks as $chunk)
			{
				DB::table('storemaster')->insert($chunk->toArray());
			}

			$response = [
				'message' => 'sukses'
			];
		}
		else
		{
			$response = [
				'message' => 'error'
			];
		}

		echo json_encode($response);
	}

	public function storemaster_export(Request $request)
	{
		$branch = $request->get('branch');

		if (isset($branch)) 
		{
			$cabang = Branch::find($branch)->CostingCode;
		}
		else
		{
			$cabang = 'ALL';
		}
		
		$date = date('Ymd');

		$row = $this->service->storemaster_export($branch);

		SimpleExcelWriter::streamDownload('ASNLTA_DRTT_StoreMaster_Daily_'.$date.$cabang.'.csv')->addRows($row['data']);
	}

	public function paket_eko()
	{
		$assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];
    
    $data = [
      'title' => 'Report - Paket EKO P&G',
      'assets' => $assets,
    ];

    return view('sap.report.paket_eko')->with($data);
	}

	public function paket_eko_search(Request $request)
	{
		$post = [
			'DocDate' => $request->date,
			'CardCode' => $request->cardCode,
			'ItemCode' => $request->itemCode
		];

		$row = $this->service->reportPaketEko(json_encode($post));

		// dd($row);

    $data = [
      'row' => $row
    ];

    return view('sap.report.paket_eko_view')->with($data);
	}

	public function cek_penjualan_do()
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    $tipe = getVendor();
    $cabang = $this->service->getCabang();

    // dd($plat);
    
    $data = [
      'title' => 'Report - Cek Penjualan Delivery',
      'assets' => $assets,
      'tipe' => $tipe,
      'cabang' => $cabang
    ];
    return view('sap.report.cek_penjualan_do')->with($data);
  }

  public function cek_penjualan_do_search(Request $request)
  {
    // $tipe = str_replace('N','&',$request->tipe);

    $data = [
      'dateFrom' => $request->dateFrom,
      'dateTo' => $request->dateTo,
      'branchCode' => $request->cabang,
      'cardName' => $request->tipe
    ];

    $row = $this->service->cek_penjualan_do_search($data);

		// dd($row);

    $view = [
      'row' => $row['data']
    ];

    return view('sap.report.cek_penjualan_do_result')->with($view);
  }

  public function cek_penjualan_do_export(Request $request)
  {
    // $tipe = str_replace('N','&',$request->get('tipe'));

    $data = [
      'dateFrom' => $request->get('dateFrom'),
      'dateTo' => $request->get('dateTo'),
      'branchCode' => $request->get('cabang'),
      'cardName' => $request->get('tipe')
    ];

    $row = $this->service->cek_penjualan_do_search($data);

    SimpleExcelWriter::streamDownload('Penjualan_DO_ID_LTA_'.$request->get('cabang').'_'.time().'.csv')  
      ->addRows($row['data']);
  }

	public function order_cut()
	{
		$assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/sweetalert2/sweetalert2.min.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js',
        'assets/plugins/sweetalert2/sweetalert2.min.js'
      )
    ];

		$cabang = Warehouse::pluck('code','code');
    
    $data = [
      'title' => 'Report - Order Cut',
      'assets' => $assets,
      'cabang' => $cabang
    ];
		
    return view('sap.report.order_cut')->with($data);
	}

	public function order_cut_export(Request $request)
	{
		$cabang = $request->get('cabang');
    $dateTo = $request->get('dateTo');
    $dateFrom = $request->get('dateFrom');

		$body = [
			'DateFrom' => $dateFrom,
			'DateTo' => $dateTo,
			'Branch' => isset($cabang) ? $cabang : 'ALL'
		];

		// dd($body);

		$data = $this->service->getOrderCut($body);

		$date = date('Ymd');

		SimpleExcelWriter::streamDownload('ID_OC_DAILY_LTA_'.$date.'.xlsx') 
			->noHeaderRow() 
      ->addRows($data['data']);

		// dd($data);
	}
}

