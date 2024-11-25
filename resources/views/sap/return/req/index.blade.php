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
      </div>
    </div>
    <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="row mb-3">
							@include('sap.return.req.form_top')
						</div>
						<div class="row mb-3">
              <div class="col-12">
                <div class="row mb-3">
                  <label class="col-sm-1 col-form-label fw-bolder">Cari Item</label>
                  <div class="col-sm-2">{!! Form::text('ItemName',null,['class'=>'form-control form-control-sm','id'=>'itemName']) !!}</div>
                </div>
								<a href="javascript:void(0);" data-id="{{ $users_id }}" class="btn btn-primary btn-sm mb-2 disc">Discount Calculation</a>
              </div>
            </div>
						<div class="row">
							<div class="col-12">
								<div id="loadTable"></div>
							</div>
							<hr>
						</div>
						<div class="row">
							@include('sap.return.req.form_bottom')
						</div>
						<div class="row mb-3">
							<div class="col-sm-2 d-grid">
								<a href="javascript:void(0);" id="save" class="btn btn-md btn-primary">Save</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
  </div>
</div>
<input type="hidden" id="minDate" value="{{ $minDate }}">
<input type="hidden" id="maxDate" value="{{ $date }}">
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
		var minDate = $("#minDate").val();
		var maxDate = $("#maxDate").val();
		
		loadTable();

    $('.datepick').datepicker({
    	autoClose:true
    });

		$('.datepick-min').datepicker({
    	autoClose:true,
			minDate: new Date(minDate),
			maxDate: new Date(maxDate)
    });

		$(".disc").click(function(e) {
      var id = $(this).data('id');
			var cardCode = $("#cardCode").val();
			var csrf = "{{ csrf_token() }}";
      var url = '{{ route('return_request.discount') }}';
      $.ajax({
        url: url,
        type: "POST",
        data : { id:id, cardCode:cardCode, _token:csrf },
        success: function (ajaxData){
          $("#modalEx").html(ajaxData);
          $("#modalEx").modal('show',{backdrop: 'true'});
        }
      });
    });

		$("#cardName").on('keypress', function (e) {
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        var cardName = $("#cardName").val();
        var csrf = "{{ csrf_token() }}";
        var url = '{{ route('return_request.search_customer') }}';
        $.ajax({
          url : url,
          data  : {cardName:cardName,_token:csrf},
          type : "POST",
          success: function (ajaxData){
            $("#modalEx").html(ajaxData);
            $("#modalEx").modal('show',{backdrop: 'true'});
          }
        });
      }
    });

		$("#itemName").on('keypress', function (e) {
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        var itemName = $("#itemName").val();
        var cardCode = $("#cardCode").val();
        var csrf = "{{ csrf_token() }}";
        var url = '{{ route('return_request.search_item') }}';
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

		$("#save").click(function(ex) {
      ex.preventDefault();
      $('#overlay').show();
      var cardCode = $("#cardCode").val();
      var docDate = $("#docDate").val();
      var docDueDate = $("#docDueDate").val();
      var numAtCard = $("#numAtCard").val();
      var SalesPersonCode = $("#SalesPersonCode").val();
      var Comments = $("#remarks").val();
      var BplId = $("#BplId").val();
      var Nopol1 = $("#Nopol1").val();
      var Nopol2 = $("#Nopol2").val();
			var U_ALASANRETUR = $("#U_ALASANRETUR").val();
      var csrf = "{!! csrf_token() !!}";
      var url = '{{ route('return_request.store') }}';
      $.ajax({
        url : url,
        data  : {
          cardCode:cardCode,
          docDate:docDate,
          docDueDate:docDueDate,
          numAtCard:numAtCard,
          SalesPersonCode:SalesPersonCode,
          Comments:Comments,
          BplId:BplId,
          Nopol1:Nopol1,
          Nopol2:Nopol2,
					U_ALASANRETUR:U_ALASANRETUR,
          _token:csrf},
        type : "POST",
        dataType : "JSON",
        success: function (response){
          if (response.message=="sukses") {
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
	});

	function loadTable(){
		var url = '{{ route('return_request.temp_load') }}';
		$.ajax({
			url: url,
			type: "GET",
			success : function(data){
				$('#loadTable').html(data);
			}
		});
	}
	</script>
@endsection