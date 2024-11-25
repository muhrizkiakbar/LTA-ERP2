<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Services\ChartServices;
use Illuminate\Http\Request;

class ChartController extends Controller
{
  public function __construct(ChartServices $service)
  {
    $this->service = $service;
  }

  public function collector_progress(Request $request)
  {
    return view('sap.chart.collector_progress');
  }
}
