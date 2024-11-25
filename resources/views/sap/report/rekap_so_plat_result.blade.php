<div class="card">
  <div class="card-body">
    <div id="print_page">
      <table border="0" width="100%">
        <tr valign="top">
          <td class="text-center" colspan="5">
            <strong>{{ $title }}</strong> <br>
            Tanggal : <strong>{{ date('d-M-Y',strtotime($date)) }}</strong>
          </td>
        </tr>
        <tr>
          <td><strong>Plat : {{ $sales }}</strong></td>
          <td class="text-right mr-2" colspan="2">
            <strong>{{ $company }}</strong><br>
            User : <strong>{{ $user }}</strong> Gudang : <strong>{{ $gudang }}</strong> Cabang : <strong>{{ $cabang }}</strong>
          </td>
          <td class="text-right" width="70px">
            <img src="{{ asset('assets/images/logo-lta-clear.png') }}" width="60px">
          </td>
        </tr>
      </table>
      <table border="1" width="100%">
        <tr>
          <td class="text-center" rowspan="2">No</td>
          <td class="text-center" rowspan="2">Cust Name</td>
          <td class="text-center" rowspan="2">Item Name</td>
          <td class="text-center" rowspan="2">UoM</td>
          <td class="text-center" colspan="2">Orderan</td>
          <td class="text-center" rowspan="2">Qty Pick</td>
        </tr>
        <tr>
          <td class="text-center">Karton</td>
          <td class="text-center">Satuan</td>
        </tr>
        @foreach ($row as $item)
        <tr>
          <td colspan="2"><strong>{{ $item['CardName'] }}</strong></td>
          <td class="text-right">
            <strong>{{ $item['Plat'] }}</strong>
            <strong style="margin-left:200px;">{{ $item['Address'] }}</strong>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="mr-5">{{ dateExp($item['DocDate']) }}</span>
            <strong>{{ $item['DocNum'] }}</strong>
          </td>
          <td>Tgl Expired :</td>
          <td colspan="4"></td>
        </tr>
        @php
            $no = 1;
            $totalktn = array_sum(array_column($item['Lines'],'Karton'));
            $totalstn = array_sum(array_column($item['Lines'],'Pcs'));
        @endphp
        @foreach ($item['Lines'] as $lines)
        <tr>
          <td class="text-center">{{ $no++ }}</td>
          <td>{{ $lines['CardName'] }}</td>
          <td>{{ $lines['ItemName'] }}</td>
          <td class="text-center">{{ $lines['UomCode'] }}</td>
          <td class="text-right">{{ $lines['Karton'] }}</td>
          <td class="text-right">{{ $lines['Pcs'] }}</td>
          <td></td>
        </tr>
        @endforeach
        <tr class="table-dark">
          <td colspan="4" class="text-center">
            <strong>TOTAL PER INVOICE</strong>
          </td>
          <td class="text-right">{{ $totalktn }}</td>
          <td class="text-right">{{ $totalstn }}</td>
          <td></td>
        </tr>
        @endforeach
        
      </table>
    </div>
  </div>
</div>