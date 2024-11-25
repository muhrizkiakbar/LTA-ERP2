<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncomingPaymentController extends Controller
{
  public function index()
  {
    $date = date('Y-m-d');

    $data = [
      'title' => 'Incoming Payment',
      'date' => $date
    ];

    return view('sap.incoming_payment.index')->with($data);
  }
}
