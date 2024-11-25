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
              <li class="breadcrumb-item">Report</li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
        <form method="POST" id="sync">
          <div class="form-group row">
            <div class="col-md-2">{!! Form::text('dateFrom',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateFrom','placeholder'=>'Tanggal Mulai']) !!}</div>
            <div class="col-md-2">{!! Form::text('dateTo',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateTo','placeholder'=>'Tanggal Akhir']) !!}</div>
            <div class="col-md-2">{!! Form::select('cabang',$cabang,null,['class'=>'form-control select2','id'=>'cabang','placeholder'=>'-- Pilih Cabang --']) !!}</div>
            <div class="col-md-2">
              <select name="tipe" id="tipe" class="form-control select2">
                <option value="">-- Pilih Tipe --</option>
                @foreach ($tipe as $tipe)
                <option value="{{ $tipe }}">{{ $tipe }}</option>    
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary mr-2">Cari</button>
              <a href="javascript:void(0);" class="btn btn-info export mr-2">
                Export
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
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

    $("#sync").on("submit",function(e){
      e.preventDefault();
      $('#overlay').show();
      var dateFrom = $("#dateFrom").val();
      var dateTo = $("#dateTo").val();
      var cabang = $("#cabang").val();
      var tipe = $("#tipe").val();
      var csrf = "{!! csrf_token() !!}";
      var url = '{{ route('report.omset_search') }}';
      $.ajax({
        url : url,
        data  : {dateFrom:dateFrom,dateTo:dateTo,cabang:cabang,tipe:tipe,_token:csrf},
        type : "POST",
        success : function(data){
          $('#loadView').html(data);
        },
        complete : function(data){
          $('#overlay').hide();
        }
      });
    });

    $(".export").click(function(e) {
      dataCaptureExport();
    });
  });

  function dataCaptureExport(){
    $('#overlay').show();
    var dateFrom = $("#dateFrom").val();
    var dateTo = $("#dateTo").val();
    var cabang = $("#cabang").val();
    var tipe = $("#tipe").val();
    var csrf = "{!! csrf_token() !!}";
    var url = '{{ route('report.omset_export') }}';
    $.ajax({
      url : url,
      data  : {dateFrom:dateFrom,dateTo:dateTo,cabang:cabang,tipe:tipe,_token:csrf},
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
</script>
@endsection
