<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\CardCode;
use App\Models\History;
use App\Http\Controllers\Controller;
use App\Models\InvoiceDetail;
use App\Models\Item;
use App\Models\OmsetDelivery;
use App\Models\OmsetReturn;
use App\Models\OrderHeader;
use App\Models\OrderLines;
use App\Models\SalesCode;
use App\Services\ApiServices;
use App\Services\InvoiceDetailService;
use App\Services\ReportServices;
use App\Services\SfaMixServices;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Services\SfaPngServices;


class DashboardController extends Controller
{
  public function index()
  {

    $today = \Carbon\Carbon::now()->format('Y-m-d');
    $auth = auth()->user()->branch_sap;
    $branch = Branch::where('id', $auth)->first();

    $so_png = DB::table('png_header')
      ->where('DocDate', $today)
      ->where('Branch', $auth)
      ->get();


    $so_mix = DB::table('mix_header')
      ->where('DocDate', $today)
      ->where('Branch', $auth)
      ->get();

    $sfaPng = (new SfaPngServices);
    $countSfaPng = collect($sfaPng->getData())->count();
    
    $sfaMix = (new SfaMixServices);
    $countSfaMix = collect($sfaMix->getData())->count();

    return view('sap.index', [
      'so' => [
        'png' => collect($so_png)->count(),
        'mix' => collect($so_mix)->count(),
        'unprocess_png' => $so_png->where('DocNum', Null)->count(),
        'unprocess_mix' => $so_mix->where('DocNum', Null)->count(),
      ],
      'sfa' => [
        'png' => $countSfaPng,
        'mix' => $countSfaMix,
      ],
      'meta' => [
        'date' => $today,
        'branch' => $branch->title,
      ]
    ]);
  }

  public function logout()
  {
    auth()->guard('web')->logout(); //JADI KITA LOGOUT SESSION DARI GUARD CUSTOMER
    Session::forget('BPLName'); // Remove Session BPLName
    return redirect(route('login'));
  }

  public function searchCustomer(Request $request)
  {
    $cust = $request->cardName;

    $service = new ApiServices;

    $post = [
      'CardName' => $cust . "%"
    ];

    $get = $service->getCustomer(json_encode($post));

    $data = [
      'row' => $get
    ];

    return view('sap.template.customer')->with($data);
  }

  public function getCustomer(Request $request)
  {
    $cust = $request->id;

    $service = new ApiServices;

    $post = [
      'CardCode' => $cust
    ];

    $get = $service->getCustomerId(json_encode($post));
    // dd($get);
    $CardCode = $get['CardCode'];
    $CardName = $get['CardName'];
    $SalesPersonCode = $get['U_SALESCODE'];
    $segment = $get['cseg4'];

    $post_sales = [
      'SlpCode' => $SalesPersonCode
    ];

    $sales = getSalesDetail(json_encode($post_sales));

    $callback = array(
      'customer' => $CardCode,
      'name' => $CardName,
      'segment' => $segment,
      'nopol1' => $get['NopolMix'],
      'nopol2' => $get['NopolPng'],
      'bplid' => getWarehouseDetail2($get['U_CLASS'])->BPLId,
      'sales' => $sales['SlpCode'],
			'price_list' => $get['PriceList'],
    );

    echo json_encode($callback);
  }

  public function searchItem(Request $request)
  {
    $cust = $request->itemName;
    $code = $request->cardCode;

    $service = new ApiServices;

    $post = [
      'a.ItemName' => "%" . $cust . "%"
    ];

    // dd($code);

    if (empty($code)) {
      $callback = array(
        'message' => 'error'
      );

      echo json_encode($callback);
    } else {
      $get = $service->getItem(json_encode($post));

      $data = [
        'row' => $get
      ];

      return view('sap.template.item')->with($data);
    }
  }

  public function getItemDetail(Request $request)
  {

    $service = new ApiServices;

    $row = $request->all();
    $post = [
      'ItemCode' => $request->id
    ];

    $get = $service->getItemId(json_encode($post));
    // dd($get);

    $cek = Item::where('code', $request->id)->first();
    if (empty($cek)) {
      $item = [
        'code' => $get['ItemCode'],
        'title' => $get['ItemName'],
        'INIT1' => $get['INIT1'],
        'INIT2' => $get['INIT2'],
        'INIT3' => $get['INIT3'],
        'INIT4' => $get['INIT4'],
        'INIT5' => $get['INIT5'],
        'INIT6' => $get['INIT6'],
        'INIT7' => $get['INIT7'],
        'CDB' => $get['CDB']
      ];

      Item::create($item);
    } else {
      $item = [
        'INIT1' => $get['INIT1'],
        'INIT2' => $get['INIT2'],
        'INIT3' => $get['INIT3'],
        'INIT4' => $get['INIT4'],
        'INIT5' => $get['INIT5'],
        'INIT6' => $get['INIT6'],
        'INIT7' => $get['INIT7'],
        'CDB' => $get['CDB']
      ];

      Item::where('code', $request->id)->update($item);
    }

    $post_cust = [
      'CardCode' => $request->cardcode
    ];
    $cust = $service->getCustomerId(json_encode($post_cust));

    // $cust = Customer::where('code',$request->cardcode)->first();
    $class = $cust['U_CLASS'];
    $whs = Warehouse::where('code', $class)->first();
    $warehouse = $whs->title;

    $post2 = [
      'ItemNo' => $request->id,
      'CardCode' => $request->cardcode,
      'WhsCode' => $warehouse
    ];

    $UomData = $service->getUomDetail(json_encode($post2));

    $post_available = [
      'ItemCode' => $request->id,
      'WhsCode' => $warehouse
    ];

    $available = getAvailable(json_encode($post_available));
    $availablex = $available['available'];

    $satuan = [
      'nisik' => $UomData['satuan_kecil'],
      'nisib' => $UomData['satuan_besar']
    ];

    $data = [
      'ItemCode' => $get['ItemCode'],
      'ItemName' => $get['ItemName'],
      'satuan' => $satuan,
      'available' => number_format($availablex, 0, '.', ',') . ' ' . $UomData['satuan_kecil'],
      'warehouse' => $warehouse,
      'CardCode' => $request->cardcode
    ];
    return view('sap.template.item_insert')->with($data);
  }

  public function syncSfaMix()
  {
    $service = new ApiServices;

    $post = [
      'tgl_order' => date('Y-m-d')
    ];

    $get = $service->getOrderDetail(json_encode($post));

    //dd($get['data']);
    $data_header = [];
    $data_lines = [];

    // dd($get['data']);

    foreach ($get['data'] as $key => $header) {
      $cek = $service->cekOrderDetail($header['NumAtCard']);
      if ($cek->count() == 0) {
        $numAtCard = $header['NumAtCard'];
        $branch = $header['kode_branch'];
        $post_cust = [
          'CardCode' => $header['CardCode']
        ];
        $customer = $service->getCustomerId(json_encode($post_cust));

        $post_slp = [
          'U_SALESCODE' => $header['SalesPersonCode']
        ];
        $sales = $service->getSalesDetail(json_encode($post_slp));

        $BplId = $service->getBranchDetail($header['kode_branch'])->BPLid;

        $data_header = [
          'Branch' => $header['kode_branch'],
          'SalesPersonCode' => $sales['SlpCode'],
          'CardCode' => $header['CardCode'],
          'NumAtCard' => $header['NumAtCard'],
          'DocDate' => $header['DocDate'],
          'DocDueDate' => $header['DocDueDate'],
          'Comments' => $header['Comments'],
          'U_NOPOLISI' => isset($customer['NopolMix']) ? $customer['NopolMix'] : '',
          'U_NOPOLISI2' => isset($customer['NopolPng']) ? $customer['NopolPng'] : '',
          'BPLId' => $BplId
        ];

        $post_header = OrderHeader::create($data_header);
        if ($post_header) {
          $this->syncSfaMixLines($numAtCard, $branch);
        }
      }
    }
  }

  public function syncSfaMixLines($numAtCard, $branch)
  {
    $service = new ApiServices;
    $data2 = [];

    $data = [
      'no_order' => $numAtCard
    ];

    $get = $service->getOrderDetailLines(json_encode($data));

    foreach ($get as $key => $lines) {
      $wareHouse = $service->getWareHouse($branch)->whsCode;

      $post2 = [
        'ItemNo' => $lines['kode_child_sku'],
        'CardCode' => $lines['kode_retailer'],
        'WhsCode' => $wareHouse
      ];

      $UomData = $service->getUomDetail(json_encode($post2));
      $UnitMsr = $lines['jml_order'] != 0 ? $UomData['satuan_kecil'] : $UomData['satuan_besar'];
      $NumPerMsr = $lines['jml_order'] != 0 ? 1 : $UomData['nisib'];
      $Quantity = $lines['jml_order'] != 0 ? $lines['jml_order'] : $lines['jml_order_cases'];
      $UnitPrice = $lines['jml_order'] != 0 ? $UomData['harga_jual_pcs'] : $UomData['harga_jual_ktn'];
      $CostingCode2 = $UomData['item_group'];

      $post3 = ['UomEntry' => $UnitMsr];
      $getUomEntry = $service->getUomEntry(json_encode($post3));
      $UomEntry = $getUomEntry['uom_entry'];

      $CostingCode = $service->getBranchDetail($branch)->CostingCode;

      $post_available = [
        'ItemCode' => $lines['kode_child_sku'],
        'WhsCode' => $wareHouse
      ];

      $available = $service->getAvailable(json_encode($post_available));
      // dd($available['available']);
      $availablex = $available['available'];

      if ($availablex > 0) {
        $qty_real = $Quantity * $NumPerMsr;

        if ($availablex > $qty_real) {
          $qty = $qty_real / $NumPerMsr;
        } else {
          if ($UnitMsr == "KTN") {
            $qty = $availablex;
            $UnitMsr = $UomData['satuan_kecil'];
            $NumPerMsr = 1;
            $UnitPrice = $UomData['harga_jual_pcs'];
          } else {
            $qty = $availablex / $NumPerMsr;
          }
        }

        $data2[] = [
          'NumAtCard' => $lines['no_order'],
          'ItemCode' => $lines['kode_child_sku'],
          'Quantity' => $qty,
          'TaxCode' => "PPNO11",
          'UnitPrice' => $UnitPrice,
          'UnitMsr' => $UnitMsr,
          'UomCode' => $UnitMsr,
          'UomEntry' => $UomEntry,
          'NumPerMsr' => $NumPerMsr,
          'CostingCode' => $CostingCode,
          'CostingCode2' => $CostingCode2,
          'CostingCode3' => 'SAL',
        ];
      }
    }

    OrderLines::insert($data2);
  }

  public function closeSales(Request $request)
  {
    $service = new ApiServices;

    $db = "LTALIVE2020_TEST";
    $url = 'https://192.168.1.81:50000/b1s/v1/Login';

    $body = [
      'CompanyDB' => $db,
      'UserName' => 'CON002',
      'Password' => 'lta2022'
    ];

    $api = $service->callApiLogin($body, $url);

    $sessionId = $api['SessionId'];
    $routeId = ".node1";
    $headers = "B1SESSION=" . $sessionId . "; ROUTEID=" . $routeId;

    $header = [
      "B1S-ReplaceCollectionsOnPatch: True",
      "Cookie: " . $headers,
      "accept: */*",
      "accept-language: en-US,en;q=0.8",
      "content-type: application/json",
    ];

    $url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders(' . $docentry . ')/Close';
    $api_sales = $service->getSalesId($header, $url_sales);

    dd($api_sales);
  }

  public function invoiceDetailMix()
  {
    $service = new ApiServices;

    $invoice = InvoiceDetail::orderBy('doc_entry', 'DESC')
      ->limit(1)
      ->first();
    if (empty($invoice)) {
      $post = [];
    } else {
      $post = [
        'inv.DocEntry' => $invoice->doc_entry
      ];
    }

    $get = $service->getInvoiceDetailMix(json_encode($post));

    //dd($post);

    $data = [];

    foreach ($get as $value) {
      $docEntry = $value['doc_entry'];
      $lineNum = $value['line_num'];

      $cek = InvoiceDetail::where('doc_entry', $docEntry)
        ->where('line_num', $lineNum)
        ->count();

      if ($cek == 0) {
        $data[] = [
          'doc_entry' => $value['doc_entry'],
          'line_num' => $value['line_num'],
          'kode_branch' => $value['kode_branch'],
          'kode_sls_rep' => $value['kode_sls_rep'],
          'no_invoice' => $value['no_invoice'],
          'tgl_invoice' => $value['tgl_invoice'],
          'kode_retailer' => $value['kode_retailer'],
          'kode_item' => $value['kode_item'],
          'jumlah' => $value['jumlah'],
          'nilai' => $value['nilai'],
          'nilai_tagihan' => $value['nilai_tagihan'],
          'order_ref_no' => $value['order_ref_no'],
          'hash' => sha1(rand())
        ];
      }
    }

    $count = count($data);

    if ($count < 1000) {
      $data = collect($data);
      $chunks = $data->chunk(200);
    } else {
      $data = collect($data);
      $chunks = $data->chunk(2000);
    }

    // dd($chunks);

    foreach ($chunks as $chunk) {
      DB::table('invoice_detail')->insert($chunk->toArray());
    }

    // dd($data);
  }

  public function invoiceDetail()
  {
    $service = new ApiServices;

    $invoice = OmsetDelivery::orderBy('DocEntry', 'DESC')
      ->limit(1)
      ->first();
    // dd($invoice);

    if (empty($invoice)) {
      $post = [];
    } else {
      $post = [
        'DocEntry' => $invoice->DocEntry
      ];
    }

    $get = $service->getInvoiceDetail(json_encode($post));

    // dd($get);

    $data = [];

    foreach ($get['data'] as $value) {
      $docEntry = $value['DocEntry'];
      $lineNum = $value['LineNum'];

      $cek = OmsetDelivery::where('DocEntry', $docEntry)
        ->where('LineNum', $lineNum)
        ->count();

      if ($cek == 0) {
        $data[] = [
          'Type' => $value['Type'],
          'DocNum' => $value['DocNum'],
          'DocEntry' => $value['DocEntry'],
          'LineNum' => $value['LineNum'],
          'DocDate' => $value['DocDate'],
          'BranchCode' => $value['BranchCode'],
          'OcrCode' => $value['OcrCode'],
          'CardCode' => $value['CardCode'],
          'CardName' => $value['CardName'],
          'Address' => $value['Address'],
          'City' => $value['City'],
          'SupplierName' => $value['SupplierName'],
          'ItemCode' => $value['ItemCode'],
          'ItemName' => $value['ItemName'],
          'BrandName' => $value['BrandName'],
          'Category' => $value['Category'],
          'Variant' => $value['Variant'],
          'ClassName' => $value['ClassName'],
          'Barcode' => $value['Barcode'],
          'NISIB' => $value['NISIB'],
          'SatuanBesar' => $value['SatuanBesar'],
          'SatuanKecil' => $value['SatuanKecil'],
          'NW' => $value['NW'],
          'HargaJual' => $value['HargaJual'],
          'QtyJual' => $value['QtyJual'],
          'Konversi' => $value['Konversi'],
          'U_DISC1' => $value['U_DISC1'],
          'U_DISC2' => $value['U_DISC2'],
          'U_DISC3' => $value['U_DISC3'],
          'U_DISC4' => $value['U_DISC4'],
          'U_DISC5' => $value['U_DISC5'],
          'U_DISC6' => $value['U_DISC6'],
          'U_DISC7' => $value['U_DISC7'],
          'U_DISC8' => $value['U_DISC8'],
          'U_DISCVALUE' => $value['U_DISCVALUE'],
          'DiscFaktur' => $value['DiscFaktur'],
          'SalesCode' => $value['SalesCode'],
          'SalesName' => $value['SalesName'],
          'Supervisor' => $value['Supervisor'],
          'Bruto' => $value['Bruto'],
          'TotalDiscRp' => $value['TotalDiscRp'],
          'Netto' => $value['Netto'],
          'SKU' => $value['SKU'],
          'SubSegmen' => $value['SubSegmen'],
          'U_CDB' => $value['U_CDB'],
          'U_CSKU_STAT' => $value['U_CSKU_STAT'],
          'U_INITIATIVE1' => $value['U_INITIATIVE1'],
          'U_INITIATIVE2' => $value['U_INITIATIVE2'],
          'WhsCode' => $value['WhsCode'],
          'NumAtCard' => $value['NumAtCard'],
          'Keterangan' => $value['Keterangan'],
          'Hash' => sha1(rand())
        ];
      }
    }

    // dd($data);

    $count = count($data);

    if ($count <= 1000) {
      $data = collect($data);
      $chunks = $data->chunk(100);
    } else {
      $data = collect($data);
      $chunks = $data->chunk(2000);
    }

    // dd($chunks);

    foreach ($chunks as $chunk) {
      DB::table('omset_delivery')->insert($chunk->toArray());
    }

    // dd($data);
  }

  public function invoiceDetailReturn()
  {
    $service = new ApiServices;

    $invoice = OmsetReturn::orderBy('DocEntry', 'DESC')
      ->limit(1)
      ->first();
    if (empty($invoice)) {
      $post = [];
    } else {
      $post = [
        'DocEntry' => $invoice->DocEntry
      ];
    }

    $get = $service->getInvoiceDetailReturn(json_encode($post));

    // dd($get);

    $data = [];

    foreach ($get['data'] as $value) {
      $docEntry = $value['DocEntry'];
      $lineNum = $value['LineNum'];

      $cek = OmsetReturn::where('DocEntry', $docEntry)
        ->where('LineNum', $lineNum)
        ->count();

      if ($cek == 0) {
        $data[] = [
          'Type' => $value['Type'],
          'DocNum' => $value['DocNum'],
          'DocEntry' => $value['DocEntry'],
          'LineNum' => $value['LineNum'],
          'DocDate' => $value['DocDate'],
          'BranchCode' => $value['BranchCode'],
          'OcrCode' => $value['OcrCode'],
          'CardCode' => $value['CardCode'],
          'CardName' => $value['CardName'],
          'Address' => $value['Address'],
          'City' => $value['City'],
          'SupplierName' => $value['SupplierName'],
          'ItemCode' => $value['ItemCode'],
          'ItemName' => $value['ItemName'],
          'BrandName' => $value['BrandName'],
          'Category' => $value['Category'],
          'Variant' => $value['Variant'],
          'ClassName' => $value['ClassName'],
          'Barcode' => $value['Barcode'],
          'NISIB' => $value['NISIB'],
          'SatuanBesar' => $value['SatuanBesar'],
          'SatuanKecil' => $value['SatuanKecil'],
          'NW' => $value['NW'],
          'HargaJual' => $value['HargaJual'],
          'QtyJual' => $value['QtyJual'],
          'Konversi' => $value['Konversi'],
          'U_DISC1' => $value['U_DISC1'],
          'U_DISC2' => $value['U_DISC2'],
          'U_DISC3' => $value['U_DISC3'],
          'U_DISC4' => $value['U_DISC4'],
          'U_DISC5' => $value['U_DISC5'],
          'U_DISC6' => $value['U_DISC6'],
          'U_DISC7' => $value['U_DISC7'],
          'U_DISC8' => $value['U_DISC8'],
          'U_DISCVALUE' => $value['U_DISCVALUE'],
          'DiscFaktur' => $value['DiscFaktur'],
          'SalesCode' => $value['SalesCode'],
          'SalesName' => $value['SalesName'],
          'Supervisor' => $value['Supervisor'],
          'Bruto' => $value['Bruto'],
          'TotalDiscRp' => $value['TotalDiscRp'],
          'Netto' => $value['Netto'],
          'SKU' => $value['SKU'],
          'SubSegmen' => $value['SubSegmen'],
          'U_CDB' => $value['U_CDB'],
          'U_CSKU_STAT' => $value['U_CSKU_STAT'],
          'U_INITIATIVE1' => $value['U_INITIATIVE1'],
          'U_INITIATIVE2' => $value['U_INITIATIVE2'],
          'WhsCode' => $value['WhsCode'],
          'NumAtCard' => $value['NumAtCard'],
          'Keterangan' => $value['Keterangan'],
          'Hash' => sha1(rand())
        ];
      }
    }

    // dd($data);

    $count = count($data);

    if ($count <= 1000) {
      $data = collect($data);
      $chunks = $data->chunk(200);
    } else {
      $data = collect($data);
      $chunks = $data->chunk(2000);
    }

    // dd($chunks);

    foreach ($chunks as $chunk) {
      DB::table('omset_return')->insert($chunk->toArray());
    }

    // dd($data);
  }

  public function getInvoiceDetailMix($limit)
  {
    $service = new InvoiceDetailService;

    $get = $service->getDataLimit($limit);

    return $get;
  }

  public function getSales()
  {
    $service = new ApiServices;

    $get = SalesCode::orderBy('id', 'DESC')
      ->limit(1)
      ->first();

    if (empty($get)) {
      $post = [];
    } else {
      $post = [
        'SlpCode' => $get->id
      ];
    }

    $getx = $service->getSales(json_encode($post));

    // dd($getx);

    $data = [];

    foreach ($getx as $value) {
      $data[] = [
        'id' => $value['SlpCode'],
        'title' => $value['SlpName'],
        'sales_code_sfa' => $value['U_SALESCODE']
      ];
    }

    $count = count($data);

    if ($count < 1000) {
      $data = collect($data);
      $chunks = $data->chunk(200);
    } else {
      $data = collect($data);
      $chunks = $data->chunk(300);
    }

    // dd($chunks);

    foreach ($chunks as $chunk) {
      DB::table('sales_code')->insert($chunk->toArray());
    }
  }

  public function getCardCode()
  {
    $service = new ApiServices;

    $get = CardCode::orderBy('card_code', 'DESC')
      ->limit(1)
      ->first();

    if (empty($get)) {
      $post = [];
    } else {
      $post = [
        'CardCode' => $get->card_code
      ];
    }

    $getx = $service->getCardCode(json_encode($post));

    dd($getx);

    $data = [];

    foreach ($getx as $value) {
      $data[] = [
        'card_code' => $value['CardCode'],
        'title' => $value['CardName'],
        'address' => $value['Address']
      ];
    }

    $count = count($data);

    if ($count < 1000) {
      $data = collect($data);
      $chunks = $data->chunk(200);
    } else {
      $data = collect($data);
      $chunks = $data->chunk(300);
    }

    // dd($chunks);

    foreach ($chunks as $chunk) {
      DB::table('card_code')->insert($chunk->toArray());
    }
  }

  // public function getAvailable()
  // {
  //   $service = new ApiServices;

  //   $data = [
  //     "ItemCode" => '4801/05/004/0007',
  //     "WhsCode" => 'SPTWHFB'
  //   ];

  //   $available = $service->getAvailable(json_encode($data));

  //   dd($available);
  // }

  public function history()
  {
    $username = auth()->user()->username;
    $role = auth()->user()->users_role_id;

    $assets = [
      'style' => array(
        'assets/plugins/datatables/custom.css'
      ),
      'script' => array(
        'assets/plugins/datatables/datatables.min.js'
      )
    ];

    $row = [];

    if ($role == 3) {
      $get = History::where('title', $username)
        ->whereNull('action')
        ->OrderBy('id', 'DESC')->limit(500)->get();
    } else {
      $get = History::OrderBy('id', 'DESC')->limit(500)->get();
    }

    foreach ($get as $value) {
      $row[] = [
        'time' => $value->created_at,
        'user' => $value->title,
        'action' => isset($value->action) ? $value->action : $value->history_category->title,
        'desc' => $value->desc
      ];
    }

    $data = [
      'title' => 'History - Log Data',
      'assets' => $assets,
      'row' => $row
    ];

    return view('sap.history')->with($data);
  }

  public function getPlat(Request $request)
  {
    $reportService = new ReportServices;

    $post = [
      'OcrCode' => $request->cabang,
      'OcrCode2' => $request->tipe
    ];

    $get = $reportService->getPlat(json_encode($post));

    // dd($get);

    $list = "<option value=''>-- Pilih Plat --</option>";
    foreach ($get as $key) {
      $list .= "<option value='" . $key['Nopol'] . "'>" . $key['Nopol'] . "</option>";
    }
    $callback = array('listdoc' => $list);
    echo json_encode($callback);
  }
}
