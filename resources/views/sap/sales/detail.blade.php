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
              <li class="breadcrumb-item"><a href="{{ route('sales') }}">Sales Order</a></li>
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
              @include('sap.sales.detail.form_top')
            </div>
            <div class="row">
              <div class="col-12">
                @if ($DocStatus=="O")
                <div class="row mb-3">
                  <label class="col-sm-1 col-form-label fw-bolder">Cari Item</label>
                  <div class="col-sm-2">{!! Form::text('ItemName',null,['class'=>'form-control form-control-sm','id'=>'itemName']) !!}</div>
                </div>
                <a href="Javascript:void(0);" data-id="{{ $DocEntry }}" class="btn btn-primary btn-sm mb-2 disc">Discount Calculation</a>
								<a href="Javascript:void(0);" data-id="{{ $DocEntry }}" class="btn btn-info btn-sm mb-2 fixbug">Fix Bug</a>
                @endif
                @include('sap.sales.detail.table')
              </div>
            </div>
            <div class="row">
              @include('sap.sales.detail.form_bottom')
            </div>
            <div class="row">
              <div class="col-md-7">
                @if ($DocStatus=="O")
                <a href="javascript:void(0);" class="btn btn-primary update" data-id="{{ $DocEntry }}">Update</a>
                <a href="javascript:void(0);" class="btn btn-danger closex" data-id="{{ $DocEntry }}">Close</a>
                @else
                <a href="javascript:void(0);" class="btn btn-warning checkx" data-id="{{ $DocEntry }}">Check Document</a>
                @endif
                <a href="{{ route('sales') }}" class="btn btn-secondary">Back</a>
              </div>
              <div class="col-md-5">
                @if ($DocStatus=="O")
                <a href="javascript:void(0);" class="btn btn-success delivery" data-id="{{ $DocEntry }}">Push To Delivery >></a>
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
    // $('.select2').select2();
    $('.datepick').datepicker({
    	autoClose:true
    });

		$(".fixbug").click(function(e){
			var id = $(this).data('id');
      var url = '{{ route('sales.fixbug') }}';
      var token = '{{ csrf_token() }}';
			$('#overlay').fadeIn();
			$.ajax({
        url: url,
        type: "POST",
        data : { id:id, _token:token },
				dataType: 'JSON',
        success:function(response){
					if (response.message=="sukses") {
						var base = "{{ url('/sales/detail/') }}";
            var href = base+"/"+response.docnum;
						$('#overlay').hide();
						Swal.fire({
							icon: 'success',
							type: 'success',
							title: 'Fix dokumen berhasil !',
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
		});

    $(".relation_maps").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('sales.relation_maps') }}';
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

    $(".disc").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('sales.discount') }}';
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

    $(".edit").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('sales.lines_item_edit') }}';
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

		$(".batch").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('sales.lines_batch') }}';
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

    $(".update").click(function(e) {
      var id = $(this).data('id');
      var SalesPersonCode = $("#SalesPersonCode").val();
      var DocDate = $("#PostingDate").val();
      var url = '{{ route('sales.update') }}';
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
            data : { id:id,SalesPersonCode:SalesPersonCode,DocDate:DocDate },
            dataType: 'JSON',
            success:function(response){
              if (response.message=="sukses") {
                var base = "{{ url('/sales/detail/') }}";
                var href = base+"/"+response.docnum;
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

    $(".closex").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('sales.close') }}';
      Swal.fire({
        title: 'Anda yakin untuk close document ?',
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
                var base = "{{ url('/sales/detail/') }}";
                var href = base+"/"+response.docnum;
                $('#overlay').hide();
                Swal.fire({
                  icon: 'success',
                  type: 'success',
                  title: 'Close dokumen berhasil !',
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

    $(".delivery").click(function(e) {
      var id = $(this).data('id');
      var DocDate = $("#PostingDateX").val();
      var url = '{{ route('sales.delivery') }}';
      Swal.fire({
        title: 'Anda yakin push dokumen ke Delivery ?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
      }).then((result) => {
        if (result) {
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "GET",
            data : { id:id,docDate:DocDate },
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

    $(".checkx").click(function(e){
      var id = $(this).data('id');
      var url = '{{ route('sales.check_document') }}';
      $('#overlay').fadeIn();
      $.ajax({
        url: url,
        type: "GET",
        data : { id:id },
        dataType: 'JSON',
        success:function(response){
          if (response.message=="sukses") {
            var base = "{{ url('/sales/detail/') }}";
            var href = base+"/"+response.docnum;
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Check dokumen berhasil !',
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
              text: 'Document di SAP telah di close !',
              timer: 3000,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        }
      });
    });

    $("#itemName").on('keypress', function (e) {
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        var itemName = $("#itemName").val();
        var cardCode = $("#cardCode").val();
        var csrf = "{{ csrf_token() }}";
        var url = '{{ route('sales.lines_item') }}';
        $.ajax({
          url : url,
          data  : {itemName:itemName,cardCode:cardCode,_token:csrf},
          type : "POST",
          success: function (ajaxData){
            if (ajaxData.message == "error") {
              Swal.fire({
                icon: 'error',
                type: 'warning',
                title: 'Oops...',
                text: 'Data customer belum dipilih !',
                timer: 1500,
                showCancelButton: false,
                showConfirmButton: false
              });
            }else {
              $("#modalEx2").html(ajaxData);
              $("#modalEx2").modal('show',{backdrop: 'true'});
            }
          }
        });
      }
    });
  });
</script>
@endsection