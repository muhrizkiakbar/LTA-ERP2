<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryVoucher extends Model
{
    protected $table = 'delivery_voucher';
    protected $fillable = [
        'DocEntry',
        'DocNum',
        'DocDate',
        'DocDueDate',
        'CardCode',
        'CardName',
        'NumAtCard',
        'DocTotal',
        'PaidToDate',
        'BalanceDue',
        'DocNumDelivery',
        'Comments',
        'OcrCode2'
    ];
}
