<div class="card-body">
  <div id="print_page">
    <table class="table table-sm table-bordered table-striped" width="100%">
      <tr>
        <th class="text-center" rowspan="2">No</th>
        <th class="text-center" rowspan="2">Tanggal</th>
        <th class="text-center" rowspan="2">Nama Penagih</th>
        <th class="text-center" rowspan="2">Perusahaan</th>
        <th class="text-center" colspan="2">Tagihan</th>
        <th class="text-center" colspan="2">Pending</th>
        <th class="text-center" rowspan="2">Performance</th>
        <th class="text-center" colspan="5">Done</th>
        <th class="text-center" rowspan="2">GRAND TOTAL (INCLUDE CN)</th>
      </tr>
      <tr>
        <th class="text-center">Invoice</th>
        <th class="text-center">Rupiah</th>
        <th class="text-center">Invoice</th>
        <th class="text-center">Rupiah</th>
        <th class="text-center">Invoice</th>
        <th class="text-center">Cash</th>
        <th class="text-center">TF</th>
        <th class="text-center">BG</th>
        <th class="text-center">CN</th>
      </tr>
      @php
          $no=1;
      @endphp
      @foreach ($row['data'] as $item)
      <tr>
        <td class="text-center">{{ $no++ }}</td>
        <td>{{ $item['date'] }}</td>
        <td>{{ $item['collector'] }}</td>
        <td>{{ $item['company'] }}</td>
        <td class="text-center">{{ $item['total_invoice'] }}</td>
        <td class="text-right">{{ rupiahnon2($item['total_tagihan']) }}</td>
        <td class="text-center">{{ $item['pending_invoice'] }}</td>
        <td class="text-right">{{ rupiahnon2($item['pending']) }}</td>
        <td class="text-center">{{ $item['progress']['percent'] }}</td>
        <td class="text-center">{{ $item['invoice_done'] }}</td>
        <td class="text-right">{{ rupiahnon2($item['tagihan_cash']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['tagihan_tf']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['tagihan_bg']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['used_cn']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['grand_total']) }}</td>
      </tr>   
      @endforeach
      <tr>
        <td class="text-center" colspan="5">
          <strong>Grand Total OBJ</strong>
        </td>
        <td class="text-center">
          {{ rupiahnon2($row['total_tagihan_all']) }}
        </td>
        <td class="text-center" colspan="2">
          <strong>Average Percentage</strong>
        </td>
        <td class="text-center">
          {{ $row['percent_avg'] }} %
        </td>
        <td class="text-center" colspan="5">
          <strong>Grand Total</strong>
        </td>
        <td class="text-right">
          {{ rupiahnon2($row['grand_total_all']) }}
        </td>
      </tr>
    </table>
  </div>
</div>