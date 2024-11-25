<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      <table class="table table-xxs table-striped table-bordered datatable-basic">
        <thead>
          <tr>
            <th class="text-center" width="5%"">No</th>
            <th class="text-center">Document Number</th>
            <th class="text-center">Kode Customer</th>
            <th class="text-center">Customer</th>
            <th class="text-center">Address</th>
            <th class="text-center">Doc Date</th>
            <th class="text-center">Branch</th>
            <th class="text-center">Sales</th>
            <th class="text-center" width="150px">Total</th>
            <th class="text-center">#</th>
          </tr>
        </thead>
        <tbody>
          @php
              $no=1;
          @endphp
          @foreach ($row as $item)
          <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $item['DocNum'] }}</td>
            <td>{{ $item['CardCode'] }}</td>
            <td>{{ $item['CardName'] }}</td>
            <td>{{ $item['Alamat'] }}</td>
            <td>{{ dateExp($item['DocDate']) }}</td>
            <td>{{ getBranchDetail2($item['BPLId'])->title }}</td>
            <td>{{ $item['SlpName'] }}</td>
            <td class="text-right">{{ rupiah($item['DocTotal']) }}</td>
            <td class="text-center">
              <a href="{{ route('sales.detail',$item['DocNum']) }}" target="_blank">
                <span class="badge badge-primary">Detail</span>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $.extend( $.fn.dataTable.defaults, {
			iDisplayLength:10,        
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