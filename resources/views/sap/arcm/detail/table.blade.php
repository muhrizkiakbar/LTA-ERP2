<div class="table-responsive">
  <table class="table table-xxs table-striped table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th class="text-center">Item No.</th>
        <th class="text-center">Item Description</th>
        <th class="text-center">Quantity</th>
        <th class="text-center">Satuan</th>
        <th class="text-center">Unit Price</th>
        <th class="text-center">Tax Code</th>
        <th class="text-center">Gudang</th>
        <th class="text-center">Distr. Rule</th>
        <th class="text-center">Discount %</th>
        <th class="text-center">Total (LC)</th>
      </tr>
    </thead>
    <tbody>
      @php
          $no=1;
      @endphp
      @foreach ($lines as $item)
      <tr>
        <td>{{ $no++ }}</td>
        <td>{{ $item['ItemCode'] }}</td>
        <td>{{ $item['Dscription'] }}</td>
        <td class="text-right">{{ $item['Quantity'] }}</td>
        <td>{{ $item['UnitMsr'] }}</td>
        <td class="text-right">{{ rupiah($item['Price']) }}</td>
        <td>{{ $item['TaxCode'] }}</td>
        <td>{{ $item['WhsCode'] }}</td>
        <td>{{ $item['cogs'] }}</td>
        <td class="text-right">{{ round($item['disc_total'],2) }}</td>
        <td class="text-right">{{ rupiah($item['lineTotal']) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>