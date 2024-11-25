<?php
namespace App\Exports;

use App\Services\ReportServices;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ExportReportOmset implements FromView
{
  use Exportable;

  protected $tipe,$dateFrom,$dateTo,$cabang;

  public function __construct(String $tipe, String $dateFrom, String $dateTo, String $cabang) 
  {
    $this->tipe = $tipe;
    $this->dateFrom = $dateFrom;
    $this->dateTo = $dateTo;
    $this->cabang = $cabang;

  }

  public function view(): View
  {
    $service = new ReportServices;

    $post = [
      "OcrCode2" => $this->tipe,
      "DocDateFrom" => $this->dateFrom,
      "DocDateTo" => $this->dateTo,
      "OcrCode" => $this->cabang
    ];

    $get = $service->reportOmset(json_encode($post));

    $data = [
      'row' => $get
    ];

    return view('sap.report.omset_export',$data);
  }
}
?>