<div class="card">
	<div class="card-body">
		<table class="table table-xxs table-striped table-bordered">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">Document Number</th>
					<th class="text-center">Referensi Number</th>
					<th class="text-center">Description</th>
					<th class="text-center">Date</th>
					<th class="text-center">Balance</th>
					<th class="text-center">Document Delivery</th>
					<th class="text-center">#</th>
				</tr>
			</thead>
			<tbody>
				@php
					$no=1;
				@endphp
				@foreach ($row as $item)
				<tr>
					<td class="text-center">{{ $no++ }}</td>
					<td>{{ $item['DocNum'] }}</td>
					<td>{{ $item['NumAtCard'] }}</td>
					<td>{{ $item['Comments'] }}</td>
					<td class="text-center">{{ $item['DocDate'] }}</td>
					<td class="text-right">{{ rupiahnon2($item['BalanceDue']) }}</td>
					<td>{{ $item['DocNumDelivery'] }} {!! $item['status'] !!}</td>
					<td class="text-center">
						<a href="{{ route('voucher_release.delete',$item['id']) }}" onclick="return confirm('Anda yakin ingin menghapus ?')">
							<span class="badge badge-danger">Delete</span>
						</a>
					</td>
				</tr>  
				@endforeach
			</tbody>
		</table>
	</div>
</div>