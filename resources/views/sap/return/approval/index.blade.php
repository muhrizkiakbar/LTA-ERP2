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
                  <th class="text-center">Company</th>
                  <th class="text-center">No. Surat Jalan</th>
                  <th class="text-center">No. Dokumen</th>
                  <th class="text-center">Customer</th>
                  <th class="text-center">Sales</th>
                  <th class="text-center">Date</th>
                  <th class="text-center">Branch</th>
                  <th class="text-center" width="150px">Total</th>
                  <th class="text-center">Status</th>
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
                  <td>{{ $item->company_detail->title }}</td>
                  <td>{{ $item->number }}</td>
                  <td>{{ $item->kd }}</td>
                  <td>{{ $item->CardName }}</td>
                  <td>{{ $item->SlpName }}</td>
                  <td>{{ $item->date }}</td>
                  <td>{{ $item->branch_detail->title }}</td>
                  <td class="text-right">{{ rupiahnon2($item->DocTotal) }}</td>
                  <td class="text-center">{!! $item->status_label !!}</td>
                  <td class="text-center">
                    <a href="javascript:void(0);" class="detail" data-id="{{ $item->kd }}">
                      <span class="badge badge-success">Detail</span>
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

        $(".detail").unbind();
        $(".detail").click(function(e) {
          var id = $(this).data('id');
          var url = '{{ route('return.approval.detail') }}';
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
  });
</script>
@endsection