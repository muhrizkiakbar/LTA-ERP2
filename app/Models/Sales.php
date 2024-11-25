<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = 'm_sales';
    protected $fillable = ['id','title','code','code_sap','code_kino'];
}
