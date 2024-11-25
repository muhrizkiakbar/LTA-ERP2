<div class="table-responsive">
  <table class="table table-xxs table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">#</th>
        <th class="text-center">Document Number</th>
        <th class="text-center">Referensi Number</th>
        <th class="text-center">Description</th>
        <th class="text-center">Date</th>
        <th class="text-center">Balance</th>
    </tr>
    </thead>
    <tbody>
      @php
        $no=1;
      @endphp
      @foreach ($voucherList as $list)
      <tr>
        <td class="text-center">{{ $no++ }}</td>
        <td>{{ $list['DocNum'] }}</td>
        <td>{{ $list['NumAtCard'] }}</td>
        <td>{{ $list['Comments'] }}</td>
        <td class="text-center">{{ $list['DocDate'] }}</td>
        <td class="text-right">{{ rupiahnon2($list['BalanceDue']) }}</td>
      </tr>  
      @endforeach
    </tbody>
  </table>
</div>
