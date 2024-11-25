<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLinesBatch extends Model
{
    protected $table = 'order_lines_batch';
    protected $fillable = [
        'DocEntry',
				'BatchNumber',
				'Quantity',
				'ItemCode',
				'order_lines_id'
    ];
}
