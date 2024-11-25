<div class="card-body">
  <div id="print_page">
    <table class="table table-sm table-bordered table-striped" width="100%">
      <tr>
        <th class="text-center">No</th>
        <th class="text-center">Tanggal</th>
        <th class="text-center">Nama Penagih</th>
        <th class="text-center">Cash</th>
        <th class="text-center">TF</th>
        <th class="text-center">BG</th>
        <th class="text-center">CN</th>
        <th class="text-center">GRAND TOTAL (INCLUDE CN)</th>
      </tr>
      @php
          $no=1;
      @endphp
      @foreach ($row as $item)
      <tr>
        <td class="text-center">{{ $no++ }}</td>
        <td>{{ $item['date'] }}</td>
        <td>{{ $item['collector'] }}</td>
        <td class="text-right">{{ rupiahnon2($item['tagihan_cash']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['tagihan_tf']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['tagihan_bg']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['used_cn']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['grand_total']) }}</td>
      </tr>
      @endforeach
    </table>
  </div>
</div>