<table>
	<tr>
		<th>No</th>
		<th>Date</th>
		<th>Item Code</th>
		<th>Item Name</th>
		<th>Customer</th>
		<th>Customer Name</th>
		<th>Harga</th>
		<th>Total Order</th>
		<th>Success</th>
		<th>Unserved</th>
		<th>Total Order Rp</th>
		<th>Total Unserved Rp</th>
		<th>Total Sucess Rp</th>
	</tr>
	@foreach($data as $index => $item) 
	<tr>
		<td>{{ $index++ }}</td>
		<td>{{ $item['DocDate'] }}</td>
		<td>{{ $item['ItemCode'] }}</td>
		<td>{{ $item['ItemName'] }}</td>
		<td>{{ $item['CardCode'] }}</td>
		<td>{{ $item['CardName'] }}</td>
		<td>{{ $item['Harga'] }}</td>
		<td>{{ $item['SfaQtyTotal'] }}</td>
		<td>{{ $item['SfaQtySuccess'] }}</td>
		<td>{{ $item['SfaQtyUnserve'] }}</td>
		<td>{{ $item['Total'] }}</td>
		<td>{{ $item['TotalUnserved'] }}</td>
		<td>{{ $item['TotalSuccess'] }}</td>
	</tr>
	@endforeach
</table>