<?php
namespace App\Exports;

use App\BosLtomset;
use App\Services\ReportServices;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ExportReportLTOmset implements FromView
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
      'Cabang' => $this->cabang,
      'DateFrom' => $this->dateFrom,
      'DateTo' => $this->dateTo,
      'Sup_Name' => $this->tipe
    ];

    $get = BosLtomset::where('CABANG',$this->cabang)
                     ->where('TGL_JUAL','>=',$this->dateFrom)
                     ->where('TGL_JUAL','<=',$this->dateTo)
                     ->where('SUPP_NAME',$this->tipe)
                     ->get();

    $data = [
      'row' => $get
    ];

    return view('sap.report.ltomset_export',$data);
  }
}
?>