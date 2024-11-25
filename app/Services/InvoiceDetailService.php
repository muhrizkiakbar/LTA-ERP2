<?php

namespace App\Services;

use App\Models\InvoiceDetail;

class InvoiceDetailService
{
  public function getDataLimit($limit)
  {
    $data = [];

    $get = InvoiceDetail::orderBy('doc_entry','DESC')
                        ->limit($limit)
                        ->get();
    
    foreach ($get as $value) 
    {
      $data[] = [
        'kode_distributor' => '2000277543',
        'kode_branch' => $value->kode_branch,
        'kode_sls_rep' => $value->kode_sls_rep,
        'no_invoice' => $value->no_invoice,
        'tgl_invoice' => $value->tgl_invoice,
        'kode_retailer' => $value->kode_retailer,
        'kode_item' => $value->kode_item,
        'jumlah' => $value->jumlah,
        'nilai' => $value->nilai,
        'nilai_tagihan' => $value->nilai_tagihan,
        'order_ref_no' => $value->order_ref_no,
        'hash' => $value->hash
      ];
    }
    
    return $data;
  }
}