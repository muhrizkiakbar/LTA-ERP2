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
              <li class="breadcrumb-item">Return</li>
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
              @include('sap.return.temp.form_top')
            </div>
            <div class="row">
              <div class="col-12">
								<a href="javascript:void(0);" class="btn btn-danger btn-sm mb-2 delete_lines" onclick="return confirm('Apakah Anda yakin ingin menghapus item yang dipilih?')">Delete Lines</a>
                <input type="hidden" name="kd" id="kd" value="{{ $docnum }}">
								<input type="hidden" name="docEntry" id="docEntry" value="{{ $DocEntry }}">
								@include('sap.return.temp.table')
              </div>
            </div>
            <div class="row">
              @include('sap.return.temp.form_bottom')
            </div>
            <div class="row">
              <div class="col-md-7">
                <a href="javascript:void(0)" class="btn btn-success push" data-id="{{ $docnum }}">Push Return</a>
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
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){
    $(".edit").click(function(e) {
      var id = $(this).data('id');
      var url = '{{ route('return.temp_lines_edit') }}';
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

		$(".delete_lines").click(function(e) {
			e.preventDefault();
			var kd = $("#kd").val();
			var docEntry = $("#docEntry").val();
			var url = '{{ route('return.delete_lines_mark') }}';
			var checkboxes = document.getElementsByName("id[]");
      var selectedItems = [];
			var token = '{{ csrf_token() }}';

			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].checked) {
					selectedItems.push(checkboxes[i].value);
				}
			}

			$.ajax({
				url: url,
				type: "POST",
				data : {data:JSON.stringify(selectedItems), kd:kd, docEntry:docEntry,  _token:token },
				dataType: 'JSON',
				success:function(response){
					if (response.message=="sukses") {
						var base = "{{ route('return.temp') }}";
						var docx = response.docnum;
						var href = base+'?docnum='+docx;
						$('#overlay').hide();
						Swal.fire({
							icon: 'success',
							type: 'success',
							title: 'Delete lines dokumen berhasil !',
							text: 'Anda akan di arahkan dalam 3 Detik',
							timer: 1500,
							showCancelButton: false,
							showConfirmButton: false
						}).then (function() {
							window.location.href = href;
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
		});

    $(".push").click(function(e) {
			e.preventDefault();
      var id = $(this).data('id');
			var U_ALASANRETUR = $("#U_ALASANRETUR").val();
      var url = '{{ route('return.push') }}';
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
            data : { id:id, U_ALASANRETUR:U_ALASANRETUR },
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
          Swal.fire('Changes are not saved', '', 'info')
        }
      });
    });
  });
</script>
@endsection