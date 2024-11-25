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
      <div class="col-12">
        @include('sap.template.alert')
        <div class="card">
          <div class="card-body">
            <form method="POST" id="sync">
              <div class="row mb-2">
                <label class="col-sm-1 col-form-label fw-bolder">Generate</label>
                <div class="col-sm-2">{!! Form::select('branch_code',$branch,null,['class'=>'form-control','id'=>'branch_code','placeholder'=>'-- Pilih Branch --']) !!}</div>
                <div class="col-sm-2">{!! Form::text('dateF',null,['class'=>'form-control datepick','id'=>'dateF','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','placeholder'=>'-- Pilih Tanggal --']) !!}</div>
                <div class="col-sm-2">{!! Form::text('dateT',null,['class'=>'form-control datepick','id'=>'dateT','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','placeholder'=>'-- Pilih Tanggal --']) !!}</div>
                <div class="col-sm-2 d-flex">
                  <button class="btn btn-primary mr-2" type="submit">Search</button>
                  <a href="javascript:void(0);" class="btn btn-info print mr-2">
                    Cetak
                  </a>
                  <button class="btn btn-success mr-2" type="button" onclick="exportExcel()">Excel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div id="result"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){

    $('.datepick').datepicker({
    	autoClose:true
    });

    $(".print").bind("click", function(event) {
	  	$('#print_page').printArea();
	  });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      $('#overlay').show();
      var branch_code = $("#branch_code").val();
      var dateF = $("#dateF").val();
      var dateT = $("#dateT").val();
      var csrf = "{{ csrf_token() }}";
      var url = '{{ route('report.unserved_order.mix_search') }}';
      $.ajax({
        url : url,
        data  : {branch_code:branch_code,dateF:dateF,dateT:dateT,_token:csrf},
        type : "POST",
        success: function (data){
          $("#result").html(data);
          console.log(data);
        },
        complete : function(data){
          $('#overlay').hide();
          console.log(data);
          
        }
      });
    });
  });

  function exportExcel() {
    var branch_code = $("#branch_code").val();
    var dateF = $("#dateF").val();
    var dateT = $("#dateT").val();


    console.log(branch_code, dateF, dateT);

    var url = `/report/unserved_order/mix_export?branch_code=${branch_code}&dateF=${dateF}&dateT=${dateT}`;
    window.location = url
  }

</script>
@endsection