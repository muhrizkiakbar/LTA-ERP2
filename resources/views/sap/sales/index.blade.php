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
            <label class="col-form-label col-md-1">Cari Dokumen</label>
            <div class="col-md-2">{!! Form::text('docnum',null,['class'=>'form-control','id'=>'docnum']) !!}</div>
            <div class="col-md-2">
              <select name="sales" id="sales" class="form-control form-control-sm select2">
                <option value="">-- Pilih Sales --</option>
                @foreach ($sales as $sales)
                <option value="{{ $sales['id'] }}">{{ $sales['title'] }}</option>    
                @endforeach
              </select>
            </div>
            <div class="col-md-2">{!! Form::select('status',$status,[],['class'=>'form-control','id'=>'status','required'=>true,'placeholder'=>'-- Pilih Status --']) !!}</div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-primary">Cari</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div id="loadView"></div>
  </div>
</div>
<div class="modal fade" id="modalEx" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
<div class="modal fade" id="modalEx2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
<div class="modal fade" id="modalEx3" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
    $('.select2').select2();

    loadCreate();

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var docnum = $("#docnum").val();
      var sales = $("#sales").val();
      var status = $("#status").val();
      var csrf = "{{ csrf_token() }}";
      var url = '{{ route('sales.search_docnum') }}';
      $.ajax({
        url : url,
        data  : {docnum:docnum,sales:sales,status:status,_token:csrf},
        type : "POST",
        success: function (ajaxData){
          $("#modalEx").html(ajaxData);
          $("#modalEx").modal('show',{backdrop: 'true'});
        }
      });
    });
  });

  function loadCreate(){
    var url = '{{ route('sales.create') }}';
    $.ajax({
      url: url,
      type: "GET",
      success : function(data){
        $('#loadView').html(data);
      }
    });
  }
</script>
@endsection
