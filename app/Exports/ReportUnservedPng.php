<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Services\ReportServices;

class ReportUnservedPng implements FromView
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(String $branch, String $dateF, String $dateT) {
        $this->branch_code = $branch;
        $this->dateF = $dateF;
        $this->dateT = $dateT;

    }
    public function view(): View
    {
        $report = new ReportServices;
        // $res = $report->unserved_png($this->branch_code, $this->dateF,$this->dateT);
        $res = $report->unservedPngView($this->branch_code, $this->dateF,$this->dateT);

        return view('sap.report.excel.report_unserved_png', [
            'data' => $res['data']
        ]);
    }
}
