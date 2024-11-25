<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PngLinesTemp extends Model
{
	use HasFactory;
	protected $table = 'png_lines_temp';
	protected $fillable = [
		'NumAtCard',
		'ItemCode',
		'ItemName',
		'Quantity',
		'QuantitySfa',
		'QuantitySfaCases',
		'QuantitySfaTotal',
		'TaxCode',
		'UnitPrice',
		'UnitMsr',
		'UomCode',
		'UomEntry',
		'NumPerMsr',
		'UnitPrice2',
		'UnitMsr2',
		'UomCode2',
		'UomEntry2',
		'NumPerMsr2',
		'CostingCode',
		'CostingCode2',
		'CostingCode3',
		'WarehouseCode',
		'SalesPersonCode',
		'SalesPersonName'
	];
}
