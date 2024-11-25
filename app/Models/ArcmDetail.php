<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArcmDetail extends Model
{
    protected $table = 'arcm_detail';
    protected $fillable = [
        'Series',
        'DocNum', 
        'DocEntry',
        'DocDate', 
        'DocDueDate', 
        'CardCode', 
        'CardName', 
        'Address',
        'SlpName',
        'U_NAME',
        'WhsCode',
        'Bruto',
        'VatSum',
        'Netto',
        'TOP',
        'NumAtCard',
        'PLAT_MIX',
        'PLAT_PNG',
        'OcrCode2',
        'Printed',
				'NumAtCard',
				'SalesPersonCode',
				'Branch',
				'BPLId',
				'Comments',
				'OcrCode',
				'DocStatus'
    ];
}
