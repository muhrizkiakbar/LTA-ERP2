<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseList extends Model
{
    protected $table = 'm_warehouse_list';
    protected $fillable = ['title','code'];
}
