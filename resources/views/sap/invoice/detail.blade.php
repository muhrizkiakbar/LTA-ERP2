@extends('layouts.backend.app')
@section('content')
<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">{{ $title }}</h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item">A/R Invoice</li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
        <form method="POST" id="sync">
          <div class="form-group row">
            <label class="col-form-label col-md-1">No Dokumen</label>
            <div class="col-md-2">{!! Form::text('docnum',null,['class'=>'form-control','id'=>'docnum','required'=>true]) !!}</div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-primary">Cari</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        @include('sap.template.alert')
        <div class="card">
          <div class="card-body">
            <div class="row mb-3">
              @include('sap.invoice.detail.form_top')
            </div>
            <div class="row">
              <div class="col-12">
                @include('sap.invoice.detail.table')
              </div>
            </div>
            <div class="row">
              @include('sap.invoice.detail.form_bottom')
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
  $(document).ready(function(){

    $(".relation_maps").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('invoice.relation_maps') }}';
      var token = '{{ csrf_token() }}';
      $.ajax({
        url: url,
        type: "POST",
        data : { id:id, _token:token },
        success: function (ajaxData){
          $("#modalEx").html(ajaxData);
          $("#modalEx").modal('show',{backdrop: 'true'});
        }
      });
    });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var docnum = $("#docnum").val();
      var url = '{{ route('invoice.search_docnum') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {docnum:docnum},
        type : "GET",
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            // var docnumx = response.docnum;
            var base = "{{ route('invoice.detail') }}";
            var docx = response.docnum;
            var href = base+'?docnum='+docx;
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Dokumen ditemukan !',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            }).then (function() {
              window.location.href = href;
            });
            // console.log(response.docnum);
          } else {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Dokumen Sales Order tidak di temukan',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        }
      });
    }); 
  });
</script>
@endsection