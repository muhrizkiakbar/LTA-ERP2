<div class="card">
  <div class="card-body">
    <div id="print_page">
      <table border="0" width="100%">
        <tr valign="top">
          <td width="30%" colspan="3">
            <h2>{{ $title }}</h2>
          </td>
          <td class="text-right">
            <img src="{{ asset('assets/images/logo-lta-clear.png') }}" width="60px">
          </td>
        </tr>
        <tr>
          <td width="40%"><strong>{{ $plat }}</strong></td>
          <td><strong>{{ date('d-M-Y',strtotime($date)) }}</strong></td>
          <td><strong></strong></td>
          <td class="text-right mr-2"><strong>{{ $company }}</strong></td>
        </tr>
      </table>
      <table border="1" width="100%">
        <tr>
          <td class="text-center">No. Delivery</td>
          <td class="text-center">No. SO</td>
          <td class="text-center">Sales</td>
          <td class="text-center">Code Customer</td>
          <td class="text-center">Nama</td>
          <td class="text-center">Netto</td>
          <td class="text-center">Bea Materai</td>
          <td class="text-center">Voucher</td>
          <td class="text-center">Total Bayar</td>
          <td class="text-center">Checklist</td>
        </tr>
        @foreach ($row as $item)
        <tr>
          <td>{{ $item['DocNum'] }}</td>
          <td>{{ $item['DocNumSO'] }}</td>
          <td>{{ $item['SlpName'] }}</td>
          <td>{{ $item['CardCode'] }}</td>
          <td>{{ $item['CardName'] }}</td>
          <td class="text-right">{{ rupiahnon2($item['Netto']) }}</td>
          <td class="text-right">{{ rupiahnon2($item['BeaMaterai']) }}</td>
          <td class="text-right">{{ rupiahnon2($item['Voucher']) }}</td>
          <td class="text-right">{{ rupiahnon2($item['Total']) }}</td>
          <td></td>
        </tr>
        @endforeach
        <tr style="border: 1px solid #fff; border-top: 1px solid #000;">
          <td><strong>TOTAL</strong></td>
          <td colspan="6"><strong>Jumlah Nota : {{ $count }} </trong></td>
          <td class="text-right"><strong>Total Bayar :</strong></td>
          <td class="text-right"><strong>{{ rupiahnon2($totalnetto) }}</strong></td>
          <td></td>
        </tr>
      </table>
      <table border="0" width="50%">
        <td class="text-center">
          <br><br><br>
          (ADMINISTRASI/FAKTURIS)
        </td>
        <td class="text-center">
          <br><br><br>
          (GUDANG)
        </td>
        <td class="text-center">
          <br><br><br>
          (EKSPEDISI)
        </td>
      </table>
    </div>
  </div>
</div>