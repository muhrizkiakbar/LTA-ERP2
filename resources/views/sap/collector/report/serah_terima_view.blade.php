<div class="card-body">
  <div id="print_page">
    @foreach ($row as $data)
    <table width="30%" border="0" class="mb-2">
      <tr>
        <td colspan="2"><strong>Collector Progress - {{ $data['collector'] }}</strong></td>
      </tr>
      <tr>
        <td><strong>Cash</strong></td>
        <td class="text-right">{{ rupiah($data['tagihan_cash']) }}</td>
      </tr>
      <tr>
        <td><strong>BG</strong></td>
        <td class="text-right">{{ rupiah($data['tagihan_bg']) }}</td>
      </tr>
      <tr>
        <td><strong>Transfer</strong></td>
        <td class="text-right">{{ rupiah($data['tagihan_tf']) }}</td>
      </tr>
      <tr>
        <td><strong>CN</strong></td>
        <td class="text-right">{{ rupiah($data['used_cn']) }}</td>
      </tr>
      <tr>
        <td><strong>Grand Total</strong></td>
        <td class="text-right">{{ rupiah($data['grand_total']) }}</td>
      </tr>
    </table>
    <table class="table table-sm table-bordered table-striped" width="100%">
      <tr>
        <th class="text-center">Nomor Invoice</th>
        <th class="text-center">Referensi</th>
        <th class="text-center">Tipe Dokumen</th>
        <th class="text-center">Tanggal Jatuh Tempo</th>
        <th class="text-center">Tipe Pembayaran</th>
        <th class="text-center">Tagihan</th>
        <th class="text-center">Pembayaran</th>
        <th class="text-center">Status</th>
      </tr>
      @foreach ($data['customer_data'] as $customer)
      <tr>
        <td colspan="2">
          <strong>{{ $customer['CardCode'] }}, {{ $customer['CardName'] }}</strong>
        </td>
        <td colspan="5">
          <strong>{{ $customer['Alamat'] }}</strong>
        </td>
      </tr>
      @php
         $no = 1; 
      @endphp
      @foreach ($customer['Lines'] as $lines)
      <tr>
        <td>{{ $lines['DocNum'] }}</td>
        <td>{{ $lines['Referensi'] }}</td>
        <td class="text-center">IN</td>
        <td class="text-center">{{ $lines['DocDueDate'] }}</td>
        <td class="text-center">{{ !empty($lines['type']) ? $lines['type'] : '-' }}</td>
        <td class="text-right">{{ rupiah($lines['Price']) }}</td>
        <td class="text-right">{{ rupiah($lines['Payment']) }}</td>
        <td class="text-center">{{ $lines['sts'] }}</td>
      </tr> 
      @endforeach
      @foreach ($customer['CN'] as $cn)
      <tr>
        <td>{{ $cn['DocNum'] }}</td>
        <td>{{ $cn['NumAtCard'] }}</td>
        <td class="text-center">CN</td>
        <td class="text-center">{{ $cn['DocDueDate'] }}</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-right">{{ rupiah($cn['Balance']) }}</td>
        <td class="text-center">{{ $cn['sts']==1 ? 'Used' : 'Available' }}</td>
      </tr> 
      @endforeach
      @endforeach
    </table>
    @endforeach
  </div>
</div>