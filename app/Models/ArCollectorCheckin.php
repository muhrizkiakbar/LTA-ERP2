<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArCollectorCheckin extends Model
{
    protected $table = 'ar_collector_checkin';
    protected $fillable = [
        'ar_collector_header_kd',
        'users_id',
        'CardCode',
        'CardName',
        'lat',
        'long',
        'datetime',
        'type',
        'file',
    ];
}
