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
        <form method="POST" id="sync">
          <div class="form-group row">
            <div class="col-md-2">{!! Form::text('dateFrom',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateFrom','placeholder'=>'Tanggal Mulai','required'=>true]) !!}</div>
            <div class="col-md-2">{!! Form::text('dateTo',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateTo','placeholder'=>'Tanggal Akhir','required'=>true]) !!}</div>
            <div class="col-md-2">{!! Form::select('branch',$branch,null,['class'=>'form-control','id'=>'branch','placeholder'=>'-- Pilih Branch --','required'=>true]) !!}</div>
            <div class="col-md-3">
              <select name="supervisor" id="supervisor" class="form-control" required>
                <option value="">-- Pilih Supervisor --</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-success mr-2">Sync</button>
              <a href="javascript:void(0);" class="btn btn-info view mr-2">
                View
              </a>
              {{-- <a href="javascript:void(0);" class="btn btn-info export mr-2">
                Export
              </a> --}}
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div id="loadView"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){

    $(".print").bind("click", function(event) {
	  	$('#print_page').printArea();
	  });

    $('.datepick').datepicker({
    	autoClose:true
    });

    $('.select2').select2();

    $("#branch").on('change', function () {
      var url = '{{ route('gps_compliance.spv_png') }}';
	    $.ajax({
	      type : "POST",
	      url : url,
	      data: {
          branch:$("#branch").val(), 
          _token:"{{ csrf_token() }}",
        },
	      dataType : "json",
	      success: function(response){ 
	        $("#supervisor").html(response.listdoc);
	      },
	    });
	  });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      $('#overlay').show();
      var dateFrom = $("#dateFrom").val();
      var dateTo = $("#dateTo").val();
      var supervisor = $("#supervisor").val();
      var csrf = "{!! csrf_token() !!}";
      var url = '{{ route('gps_compliance.sync_png') }}';
      $.ajax({
        url : url,
        data  : {dateFrom:dateFrom,dateTo:dateTo,supervisor:supervisor,_token:csrf},
        type : "POST",
        dataType: 'JSON',
        success : function(data){
          if (data.message=="sukses") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Sukses !',
              text: 'Data GPS Compliance berhasil di sync',
              timer: 2000,
              showCancelButton: false,
              showConfirmButton: false
            });
          } else {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Data GPS Compliance tidak di temukan',
              timer: 2000,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        },
        complete : function(data){
          loadView();
        }
      });
    });

    $(".view").click(function(e) {
      loadView();
    });

    $(".export").click(function(e) {
      dataCaptureExport();
    });
  });

  function dataCaptureExport(){
    $('#overlay').show();
    var dateFrom = $("#dateFrom").val();
    var dateTo = $("#dateTo").val();
    var supervisor = $("#supervisor").val();
    var csrf = "{!! csrf_token() !!}";
    var url = '{{ route('report.ltomset_export') }}';
    $.ajax({
      url : url,
      data  : {dateFrom:dateFrom,dateTo:dateTo,supervisor:supervisor,_token:csrf},
      type : "POST",
      success : function(response){
        var a = document.createElement("a");
        a.href = response.file; 
        a.download = response.name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        $('#overlay').hide();
      }
    });
  }

  function loadView(){
    var dateFrom = $("#dateFrom").val();
    var dateTo = $("#dateTo").val();
    var supervisor = $("#supervisor").val();
    var csrf = "{!! csrf_token() !!}";
    var url = '{{ route('gps_compliance.view_png') }}';
    $.ajax({
      url : url,
      data  : {dateFrom:dateFrom,dateTo:dateTo,supervisor:supervisor,_token:csrf},
      type : "POST",
      success : function(response){
        $('#loadView').html(response);
      }
    });
  }
</script>
@endsection