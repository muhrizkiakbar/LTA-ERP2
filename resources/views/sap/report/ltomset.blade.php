@extends('layouts.backend.app')
@section('content')
<div id="overlay" style="display:none;">
  <div class="spinner"></div>
  <br/>
  Loading...
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
            <div class="col-md-2">{!! Form::text('dateFrom',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateFrom','placeholder'=>'Tanggal Mulai','required'=>true]) !!}</div>
            <div class="col-md-2">{!! Form::text('dateTo',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateTo','placeholder'=>'Tanggal Akhir','required'=>true]) !!}</div>
            <div class="col-md-2">{!! Form::select('cabang',$cabang,null,['class'=>'form-control select2','id'=>'cabang','placeholder'=>'-- Pilih Cabang --']) !!}</div>
            <div class="col-md-3">
              <select name="tipe" id="tipe" class="form-control select2" required>
                <option value="">-- Pilih Vendor --</option>
                @foreach ($tipe as $tipe)
                <option value="{!! $tipe['title'] !!}">{!! $tipe['title'] !!}</option>    
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary mr-2">Sync</button>
              <a href="javascript:void(0);" class="btn btn-info export2 mr-2">
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
      var url = '{{ route('report.ltomset_search') }}';
      $.ajax({
        url : url,
        data  : {dateFrom:dateFrom,dateTo:dateTo,cabang:cabang,tipe:tipe,_token:csrf},
        type : "POST",
        dataType : "JSON",
        success : function(data){
          if (data.message=="sukses") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Sync Report Berhasil!',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            });
          } else if(data.message=="bigger"){
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Data LT Omset terlalu besar',
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
              text: 'Data LT Omset tidak di temukan',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        }
      });
    });

    $(".export2").click(function(e) {
      dataCaptureExport2();
    });
  });

  function dataCaptureExport2(){
    $('#overlay').show();
    var dateFrom = $("#dateFrom").val();
    var dateTo = $("#dateTo").val();
    var cabang = $("#cabang").val();
    var tipe = $("#tipe").val();
    var csrf = "{!! csrf_token() !!}";
    var url = '{{ route('report.ltomset_export2') }}';
    $.ajax({
      url : url,
      data  : {dateFrom:dateFrom,dateTo:dateTo,cabang:cabang,tipe:tipe,_token:csrf},
      type : "POST",
      dataType : "JSON",
      success : function(response){
        var a = document.createElement("a");
        a.href = response.file; 
        a.download = response.name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        $('#overlay').hide();
        // downloadFile(response.file, response.name);
      }
    });
  }
</script>
@endsection
