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
              @include('sap.delivery.detail.form_top')
            </div>
            <div class="row">
              <div class="col-12">
                @if ($DocStatus=="O")
                {{-- <a href="Javascript:void(0);" data-id="{{ $DocEntry }}" class="btn btn-primary btn-sm mb-2 voucher">CN/Voucher</a> --}}
                @endif
                @if ($voucher != 0)
                  @if ($role==1 || $role==2)
                    <a href="Javascript:void(0);" data-id="{{ $docnum }}" class="btn btn-danger btn-sm mb-2 voucher_release">Release Voucher</a>
                  @endif
                @endif
                @include('sap.delivery.detail.table')
                @if ($voucher != 0)
                  <h5>Voucher CN</h5>
                  @include('sap.delivery.detail.table_cn')
                @endif
              </div>
            </div>
            <div class="row">
              @include('sap.delivery.detail.form_bottom')
            </div>
            <div class="row">
              <div class="col-md-7">
                {{-- <a href="{{ route('delivery.print',$docnum) }}" target="_blank" class="btn btn-primary" data-id="{{ $numAtCard }}">Print Priview</a> --}}
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                  <div class="btn-group" role="group">
                    <button id="btnGroupVerticalDrop2" type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Print Preview <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop2" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                      <a class="dropdown-item" href="{{ route('delivery.print',$docnum) }}" target="_blank">Print Nota</a>
                      {{-- <a class="dropdown-item" href="{{ route('delivery.print_png',$docnum) }}" target="_blank">Print Nota - P&G NEW</a> --}}
                      <a class="dropdown-item" href="{{ route('delivery.print_png5',$docnum) }}" target="_blank">Print Nota - P&G NEW (+PPN)</a>
                      <a class="dropdown-item" href="{{ route('delivery.print_obat',$docnum) }}" target="_blank">Print Nota - Obat</a>
                    </div>
                  </div>
                </div>
                <a href="javascript:void(0);" class="btn btn-info check" data-id="{{ $DocEntry }}">Check Return</a>
              </div>
              <div class="col-md-5">
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                  <div class="btn-group" role="group">
                    <button id="btnGroupVerticalDrop2" type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     Push To <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop2" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                      
											@if (auth()->user()->users_role_id==9 || auth()->user()->id==1)
												<a class="dropdown-item return" href="javascript:void(0);" data-id="{{ $docnum }}">Return</a>
											@else
												<a class="dropdown-item invoice" href="javascript:void(0);" data-id="{{ $docnum }}">A/R Invoice</a>
											@endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalEx" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
</div>
<div class="modal fade" id="modalEx2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
</div>
<div class="modal fade" id="modalEx3" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });

    $(".relation_maps").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('delivery.relation_maps') }}';
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

    // $('#overlay').fadeIn();
    var id = '{{ $DocEntry }}';
    var url = '{{ route('delivery.voucher_generate') }}';
    var token = '{{ csrf_token() }}';
    $.ajax({
      url: url,
      type: "POST",
      data : { id:id, _token:token },
      dataType: 'JSON',
      success:function(response){
        if (response.message=="sukses") {
          $('#overlay').hide();
          Swal.fire({
            icon: 'success',
            type: 'success',
            text: 'Voucher berhasil di generate dalam 3 Detik',
            timer: 1500,
            showCancelButton: false,
            showConfirmButton: false
          }).then (function() {
            voucher_list(response.kd);
          });
        } else if (response.message=="error_val") {
          $('#overlay').hide();
          Swal.fire({
            icon: 'error',
            type: 'warning',
            title: 'Oops...',
            text: 'Voucher tidak di mencukupi !',
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
            text: 'Voucher tidak di temukan !',
            timer: 1500,
            showCancelButton: false,
            showConfirmButton: false
          });
        }
      }
    });

    $(".invoice").click(function(e) {
      var id = $(this).data('id');
      var postingDate = $("#PostingDate").val();
      var url = '{{ route('delivery.invoice') }}';
      Swal.fire({
        title: 'Anda yakin push dokumen ke A/R Invoice ?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
      }).then((result) => {
        if (result) {
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "GET",
            data : { id:id,postingDate:postingDate },
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
                  title: 'Push dokumen berhasil !',
                  text: 'Anda akan di arahkan dalam 3 Detik',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                }).then (function() {
                  window.location.href = href;
                });
                // console.log(response.docnum);
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
              } else if (response.message=="error_nominal") {
                $('#overlay').hide();
                Swal.fire({
                  icon: 'error',
                  type: 'warning',
                  title: 'Oops...',
                  text: response.text,
                  timer: 3000,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              } else {
                $('#overlay').hide();
                Swal.fire({
                  icon: 'error',
                  type: 'warning',
                  title: 'Oops...',
                  text: 'Push document gagal, silahkan cek history anda',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              }
            }
          });
        } else {
          $('#overlay').hide();
          Swal.fire('Changes are not saved', '', 'info')
        }
      });
    });

    $(".return").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('delivery.return_temp') }}';
      Swal.fire({
        title: 'Anda yakin push dokumen ke Return ?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
      }).then((result) => {
        if (result) {
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "GET",
            data : { id:id },
            dataType: 'JSON',
            success:function(response){
              if (response.message=="sukses") {
                // var docnumx = response.docnum;
                var base = "{{ route('return.temp') }}";
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
              } else {
                $('#overlay').hide();
                Swal.fire({
                  icon: 'error',
                  type: 'warning',
                  title: 'Oops...',
                  text: 'Push document gagal, silahkan cek history anda',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              }
            }
          });
        } else {
          $('#overlay').hide();
          Swal.fire('Changes are not saved', '', 'info')
        }
      });
    });

    $("#sync").on("submit",function(e){
      e.preventDefault();
      var docnum = $("#docnum").val();
      var url = '{{ route('delivery.search_docnum') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url : url,
        data  : {docnum:docnum},
        type : "GET",
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            // var docnumx = response.docnum;
            var base = "{{ route('delivery.detail') }}";
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
    
    $(".check").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('delivery.return_check') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url: url,
        type: "GET",
        data : { id:id },
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            // var docnumx = response.docnum;
            var base = "{{ route('delivery.detail') }}";
            var docx = response.docnum;
            var href = base+'?docnum='+docx;
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Check return !',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            }).then (function() {
              window.location.href = href;
            });
            // console.log(response.docnum);
          } else {
            if (response.message=="already") {
              $('#overlay').hide();
              Swal.fire({
                icon: 'error',
                type: 'warning',
                title: 'Oops...',
                text: 'Check return telah di lakukan !',
                timer: 1500,
                showCancelButton: false,
                showConfirmButton: false
              });
            } else { 
              Swal.fire({
                icon: 'error',
                type: 'warning',
                title: 'Oops...',
                text: 'Barang return tidak ada !',
                timer: 1500,
                showCancelButton: false,
                showConfirmButton: false
              });
            }
          }
        }
      });
    });

    $(".disc").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('delivery.discount') }}';
      $.ajax({
        url: url,
        type: "GET",
        data : { id:id },
        success: function (ajaxData){
          $("#modalEx").html(ajaxData);
          $("#modalEx").modal('show',{backdrop: 'true'});
        }
      });
    });

    // $(".voucher").click(function(e) {
    //   $('#overlay').fadeIn();
    //   var id = $(this).data('id');
    //   var url = '{{ route('delivery.voucher_generate') }}';
    //   var token = '{{ csrf_token() }}';
    //   $.ajax({
    //     url: url,
    //     type: "POST",
    //     data : { id:id, _token:token },
    //     dataType: 'JSON',
    //     success:function(response){
    //       if (response.message=="sukses") {
    //         $('#overlay').hide();
    //         Swal.fire({
    //           icon: 'success',
    //           type: 'success',
    //           text: 'Voucher berhasil di generate dalam 3 Detik',
    //           timer: 1500,
    //           showCancelButton: false,
    //           showConfirmButton: false
    //         }).then (function() {
    //           voucher_list(response.kd);
    //         });
    //       } else {
    //         $('#overlay').hide();
    //         Swal.fire({
    //           icon: 'error',
    //           type: 'warning',
    //           title: 'Oops...',
    //           text: 'Voucher tidak di temukan !',
    //           timer: 1500,
    //           showCancelButton: false,
    //           showConfirmButton: false
    //         }).then (function() {
    //           voucher_list(response.kd);
    //         });
    //       }
    //     }
    //   });
    // });

    $(".update").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('delivery.update') }}';
      Swal.fire({
        title: 'Anda yakin untuk update data ?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
      }).then((result) => {
        if (result) {
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "GET",
            data : { id:id },
            dataType: 'JSON',
            success:function(response){
              if (response.message=="sukses") {
                var base = "{{ route('delivery.detail') }}";
                var docx = response.docnum;
                var href = base+'?docnum='+docx;
                $('#overlay').hide();
                Swal.fire({
                  icon: 'success',
                  type: 'success',
                  title: 'Update dokumen berhasil !',
                  text: 'Anda akan di arahkan dalam 3 Detik',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                }).then (function() {
                  window.location.href = href
                });
              } else {
                $('#overlay').hide();
                Swal.fire({
                  icon: 'error',
                  type: 'warning',
                  title: 'Oops...',
                  text: 'Error, harap cek history !',
                  timer: 3000,
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

    $(".voucher_release").click(function(e) {
      var id = $(this).data('id');
      var token = '{{ csrf_token() }}';
      var url = '{{ route('delivery.voucher_release') }}';
      Swal.fire({
        title: 'Anda yakin release voucher ?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
      }).then((result) => {
        if (result) {
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id, _token:token },
            dataType: 'JSON',
            success:function(response){
              if (response.message=="sukses") {
                // var docnumx = response.docnum;
                var base = "{{ route('delivery.detail') }}";
                var docx = response.kd;
                var href = base+'?docnum='+docx;
                $('#overlay').hide();
                Swal.fire({
                  icon: 'success',
                  type: 'success',
                  title: 'Voucher berhasil di release !',
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
                  text: 'Release voucher gagal, silahkan cek history anda',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              }
            }
          });
        } else {
          $('#overlay').hide();
          Swal.fire('Changes are not saved', '', 'info')
        }
      });
    });

  });

  function voucher_list(kd){
    var url = '{{ route('delivery.voucher') }}';
    var token = '{{ csrf_token() }}';
    $.ajax({
      url: url,
      type: "POST",
      data : { id:kd, _token:token },
      success: function (ajaxData){
        $("#modalEx3").html(ajaxData);
        $("#modalEx3").modal('show',{backdrop: 'static', keyboard: false});
      }
    });
  }
</script>
@endsection