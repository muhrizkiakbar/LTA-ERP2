<?php

namespace App\Http\Controllers\Sap;

use App\Models\Company;
use App\Models\CompanyApi;
use App\Http\Controllers\Controller;
use App\Services\ListingApiServices;
use Illuminate\Http\Request;

class ListingApiController extends Controller
{
  public function __construct(ListingApiServices $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    $assets = [
      'style' => array(
        'assets/plugins/select2/select2.min.css',
        'assets/css/loading.css',
        'assets/plugins/datatables/custom.css'
      ),
      'script' => array(
        'assets/plugins/select2/select2.min.js',
        'assets/plugins/datatables/datatables.min.js',
      )
    ];

    $company = Company::pluck('title','id');

    $row = CompanyApi::orderBy('company_id','ASC')->orderBy('title','ASC')->get();

    $type = [
      '1' => 'Internal',
      '2' => 'External'
    ];

    $data = [
      'title' => 'Listing API',
      'assets' => $assets,
      'company' => $company,
      'row' => $row,
      'type' => $type
    ];

    return view('sap.listing_api.index')->with($data);
  }

  public function store(Request $request)
  {
    $title = $request->title;
    $type = $request->type;
    $desc = $request->desc;

    $company = Company::find($request->company_id);
    

    if ($type==1) 
    {
      $url = $company->prefix_url.$title;
    }
    else
    {
      $url = $request->url;
    }

    $data = [
      'title' => $title,
      'company_id' => $request->company_id,
      'url' => $url,
      'desc' => $desc
    ];

    $cek = CompanyApi::where('title',$title)
                     ->where('company_id',$request->company_id)
                     ->get();

    if(count($cek)==0)
    {
      CompanyApi::create($data);

      $alert = array(
        'type' => 'info',
        'message' => 'Data berhasil di input'
      );
    }
    else
    {
      $alert = array(
        'type' => 'danger',
        'message' => 'API sudah ada !!!'
      );
    }

    return redirect()->back()->with($alert);
  }
}
