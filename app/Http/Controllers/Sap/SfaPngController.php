<?php

namespace App\Http\Controllers\Sap;

use App\Models\Branch;
use App\Models\ClosingDate;
use App\Models\History;
use App\Http\Controllers\Controller;
use App\Models\OrderHeader;
use App\Models\OrderLines;
use App\Models\PngHeader;
use App\Models\PngLines;
use App\Services\ApiServices;
use App\Services\SfaMixServices;
use App\Services\SfaPngServices;
use Illuminate\Http\Request;

class SfaPngController extends Controller
{
	public function __construct(SfaPngServices $service)
	{
		$this->service = $service;
	}

	public function index()
	{
		$role = auth()->user()->users_role_id;
		$branchx = auth()->user()->branch_sap;

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

		$get = $this->service->getData();

		if ($role == 1) {
			$branch = Branch::pluck('title', 'id');
		} else {
			$branch = Branch::where('id', $branchx)->pluck('title', 'id');
		}

		// dd($branch);

		$data = [
			'title' => 'Data SFA P&G',
			'assets' => $assets,
			'row' => $get,
			'branch' => $branch,
			'role' => $role
		];

		return view('sap.sfapng.index')->with($data);
	}

	public function sync(Request $request)
	{
		$branch = $request->branch;
		$date = $request->date;

		if (empty($date)) {
			$post = [
				'tgl_order' => date('Y-m-d'),
				'kode_branch' => $branch
			];
		} else {
			$post = [
				'tgl_order' => $date,
				'kode_branch' => $branch
			];
		}

		// dd(json_encode($post));

		$get = $this->service->getOrderDetailBranch(json_encode($post));

		// dd($get);

		$data_header = [];

		if (isset($get['data'])) {
			$no = 1;
			$nox = 0;
			foreach ($get['data'] as $key => $header) {
				$cek = $this->service->cekOrderDetail($header['NumAtCard']);
				if ($cek->count() == 0) {
					$numAtCard = $header['NumAtCard'];
					$branch = $header['Branch'];

					$post_cust = [
						'CardCode' => $header['CardCode']
					];
					$customer = getCustomerId(json_encode($post_cust));

					if (isset($customer['U_CLASS'])) {
						$nopol_mix = $customer['NopolMix'];
						$nopol_png = $customer['NopolPng'];
						$uclass = $customer['U_CLASS'];

						$warehouse = getWarehouseDetail($uclass, $header['SalesPersonCode']);

						$post_slp = [
							'code' => $header['SalesPersonCode']
						];
						$sales = getSalesDetail(json_encode($post_slp));

						// dd($sales);

						$BplId = getBranchDetail($header['Branch'])->BPLid;

						$data_header = [
							'Branch' => $header['Branch'],
							'SalesPersonCode' => $sales['SlpCode'],
							'SalesPersonName' => $sales['SlpName'],
							'CardCode' => $header['CardCode'],
							'CardName' => $customer['CardName'],
							'Address' => $customer['Address'],
							'NumAtCard' => $header['NumAtCard'],
							'DocDate' => $header['DocDate'],
							'DocDueDate' => $header['DocDueDate'],
							'Comments' => $header['Comments'],
							'U_NOPOLISI' => $nopol_mix,
							'U_NOPOLISI2' => $nopol_png,
							'BPLId' => $BplId,
							'BLITZ' => $header['non_im']
						];

						$post_header = PngHeader::create($data_header);
						if ($post_header) {
							// $this->syncSfaMixLines($numAtCard, $warehouse, $header['CardCode'], $uclass);
							$this->syncSfaPngLines($numAtCard, $warehouse, $header['CardCode'], $uclass, $header['Lines']);
						}

						$nox = $no++;
					}
				}
			}

			// dd($nox);

			// dd($data_header);

			if ($nox < 1) {
				$callback = array(
					'message' => 'error'
				);

				echo json_encode($callback);
			} else {
				$callback = array(
					'message' => 'sukses'
				);

				echo json_encode($callback);
			}
		} else {
			$callback = array(
				'message' => 'error'
			);

			echo json_encode($callback);
		}
	}

	public function syncSfaMixLines($numAtCard, $warehouse, $cardcode, $class)
	{
		$data2 = [];

		$data = [
			'no_order' => $numAtCard
		];

		$get = $this->service->getOrderDetailLines(json_encode($data));

		foreach ($get as $key => $lines) {
			$post2 = [
				'ItemNo' => $lines['kode_child_sku'],
				'CardCode' => $cardcode,
				'WhsCode' => $warehouse
			];

			$UomData = $this->service->getUomDetail(json_encode($post2));

			$jml_order = $lines['jml_order'];
			$jml_order_cases = $lines['jml_order_cases'];

			if (isset($UomData)) {
				if ($jml_order > 0 && $jml_order_cases > 0) {
					$UnitMsr = $UomData['satuan_kecil'];
					$NumPerMsr = 1;
					$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;

					$UnitMsr2 = $UomData['satuan_besar'];
					$NumPerMsr2 = $UomData['nisib'];
					$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;

					$Quantity1 = $jml_order;
					$Quantity2 = $jml_order_cases * $NumPerMsr2;
					$Quantity = $Quantity1 + $Quantity2;
					$QuantitySfaTotal = $Quantity;
				} else if ($jml_order > 0) {
					$UnitMsr = $UomData['satuan_kecil'];
					$NumPerMsr = 1;
					$Quantity = $jml_order;
					$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;

					$UnitMsr2 = $UomData['satuan_besar'];
					$NumPerMsr2 = $UomData['nisib'];
					$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;
					$QuantitySfaTotal = $Quantity;
				} else {
					$UnitMsr = $UomData['satuan_besar'];
					$NumPerMsr = $UomData['nisib'];
					$Quantity = $lines['jml_order_cases'];
					$UnitPrice = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;

					$UnitMsr2 = $UomData['satuan_kecil'];
					$NumPerMsr2 = 1;
					$UnitPrice2 = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;
					$QuantitySfaTotal = $Quantity * $NumPerMsr;
				}

				$CostingCode2 = $UomData['item_group'];

				$post3 = ['UomEntry' => $UnitMsr];
				$post4 = ['UomEntry' => $UnitMsr2];


				$post_available = [
					'ItemCode' => $lines['kode_child_sku'],
					'WhsCode' => $warehouse
				];

				$available = getAvailable(json_encode($post_available));
				$availablex = isset($available['available']) ? $available['available'] : 0;

				if ($availablex > 0) {
					$qty_real = $Quantity * $NumPerMsr;

					if ($availablex > $qty_real) {
						$qty = $qty_real / $NumPerMsr;  //true
					} else {
						if ($UnitMsr == "KTN" || $UnitMsr == "CASE" || $UnitMsr == "LSN") {
							$qty = $availablex;
							$UnitMsr = $UomData['satuan_kecil'];
							$NumPerMsr = 1;
							$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;

							$UnitMsr2 = $UomData['satuan_besar'];
							$NumPerMsr2 = $UomData['nisib'];
							$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;

							$post3 = ['UomEntry' => $UomData['satuan_kecil']];
							$post4 = ['UomEntry' => $UomData['satuan_besar']];
						} else {
							$qty = $availablex / $NumPerMsr;
						}
					}
				} else {
					$qty = 0;
				}

				$getUomEntry = getUomEntry(json_encode($post3));
				$UomEntry = isset($getUomEntry['uom_entry']) ? $getUomEntry['uom_entry'] : '';

				$getUomEntry2 = getUomEntry(json_encode($post4));
				$UomEntry2 = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : '';
			} else {
				$UnitMsr = '';
				$UomEntry = '';
				$UnitPrice = '';
				$NumPerMsr = '';
				$UnitMsr2 = '';
				$UomEntry2 = '';
				$UnitPrice2 = '';
				$NumPerMsr2 = '';
				$CostingCode2 = '';
				$qty = 0;
				$Quantity = 0;
				$QuantitySfaTotal = $Quantity;
			}

			$post_item = [
				'ItemCode' => $lines['kode_child_sku']
			];

			$item = getItemId(json_encode($post_item));

			$total = 0;
			$total += $qty * isset($UnitPrice) ? $UnitPrice : 0;

			$data2[] = [
				'NumAtCard' => $lines['no_order'],
				'ItemCode' => $lines['kode_child_sku'],
				'ItemName' => isset($item['ItemName']) ? $item['ItemName'] : '',
				'Quantity' => $qty,
				'QuantitySfa' => $jml_order,
				'QuantitySfaCases' => $jml_order_cases,
				'QuantitySfaTotal' => $QuantitySfaTotal,
				'TaxCode' => "PPNO11",
				'UnitPrice' => isset($UnitPrice) ? $UnitPrice : 0,
				'UnitMsr' => $UnitMsr,
				'UomCode' => $UnitMsr,
				'UomEntry' => $UomEntry,
				'NumPerMsr' => $NumPerMsr,
				'UnitPrice2' => isset($UnitPrice2) ? $UnitPrice2 : 0,
				'UnitMsr2' => $UnitMsr2,
				'UomCode2' => $UnitMsr2,
				'UomEntry2' => $UomEntry2,
				'NumPerMsr2' => $NumPerMsr2,
				'CostingCode' => $class,
				'CostingCode2' => $CostingCode2,
				'CostingCode3' => 'SAL',
				'WarehouseCode' => $warehouse
			];
		}

		// return $data2;

		$post = PngLines::insert($data2);
		if ($post) {
			$update['DocTotal'] = $this->service->getTotal($numAtCard);
			PngHeader::where('NumAtCard', $numAtCard)->update($update);
		}
	}

	public function syncSfaPngLines($numAtCard, $warehouse, $cardcode, $class, $data_lines)
	{
		// $data2 = [];

		// $data = [
		// 	'no_order' => $numAtCard
		// ];

		// $get = $this->service->getOrderDetailLines(json_encode($data));

		foreach ($data_lines as $key => $lines) {
			$post2 = [
				'ItemNo' => $lines['ItemCode'],
				'CardCode' => $cardcode,
				'WhsCode' => $warehouse
			];

			$UomData = $this->service->getUomDetail(json_encode($post2));

			$jml_order = $lines['QuantitySfa'];
			$jml_order_cases = $lines['QuantitySfaCases'];

			if (isset($UomData)) {
				if ($jml_order > 0 && $jml_order_cases > 0) {
					$UnitMsr = $UomData['satuan_kecil'];
					$NumPerMsr = 1;
					$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;

					$UnitMsr2 = $UomData['satuan_besar'];
					$NumPerMsr2 = $UomData['nisib'];
					$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;

					$Quantity1 = $jml_order;
					$Quantity2 = $jml_order_cases * $NumPerMsr2;
					$Quantity = $Quantity1 + $Quantity2;
					$QuantitySfaTotal = $Quantity;
				} else if ($jml_order > 0) {
					$UnitMsr = $UomData['satuan_kecil'];
					$NumPerMsr = 1;
					$Quantity = $jml_order;
					$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;

					$UnitMsr2 = $UomData['satuan_besar'];
					$NumPerMsr2 = $UomData['nisib'];
					$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;
					$QuantitySfaTotal = $Quantity;
				} else {
					$UnitMsr = $UomData['satuan_besar'];
					$NumPerMsr = $UomData['nisib'];
					$Quantity = $jml_order_cases;
					$UnitPrice = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;

					$UnitMsr2 = $UomData['satuan_kecil'];
					$NumPerMsr2 = 1;
					$UnitPrice2 = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;
					$QuantitySfaTotal = $Quantity * $NumPerMsr;
				}

				$CostingCode2 = $UomData['item_group'];

				$post3 = ['UomEntry' => $UnitMsr];
				$post4 = ['UomEntry' => $UnitMsr2];


				$post_available = [
					'ItemCode' => $lines['ItemCode'],
					'WhsCode' => $warehouse
				];

				$available = getAvailable(json_encode($post_available));
				$availablex = isset($available['available']) ? $available['available'] : 0;

				if ($availablex > 0) {
					$qty_real = $Quantity * $NumPerMsr;

					if ($availablex > $qty_real) {
						$qty = $qty_real / $NumPerMsr;  //true
					} else {
						if ($UnitMsr == "KTN" || $UnitMsr == "CASE" || $UnitMsr == "LSN") {
							$qty = $availablex;
							$UnitMsr = $UomData['satuan_kecil'];
							$NumPerMsr = 1;
							$UnitPrice = isset($UomData['harga_jual_pcs']) ?	$UomData['harga_jual_pcs'] : 0;

							$UnitMsr2 = $UomData['satuan_besar'];
							$NumPerMsr2 = $UomData['nisib'];
							$UnitPrice2 = isset($UomData['harga_jual_ktn']) ? $UomData['harga_jual_ktn'] : 0;

							$post3 = ['UomEntry' => $UomData['satuan_kecil']];
							$post4 = ['UomEntry' => $UomData['satuan_besar']];
						} else {
							$qty = $availablex / $NumPerMsr;
						}
					}
				} else {
					$qty = 0;
				}

				$getUomEntry = getUomEntry(json_encode($post3));
				$UomEntry = isset($getUomEntry['uom_entry']) ? $getUomEntry['uom_entry'] : '';

				$getUomEntry2 = getUomEntry(json_encode($post4));
				$UomEntry2 = isset($getUomEntry2['uom_entry']) ? $getUomEntry2['uom_entry'] : '';
			} else {
				$UnitMsr = '';
				$UomEntry = '';
				$UnitPrice = '';
				$NumPerMsr = '';
				$UnitMsr2 = '';
				$UomEntry2 = '';
				$UnitPrice2 = '';
				$NumPerMsr2 = '';
				$CostingCode2 = '';
				$qty = 0;
				$Quantity = 0;
				$QuantitySfaTotal = $Quantity;
			}

			// $post_item = [
			// 	'ItemCode' => $lines['kode_child_sku']
			// ];

			// $item = getItemId(json_encode($post_item));

			$total = 0;
			$total += $qty * isset($UnitPrice) ? $UnitPrice : 0;

			$data2[] = [
				'NumAtCard' => $numAtCard,
				'ItemCode' => $lines['ItemCode'],
				'ItemName' => isset($lines['ItemName']) ? $lines['ItemName'] : '',
				'Quantity' => $qty,
				'QuantitySfa' => $jml_order,
				'QuantitySfaCases' => $jml_order_cases,
				'QuantitySfaTotal' => $QuantitySfaTotal,
				'TaxCode' => "PPNO11",
				'UnitPrice' => isset($UnitPrice) ? $UnitPrice : 0,
				'UnitMsr' => $UnitMsr,
				'UomCode' => $UnitMsr,
				'UomEntry' => $UomEntry,
				'NumPerMsr' => $NumPerMsr,
				'UnitPrice2' => isset($UnitPrice2) ? $UnitPrice2 : 0,
				'UnitMsr2' => $UnitMsr2,
				'UomCode2' => $UnitMsr2,
				'UomEntry2' => $UomEntry2,
				'NumPerMsr2' => $NumPerMsr2,
				'CostingCode' => $class,
				'CostingCode2' => $CostingCode2,
				'CostingCode3' => 'SAL',
				'WarehouseCode' => $warehouse
			];
		}

		// return $data2;

		$post = PngLines::insert($data2);
		if ($post) {
			$update['DocTotal'] = $this->service->getTotal($numAtCard);
			PngHeader::where('NumAtCard', $numAtCard)->update($update);
		}
	}

	public function detail(Request $request)
	{
		$role = auth()->user()->users_role_id;
		$id = $request->id;

		$date = date('Y-m-d');
		$date_closing = ClosingDate::where('status', 1)
			->orderBy('id', 'DESC')
			->limit(1)
			->first();

		$get = $this->service->getDataLines($id);

		$data = [
			'title' => 'Detail Order',
			'row' => $get,
			'id' => $id,
			'role' => $role,
			'closing' => isset($date_closing->date) ? $date_closing->date : '',
			'date' => $date
		];

		return view('sap.sfapng.detail')->with($data);
	}

	public function push(Request $request)
	{
		$user = auth()->user()->username_sap;
		$pass = auth()->user()->password_sap;
		$username = auth()->user()->username;

		$apiService = new ApiServices;

		$id = $request->id;
		$date = $request->docDate;

		$get = PngHeader::where('NumAtCard',$id)->first();
		$docTotal = $get->docTotal;
		$blitz = $get->BLITZ;

		$datexx = '2024-04-03'; 
		
		$json = $this->service->getDataDetail($id, $date);

		// dd($json);

		// dd($totalOrderRupiah);

		$db = 'LTALIVE2020';
		$url = 'https://192.168.1.81:50000/b1s/v1/Login';

		$body = [
			'CompanyDB' => $db,
			'UserName' => $user,
			'Password' => $pass
		];

		$api = $apiService->callApiLogin($body, $url);

		$sessionId = $api['SessionId'];
		$routeId = ".node1";
		$headers = "B1SESSION=" . $sessionId . "; ROUTEID=" . $routeId;

		$header = [
			"Cookie: " . $headers,
			"accept: */*",
			"accept-language: en-US,en;q=0.8",
			"content-type: application/json",
		];

		$period = checkPeriod($date);

		if ($period == "Y" || $period == "C") {
			$callback = array(
				'message' => 'period',
				'text' => 'Maaf, Period sudah di lock'
			);

			echo json_encode($callback);
		} 
		else 
		{
			$totalOrderRupiah = $this->service->getTotal($id);

			// dd($totalOrderRupiah);

			$dpp = 50000;

			if($totalOrderRupiah > 50000 && $blitz==0)
			{
				// dd($header);
				$url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders';
				$api_sales = $this->service->postSales($header, $url_sales, json_encode($json));

				// dd($api_sales);

				if (isset($api_sales['DocNum'])) {
					$lines = $this->decodeJsonLines($api_sales['DocumentLines'], $api_sales['DocEntry'], $api_sales['NumAtCard']);

					$branch = Branch::where('BPLid', $api_sales['BPL_IDAssignedToInvoice'])->first();

					$DocTotal = $api_sales['DocTotal'] - $api_sales['VatSum'];

					$header = [
						'CardCode' => $api_sales['CardCode'],
						'NumAtCard' => $api_sales['NumAtCard'],
						'DocNum' => $api_sales['DocNum'],
						'DocEntry' => $api_sales['DocEntry'],
						'VatSum' => $api_sales['VatSum'],
						'DocTotal' => $DocTotal,
						'DocStatus' => "O",
						'DocDate' => $api_sales['DocDate'],
						'DocDueDate' => $api_sales['DocDueDate'],
						'BPLId' => $api_sales['BPL_IDAssignedToInvoice'],
						'SalesPersonCode' => $api_sales['SalesPersonCode'],
						'U_NOPOLISI' => $api_sales['U_NOPOLISI'],
						'U_NOPOLISI2' => $api_sales['U_NOPOLISI2'],
						'Comments' => $api_sales['Comments'],
						'OcrCode' => $lines[0]['CostingCode'],
						'OcrCode2' => $lines[0]['CostingCode2'],
						'Branch' => $branch->id
					];

					$data2 = [
						'DocNum' => $api_sales['DocNum']
					];
					PngHeader::where('NumAtCard', $id)->update($data2);
					$post = OrderHeader::create($header);
					if ($post) {
						OrderLines::insert($lines);
					}

					$history = [
						'title' => $username,
						'history_category_id' => 1,
						'card_code' => $api_sales['CardCode'],
						'desc' => 'Sukses push data <strong>' . $api_sales['CardCode'] . '</strong> Sales Order ke SAP dengan Document Number <strong>' . $api_sales['DocNum'] . '</strong>'
					];

					History::create($history);

					$callback = array(
						'message' => 'sukses'
					);

					echo json_encode($callback);
				} else {
					$error = $api_sales['error']['message']['value'];

					$history = [
						'title' => $username,
						'history_category_id' => 1,
						'card_code' => $id,
						'desc' => 'Error push data Sales Order ke SAP dengan pesan <strong>' . $error . '</strong>'
					];

					History::create($history);

					$callback = array(
						'message' => 'error'
					);

					echo json_encode($callback);
				}
			}
			else if ($totalOrderRupiah > 50000 && $blitz==2)
			{
				// dd($header);
				$url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders';
				$api_sales = $this->service->postSales($header, $url_sales, json_encode($json));

				// dd($api_sales);

				if (isset($api_sales['DocNum'])) {
					$lines = $this->decodeJsonLines($api_sales['DocumentLines'], $api_sales['DocEntry'], $api_sales['NumAtCard']);

					$branch = Branch::where('BPLid', $api_sales['BPL_IDAssignedToInvoice'])->first();

					$DocTotal = $api_sales['DocTotal'] - $api_sales['VatSum'];

					$header = [
						'CardCode' => $api_sales['CardCode'],
						'NumAtCard' => $api_sales['NumAtCard'],
						'DocNum' => $api_sales['DocNum'],
						'DocEntry' => $api_sales['DocEntry'],
						'VatSum' => $api_sales['VatSum'],
						'DocTotal' => $DocTotal,
						'DocStatus' => "O",
						'DocDate' => $api_sales['DocDate'],
						'DocDueDate' => $api_sales['DocDueDate'],
						'BPLId' => $api_sales['BPL_IDAssignedToInvoice'],
						'SalesPersonCode' => $api_sales['SalesPersonCode'],
						'U_NOPOLISI' => $api_sales['U_NOPOLISI'],
						'U_NOPOLISI2' => $api_sales['U_NOPOLISI2'],
						'Comments' => $api_sales['Comments'],
						'OcrCode' => $lines[0]['CostingCode'],
						'OcrCode2' => $lines[0]['CostingCode2'],
						'Branch' => $branch->id
					];

					$data2 = [
						'DocNum' => $api_sales['DocNum']
					];
					PngHeader::where('NumAtCard', $id)->update($data2);
					$post = OrderHeader::create($header);
					if ($post) {
						OrderLines::insert($lines);
					}

					$history = [
						'title' => $username,
						'history_category_id' => 1,
						'card_code' => $api_sales['CardCode'],
						'desc' => 'Sukses push data <strong>' . $api_sales['CardCode'] . '</strong> Sales Order ke SAP dengan Document Number <strong>' . $api_sales['DocNum'] . '</strong>'
					];

					History::create($history);

					$callback = array(
						'message' => 'sukses'
					);

					echo json_encode($callback);
				} else {
					$error = $api_sales['error']['message']['value'];

					$history = [
						'title' => $username,
						'history_category_id' => 1,
						'card_code' => $id,
						'desc' => 'Error push data Sales Order ke SAP dengan pesan <strong>' . $error . '</strong>'
					];

					History::create($history);

					$callback = array(
						'message' => 'error'
					);

					echo json_encode($callback);
				}
			}
			else if ($blitz==1) 
			{
				// dd($header);
				$url_sales = 'https://192.168.1.81:50000/b1s/v1/Orders';
				$api_sales = $this->service->postSales($header, $url_sales, json_encode($json));

				// dd($api_sales);

				if (isset($api_sales['DocNum'])) {
					$lines = $this->decodeJsonLines($api_sales['DocumentLines'], $api_sales['DocEntry'], $api_sales['NumAtCard']);

					$branch = Branch::where('BPLid', $api_sales['BPL_IDAssignedToInvoice'])->first();

					$DocTotal = $api_sales['DocTotal'] - $api_sales['VatSum'];

					$header = [
						'CardCode' => $api_sales['CardCode'],
						'NumAtCard' => $api_sales['NumAtCard'],
						'DocNum' => $api_sales['DocNum'],
						'DocEntry' => $api_sales['DocEntry'],
						'VatSum' => $api_sales['VatSum'],
						'DocTotal' => $DocTotal,
						'DocStatus' => "O",
						'DocDate' => $api_sales['DocDate'],
						'DocDueDate' => $api_sales['DocDueDate'],
						'BPLId' => $api_sales['BPL_IDAssignedToInvoice'],
						'SalesPersonCode' => $api_sales['SalesPersonCode'],
						'U_NOPOLISI' => $api_sales['U_NOPOLISI'],
						'U_NOPOLISI2' => $api_sales['U_NOPOLISI2'],
						'Comments' => $api_sales['Comments'],
						'OcrCode' => $lines[0]['CostingCode'],
						'OcrCode2' => $lines[0]['CostingCode2'],
						'Branch' => $branch->id
					];

					$data2 = [
						'DocNum' => $api_sales['DocNum']
					];
					PngHeader::where('NumAtCard', $id)->update($data2);
					$post = OrderHeader::create($header);
					if ($post) {
						OrderLines::insert($lines);
					}

					$history = [
						'title' => $username,
						'history_category_id' => 1,
						'card_code' => $api_sales['CardCode'],
						'desc' => 'Sukses push data <strong>' . $api_sales['CardCode'] . '</strong> Sales Order ke SAP dengan Document Number <strong>' . $api_sales['DocNum'] . '</strong>'
					];

					History::create($history);

					$callback = array(
						'message' => 'sukses'
					);

					echo json_encode($callback);
				} else {
					$error = $api_sales['error']['message']['value'];

					$history = [
						'title' => $username,
						'history_category_id' => 1,
						'card_code' => $id,
						'desc' => 'Error push data Sales Order ke SAP dengan pesan <strong>' . $error . '</strong>'
					];

					History::create($history);

					$callback = array(
						'message' => 'error'
					);

					echo json_encode($callback);
				}
			}
			else
			{
				$error = '(1) [SO0010] DPP Tidak Bisa Di bawah Rp 50.000 terkecuali Order BLITZ !';

				$history = [
					'title' => $username,
					'history_category_id' => 1,
					'card_code' => $id,
					'desc' => 'Error push data Sales Order ke SAP dengan pesan <strong>' . $error . '</strong>'
				];

				History::create($history);

				$callback = array(
					'message' => 'error'
				);

				echo json_encode($callback);
			}
		}
	}

	public function decodeJsonLines($row, $docentry, $numatcard)
	{
		$data = [];
		foreach ($row as $value) {
			$data[] = [
				'ItemCode' => $value['ItemCode'],
				'ItemDescription' => $value['ItemDescription'],
				'Quantity' => $value['Quantity'],
				'TaxCode' => $value['TaxCode'],
				'UnitPrice' => $value['UnitPrice'],
				'CostingCode' => $value['CostingCode'],
				'CostingCode2' => $value['CostingCode2'],
				'CostingCode3' => $value['CostingCode3'],
				'UnitMsr' => $value['MeasureUnit'],
				'UomCode' => $value['UoMCode'],
				'UomEntry' => $value['UoMEntry'],
				'NumPerMsr' => $value['UnitsOfMeasurment'],
				'WarehouseCode' => $value['WarehouseCode'],
				'LineNum' => $value['LineNum'],
				'DocEntry' => $docentry,
				'NumAtCard' => $numatcard
			];
		}

		return $data;
	}

	public function delete($numAtCard)
	{
		$delete = PngHeader::where('NumAtCard', $numAtCard)->delete();
		if ($delete) {
			PngLines::where('NumAtCard', $numAtCard)->delete();
		}
		return redirect()->back();
	}

	public function fixed(Request $request)
	{
		$branch = $request->branch;

		$get = $this->service->getDataFixed($branch);

		// dd($get);
		$no = 1;
		$nox = 0;

		foreach ($get as $key => $value) {
			$id = $value['id'];

			if (empty($value['CardName'])) {
				$post_cust = [
					'CardCode' => $value['CardCode']
				];
				$cust = getCustomerId(json_encode($post_cust));

				$data['CardName'] = $cust['CardName'];
			}

			if (empty($value['Address'])) {
				$post_cust = [
					'CardCode' => $value['CardCode']
				];
				$cust = getCustomerId(json_encode($post_cust));

				$data['Address'] = $cust['Address'];
			}



			if (empty($value['SalesPersonName'])) {
				$post_sales = [
					'SlpCode' => $value['SalesPersonCode']
				];
				$sales = getSalesEmployeeId(json_encode($post_sales));

				$data['SalesPersonName'] = $sales;
			}

			$data['DocTotal'] = $this->service->getTotal($value['NumAtCard']);

			PngHeader::find($id)->update($data);

			$nox = $no++;
		}

		if ($nox < 1) {
			$callback = array(
				'message' => 'error'
			);

			echo json_encode($callback);
		} else {
			$callback = array(
				'message' => 'sukses'
			);

			echo json_encode($callback);
		}
	}

	public function close(Request $request)
	{
	}
}
