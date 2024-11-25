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
        <form method="POST" id="sync">
          <div class="form-group row">
            <label class="col-form-label col-md-1">Pilih Branch</label>
            @if ($role==1)
            <div class="col-md-2">{!! Form::text('date',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'date']) !!}</div>  
            @endif
            <div class="col-md-3">{!! Form::select('branch',$branch,null,['class'=>'form-control','id'=>'branch','placeholder'=>'-- Pilih Branch --','required'=>true]) !!}</div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary">Sync SFA</button>
              <a href="javascript:void(0);" class="btn btn-info history">History</a>
              @if ($role==1)
              <a href="javascript:void(0);" class="btn btn-warning fixed">Fixed</a>
              @endif
            </div>
          </div>
        </form>
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
                  <th class="text-center">Kode Customer</th>
                  <th class="text-center">Customer</th>
                  <th class="text-center">Address</th>
                  <th class="text-center">Doc Date</th>
                  <th class="text-center">Branch</th>
                  <th class="text-center">Sales</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">#</th>
                </tr>
              </thead>
              <tbody>
                @php $no=1; @endphp
                @foreach ($row as $item)
                <tr>
                  <td class="text-center">{{ $no++ }}</td>
                  <td>{{ $item['CardCode'] }}</td>
                  <td>{{ $item['CardName'] }}</td>
                  <td>{{ $item['Address'] }}</td>
                  <td>{{ $item['DocDate'] }}</td>
                  <td class="text-center">{{ $item['Branch'] }}</td>
                  <td>{{ $item['SalesPersonCode'] }}</td>
                  <td class="text-right">{{ $item['Total'] }}</td>
                  <td class="text-center">
                    <a href="javascript:void(0);" class="detail" data-id="{{ $item['NumAtCard'] }}">
                      <span class="badge badge-primary">Detail</span>
                    </a>
										@if (auth()->user()->id=='1')
										<a href="{{ route('sfamix.delete',$item['NumAtCard']) }}" onclick="return confirm('Anda yakin ingin menghapus ?')">
                      <span class="badge badge-danger">Delete</span>
                    </a>		
										@endif
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
          var url = '{{ route('sfamix.detail') }}';
          $.ajax({
            url: url,
            type: "GET",
            data : { id:id },
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

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var date = $("#date").val();
      var branch = $("#branch").val();
      var url = '{{ route('sfamix.sync') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {branch:branch,date:date},
        type : "GET",
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
              window.location.href = "{!! route('sfamix') !!}";
            });
          } else {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Data SFA tidak di temukan',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        },
      });
    });

    $(".fixed").click(function (e) {
      e.preventDefault();
      var date = $("#date").val();
      var branch = $("#branch").val();
      var url = '{{ route('sfamix.fixed') }}';
      var token = '{{ csrf_token() }}'
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {branch:branch,date:date,_token:token},
        type : "POST",
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Fixed Berhasil!',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
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

    $(".history").click(function(e) {
      var url = '{{ route('sfamix.history') }}';
      $.ajax({
        url: url,
        type: "GET",
        success: function (ajaxData){
          $("#modalEx").html(ajaxData);
          $("#modalEx").modal('show',{backdrop: 'true'});
        }
      });
    });
  });
</script>
@endsection