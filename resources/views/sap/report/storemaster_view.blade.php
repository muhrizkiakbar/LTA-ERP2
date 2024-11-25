<table class="table table-xxs table-striped table-bordered datatable-basic">
	<thead>
		<tr>
			<th class="text-center" width="5%"">No</th>
			<th class="text-center">Kode Customer</th>
			<th class="text-center">Customer</th>
			<th class="text-center">Address</th>
			<th class="text-center">Store Attribute</th>
			<th class="text-center">Pasar</th>
			<th class="text-center">Seller Type</th>
		</tr>
	</thead>
	<tbody>
		@php
			$no=1;
		@endphp
		@foreach ($row as $item)
		<tr>
			<td class="text-center">{{ $no++ }}</td>
			<td>{{ $item->LGCY_STORE_ID }}</td>
			<td>{{ $item->STORE_NAME }}</td>
			<td>{{ $item->Street }}</td>
			<td>{{ $item->U_STOREATTRIBUTE }}</td>
			<td>{{ $item->U_NAMAPASAR }}</td>
			<td>{{ $item->U_SELLERTYPE }}</td>
		</tr>		
		@endforeach
	</tbody>
</table>

<script type="text/javascript">
  $(document).ready(function(){
		$.extend( $.fn.dataTable.defaults, {
			iDisplayLength:50,        
			autoWidth: false,
			columnDefs: [{ 
				orderable: false,
				targets: [  ]
			}],
			dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
			language: {
				search: '<span>Filter:</span> _INPUT_',
				searchPlaceholder: 'Type to filter...',
				lengthMenu: '<span>Show:</span> _MENU_',
				paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
			}
		});

		var oTable = $('.datatable-basic').DataTable({
			"select": "single",
			"serverSide": false,
			drawCallback: function() {
				$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
			},
			preDrawCallback: function() {
				$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
			} 
		});
	});
</script>