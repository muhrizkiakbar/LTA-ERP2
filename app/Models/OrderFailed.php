<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFailed extends Model
{
    protected $table = 'order_failed';
    protected $fillable = [
        'DocNum',
        'Branch',
        'CardCode',
        'DocDueDate',
        'NumAtCard',
        'DocDate',
        'VatSum',
        'DocTotal',
        'remarks',
        'code'
    ];
}
