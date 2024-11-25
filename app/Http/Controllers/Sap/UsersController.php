<?php

namespace App\Http\Controllers\Sap;

use App\Models\ArCollectorCategory;
use App\Models\Branch;
use App\Http\Controllers\Controller;
use App\Services\UsersServices;
use App\Models\User;
use App\Models\UserCollector;
use App\Models\UserSales;
use App\Models\UsersCollectorSales;
use App\Models\UsersRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
  public function index()
  {
    $rolex = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    $service = new UsersServices;

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

    $row = $service->getData();

    if ($rolex==1) 
    {
      $branch = Branch::pluck('title','id');
      $role = UsersRole::pluck('title','id');
    } 
    else 
    {
      $branch = Branch::where('id',$branchx)->pluck('title','id');
      $role = UsersRole::whereIn('id',['4','5'])->pluck('title','id');
    }

    $data = [
      'title' => 'Users Management',
      'assets' => $assets,
      'branch' => $branch,
      'role' => $role,
      'role_id' => $rolex,
      'row' => $row
    ];

    return view('sap.users.index')->with($data);
  }

  public function store(Request $request)
  {
    $name = $request->name;

    $cek = User::where('name',$name)->get();

    if (count($cek)==0) 
    {
      $data = $request->all();
      $data['password'] = Hash::make($request->password);

      User::create($data);

      $alert = array(
        'type' => 'info',
        'message' => 'Data berhasil di input'
      );
    }
    else
    {
      $alert = array(
        'type' => 'danger',
        'message' => 'Maaf, data sudah di input !!!'
      );
    }

    return redirect()->back()->with($alert);
  }

  public function collector(Request $request)
  {
    $id = $request->id;

    $collector = ArCollectorCategory::get();

    $row = UserCollector::where('users_id',$id)->first();
    $users_id = isset($row->users_id) ? $row->users_id : $id;
    $collector_id = isset($row->collector_id) ? $row->collector_id : '';

    $data = [
      'users_id' => $users_id,
      'collector_id' => $collector_id,
      'collector' => $collector
    ];

    return view('sap.users.collector_update')->with($data);
  }

  public function collector_update(Request $request)
  {
    $cek = UserCollector::where('users_id',$request->users_id)
                        ->get();

    if (count($cek)==0) 
    {
      $data = [
        'users_id' => $request->users_id,
        'collector_id' => $request->collector_id
      ];

      UserCollector::create($data);
    }
    else
    {
      foreach ($cek as $key => $value) 
      {
        $id = $value->id;
      }

      $data = [
        'collector_id' => $request->collector_id
      ];

      UserCollector::find($id)->update($data);
    }

    $alert = array(
      'type' => 'info',
      'message' => 'Data berhasil di update'
    );

    return redirect()->back()->with($alert);
  }

  public function edit(Request $request)
  {
    $rolex = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    if ($rolex==1) 
    {
      $branch = Branch::pluck('title','id');
      $role = UsersRole::pluck('title','id');
    } 
    else 
    {
      $branch = Branch::where('id',$branchx)->pluck('title','id');
      $role = UsersRole::whereIn('id',['4','5'])->pluck('title','id');
    }

    $row = User::find($request->id);

    $data = [
      'title' => 'Users Management - Edit Data',
      'branch' => $branch,
      'role' => $role,
      'role_id' => $rolex,
      'row' => $row
    ];

    return view('sap.users.edit')->with($data);
  }

  public function update(Request $request, $id)
  {
    $password = $request->password;

    $data = [
      'name' => $request->name,
      'username' => $request->username,
      'branch_sap' => $request->branch_sap,
      'users_role_id' => $request->users_role_id
    ];

    if (empty($password)) 
    {
      User::find($id)->update($data);
    }
    else
    {
      $data['password'] = Hash::make($request->password);
      User::find($id)->update($data);
    }

    $alert = array(
      'type' => 'info',
      'message' => 'Data berhasil di update'
    );

    return redirect()->back()->with($alert);
  }

  public function sales_collector(Request $request)
  {
    $id = $request->id;

    $users = User::find($id);

    $sales = User::where('branch_sap',$users->branch_sap)
                 ->where('users_role_id',4)
                 ->pluck('name','id');
    
    $row = UsersCollectorSales::where('users_id',$id)->get();

    $data = [
      'users_id' => $id,
      'sales' => $sales,
      'row' => $row
    ];

    return view('sap.users.sales_collector')->with($data);
  }

  public function sales_collector_store(Request $request)
  {
    $user_detail = User::find($request->users_sales_id);

    $data = [
      'users_id' => $request->users_id,
      'users_sales_id' => $request->users_sales_id,
      'users_sales_name' => $user_detail->name
    ];
    
    UsersCollectorSales::create($data);

    $alert = array(
      'type' => 'success',
      'message' => 'Data berhasil di update'
    );

    return redirect()->back()->with($alert);
  }

  public function sales_collector_delete($id)
  {
    # code...
  }

  public function mapping(Request $request)
  {
    $get = User::find($request->id);

    $post_sales = [
      'U_BRANCHCODESFA' => $get->branch_sap
    ];

    $sales = callSapApiLtaWithPost('getSalesByBranch',json_encode($post_sales));

    $data = [
      'users_id' => $request->id,
      'sales' => $sales
    ];

    return view('sap.users.mapping')->with($data);
  }

  public function mapping_update(Request $request)
  {
    // dd($request->all());

    $post_sales = [
      'U_SALESCODE' => $request->sales
    ];

    $sales = callSapApiLtaWithPost('getSalesDetail',json_encode($post_sales));

    $data = [
      'users_id' => $request->users_id,
      'SalesPersonCode' => $sales['SlpCode'],
      'SalesPersonCodeSfa' => $sales['U_SALESCODE'],
      'SalesPersonName' => $sales['SlpName']
    ];

    UserSales::create($data);

    $alert = array(
      'type' => 'success',
      'message' => 'Data berhasil di update'
    );

    return redirect()->back()->with($alert);
  }

  public function delete($id)
  {
    User::find($id)->delete();

    $alert = array(
      'type' => 'success',
      'message' => 'Data berhasil di hapus'
    );

    return redirect()->back()->with($alert);
  }
}
