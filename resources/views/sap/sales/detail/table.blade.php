<div class="table-responsive">
  <table class="table table-xxs table-striped table-bordered">
    <thead>
      <tr>
        <th {!! $DocStatus=='O' ? 'colspan="2"' : '' !!}>#</th>
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
        @if ($DocStatus!='C')
        <td>
          <a href="javascript:void(0);" class="edit" data-id="{{ $item['id'] }}">
            Edit
          </a>
          <a href="{{ route('sales.lines_item_delete',$item['id']) }}" onclick="return confirm('Are you sure you want to delete this item?');" class="text-danger">
            Delete
          </a>
					<a href="javascript:void(0);" class="batch" data-id="{{ $item['id'] }}">
            Batch
          </a>
        </td>
        @endif
        <td>{{ $no++ }}</td>
        <td>{{ $item['itemCode'] }}</td>
        <td>{{ $item['itemDesc'] }}</td>
        <td class="text-right">{{ $item['qty'] }}</td>
        <td>{{ $item['unitMsr'] }}</td>
        <td class="text-right">{{ rupiah($item['unitPrice']) }}</td>
        <td>{{ $item['taxCode'] }}</td>
        <td>{{ $item['whsCode'] }}</td>
        <td>{{ $item['cogs'] }}</td>
        <td class="text-right">{{ round($item['disc_total'],2) }}</td>
        <td class="text-right">{{ rupiah($item['docTotal']) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
