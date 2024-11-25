<div class="card">
  <div class="card-body">
    <div id="print_page">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
          <tr>
            <td>Tipe</td>
            <td>Tanggal Jual</td>
            <td>Periode</td>
            <td>Cabang</td>
            <td>No. Faktur</td>
            <td>Cust Code</td>
            <td>Cust Name</td>
            <td>Pasar</td>
            <td>Kota</td>
            <td>Supp Name</td>
            <td>Kategori</td>
            <td>Brand Name</td>
            <td>Varian Name</td>
            <td>Class Name</td>
            <td>Item Code</td>
            <td>Item Name</td>
            <td>Isi Besar</td>
            <td>Satuan Besar</td>
            <td>Satuan</td>
            <td>Harga Jual</td>
            <td>Qty Jual</td>
            <td>Konversi</td>
            <td>Diskon 1</td>
            <td>Diskon 2</td>
            <td>Diskon 3</td>
            <td>Diskon 4</td>
            <td>Diskon 5</td>
            <td>Sales Code</td>
            <td>Sales Name</td>
            <td>Supervisor</td>
            <td>Bruto</td>
            <td>Total Disc</td>
            <td>Disc Value</td>
            <td>Disc Golden</td>
            <td>Disc Bundle</td>
            <td>Total Disc Rp</td>
            <td>Netto</td>
            <td>PPN</td>
            <td>Sub Segment</td>
            <td>SKU</td>
            <td>Group</td>
            <td>Channel</td>
            <td>Ref Number</td>
            <td>Gudang</td>
          </tr>
          @foreach ($row as $item)
          <tr>
            <td>{{ $item['Type'] }}</td>
            <td>{{ dateExp2($item['Tgl_Jual']) }}</td>
            <td>{{ dateExp3($item['Tgl_Jual']) }}</td>
            <td>{{ $item['Cabang'] }}</td>
            <td>{{ $item['No_Faktur'] }}</td>
            <td>{{ $item['Cust_Code'] }}</td>
            <td>{{ $item['Cust_Name'] }}</td>
            <td>{{ $item['Pasar'] }}</td>
            <td>{{ $item['Kota'] }}</td>
            <td>{{ $item['Sup_Name'] }}</td>
            <td>{{ $item['Categori_Name'] }}</td>
            <td>{{ $item['Brand_Name'] }}</td>
            <td>{{ $item['Variant_Name'] }}</td>
            <td>{{ $item['Class_Name'] }}</td>
            <td>{{ $item['ItemCode'] }}</td>
            <td>{{ $item['ItemName'] }}</td>
            <td>{{ $item['Isi_Besar'] }}</td>
            <td>{{ $item['Satuan_Besar'] }}</td>
            <td>{{ $item['satuan_Kecil'] }}</td>
            <td>{{ $item['Harga_Jual'] }}</td>
            <td>{{ $item['Qty_Jual'] }}</td>
            <td>{{ $item['Konversi'] }}</td>
            <td>{{ $item['DISC1'] }}</td>
            <td>{{ $item['DISC2'] }}</td>
            <td>{{ $item['DISC3'] }}</td>
            <td>{{ $item['DISC4'] }}</td>
            <td>{{ $item['DISC5'] }}</td>
            <td>{{ $item['Sales_Code'] }}</td>
            <td>{{ $item['Sales_Name'] }}</td>
            <td>{{ $item['Supervisor'] }}</td>
            <td>{{ $item['Bruto'] }}</td>
            <td>{{ $item['Disc_Total'] }}</td>
            <td>{{ $item['Disc_Value'] }}</td>
            <td>{{ $item['Disc_Golden'] }}</td>
            <td>{{ $item['BUNDLEPRICE'] }}</td>
            <td>{{ $item['Total_DiscRp'] }}</td>
            <td>{{ $item['Netto'] }}</td>
            <td>{{ $item['PPN'] }}</td>
            <td>{{ $item['Sub_Segmen'] }}</td>
            <td>{{ $item['Sku'] }}</td>
            <td>{{ $item['Fix_Group'] }}</td>
            <td>{{ $item['Sku'] }}</td>
            <td>{{ $item['Ref_Number1'] }}</td>
            <td>{{ $item['Gudang'] }}</td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
  </div>
</div>