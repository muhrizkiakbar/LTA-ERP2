<div class="table-responsive">
  <table class="table table-xxs table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">
					<input type="checkbox" id="select-all">
				</th>
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
					$nox=0
      @endphp
      @foreach ($lines as $item)
      <tr>
        <td class="text-center">
					<input type="checkbox" class="item" name="id[]" value="{{ $item['id'] }}">
          <a href="javascript:void(0);" class="edit" data-id="{{ $item['id'] }}">
            <i class="feather-edit mr2"></i>
          </a>
          <a href="{{ route('return.temp_lines_delete',$item['id']) }}" onclick="return confirm('Are you sure you want to delete this item?');" class="text-danger">
            <i class="feather-trash-2"></i>
          </a>
        </td>
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
			@php
				$no++;
			@endphp
      @endforeach
    </tbody>
  </table>
</div>
<script type="text/javascript">
	document.getElementById('select-all').addEventListener('change', function() {
		var checkboxes = document.getElementsByClassName('item');
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = this.checked;
		}
	});
</script>
