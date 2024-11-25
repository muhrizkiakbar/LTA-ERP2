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
              <li class="breadcrumb-item">Sales A/R</li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <form method="POST" id="sync">
							<div class="form-group row">
                <label class="col-form-label col-md-3">Tanggal</label>
                <div class="col-md-3">{!! Form::text('date',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'date','required'=>true]) !!}</div>
              </div>
							<div class="form-group row">
                <label class="col-form-label col-md-3">Kode Customer</label>
                <div class="col-md-3">{!! Form::text('cardCode',null,['class'=>'form-control','id'=>'cardCode','required'=>true]) !!}</div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">Item Code</label>
                <div class="col-md-3">{!! Form::text('itemCode',null,['class'=>'form-control','id'=>'itemCode','required'=>true]) !!}</div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3"></label>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-primary">Cari</button>
                </div>
              </div>
              </div>
            </form>
          </div>
        </div>
      </div>
			<div class="row">
				<div class="col-md-12">
					<div id="loadView"></div>
				</div>
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
      var date = $("#date").val();
      var cardCode = $("#cardCode").val();
			var itemCode = $("#itemCode").val();
      var csrf = "{!! csrf_token() !!}";
      var url = '{{ route('report.paket_eko_search') }}';
      $.ajax({
        url : url,
        data  : {date:date,cardCode:cardCode,itemCode:itemCode,_token:csrf},
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
