<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArTitip extends Model
{
    use SoftDeletes;

    protected $table = 'ar_titip';
    protected $fillable = [
        'ar_collector_header_kd',
        'users_collector_id',
        'DocEntry',
        'DocNum',
        'DocNumDO',
        'CardCode',
        'CardName',
        'DocDueDate',
        'DocDate',
        'OcrCode',
        'OcrCode2',
        'Alamat',
        'Netto',
        'Balance',
        'BalanceDue',
        'GroupCode',
        'Lat',
        'Long',
        'NumAtCard',
        'status'
    ];
}
