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
            <div class="col-md-2">{!! Form::select('branch',$branch,null,['class'=>'form-control','id'=>'branch','placeholder'=>'-- Pilih Branch --','required'=>true]) !!}</div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary">Sync</button>
              <a href="javascript:void(0);" class="btn btn-info" onclick="exportExcel()">Export</a>
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
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
		loadView();

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var branch = $("#branch").val();
			var token = '{{ csrf_token() }}';
      var url = '{{ route('report.storemaster_sync') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {branch:branch,_token:token},
        type : "POST",
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

					loadView();
        },
      });
    });
  });

	function loadView() {
		var token = '{{ csrf_token() }}';
    var url = '{{ route('report.storemaster_view') }}';
		$.ajax({
			url : url,
			data  : {_token:token},
			type : "POST",
			success:function(response){
				$("#loadView").html(response);
			}
		});
	}

	function exportExcel() {
    var branch = $("#branch").val();

    var url = `/report/storemaster_export?branch=${branch}`;
    window.location = url
  }
</script>
@endsection