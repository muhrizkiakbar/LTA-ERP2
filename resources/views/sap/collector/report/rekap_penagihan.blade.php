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
          <form method="POST" id="searchx">
            <div class="row mb-2">
              <label class="col-sm-1 col-form-label fw-bolder">Generate</label>
              <div class="col-sm-2">{!! Form::select('branch_code',$branch,null,['class'=>'form-control','id'=>'branch_code','placeholder'=>'-- Pilih Branch --']) !!}</div>
              <div class="col-sm-2">{!! Form::text('date',null,['class'=>'form-control datepick','id'=>'date','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','placeholder'=>'-- Pilih Tanggal --']) !!}</div>
              <div class="col-sm-2">
                <button class="btn btn-primary btn-sm" type="submit">Search</button>
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
          <div id="view"></div>
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

    $("#searchx").on("submit",function(e){
      e.preventDefault();
      $('#overlay').show();
      var branch_code = $("#branch_code").val();
      var date = $("#date").val();
      var csrf = "{{ csrf_token() }}";
      var url = '{{ route('collector.report_rekap_penagihan_search') }}';
      $.ajax({
        url : url,
        data  : {branch_code:branch_code,date:date,_token:csrf},
        type : "POST",
        success: function (data){
          $("#view").html(data);
        },
        complete : function(data){
          $('#overlay').hide();
        }
      });
    });
  });
</script>
@endsection