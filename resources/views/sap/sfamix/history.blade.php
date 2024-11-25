<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      <table class="table table-xxs table-striped table-bordered datatable-basic2">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">User</th>
            <th class="text-center">Date & Time</th>
            <th class="text-center">Description</th>
          </tr>
        </thead>
        <tbody>
          @php $no=1; @endphp
          @foreach ($row as $item)
          <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $item['title'] }}</td>
            <td class="text-center">{{ $item['time'] }}</td>
            <td>{!! $item['desc'] !!}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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

    var oTable = $('.datatable-basic2').DataTable({
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