@extends('layouts.backend.app')
@section('content')
<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">{{ $title }}</h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <table class="table table-xxs table-striped table-bordered datatable-basic">
              <thead>
                <tr>
                  <th class="text-center" width="5%"">No</th>
                  <th class="text-center">Date & Time</th>
                  <th class="text-center">User</th>
                  <th class="text-center">Action</th>
                  <th class="text-center">Desc</th>
                </tr>
              </thead>
              <tbody>
                @php $no=1; @endphp
                @foreach ($row as $item)
                <tr>
                  <td class="text-center">{{ $no++ }}</td>
                  <td class="text-center">{{ $item['time'] }}</td>
                  <td>{{ $item['user'] }}</td>
                  <td class="text-center">{!! $item['action'] !!}</td>
                  <td>{!! $item['desc'] !!}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>     
  </div>
</div>
@endsection
@section('customjs')
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
@endsection