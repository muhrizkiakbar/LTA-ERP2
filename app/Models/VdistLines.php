<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VdistLines extends Model
{
    protected $table = 'vdist_lines';
    protected $fillable = [
        'NumAtCard',
        'ItemCode',
        'ItemCodeVdist',
        'ItemName',
        'Quantity',
        'QuantityVdist',
        'TaxCode',
        'UnitPrice',
        'Price',
        'CostingCode',
        'CostingCode2',
        'CostingCode3',
        'WarehouseCode',
        'UoMCode',
        'UoMEntry',
        'UnitsOfMeasurment',
        'LineTotal',
        'DiscountPercent',
        'U_DISC1',
        'U_DISCVALUE1',
        'U_DISC3',
        'U_DISCVALUE3',
        'U_DISC4',
        'U_DISCVALUE4',
				'U_DISC2',
        'U_DISCVALUE2',
				'U_DISC5',
        'U_DISCVALUE5',
				'U_DISC6',
        'U_DISCVALUE6',
				'U_DISC7',
        'U_DISCVALUE7',
				'U_DISC8',
        'U_DISCVALUE8',
				'flag_label'
    ];
}
