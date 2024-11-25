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
              <li class="breadcrumb-item">Collector</li>
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
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-sm-3 col-form-label fw-bolder">Nama Customer</label>
                  <div class="col-sm-6">
                    <select name="CardCode" class="form-control select2" id="cardCode">
                      <option value="">-- Pilih Customer --</option>
                      @if (!empty($customer))
                        @foreach ($customer as $cust)
                        <option value="{{ $cust['CardCode'] }}">{{ $cust['CardName'] }}</option>    
                        @endforeach  
                      @endif
                    </select>
                  </div>
                  <input type="hidden" name="kd" id="kd" value="{{ $kd }}">
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-12">
                <div id="view"></div>
              </div>
            </div>
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
  $(document).ready(function() {
    $('.select2').select2();

    $("#cardCode").on('change', function () {
      var cardCode = $("#cardCode").val();
      var kd = $("#kd").val();
      var url = '{{ route('collector.additional_invoice2') }}';
	    $.ajax({
	      type : "POST",
	      url : url,
	      data: { cardCode:cardCode, kd:kd, _token:"{{ csrf_token() }}"},
	      success: function(response){ 
	        $("#view").html(response);
	      },
	    });
	  });
  });
</script>
@endsection
