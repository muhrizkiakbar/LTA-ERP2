<div class="card">
  <div class="card-body">
    <div id="print_page">
      <table border="0" width="100%">
        <tr valign="top">
          <td rowspan="2" width="30%">
            <h2>GLOBALAN SR</h2>
          </td>
          <td colspan="2"><strong>{{ date('d-M-Y',strtotime($dateFrom)) }} S/D {{ date('d-M-Y',strtotime($dateTo)) }}</strong></td>
          <td rowspan="2">
            <img src="{{ asset('assets/images/logo-lta-clear.png') }}" width="60px">
          </td>
        </tr>
        <tr>
          <td width="40%"><strong>No Polisi : {{ $plat }}</strong></td>
          <td class="text-right mr-2"><strong>{{ $company }}</strong></td>
        </tr>
      </table>
      <table class="table table-bordered table-xs">
        <tr>
          <td class="text-center">Kode Barang</td>
          <td class="text-center">Nama</td>
          <td class="text-center">NISIB</td>
          <td class="text-center">KARTON</td>
          <td class="text-center">Satuan</td>
        </tr>
        @foreach ($row as $item)
        <tr>
          <td>{{ $item['ItemCode'] }}</td>
          <td>{{ $item['Dscription'] }}</td>
          <td class="text-right">{{ round($item['U_NISIB'],0) }}</td>
          <td class="text-right">{{ round($item['KARTON'],0) }}</td>
          <td class="text-right">{{ round($item['SATUAN'],0) }}</td>
        </tr>
        @endforeach
      </table>
      <table border="0" width="70%">
        <tr valign="top">
          <td rowspan="1" colspan="2">
            <strong>JUMLAH SKU TERJUAL : {{ $count }}</strong>
          </td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right"><strong>Total KTN : </strong></td>
          <td class="text-right"><strong>{{ $totalktn }}</strong></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right"><strong>Total STN : </strong></td>
          <td class="text-right"><strong>{{ $totalstn }}</strong></td>
        </tr>
      </table>
    </div>
  </div>
</div>