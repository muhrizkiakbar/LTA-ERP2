@extends('layouts.backend.app')
@section('content')
<div id="overlay" style="display:none;">
  <div class="spinner-border text-primary m-2" role="status">
    <span class="sr-only">Loading...</span>
  </div>  
</div>
<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">{{ $title }}</h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item">Dashboard</li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-8">
        @include('sap.template.alert')
        <div class="card">
          <table class="table table-xxs table-striped table-bordered datatable-basic">
            <thead>
              <tr>
                <th class="text-center" width="2%"">No</th>
                <th class="text-center">Company</th>
                <th class="text-center">Function</th>
                <th class="text-center">Desc</th>
                <th class="text-center">URL</th>
                <th class="text-center" width="100px">#</th>
              </tr>
            </thead>
            <tbody>
              @php
                  $no=1;
              @endphp
              @foreach ($row as $item)
              <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item->company->title }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->desc }}</td>
                <td>{{ $item->url }}</td>
                <td class="text-center">
                  <a href="">
                    <span class="badge badge-info">Edit</span>
                  </a>
                  <a href="">
                    <span class="badge badge-danger">Delete</span>
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-4">
        <div class="card">
          <div class="card-body">
          {!! Form::open(['route'=>['dashboard.listing_api_store'],'method'=>'POST']) !!}
            @include('sap.listing_api.form')
            <div class="row mb-2">
              <label class="col-sm-3 col-form-label fw-bolder"></label>
              <div class="col-sm-6">
                <button class="btn btn-primary btn-sm" type="submit">Simpan</button>
              </div>
            </div>
          {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalEx" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
    $.extend( $.fn.dataTable.defaults, {
			iDisplayLength:25,        
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

        $(".sales").unbind();
        $(".sales").click(function(e) {
          var id = $(this).data('id');
          var url = '{{ route('users.sales') }}';
          var token = "{{ csrf_token() }}";
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id,_token:token },
            success: function (ajaxData){
              $("#modalEx").html(ajaxData);
              $("#modalEx").modal('show',{backdrop: 'true'});
            }
          });
        });

        $(".collector").unbind();
        $(".collector").click(function(e) {
          var id = $(this).data('id');
          var url = '{{ route('users.collector') }}';
          var token = "{{ csrf_token() }}";
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id,_token:token },
            success: function (ajaxData){
              $("#modalEx").html(ajaxData);
              $("#modalEx").modal('show',{backdrop: 'true'});
            }
          });
        });
      },
      preDrawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
      } 
    });
  });
</script>
@endsection