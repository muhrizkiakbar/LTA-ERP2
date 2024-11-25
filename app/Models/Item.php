<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'm_item';
    protected $fillable = ['code','title','flag_bonus','flag_active'];
}
