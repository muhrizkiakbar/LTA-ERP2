<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    protected $table = 'return_detail';
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
        'Printed',
				'NumAtCard',
				'SalesPersonCode',
				'Branch',
				'BPLId',
				'U_NOPOLISI',
				'U_NOPOLISI2',
				'Comments',
				'OcrCode',
				'OcrCode2'
    ];
}
