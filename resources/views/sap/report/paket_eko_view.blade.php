<div class="card">
  <div class="card-body">
		<table border="1" width="100%">
			<tr>
				<td class="text-center">No. Delivery</td>
				<td class="text-center">Item Code</td>
				<td class="text-center">Item Name</td>
				<td class="text-center">Qty</td>
				<td class="text-center">Konversi</td>
				<td class="text-center">EKO</td>
			</tr>
			@foreach ($row as $item)
			<tr>
				<td>{{ $item['DocNum'] }}</td>
				<td>{{ $item['ItemCode'] }}</td>
				<td>{{ $item['ItemName'] }}</td>
				<td class="text-center">{{ round($item['Quantity'],0) }} {{ $item['UomCode'] }}</td>
				<td class="text-center">{{ $item['Jumlah'] }}</td>
				<td class="text-center">{{ round($item['VALUE_EKO'],0) }}</td>
			</tr>
			@endforeach
		</table>
	</div>
</div>