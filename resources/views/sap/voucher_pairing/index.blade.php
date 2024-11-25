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
        <form method="POST" id="sync">
          <div class="form-group row">
            <label class="col-form-label col-md-1">Cust Code</label>
            <div class="col-md-2">{!! Form::text('docnum',null,['class'=>'form-control','id'=>'docnum','required'=>true]) !!}</div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-primary">Cari</button>
            </div>
          </div>
        </form>
				<div class="row">
					<div class="col-md-12">
						<div id="loadTable"></div>
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
    $("#sync").on("submit",function(e){
      e.preventDefault();
      var docnum = $("#docnum").val();
			var token = '{{ csrf_token() }}';
      var url = '{{ route('voucher_pairing.search') }}';
      $.ajax({
        url : url,
        data  : {cardCode:docnum, _token:token},
        type : "POST",
        success : function(data){
					$("#modalEx").html(data);
          $("#modalEx").modal('show',{backdrop: 'true'});
				}
      });
    });
  });
</script>
@endsection
