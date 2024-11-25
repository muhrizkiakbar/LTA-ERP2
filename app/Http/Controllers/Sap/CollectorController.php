<?php

namespace App\Http\Controllers\Sap;

use App\ArCollectorCategory;
use App\ArCollectorHeader;
use App\ArCollectorLines;
use App\ArTitip;
use App\Branch;
use App\Company;
use App\Http\Controllers\Controller;
use App\Services\ApiServices;
use App\Services\CollectorServices;
use App\User;
use App\UserCollector;
use App\Warehouse;
use Illuminate\Http\Request;

class CollectorController extends Controller
{
  public function __construct(CollectorServices $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    $id = auth()->user()->id;
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/datatables/custom.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/datatables/datatables.min.js',
      )
    ];

    if ($users_role==1) 
    {
      $collector = User::where('users_role_id',4)->pluck('name','id');
      $branch = Warehouse::pluck('code','code');

      $row = $this->service->getDataAll();
    }
    else if ($users_role==2) 
    {
      $collector = User::where('branch_sap',$branch_sap)
                        ->where('users_role_id',4)
                        ->pluck('name','id');
      $bpl = Branch::find($branch_sap)->BPLid;
      $branch = Warehouse::where('BPLId',$bpl)->pluck('code','code');

      $row = $this->service->getDataByBranch($branch);
    }
    else
    {
      $collector = User::where('branch_sap',$branch_sap)
                        ->where('users_role_id',4)
                        ->pluck('name','id');

      $bpl = Branch::find($branch_sap)->BPLid;
      $branch = Warehouse::where('BPLId',$bpl)->pluck('code','code');

      $row = $this->service->getDataByUser($id);
    }

    // dd($row);

    $category = ArCollectorCategory::pluck('title','title');

    $company = Company::pluck('title','id');

    $data = [
      'title' => 'A/R Invoice Collector',
      'assets' => $assets,
      'branch' => $branch,
      'category' => $category,
      'collector' => $collector,
      'company' => $company,
      'row' => $row,
      'role' => $users_role
    ];

    return view('sap.collector.index')->with($data);
  }

  public function generate(Request $request)
  {
    $users_id = auth()->user()->id;

    $data_lines = [];

    $date = $request->date;
    $hari = format_hari_indo($date);

    $collector = UserCollector::where('users_id',$request->users_id)->first();
    $user = User::find($request->users_id);

    $company = $request->company_id;
    $api = 'generateCollector';
    
    $post = [
      'DocDueDate' => $date,
      'OcrCode' => $request->branch_code,
      'Jadwal' => $hari,
      'Kategori' => $collector->collector->title,
      'SlpName' => $user->name
    ];

    // dd($post);
    if ($company==1) 
    {
      $row = callSapApiLtaWithPost($api,json_encode($post));
    }
    else
    {
      $row = callSapApiTaaWithPost($api,json_encode($post));
    }
    

    // dd($row);

    $kd = $request->branch_code.time();

    if (isset($row)) 
    {
      $data_header = [
        'kd' => $kd,
        'users_collector_id' => $request->users_id,
        'branch_code' => $request->branch_code,
        'category' => $collector->collector->title,
        'date' => $request->date,
        'users_admin_id' => $users_id,
        'company_id' => $company
      ];

      // dd($data_header);

      $cek_header = ArCollectorHeader::where('users_collector_id',$request->users_id)
                                    ->where('branch_code',$request->branch_code)
                                    ->where('category',$request->ar_collector_category_id)
                                    ->where('date',$request->date)
                                    ->where('company_id',$company)
                                    ->get();

      // dd($cek_header);

      if ($cek_header->count()==0)
      {
        foreach ($row as $value) 
        {
          // $api_cn = 'generateCollectorCN';
          // $this->service->updateCollectorCN($company,$api_cn,$value['CardCode']);

          foreach ($value['Lines'] as $lines) 
          {
            $docNum = explode('/',$lines['DocNum']);

            $cek = ArCollectorLines::where('DocNum',$docNum[1])->where('status',0)->get();

            if (count($cek)==0) 
            {
              $balance = $lines['Netto'] - $lines['BalanceDue'];

              $data_lines[] = [
                'ar_collector_header_kd' => $kd,
                'users_collector_id' => $request->users_id,
                'DocEntry' => $lines['DocEntry'],
                'DocNum' => $docNum[1],
                'DocNumDO' => $lines['DocNumDO'],
                'CardCode' => $lines['CardCode'],
                'CardName' => $lines['CardName'],
                'DocDueDate' => $lines['DocDueDate'],
                'DocDate' => $lines['DocDate'],
                'OcrCode' => $lines['OcrCode'],
                'OcrCode2' => $lines['OcrCode2'],
                'Alamat' => $lines['Alamat'],
                'Netto' => $lines['Netto'],
                'GroupCode' => $lines['GroupCode'],
                'BalanceDue' => $lines['BalanceDue'],
                'Balance' => $balance,
                'Lat' => $lines['Lat'],
                'Long' => $lines['Long'],
                'NumAtCard' => $lines['NumAtCard']
              ];
            }
          }
        }

        // dd($data_lines);

        if (count($data_lines) > 0) 
        {
          ArCollectorLines::insert($data_lines);
          ArCollectorHeader::create($data_header);

          $alert = array(
            'type' => 'info',
            'message' => 'Data berhasil di input'
          );
        }
        else
        {
          $alert = array(
            'type' => 'danger',
            'message' => 'Maaf, Data tidak di temukan !!'
          );
        }
      }
      else
      {
        $alert = array(
          'type' => 'danger',
          'message' => 'Maaf, Data telah di generate !!'
        );
      }
    }
    else
    {
      $alert = array(
        'type' => 'danger',
        'message' => 'Maaf, Data tidak di temukan !!'
      );
    }

    return redirect()->back()->with($alert);
  }

  public function detail(Request $request)
  {
    $id = $request->id;

    $row = $this->service->getDataDetail($id);

    // dd($row);

    $data = [
      'title' => "Detail Collector",
      'row' => $row
    ];

    return view('sap.collector.detail')->with($data);
  }

  public function search_collector(Request $request)
  {
    $branch_code = $request->branch_code;

    $code = Warehouse::where('code',$branch_code)
                     ->first();

    $get = User::where('branch_sap', $code->Branch)
                ->where('users_role_id',4)
                ->get();
                      
    $list = "<option value=''>-- Pilih Collector --</option>";
    foreach ($get as $key) 
    {
      $list .= "<option value='" . $key->id . "'>" . $key->name. "</option>";
    }

    $callback = array('listdoc' => $list);
    echo json_encode($callback);
  }

  public function start_day($kd)
  {
    $get = ArCollectorHeader::where('kd',$kd)->first();
    $user = User::find($get->users_collector_id);

    $data = [
      'users_admin_st' => 1
    ];

    ArCollectorHeader::where('kd',$kd)->update($data);

    $json = [
      'to' => $user->fcm,
      'notification' => [
        'title' => 'Data Collector Baru !!',
        'body' => 'Admin mengirim tugas baru, harap segera cek apps anda !',
        'mutable_content' => false
      ]
    ];

    $this->service->notifikasiFirebase(json_encode($json));

    $alert = array(
      'type' => 'info',
      'message' => 'Dokumen berhasil di serah terima !'
    );

    return redirect()->back()->with($alert);
  }

  public function report_serah_terima()
  {
    $id = auth()->user()->id;
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/datatables/custom.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/datatables/datatables.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    if ($users_role==1) 
    {
      $collector = User::where('users_role_id',4)->pluck('name','id');
      $branch = Warehouse::pluck('code','code');

      $row = $this->service->getDataAll();
    }
    else
    {
      $collector = User::where('branch_sap',$branch_sap)
                        ->where('users_role_id',4)
                        ->pluck('name','id');

      $bpl = Branch::find($branch_sap)->BPLid;
      $branch = Warehouse::where('BPLId',$bpl)->pluck('code','code');

      $row = $this->service->getDataByUser($id);
    }

    // dd($row);

    $data = [
      'title' => 'Report Serah Terima - Tagihan',
      'assets' => $assets,
      'branch' => $branch,
      'collector' => $collector,
      'row' => $row
    ];

    return view('sap.collector.report.serah_terima')->with($data);
  }

  public function report_serah_terima_search(Request $request)
  {
    $branch = $request->branch_code;
    $date = $request->date;
    $user = $request->users_id;

    $row = $this->service->reportSerahTerima($branch,$user,$date);

    // dd($row);
    
    $data = [
      'row' => $row
    ];
    
    return view('sap.collector.report.serah_terima_view')->with($data);
  }

  public function report_rekap_penagihan()
  {
    $id = auth()->user()->id;
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/printArea/jquery.PrintArea.js'
      )
    ];

    if ($users_role==1) 
    {
      $branch = Warehouse::pluck('code','code');
    }
    else
    {
      $bpl = Branch::find($branch_sap)->BPLid;
      $branch = Warehouse::where('BPLId',$bpl)->pluck('code','code');
    }

    // dd($row);

    $data = [
      'title' => 'Rekapitalusi Setoran Penagihan',
      'assets' => $assets,
      'branch' => $branch,
    ];

    return view('sap.collector.report.rekap_penagihan')->with($data);
  }

  public function report_rekap_penagihan_search(Request $request)
  {
    $branch = $request->branch_code;
    $date = $request->date;

    $row = $this->service->reportRekapPenagihan($branch,$date);

    // dd($row);
    
    $data = [
      'row' => $row
    ];
    
    return view('sap.collector.report.rekap_penagihan_view')->with($data);
  }

  public function report_compliance_collector()
  {
    $id = auth()->user()->id;
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

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

    if ($users_role==1) 
    {
      $branch = Warehouse::pluck('code','code');
    }
    else
    {
      $bpl = Branch::find($branch_sap)->BPLid;
      $branch = Warehouse::where('BPLId',$bpl)->pluck('code','code');
    }

    // dd($row);

    $data = [
      'title' => 'Daily - Collector Compliance',
      'assets' => $assets,
      'branch' => $branch,
    ];

    return view('sap.collector.report.collector_compliance')->with($data);
  }

  public function report_compliance_collector_search(Request $request)
  {
    $branch = $request->branch_code;
    $date = $request->date;

    $row = $this->service->reportComplianceCollector($branch,$date);

    // dd($row);
    
    $data = [
      'row' => $row
    ];
    
    return view('sap.collector.report.collector_compliance_view')->with($data);
  }

  public function delete($id)
  {
    ArCollectorHeader::where('kd',$id)->delete();
    ArCollectorLines::where('ar_collector_header_kd',$id)->delete();
    
    $alert = array(
      'type' => 'info',
      'message' => 'Dokumen berhasil di hapus !'
    );

    return redirect()->back()->with($alert);
  }

  public function cek()
  {
    $get = ArCollectorHeader::get();

    foreach ($get as $value) 
    {
      $kd = $value->kd;
      $user = $value->users_collector_id;

      $lines = ArCollectorLines::where('ar_collector_header_kd',$kd)
                               ->whereNull('users_collector_id')
                               ->get();

      foreach ($lines as $key) 
      {
        $data = [
          'users_collector_id' => $user
        ];

        ArCollectorLines::find($key->id)->update($data);
      }
    }

    // dd($get);
  }

  public function additional($kd)
  {
    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/datatables/custom.css'
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/datatables/datatables.min.js',
      )
    ];

    $data = [
      'title' => 'Additional Tasks',
      'assets' => $assets ,
      'kd' => $kd
    ];

    return view('sap.collector.additional.index')->with($data);
  }

  public function additional_customer(Request $request)
  {
    $cust = $request->cardCode;
    $kd = $request->kd;

    $get = ArCollectorHeader::where('kd',$kd)->first();

    $company = $get->company_id;
    $api = 'getCustomerByClass';

    $post = [
      'U_CLASS' => $get->branch_code,
      'CardCode' => $cust,
    ];

    // dd($post);
    if ($company==1) 
    {
      $row = callSapApiLtaWithPost($api,json_encode($post));
    }    
    else
    {
      $row = callSapApiTaaWithPost($api,json_encode($post));
    }

    $data = [
      'row' => $row,
      'kd' => $kd
    ];
    
    return view('sap.collector.additional.search_customer')->with($data);
  }

  public function additional_customer_search(Request $request)
  {
    // dd($request->all());

    $cust = $request->id;
    $kd = $request->kd;

    $post_customer = [
      'CardCode' => $cust
    ];

    $custx = getCustomerId(json_encode($post_customer));

    $get = ArCollectorHeader::where('kd',$kd)->first();

    $json = [
      'kd' => $kd,
      'CardCode' => $cust,
      'CardName' => $custx['CardName'],
      'DocDueDate' => $get->date,
      'Kategori' => $get->category,
      'SlpName' => $get->user_collector->name,
      'Branch' => $get->branch_code,
      'Company' => $get->company_id
    ];

    return response()->json($json);
  }

  public function additional_invoice(Request $request)
  {
    $kd = $request->kd;
    $company = $request->Company;
    $api = 'generateCollectorAdditional';

    $post = [
      'CardCode' => $request->CardCode,
      'Kategori' => $request->Kategori,
      'SlpName' => $request->SlpName
    ];

    if ($company==1) 
    {
      $row = callSapApiLtaWithPost($api,json_encode($post));
    }
    else
    {
      $row = callSapApiTaaWithPost($api,json_encode($post));
    }
    

    $datax = [];

    foreach ($row as $item) 
    {
      $docNum = explode('/',$item['DocNum']);
      $netto = $item['Netto'];
      $balanceDue = $item['BalanceDue'];
      $balance = $netto - $balanceDue;

      $cekLines = ArCollectorLines::where('DocNum',$docNum[1])
                                  ->where('status',0)
                                  ->get();
      if(count($cekLines)==0)
      {
        $datax[] = [
          'DocEntry' => $item['DocEntry'],
          'DocNum' => $docNum[1],
          'DocNumDO' => $item['DocNumDO'],
          'CardCode' => $item['CardCode'],
          'CardName' => $item['CardName'],
          'GroupCode' => $item['GroupCode'],
          'DocDueDate' => $item['DocDueDate'],
          'DocDate' => $item['DocDate'],
          'NumAtCard' => $item['NumAtCard'],
          'OcrCode' => $item['OcrCode'],
          'OcrCode2' => $item['OcrCode2'],
          'Alamat' => $item['Alamat'],
          'Netto' => $netto,
          'BalanceDue' => $balanceDue,
          'Balance' => $balance,
          'Lat' => $item['Lat'],
          'Long' => $item['Long'],
          'Jadwal' => $item['Jadwal']
        ];
      }
    }

    // dd($datax);

    $data = [
      'row' => $datax,
      'kd' => $kd
    ];

    return view('sap.collector.additional.table')->with($data);
  }

  public function additional_generate(Request $request)
  {
    $data = [];

    $kd = $request->kd;

    $get = ArCollectorHeader::where('kd',$kd)->first();

    $check = $request->check;
    $DocEntry = $request->DocEntry;
    $DocNum = $request->DocNum;
    $DocNumDO = $request->DocNumDO;
    $CardCode = $request->CardCode;
    $CardName = $request->CardName;
    $GroupCode = $request->GroupCode;
    $DocDueDate = $request->DocDueDate;
    $DocDate = $request->DocDate;
    $NumAtCard = $request->NumAtCard;
    $OcrCode = $request->OcrCode;
    $OcrCode2 = $request->OcrCode2;
    $Alamat = $request->Alamat;
    $Netto = $request->Netto;
    $BalanceDue = $request->BalanceDue;
    $Lat = $request->Lat;
    $Long = $request->Long;

    foreach ($check as $key => $value) 
    {
      $docNum = $DocNum[$key];
      $netto = $Netto[$key];
      $balanceDue = $BalanceDue[$key];
      $balance = $netto - $balanceDue;

      $cekLines = ArCollectorLines::where('DocNum',$docNum)
                                  ->where('status',0)
                                  ->limit(1)->get();
      if(count($cekLines)==0)
      {
        $data[] = [
          'ar_collector_header_kd' => $kd,
          'users_collector_id' => $get->users_collector_id,
          'DocEntry' => $DocEntry[$key],
          'DocNum' => $docNum,
          'DocNumDO' => $DocNumDO[$key],
          'CardCode' => $CardCode[$key],
          'CardName' => $CardName[$key],
          'GroupCode' => $GroupCode[$key],
          'DocDueDate' => $DocDueDate[$key],
          'DocDate' => $DocDate[$key],
          'NumAtCard' => $NumAtCard[$key],
          'OcrCode' => $OcrCode[$key],
          'OcrCode2' => $OcrCode2[$key],
          'Alamat' => $Alamat[$key],
          'Netto' => $netto,
          'BalanceDue' => $balanceDue,
          'Balance' => $balance,
          'Lat' => $Lat[$key],
          'Long' => $Long[$key],
        ];
      }
    }

    if (count($data)==0) 
    {
      $alert = array(
        'type' => 'danger',
        'message' => 'Data tidak ada / sudah di generate !!'
      );
    }
    else
    {
      ArCollectorLines::insert($data);

      $alert = array(
        'type' => 'success',
        'message' => 'Data additional sukses di generate !!'
      );
    }

    return redirect()->back()->with($alert);
  }

  public function close($kd)
  {
    $data_header = [
      'users_collector_st' => 2
    ];

    ArCollectorHeader::where('kd',$kd)->update($data_header);
    $cek = ArCollectorLines::where('ar_collector_header_kd',$kd)->where('status',0)->get();

    if (count($cek) > 0) 
    {
      foreach ($cek as $value) 
      {
        $data_lines = [
          'status' => 2
        ];

        ArCollectorLines::find($value->id)->update($data_lines);
      }
    }
    

    $alert = array(
      'type' => 'success',
      'message' => 'Data sukses di close !!'
    );

    return redirect()->back()->with($alert);
  }

  public function report_performance()
  {
    $id = auth()->user()->id;
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
      )
    ];

    if ($users_role==1) 
    {
      $collector = User::where('users_role_id',4)->pluck('name','id');
    }
    else
    {
      $collector = User::where('users_role_id',4)
                        ->where('branch_sap',$branch_sap)
                        ->pluck('name','id');
    }

    // dd($row);

    $data = [
      'title' => 'Report - Collector Performance',
      'assets' => $assets,
      'collector' => $collector
    ];

    return view('sap.collector.report.monthly_report')->with($data);
  }

  public function report_performance_search(Request $request)
  {
    $user_collector = $request->users_collector_id;
    $date1 = $request->dateT;
    $date2 = $request->dateF;

    $row = $this->service->reportPerformance($user_collector,$date1,$date2);

    // dd($row);
    
    $data = [
      'row' => $row
    ];
    
    return view('sap.collector.report.monthly_report_view')->with($data);
  }

  public function additional2($kd)
  {
    $assets = [
      'style' => array(
        'assets/plugins/select2/select2.min.css',
      ),
      'script' => array(
        'assets/plugins/select2/select2.min.js',
      )
    ];

    $header = ArCollectorHeader::where('kd',$kd)->first();
    
    $function = 'getCustomerTitip';

    $post = [
      'Kategori' => $header->category
    ];

    if ($header->company_id==1) 
    {
      $customer = callSapApiLtaWithPost($function,json_encode($post));
    }
    else
    {
      $customer = callSapApiTaaWithPost($function,json_encode($post));
    }

    // dd($customer);

    $data = [
      'title' => 'Titip Nota',
      'customer' => isset($customer) ? $customer : [],
      'kd' => $kd,
      'assets' => $assets
    ];

    return view('sap.collector.additional2.index')->with($data);
  }

  public function additional_invoice2(Request $request)
  {
    $kd = $request->kd;
    $api = 'generateCollectorAdditional';

    $header = ArCollectorHeader::where('kd',$kd)->first();
    $company = $header->company_id;
    $kategori = $header->category;
    $sales = $header->user_collector->name;

    $post = [
      'CardCode' => $request->cardCode,
      'Kategori' => $kategori,
      'SlpName' => $sales
    ];

    if ($company==1) 
    {
      $row = callSapApiLtaWithPost($api,json_encode($post));
    }
    else
    {
      $row = callSapApiTaaWithPost($api,json_encode($post));
    }

    // dd($row);

    $datax = [];

    foreach ($row as $item) 
    {
      $docNum = explode('/',$item['DocNum']);
      $netto = $item['Netto'];
      $balanceDue = $item['BalanceDue'];
      $balance = $netto - $balanceDue;

      $cekLines = ArTitip::where('DocNum',$docNum[1])
                                  ->where('status',0)
                                  ->get();
      if(count($cekLines)==0)
      {
        $tipe = [
          'cash' => 'cash',
          'tf' => 'tf',
          'bg' => 'bg' 
        ];

        $datax[] = [
          'DocEntry' => $item['DocEntry'],
          'DocNum' => $docNum[1],
          'DocNumDO' => $item['DocNumDO'],
          'CardCode' => $item['CardCode'],
          'CardName' => $item['CardName'],
          'GroupCode' => $item['GroupCode'],
          'DocDueDate' => $item['DocDueDate'],
          'DocDate' => $item['DocDate'],
          'NumAtCard' => $item['NumAtCard'],
          'OcrCode' => $item['OcrCode'],
          'OcrCode2' => $item['OcrCode2'],
          'Alamat' => $item['Alamat'],
          'Netto' => $netto,
          'BalanceDue' => $balanceDue,
          'Balance' => $balance,
          'Lat' => $item['Lat'],
          'Long' => $item['Long'],
          'Jadwal' => $item['Jadwal'],
          'tipe' => $tipe
        ];
      }
    }

    // dd($datax);

    $data = [
      'row' => $datax,
      'kd' => $kd
    ];

    return view('sap.collector.additional2.table')->with($data);
  }

  public function additional_generate2(Request $request)
  {
    $data = [];

    $kd = $request->kd;

    $get = ArCollectorHeader::where('kd',$kd)->first();

    $check = $request->check;
    $DocEntry = $request->DocEntry;
    $DocNum = $request->DocNum;
    $DocNumDO = $request->DocNumDO;
    $CardCode = $request->CardCode;
    $CardName = $request->CardName;
    $GroupCode = $request->GroupCode;
    $DocDueDate = $request->DocDueDate;
    $DocDate = $request->DocDate;
    $NumAtCard = $request->NumAtCard;
    $OcrCode = $request->OcrCode;
    $OcrCode2 = $request->OcrCode2;
    $Alamat = $request->Alamat;
    $Netto = $request->Netto;
    $BalanceDue = $request->BalanceDue;
    $Lat = $request->Lat;
    $Long = $request->Long;
    $Type = $request->Type;
    $Payment = $request->Payment;

    foreach ($check as $key => $value) 
    {
      $docNum = $DocNum[$key];
      $netto = $Netto[$key];
      $balanceDue = $BalanceDue[$key];
      $balance = $netto - $balanceDue;

      $cekLines = ArTitip::where('DocNum',$docNum)
                                  ->where('status',0)
                                  ->limit(1)->get();
      if(count($cekLines)==0)
      {
        $data[] = [
          'ar_collector_header_kd' => $kd,
          'users_collector_id' => $get->users_collector_id,
          'DocEntry' => $DocEntry[$key],
          'DocNum' => $docNum,
          'DocNumDO' => $DocNumDO[$key],
          'CardCode' => $CardCode[$key],
          'CardName' => $CardName[$key],
          'GroupCode' => $GroupCode[$key],
          'DocDueDate' => $DocDueDate[$key],
          'DocDate' => $DocDate[$key],
          'NumAtCard' => $NumAtCard[$key],
          'OcrCode' => $OcrCode[$key],
          'OcrCode2' => $OcrCode2[$key],
          'Alamat' => $Alamat[$key],
          'Netto' => $netto,
          'BalanceDue' => $balanceDue,
          'Balance' => $balance,
          'Lat' => $Lat[$key],
          'Long' => $Long[$key],
          'status' => 0
        ];
      }
    }

    // dd($data);

    if (count($data)==0) 
    {
      $alert = array(
        'type' => 'danger',
        'message' => 'Data tidak ada / sudah di generate !!'
      );
    }
    else
    {
      ArTitip::insert($data);

      $alert = array(
        'type' => 'success',
        'message' => 'Data titip nota sukses di generate !!'
      );
    }

    return redirect()->back()->with($alert);
  }

  public function track_collector()
  {
    $id = auth()->user()->id;
    $branch_sap = auth()->user()->branch_sap;
    $users_role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/air-datepicker/css/datepicker.min.css',
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
      ),
      'script' => array(
        'assets/plugins/air-datepicker/js/datepicker.min.js',
				'assets/plugins/air-datepicker/js/i18n/datepicker.en.js',
        'assets/plugins/select2/select2.min.js',
      )
    ];

    if ($users_role==1) 
    {
      $collector = User::where('users_role_id',4)->pluck('name','id');
    }
    else
    {
      $collector = User::where('users_role_id',4)
                        ->where('branch_sap',$branch_sap)
                        ->pluck('name','id');
    }

    // dd($row);

    $data = [
      'title' => 'Track Collector',
      'assets' => $assets,
      'collector' => $collector
    ];

    return view('sap.collector.report.track_collector')->with($data);
  }

  public function track_collector_search(Request $request)
  {
    $user_collector = $request->users_collector_id;
    $date1 = $request->dateT;

    $row = $this->service->trackCollector($user_collector,$date1);

    // dd($row);
    
    $data = [
      'row' => $row
    ];
    
    return view('sap.collector.report.track_collector_view')->with($data);
  }

  public function generateHeaderEx()
  {
    $collector = '85';

    $get = ArCollectorLines::where('users_collector_id',$collector)
                           ->groupBy('ar_collector_header_kd')
                           ->get();

    foreach ($get as $key => $value) 
    {
      $data[] = [
        'kd' => $value->ar_collector_header_kd,
        'users_collector_id' => $collector,
        'users_collector_st' => 2,
        'branch_code' => 'BPP',
        'category' => 'COLLECTOR1',
        'date' => isset($value->updated_at) ? $value->updated_at : date('Y-m-d'),
        'company_id' => 1,
        'users_admin_id' => 88,
        'users_admin_st' => 2
      ]; 
    }

    ArCollectorHeader::insert($data);
  }
}
