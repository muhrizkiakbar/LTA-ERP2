<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCollector;

class UsersServices
{
  public function getData()
  {
    $role = auth()->user()->users_role_id;
    $branchx = auth()->user()->branch_sap;

    $data = [];

    if ($role==1) 
    {
      $get = User::orderBy('id','DESC')->get();
    }
    else
    {
      $get = User::where('branch_sap',$branchx)
                 ->whereIn('users_role_id',['4','5'])
                 ->orderBy('id','DESC')->get();
    }
    
    foreach ($get as $key => $value) 
    {
      if ($value->users_role_id==4) 
      {
        $role = $value->role->title.' ('.$this->getCategoryCollector($value->id).')';
      }
      else
      {
        $role = $value->role->title;
      }

      $data[] = [
        'id' => $value->id,
        'nama' => $value->name,
        'username' => $value->username,
        'branch' => isset($value->branch) ? $value->branch->title : '-',
        'role' => $role,
        'role_id' => $value->users_role_id
      ];
    }

    return $data;
  }

  public function getCategoryCollector($id)
  {
    $get = UserCollector::where('users_id',$id)->first();

    if (!empty($get)) 
    {
      $return = isset($get->collector) ? $get->collector->title : '';
    } 
    else 
    {
      $return = '';
    }
    
    return $return;
  }
}