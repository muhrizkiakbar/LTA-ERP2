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
              <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
				<div class="form-group row">
					<div class="col-md-3">
						<a href="javascript:void(0);" class="btn btn-primary sync">Sync</a>
						<a href="javascript:void(0);" class="btn btn-secondary import">Import CSV</a>
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
									<th class="text-center" width="2%">No</th>
									<th class="text-center">Title</th>
									<th class="text-center">Code SAP</th>
									<th class="text-center">Code SFA</th>
									<th class="text-center">Code KINO</th>
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
									<td>{{ $item->title }}</td>
									<td>{{ $item->code_sap }}</td>
									<td>{{ $item->code }}</td>
									<td>{{ $item->code_kino }}</td>
									<td class="text-center">
										<a href="javascript:void(0);" class="edit" data-id="{{ $item->id }}">
											<span class="badge badge-info">Edit</span>
										</a>
									</td>
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
<div class="modal fade" id="modalEx" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });

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

				$(".edit").unbind();
        $(".edit").click(function(e) {
          var id = $(this).data('id');
          var url = '{{ route('master.sales_employee_edit') }}';
          var token = '{{ csrf_token() }}';
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id, _token:token },
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

		$(".sync").click(function(e) {
      e.preventDefault();
      var token = '{{ csrf_token() }}';
      var url = '{{ route('master.sales_employee_sync') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {_token:token},
        type : "POST",
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Sync Berhasil!',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            }).then (function() {
              window.location.href = "{!! route('master.sales_employee') !!}";
            });
          } else {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Data tidak di temukan',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        },
      });
    });
  });
</script>
@endsection