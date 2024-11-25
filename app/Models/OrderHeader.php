<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHeader extends Model
{
    protected $table = 'order_header';
    protected $fillable = [
        'Branch',
        'CardCode',
        'DocDueDate',
        'NumAtCard',
        'DocDate',
        'BPLId',
        'SalesPersonCode',
        'U_NOPOLISI',
        'U_NOPOLISI2',
        'Comments',
        'VatSum',
        'DocTotal',
        'DocStatus',
        'DocNum',
        'DocEntry',
        'OcrCode',
        'OcrCode2'
    ];
}
