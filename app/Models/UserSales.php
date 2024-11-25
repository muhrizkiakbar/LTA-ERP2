<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSales extends Model
{
  protected $table = 'users_sales';

  protected $fillable = ['users_id','SalesPersonCode','SalesPersonCodeSfa','SalesPersonName','WhsCode'];
}
