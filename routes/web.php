<?php

use App\Http\Controllers\Sap\ArcmController;
use App\Http\Controllers\Sap\DashboardController;
use App\Http\Controllers\Sap\DeliveryOrderController;
use App\Http\Controllers\Sap\GpsComplianceController;
use App\Http\Controllers\Sap\InterfacingController;
use App\Http\Controllers\Sap\InvoiceController;
use App\Http\Controllers\Sap\MasterController;
use App\Http\Controllers\Sap\ReportController;
use App\Http\Controllers\Sap\ReturnController;
use App\Http\Controllers\Sap\ReturnRequestController;
use App\Http\Controllers\Sap\SalesOrderController;
use App\Http\Controllers\Sap\SfaMixController;
use App\Http\Controllers\Sap\SfaPngController;
use App\Http\Controllers\Sap\UsersController;
use App\Http\Controllers\Sap\VdistController;
use App\Http\Controllers\Sap\VoucherPairingController;
use App\Http\Controllers\Sap\VoucherReleaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes([
	'register' => false, // Registration Routes...
	'reset' => false, // Password Reset Routes...
	'verify' => false, // Email Verification Routes...
]);

Route::middleware(['auth'])->group(function () {
	Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('/dashboard/logout', [DashboardController::class, 'logout'])->name('dashboard.logout');

	Route::post('/search_customer',[DashboardController::class, 'searchCustomer'])->name('dashboard.search_customer');
  Route::post('/get_customer',[DashboardController::class, 'getCustomer'])->name('dashboard.get_customer');
  Route::post('/searchItem',[DashboardController::class, 'searchItem'])->name('dashboard.searchItem');
  Route::post('/getItemDetail',[DashboardController::class, 'getItemDetail'])->name('dashboard.getItemDetail');
  Route::post('/getPlat',[DashboardController::class, 'getPlat'])->name('dashboard.getPlat');

  Route::get('/history', [DashboardController::class, 'history'])->name('dashboard.history');

	//Sales Order
  Route::get('/sales',[SalesOrderController::class, 'index'])->name('sales');
  Route::get('/sales/create',[SalesOrderController::class, 'create'])->name('sales.create');
  Route::post('/sales/create_temp',[SalesOrderController::class, 'create_temp'])->name('sales.create_temp');
  Route::get('/sales/create_temp_load',[SalesOrderController::class, 'create_temp_load'])->name('sales.create_temp_load');
  Route::post('/sales/create_temp_delete',[SalesOrderController::class, 'create_temp_delete'])->name('sales.create_temp_delete');
  Route::get('/sales/detail/{docnum}',[SalesOrderController::class, 'detail'])->name('sales.detail');
  Route::get('/sales/detail2/{docnum}',[SalesOrderController::class, 'detail2'])->name('sales.detail2');
  Route::post('/sales/detail_temp',[SalesOrderController::class, 'detail_temp'])->name('sales.detail_temp');
  Route::post('/sales/search_docnum',[SalesOrderController::class, 'search_docnum'])->name('sales.search_docnum');
  Route::get('/sales/discount',[SalesOrderController::class, 'discount'])->name('sales.discount');
  Route::post('/sales/discount_update',[SalesOrderController::class, 'discount_update'])->name('sales.discount_update');
  Route::get('/sales/update',[SalesOrderController::class, 'update'])->name('sales.update');
  Route::get('/sales/close',[SalesOrderController::class, 'close'])->name('sales.close');
  Route::get('/sales/delivery',[SalesOrderController::class, 'delivery'])->name('sales.delivery');
  Route::post('/sales/manual',[SalesOrderController::class, 'manual'])->name('sales.manual');
  Route::post('/sales/lines_item',[SalesOrderController::class, 'lines_item'])->name('sales.lines_item');
  Route::post('/sales/lines_item_detail',[SalesOrderController::class, 'lines_item_detail'])->name('sales.lines_item_detail');
  Route::post('/sales/lines_item_store',[SalesOrderController::class, 'lines_item_store'])->name('sales.lines_item_store');
  Route::get('/sales/lines_item_edit',[SalesOrderController::class, 'lines_item_edit'])->name('sales.lines_item_edit');
  Route::post('/sales/lines_item_update',[SalesOrderController::class, 'lines_item_update'])->name('sales.lines_item_update');
  Route::get('/sales/lines_item_delete/{id}',[SalesOrderController::class, 'lines_item_delete'])->name('sales.lines_item_delete');
  Route::get('/sales/check_document',[SalesOrderController::class, 'check_document'])->name('sales.check_document');
  Route::post('/sales/relation_maps',[SalesOrderController::class, 'relation_maps'])->name('sales.relation_maps');
	Route::post('/sales/fixbug',[SalesOrderController::class, 'fixbug'])->name('sales.fixbug');
	Route::post('/sales/lines_batch',[SalesOrderController::class, 'lines_batch'])->name('sales.lines_batch');
	Route::post('/sales/lines_batch_update',[SalesOrderController::class, 'lines_batch_update'])->name('sales.lines_batch_update');
	

	//Delivery Order
  Route::get('/delivery',[DeliveryOrderController::class, 'index'])->name('delivery');
  Route::get('/delivery/search_docnum',[DeliveryOrderController::class, 'search_docnum'])->name('delivery.search_docnum');
  Route::get('/delivery/detail',[DeliveryOrderController::class, 'detail'])->name('delivery.detail');
  Route::get('/delivery/print/{docnum}',[DeliveryOrderController::class, 'print'])->name('delivery.print');
  Route::get('/delivery/invoice',[DeliveryOrderController::class, 'invoice'])->name('delivery.invoice');
  Route::get('/delivery/return_temp',[DeliveryOrderController::class, 'return_temp'])->name('delivery.return_temp');
  Route::get('/delivery/return_check',[DeliveryOrderController::class, 'return_check'])->name('delivery.return_check');
  Route::get('/delivery/discount',[DeliveryOrderController::class, 'discount'])->name('delivery.discount');
  Route::post('/delivery/discount_update',[DeliveryOrderController::class, 'discount_update'])->name('delivery.discount_update');
  Route::get('/delivery/update',[DeliveryOrderController::class, 'update'])->name('delivery.update');
  Route::get('/delivery/print2/{docnum}',[DeliveryOrderController::class, 'print2'])->name('delivery.print2');
  Route::post('/delivery/voucher',[DeliveryOrderController::class, 'voucher'])->name('delivery.voucher');
  Route::post('/delivery/voucher_update',[DeliveryOrderController::class, 'voucher_update'])->name('delivery.voucher_update');
  Route::post('/delivery/voucher_generate',[DeliveryOrderController::class, 'voucher_generate'])->name('delivery.voucher_generate');
  Route::post('/delivery/voucher_release',[DeliveryOrderController::class, 'voucher_release'])->name('delivery.voucher_release');
  Route::get('/delivery/print_obat/{docnum}',[DeliveryOrderController::class, 'print_obat'])->name('delivery.print_obat');
  Route::get('/delivery/print_baru/{docnum}',[DeliveryOrderController::class, 'print_baru'])->name('delivery.print_baru');
  Route::get('/delivery/print_png/{docnum}',[DeliveryOrderController::class, 'print_png'])->name('delivery.print_png');
  Route::get('/delivery/print_png2/{docnum}',[DeliveryOrderController::class, 'print_png2'])->name('delivery.print_png2');
  Route::get('/delivery/print_png3/{docnum}',[DeliveryOrderController::class, 'print_png3'])->name('delivery.print_png3');
  Route::get('/delivery/print_png4/{docnum}',[DeliveryOrderController::class, 'print_png4'])->name('delivery.print_png4');
  Route::get('/delivery/print_png5/{docnum}',[DeliveryOrderController::class, 'print_png5'])->name('delivery.print_png5');
  Route::get('/delivery/print_png6/{docnum}',[DeliveryOrderController::class, 'print_png6'])->name('delivery.print_png6');
  Route::get('/delivery/print_png_dev/{docnum}',[DeliveryOrderController::class, 'print_png_dev'])->name('delivery.print_]png_dev');
  Route::post('/delivery/relation_maps',[DeliveryOrderController::class, 'relation_maps'])->name('delivery.relation_maps');

	//A/R Invoice
  Route::get('/invoice',[InvoiceController::class, 'index'])->name('invoice');
  Route::post('/invoice/search_docnum',[InvoiceController::class, 'search_docnum'])->name('invoice.search_docnum');
  Route::get('/invoice/detail',[InvoiceController::class, 'detail'])->name('invoice.detail');
  Route::post('/invoice/relation_maps',[InvoiceController::class, 'relation_maps'])->name('invoice.relation_maps');

	// Return
	Route::get('/return',[ReturnController::class, 'index'])->name('return');
	Route::get('/return/search',[ReturnController::class, 'search'])->name('return.search');
	Route::get('/return/detail',[ReturnController::class, 'detail'])->name('return.detail');
	Route::get('/return/print/{DocNum}',[ReturnController::class, 'print'])->name('return.print');
	Route::post('/return/pushCreditNotes',[ReturnController::class, 'pushCreditNotes'])->name('return.pushCreditNotes');

	Route::post('/return/delete_lines_mark',[ReturnController::class, 'delete_lines_mark'])->name('return.delete_lines_mark');

	Route::get('/return/temp',[ReturnController::class, 'temp'])->name('return.temp');
  Route::get('/return/temp_lines_edit',[ReturnController::class, 'temp_lines_edit'])->name('return.temp_lines_edit');
  Route::put('/return/temp_lines_update/{id}',[ReturnController::class, 'temp_lines_update'])->name('return.temp_lines_update');
  Route::get('/return/temp_lines_delete/{id}',[ReturnController::class, 'temp_lines_delete'])->name('return.temp_lines_delete');
  Route::get('/return/push',[ReturnController::class, 'push'])->name('return.push');
  Route::post('/return/update_printed',[ReturnController::class, 'update_printed'])->name('return.update_printed');
  

  //SFA MIX
  Route::get('/sfamix',[SfaMixController::class, 'index'])->name('sfamix');
  Route::get('/sfamix/detail',[SfaMixController::class, 'detail'])->name('sfamix.detail');
  Route::get('/sfamix/sync',[SfaMixController::class, 'sync'])->name('sfamix.sync');
  Route::get('/sfamix/push',[SfaMixController::class, 'push'])->name('sfamix.push');
  Route::get('/sfamix/history',[SfaMixController::class, 'history'])->name('sfamix.history');
  Route::post('/sfamix/fixed',[SfaMixController::class, 'fixed'])->name('sfamix.fixed');
  Route::get('/sfamix/close',[SfaMixController::class, 'close'])->name('sfamix.close');
  Route::get('/sfamix/delete/{numAtCard}',[SfaMixController::class, 'delete'])->name('sfamix.delete');

  //SFA P&G
  Route::get('/sfapng',[SfaPngController::class, 'index'])->name('sfapng');
  Route::get('/sfapng/detail',[SfaPngController::class, 'detail'])->name('sfapng.detail');
  Route::get('/sfapng/sync',[SfaPngController::class, 'sync'])->name('sfapng.sync');
  Route::get('/sfapng/push',[SfaPngController::class, 'push'])->name('sfapng.push');
  Route::get('/sfapng/history',[SfaPngController::class, 'history'])->name('sfapng.history');
  Route::get('/sfapng/delete/{numAtCard}',[SfaPngController::class, 'delete'])->name('sfapng.delete');
  Route::post('/sfapng/fixed',[SfaPngController::class, 'fixed'])->name('sfapng.fixed');
  Route::get('/sfapng/close',[SfaPngController::class, 'close'])->name('sfapng.close');

  //Report
  Route::get('/report/globalan',[ReportController::class, 'globalan'])->name('report.globalan');
  Route::post('/report/globalan_search',[ReportController::class, 'globalan_search'])->name('report.globalan_search');
  Route::get('/report/delivery_sales',[ReportController::class, 'delivery_sales'])->name('report.delivery_sales');
  Route::post('/report/delivery_sales_search',[ReportController::class, 'delivery_sales_search'])->name('report.delivery_sales_search');
  Route::get('/report/delivery_plat',[ReportController::class, 'delivery_plat'])->name('report.delivery_plat');
  Route::post('/report/delivery_plat_search',[ReportController::class, 'delivery_plat_search'])->name('report.delivery_plat_search');
  Route::get('/report/rekap_so',[ReportController::class, 'rekap_so'])->name('report.rekap_so');
  Route::post('/report/rekap_so_search',[ReportController::class, 'rekap_so_search'])->name('report.rekap_so_search');
  Route::get('/report/omset',[ReportController::class, 'omset'])->name('report.omset');
  Route::post('/report/omset_search',[ReportController::class, 'omset_search'])->name('report.omset_search');
  Route::post('/report/omset_export',[ReportController::class, 'omset_export'])->name('report.omset_export');
  Route::get('/report/ltomset',[ReportController::class, 'ltomset'])->name('report.ltomset');
  Route::post('/report/ltomset_search',[ReportController::class, 'ltomset_search'])->name('report.ltomset_search');
  Route::post('/report/ltomset_export',[ReportController::class, 'ltomset_export'])->name('report.ltomset_export');
  Route::post('/report/ltomset_export2',[ReportController::class, 'ltomset_export2'])->name('report.ltomset_export2');
  Route::get('/report/rekap_so_plat',[ReportController::class, 'rekap_so_plat'])->name('report.rekap_so_plat');
  Route::post('/report/rekap_so_plat_search',[ReportController::class, 'rekap_so_plat_search'])->name('report.rekap_so_plat_search');
  Route::get('/report/rekap_do_plat',[ReportController::class, 'rekap_do_plat'])->name('report.rekap_do_plat');
  Route::post('/report/rekap_do_plat_search',[ReportController::class, 'rekap_do_plat_search'])->name('report.rekap_do_plat_search');

  //Users Management
  Route::get('/users',[UsersController::class, 'index'])->name('users');
  Route::post('/users/store',[UsersController::class, 'store'])->name('users.store');
  Route::post('/users/edit',[UsersController::class, 'edit'])->name('users.edit');
  Route::put('/users/update/{id}',[UsersController::class, 'update'])->name('users.update');
  Route::get('/users/delete/{id}',[UsersController::class, 'delete'])->name('users.delete');
  Route::post('/users/sales',[UsersController::class, 'sales'])->name('users.sales');
  Route::post('/users/sales_update',[UsersController::class, 'sales_update'])->name('users.sales_update');
  Route::get('/users/sales_delete/{id}',[UsersController::class, 'sales_delete'])->name('users.sales_delete');
  Route::post('/users/collector',[UsersController::class, 'collector'])->name('users.collector');
  Route::post('/users/collector_update',[UsersController::class, 'collector_update'])->name('users.collector_update');
  Route::post('/users/sales_collector',[UsersController::class, 'sales_collector'])->name('users.sales_collector');
  Route::post('/users/sales_collector_store',[UsersController::class, 'sales_collector_store'])->name('users.sales_collector_store');
  Route::get('/users/sales_collector_delete/{id}',[UsersController::class, 'sales_collector_delete'])->name('users.sales_collector_delete');

  Route::post('/users/mapping',[UsersController::class, 'mapping'])->name('users.mapping');
  Route::post('/users/mapping_update',[UsersController::class, 'mapping_update'])->name('users.mapping_update');

  // Route::get('/barcode','Sap\ReportController@barcode');

  Route::get('/gps_compliance/png',[GpsComplianceController::class, 'png'])->name('gps_compliance.png');
  Route::post('/gps_compliance/sync_png',[GpsComplianceController::class, 'sync_png'])->name('gps_compliance.sync_png');
  Route::post('/gps_compliance/view_png',[GpsComplianceController::class, 'view_png'])->name('gps_compliance.view_png');
  Route::get('/gps_compliance/mix',[GpsComplianceController::class, 'mix'])->name('gps_compliance.mix');
  Route::post('/gps_compliance/sync_mix',[GpsComplianceController::class, 'sync_mix'])->name('gps_compliance.sync_mix');
  Route::post('/gps_compliance/view_mix',[GpsComplianceController::class, 'view_mix'])->name('gps_compliance.view_mix');
  Route::post('/gps_compliance/temuan',[GpsComplianceController::class, 'temuan'])->name('gps_compliance.temuan');
  Route::post('/gps_compliance/spv_png',[GpsComplianceController::class, 'spv_png'])->name('gps_compliance.spv_png');
  Route::post('/gps_compliance/spv_mix',[GpsComplianceController::class, 'spv_mix'])->name('gps_compliance.spv_mix');

  // Route::get('/collector','Sap\CollectorController@index')->name('collector');
  // Route::post('/collector/generate','Sap\CollectorController@generate')->name('collector.generate');
  // Route::post('/collector/detail','Sap\CollectorController@detail')->name('collector.detail');
  // Route::post('/collector/search_collector','Sap\CollectorController@search_collector')->name('collector.search_collector');
  // Route::get('/collector/start_day/{kd}','Sap\CollectorController@start_day')->name('collector.start_day');
  // Route::get('/collector/report/serah_terima','Sap\CollectorController@report_serah_terima')->name('collector.report_serah_terima');
  // Route::post('/collector/report/serah_terima_search','Sap\CollectorController@report_serah_terima_search')->name('collector.report_serah_terima_search');
  // Route::get('/collector/report/rekap_penagihan','Sap\CollectorController@report_rekap_penagihan')->name('collector.report_rekap_penagihan');
  // Route::post('/collector/report/rekap_penagihan_search','Sap\CollectorController@report_rekap_penagihan_search')->name('collector.report_rekap_penagihan_search');
  // Route::get('/collector/report/compliance_collector','Sap\CollectorController@report_compliance_collector')->name('collector.report_compliance_collector');
  // Route::post('/collector/report/compliance_collector_search','Sap\CollectorController@report_compliance_collector_search')->name('collector.report_compliance_collector_search');
  // Route::get('/collector/report/tracking_collector','Sap\CollectorController@report_tracking_collector')->name('collector.report_tracking_collector');
  // Route::post('/collector/report/tracking_collector_search','Sap\CollectorController@report_tracking_collector_search')->name('collector.report_tracking_collector_search');
  // Route::get('/collector/delete/{id}','Sap\CollectorController@delete')->name('collector.delete');
  // Route::get('/collector/cek','Sap\CollectorController@cek')->name('collector.cek');
  // Route::get('/collector/additional/{kd}','Sap\CollectorController@additional')->name('collector.additional');
  // Route::post('/collector/additional_customer','Sap\CollectorController@additional_customer')->name('collector.additional_customer');
  // Route::post('/collector/additional_customer_search','Sap\CollectorController@additional_customer_search')->name('collector.additional_customer_search');
  // Route::post('/collector/additional_invoice','Sap\CollectorController@additional_invoice')->name('collector.additional_invoice');
  // Route::post('/collector/additional_generate','Sap\CollectorController@additional_generate')->name('collector.additional_generate');
  // Route::get('/collector/close/{kd}','Sap\CollectorController@close')->name('collector.close');
  // Route::get('/collector/report/performance','Sap\CollectorController@report_performance')->name('collector.report_performance');
  // Route::post('/collector/report/performance_search','Sap\CollectorController@report_performance_search')->name('collector.report_performance_search');
  // Route::get('/collector/generateHeaderEx','Sap\CollectorController@generateHeaderEx')->name('collector.generateHeaderEx');


  // Route::get('/incoming_payment','Sap\IncomingPaymentController@index')->name('incoming_payment');
  // Route::post('/incoming_payment/search','Sap\IncomingPaymentController@search')->name('incoming_payment.search');

  // Route::get('/performance/order_png','Sap\PerformanceController@order_png')->name('performance.order_png');
  // Route::post('/performance/order_png_search','Sap\PerformanceController@order_png_search')->name('performance.order_png_search');

  // Route::post('/chart/collector_progress','Sap\ChartController@collector_progress')->name('chart.collector_progress');

  // Route::get('/listing_api','Sap\ListingApiController@index')->name('dashboard.listing_api');
  // Route::post('/listing_api/store','Sap\ListingApiController@store')->name('dashboard.listing_api_store');

  // Route::get('/collector/additional2/{kd}','Sap\CollectorController@additional2')->name('collector.additional2');
  // Route::post('/collector/additional_invoice2','Sap\CollectorController@additional_invoice2')->name('collector.additional_invoice2');
  // Route::post('/collector/additional_generate2','Sap\CollectorController@additional_generate2')->name('collector.additional_generate2');
  // Route::get('/collector/report/track_collector','Sap\CollectorController@track_collector')->name('collector.track_collector');
  // Route::post('/collector/report/track_collector_search','Sap\CollectorController@track_collector_search')->name('collector.track_collector_search');

  Route::get('/report/unserved_order/mix',[ReportController::class, 'unserved_mix'])->name('report.unserved_order.mix');
  Route::post('/report/unserved_order/mix_search',[ReportController::class, 'unserved_mix_search'])->name('report.unserved_order.mix_search');
  Route::get('/report/unserved_order/mix_export', [ReportController::class, 'unserved_mix_export'])->name('report.unserved_order.mix_export');
  Route::get('/report/unserved_order/png',[ReportController::class, 'unserved_png'])->name('report.unserved_order.png');
  Route::post('/report/unserved_order/png_search',[ReportController::class, 'unserved_png_search'])->name('report.unserved_order.png_search');
  Route::get('/report/unserved_order/png_export', [ReportController::class, 'unserved_png_export'])->name('report.unserved_order.png_export');
  Route::post('/report/unserved_order/png_sap', [ReportController::class, 'unserved_png_sap'])->name('report.unserved_order.unserved_png_sap');

  
  Route::get('/vdist',[VdistController::class, 'index'])->name('vdist');
  Route::post('/vdist/sync',[VdistController::class, 'sync'])->name('vdist.sync');
  Route::post('/vdist/detail',[VdistController::class, 'detail'])->name('vdist.detail');
  Route::post('/vdist/push',[VdistController::class, 'push'])->name('vdist.push');
  Route::get('/vdist/unserved',[VdistController::class, 'unserved'])->name('vdist.unserved');
  Route::post('/vdist/unserved_search',[VdistController::class, 'unserved_search'])->name('vdist.unserved_search');
  Route::get('/vdist/delete/{id}',[VdistController::class, 'delete'])->name('vdist.delete');
  Route::post('/vdist/import',[VdistController::class, 'import'])->name('vdist.import');
  Route::post('/vdist/import_store',[VdistController::class, 'import_store'])->name('vdist.import_store');

  // ARCM
  Route::get('/arcm',[ArcmController::class, 'index'])->name('arcm');
  Route::get('/arcm/search',[ArcmController::class, 'search'])->name('arcm.search');
  Route::get('/arcm/detail',[ArcmController::class, 'detail'])->name('arcm.detail');
  Route::get('/arcm/print/{DocNum}',[ArcmController::class, 'print'])->name('arcm.print');
  Route::get('/arcm/print_kwitansi/{DocNum}',[ArcmController::class, 'print_kwitansi'])->name('arcm.print_kwitansi');
  Route::get('/arcm/print_tanda_terima/{DocNum}',[ArcmController::class, 'print_tanda_terima'])->name('arcm.print_tanda_terima');
  Route::get('/arcm/print_bs/{DocNum}',[ArcmController::class, 'print_bs'])->name('arcm.print_bs');
  Route::post('/arcm/update_printed',[ArcmController::class, 'update_printed'])->name('arcm.update_printed');

  // // E-Return 
  // Route::get('return/approval','Sap\ReturnController@approval')->name('return.approval');
  // Route::post('return/approval/detail','Sap\ReturnController@approval_detail')->name('return.approval.detail');
  // Route::get('return/approval/view/{kd}','Sap\ReturnController@approval_view')->name('return.approval.view');
  // Route::get('return/approval/print/{id}','Sap\ReturnController@approval_print')->name('return.approval.print');

  Route::get('voucher_release',[VoucherReleaseController::class, 'index'])->name('voucher_release');
  Route::post('voucher_release/search',[VoucherReleaseController::class, 'search'])->name('voucher_release.search');
	Route::post('voucher_release/view',[VoucherReleaseController::class, 'view'])->name('voucher_release.view');
  Route::get('voucher_release/delete/{id}',[VoucherReleaseController::class, 'delete'])->name('voucher_release.delete');

  Route::get('/report/cek_penjualan',[ReportController::class, 'cek_penjualan'])->name('report.cek_penjualan');
  Route::post('/report/cek_penjualan_search',[ReportController::class, 'cek_penjualan_search'])->name('report.cek_penjualan_search');
  Route::get('/report/cek_penjualan_export',[ReportController::class, 'cek_penjualan_export'])->name('report.cek_penjualan_export');

	Route::get('/report/cek_penjualan_do',[ReportController::class, 'cek_penjualan_do'])->name('report.cek_penjualan_do');
  Route::post('/report/cek_penjualan_do_search',[ReportController::class, 'cek_penjualan_do_search'])->name('report.cek_penjualan_do_search');
  Route::get('/report/cek_penjualan_do_export',[ReportController::class, 'cek_penjualan_do_export'])->name('report.cek_penjualan_do_export');

	Route::get('voucher_pairing',[VoucherPairingController::class, 'index'])->name('voucher_pairing');
	Route::post('voucher_pairing/search',[VoucherPairingController::class, 'search'])->name('voucher_pairing.search');
	Route::post('voucher_pairing/generate',[VoucherPairingController::class, 'generate'])->name('voucher_pairing.generate');

	Route::get('report/storemaster',[ReportController::class, 'storemaster'])->name('report.storemaster');
	Route::post('report/storemaster_view',[ReportController::class, 'storemaster_view'])->name('report.storemaster_view');
	Route::post('report/storemaster_sync',[ReportController::class, 'storemaster_sync'])->name('report.storemaster_sync');
	Route::get('report/storemaster_export',[ReportController::class, 'storemaster_export'])->name('report.storemaster_export');

	Route::get('interfacing/kino',[InterfacingController::class, 'kino'])->name('interfacing.kino');
	Route::post('interfacing/kino_import',[InterfacingController::class, 'kino_import'])->name('interfacing.kino_import');
	Route::post('interfacing/kino_upload',[InterfacingController::class, 'kino_upload'])->name('interfacing.kino_upload');
	Route::post('interfacing/kino_detail',[InterfacingController::class, 'kino_detail'])->name('interfacing.kino_detail');
	Route::post('interfacing/kino_push',[InterfacingController::class, 'kino_push'])->name('interfacing.kino_push');
	Route::get('interfacing/kino_delete/{id}',[InterfacingController::class, 'kino_delete'])->name('interfacing.kino_delete');

	Route::get('master/sales_employee',[MasterController::class, 'sales_employee'])->name('master.sales_employee');
	Route::post('master/sales_employee_sync',[MasterController::class, 'sales_employee_sync'])->name('master.sales_employee_sync');
	Route::post('master/sales_employee_edit',[MasterController::class, 'sales_employee_edit'])->name('master.sales_employee_edit');
	Route::put('master/sales_employee_update/{id}',[MasterController::class, 'sales_employee_update'])->name('master.sales_employee_update');

	Route::get('master/item_kino',[MasterController::class, 'item_kino'])->name('master.item_kino');
	Route::post('master/item_kino_sync',[MasterController::class, 'item_kino_sync'])->name('master.item_kino_sync');
	Route::post('master/item_kino_edit',[MasterController::class, 'item_kino_edit'])->name('master.item_kino_edit');
	Route::put('master/item_kino_update/{id}',[MasterController::class, 'item_kino_update'])->name('master.item_kino_update');
	Route::post('master/item_kino_export',[MasterController::class, 'item_kino_export'])->name('master.item_kino_export');

	Route::get('return_request',[ReturnRequestController::class, 'index'])->name('return_request');
	Route::get('return_request/temp_load',[ReturnRequestController::class, 'temp_load'])->name('return_request.temp_load');
	Route::post('return_request/search_customer',[ReturnRequestController::class, 'search_customer'])->name('return_request.search_customer');
	Route::post('return_request/select_customer',[ReturnRequestController::class, 'select_customer'])->name('return_request.select_customer');
	Route::post('return_request/search_item',[ReturnRequestController::class, 'search_item'])->name('return_request.search_item');
	Route::post('return_request/select_item',[ReturnRequestController::class, 'select_item'])->name('return_request.select_item');
	Route::post('return_request/temp_store',[ReturnRequestController::class, 'temp_store'])->name('return_request.temp_store');
	Route::post('return_request/temp_delete',[ReturnRequestController::class, 'temp_delete'])->name('return_request.temp_delete');
	Route::post('return_request/discount',[ReturnRequestController::class, 'discount'])->name('return_request.discount');
	Route::post('return_request/discount_update',[ReturnRequestController::class, 'discount_update'])->name('return_request.discount_update');
	Route::post('return_request/store',[ReturnRequestController::class, 'store'])->name('return_request.store');

	Route::get('/report/paket_eko',[ReportController::class, 'paket_eko'])->name('report.paket_eko');
  Route::post('/report/paket_eko_search',[ReportController::class, 'paket_eko_search'])->name('report.paket_eko_search');

	Route::get('/report/order_cut',[ReportController::class, 'order_cut'])->name('report.order_cut');
  Route::get('/report/order_cut_export',[ReportController::class, 'order_cut_export'])->name('report.order_cut_export');
});