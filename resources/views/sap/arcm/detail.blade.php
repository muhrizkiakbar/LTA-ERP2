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
              <li class="breadcrumb-item">Delivery Order</li>
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
              @include('sap.arcm.detail.form_top')
            </div>
            <div class="row">
              <div class="col-12">
                @include('sap.arcm.detail.table')
              </div>
            </div>
            <div class="row">
              @include('sap.arcm.detail.form_bottom')
            </div>
            <div class="row">
              <div class="col-md-7">
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                  <div class="btn-group" role="group">
                    <button id="btnGroupVerticalDrop2" type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Print Preview <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop2" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                      <a class="dropdown-item" href="{{ route('arcm.print',$header->DocNum) }}" target="_blank">Nota Retur</a>
                      <a class="dropdown-item" href="{{ route('arcm.print_kwitansi',$header->DocNum) }}" target="_blank">Kwitansi</a>
                      <a class="dropdown-item" href="{{ route('arcm.print_tanda_terima',$header->DocNum) }}" target="_blank">Tanda Terima</a>
                      <a class="dropdown-item" href="{{ route('arcm.print_bs',$header->DocNum) }}" target="_blank">Nota Retur BS</a>
                    </div>
                  </div>
                </div>
                {{-- <a href="{{ route('arcm.print',$header->DocNum) }}"  class="btn btn-primary">Print Priview</a> --}}
              </div>
              <div class="col-md-5">
                
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
<div class="modal fade" id="modalEx2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
<div class="modal fade" id="modalEx3" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var docnum = $("#docnum").val();
      var url = '{{ route('arcm.search') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {docnum:docnum},
        type : "GET",
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            // var docnumx = response.docnum;
            var base = "{{ route('arcm.detail') }}";
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
          } else if(response.message=="close") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Maaf, dokumen telah di close',
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
              text: 'Dokumen Delivery Order tidak di temukan',
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