<table>
  <tr>
    <td>Tanggal Jual</td>
    <td>Periode</td>
    <td>Cabang</td>
    <td>No Faktur</td>
    <td>CKET</td>
    <td>Cust Code</td>
    <td>Cust Name</td>
    <td>Pasar</td>
    <td>Kota</td>
    <td>Supp Name</td>
    <td>Brand Name</td>
    <td>Kategori Name</td>
    <td>Variant Name</td>
    <td>Class Name</td>
    <td>Item Code</td>
    <td>Barcode</td>
    <td>Item Name</td>
    <td>Isi Besar</td>
    <td>Satuan Besar</td>
    <td>Satuan Kecil</td>
    <td>NW</td>
    <td>Harga Jual</td>
    <td>Qty Jual</td>
    <td>Konversi</td>
    <td>Diskon 1</td>
    <td>Diskon 2</td>
    <td>Diskon 3</td>
    <td>Diskon 4</td>
    <td>Diskon 5</td>
    <td>Diskon 6</td>
    <td>Diskon 7</td>
    <td>Diskon 8</td>
    <td>Value Disc</td>
    <td>Disc Faktur</td>
    <td>Sales Code</td>
    <td>Sales Name</td>
    <td>Supervisor</td>
    <td>Bruto</td>
    <td>Total Disc Rp</td>
    <td>Netto</td>
    <td>SKU</td>
    <td>Sub Segment</td>
    <td>Nama Sbd</td>
    <td>Status SKU</td>
    <td>Kode INI1</td>
    <td>Kode INI2</td>
    <td>Keterangan</td>
    <td>C. Gudang</td>
    <td>No Ref</td>
  </tr>
  @foreach ($row as $item)
  <tr>
    <td>{{ $item['TGL_JUAL'] }}</td>
    <td>{{ $item['PERIODE'] }}</td>
    <td>{{ $item['CABANG'] }}</td>
    <td>{{ $item['NO_FAKTUR'] }}</td>
    <td>{{ $item['CKET'] }}</td>
    <td>{{ $item['CUST_CODE'] }}</td>
    <td>{{ $item['CUST_NAME'] }}</td>
    <td>{{ $item['PASAR'] }}</td>
    <td>{{ $item['KOTA'] }}</td>
    <td>{{ $item['SUPP_NAME'] }}</td>
    <td>{{ $item['BRAND_NAME'] }}</td>
    <td>{{ $item['CATEGORY_N'] }}</td>
    <td>{{ $item['VARIANT_NA'] }}</td>
    <td>{{ $item['CLASS_NAME'] }}</td>
    <td>{{ $item['ITEM_CODE'] }}</td>
    <td>{{ $item['CODE_BARCODE'] }}</td>
    <td>{{ $item['ITEM_NAME'] }}</td>
    <td>{{ $item['ISI_BESAR'] }}</td>
    <td>{{ $item['SATUAN_BES'] }}</td>
    <td>{{ $item['SATUAN_KEC'] }}</td>
    <td>{{ $item['NW'] }}</td>
    <td>{{ $item['HARGA_JUAL'] }}</td>
    <td>{{ $item['QTY_JUAL'] }}</td>
    <td>{{ $item['KONVERSI'] }}</td>
    <td>{{ $item['DISC_BRG1'] }}</td>
    <td>{{ $item['DISC_BRG2'] }}</td>
    <td>{{ $item['DISC_BRG3'] }}</td>
    <td>{{ $item['DISC_BRG4'] }}</td>
    <td>{{ $item['DISC_BRG5'] }}</td>
    <td>{{ $item['DISC_BRG6'] }}</td>
    <td>{{ $item['DISC_BRG7'] }}</td>
    <td>{{ $item['DISC_BRG8'] }}</td>
    <td>{{ $item['VALUE_DISC'] }}</td>
    <td>{{ $item['DISC_FAKTU'] }}</td>
    <td>{{ $item['SALES_CODE'] }}</td>
    <td>{{ $item['SALES_NAME'] }}</td>
    <td>{{ $item['SUPERVISOR'] }}</td>
    <td>{{ $item['BRUTTO'] }}</td>
    <td>{{ $item['TOT_DISCRP'] }}</td>
    <td>{{ $item['NETTO'] }}</td>
    <td>{{ $item['SKU'] }}</td>
    <td>{{ $item['SUB_SEGMEN'] }}</td>
    <td>{{ $item['NAMA_SBD'] }}</td>
    <td>{{ $item['STATUS_SKU'] }}</td>
    <td>{{ $item['KODE_INI1'] }}</td>
    <td>{{ $item['KODE_INI2'] }}</td>
    <td>{{ $item['KETERANGAN'] }}</td>
    <td>{{ $item['CGUDANG'] }}</td>
    <td>{{ $item['CUSTREF'] }}</td>
  </tr>
  @endforeach
</table>