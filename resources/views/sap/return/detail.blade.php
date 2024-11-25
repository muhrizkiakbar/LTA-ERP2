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
              @include('sap.return.detail.form_top')
            </div>
            <div class="row">
              <div class="col-12">
                @include('sap.return.detail.table')
              </div>
            </div>
            <div class="row">
              @include('sap.return.detail.form_bottom')
            </div>
            <div class="row">
              <div class="col-md-7">
                <a href="{{ route('return.print',$header->DocNum) }}" target="_blank" class="btn btn-primary">Print Priview</a>
              </div>
              <div class="col-md-5">
                @if (auth()->user()->branch_sap=='130' || auth()->user()->id==1)
									@if ($header->DocStatus!='C')
										<a href="javascript:void(0);" class="btn btn-success push" data-id="{{ $header->DocEntry }}">Push To A/R Credit Memo</a>
									@endif
                @endif
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
    $('.datepick2').datepicker({
    	autoClose:true
    });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var docnum = $("#docnum").val();
      var url = '{{ route('return.search') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {docnum:docnum},
        type : "GET",
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            // var docnumx = response.docnum;
            var base = "{{ route('return.detail') }}";
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

		$(".push").click(function(e) {
      var id = $(this).data('id');
      var DocDate = $("#PostingDate").val();
			var NumAtCard = $("#numAtCard").val();
			var csrf = '{{ csrf_token() }}';
      var url = '{{ route('return.pushCreditNotes') }}';
      Swal.fire({
        title: 'Anda yakin push ke A/R Credit Memo ?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
      }).then((result) => {
        if (result) {
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id,docDate:DocDate, numAtCard:NumAtCard, _token:csrf },
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
                  title: 'Push dokumen berhasil !',
                  text: 'Anda akan di arahkan dalam 3 Detik',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                }).then (function() {
                  window.location.href = href;
                });
                // console.log(response.docnum);
              } else if((response.message=="sap-error")) { 
                $('#overlay').hide();
                Swal.fire({
                  icon: 'error',
                  type: 'warning',
                  title: 'Oops...',
                  text: response.text,
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              } else if (response.message=="period") {
                $('#overlay').hide();
                Swal.fire({
                  icon: 'error',
                  type: 'warning',
                  title: 'Oops...',
                  text: response.text,
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
                  text: 'Push document gagal, silahkan cek history anda ',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              }
            }
          });
        } else {
          Swal.fire('Changes are not saved', '', 'info')
        }
      });
    });
  });
</script>
@endsection