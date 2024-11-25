<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VdistHeader extends Model
{
    protected $table = 'vdist_header';
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
        'DocEntry',
        'CardName',
        'Address',
        'SalesPersonName',
        'BLITZ',
        'slug',
				'flag_label'
    ];

    public function getWarehouseDetailAttribute()
    {
        return Warehouse::find($this->Branch);
    }

		public function getWarehouseVdistAttribute()
    {
        return WarehouseVdist::find($this->Branch);
    }
}
