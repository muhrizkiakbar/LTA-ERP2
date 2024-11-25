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
            <div class="col-md-2">{!! Form::text('date',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'date']) !!}</div>  
            <div class="col-md-2">{!! Form::select('branch',$branch,null,['class'=>'form-control','id'=>'branch','placeholder'=>'-- Pilih Branch --','required'=>true]) !!}</div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary">Sync</button>
              <a href="javascript:void(0);" class="btn btn-info print mr-2">Cetak</a>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div id="loadView"></div>
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

    $(".print").bind("click", function(event) {
	  	$('#print_page').printArea();
	  });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var date = $("#date").val();
      var branch = $("#branch").val();
      var url = '{{ route('vdist.unserved_search') }}';
      var token = '{{ csrf_token() }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {branch:branch,date:date,_token:token},
        type : "POST",
        success : function(data){
          $('#loadView').html(data);
        },
        complete : function(data){
          $('#overlay').hide();
        }
      });
    });
  });
</script>
@endsection