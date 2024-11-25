<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MixHeader extends Model
{
    protected $table = 'mix_header';
    protected $fillable = [
        'DocNum',
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
        'CardName',
        'Address',
        'SalesPersonName',
        'BLITZ'
    ];
}
