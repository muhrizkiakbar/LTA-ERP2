<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Batch Number</h5>
    </div>
		<div class="modal-body">
			@if (count($batch) > 0)
			<form method="POST" id="postx">
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Item Code</label>
          <div class="col-sm-7">{!! Form::text('ItemCode',$ItemCode,['class'=>'form-control form-control-sm','id'=>'itemCode','readonly'=>true]) !!}</div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Item Name</label>
          <div class="col-sm-7">{!! Form::text('ItemName',$ItemName,['class'=>'form-control form-control-sm','id'=>'itemName','readonly'=>true]) !!}</div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Warehouse</label>
          <div class="col-sm-6">
            {!! Form::text('Warehouse',$warehouse,['class'=>'form-control form-control-sm','id'=>'warehouse','readonly'=>true]) !!}
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Batch Number</label>
          <div class="col-sm-3">
						<select name="BatchNumber" id="batch" class="form-control form-control-sm" required>
							<option value="">-- Pilih Batch --</option>
							@foreach ($batch as $batch_list)
							<option value="{{ $batch_list['batch'] }}" {{ $batch_list['batch']==$BatchNumber ? 'selected' : NULL }}>{{ $batch_list['batch'] }}</option>
							@endforeach
						</select>
					</div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder"></label>
          <div class="col-sm-4">
						<input type="hidden" name="DocNum" id="docNum" value="{{ $DocNum }}">
            <input type="hidden" name="DocEntry" id="docEntry" value="{{ $DocEntry }}">
						<input type="hidden" name="Quantity" id="quantity" value="{{ $Quantity }}">
						<input type="hidden" name="order_lines_id" id="order_lines_id" value="{{ $id }}">
            {{ csrf_field() }}
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary add">Add Batch</button>
          </div>
        </div>
      </form>
			@else
				<div class="text-center">
					<h4>Batch number tidak di temukan</h4>
				</div>
				<div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder"></label>
          <div class="col-sm-4">=
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
			@endif
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $(".add").click( function(e) {
      e.preventDefault();
      $.ajax({
        url: "{!! route('sales.lines_batch_update') !!}",
        type: "POST",
        data: $("#postx").serialize(),
        dataType: 'JSON',
        success:function(response){
          if (response.message == "sukses") {
            var base = "{{ url('/sales/detail/') }}";
            var href = base+"/"+response.docnum;
            window.location.href = href;
            $("#modalEx3").modal('hide');
          }else if(response.message == "already"){
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Item code telah di pakai !',
              timer: 3000,
              showCancelButton: false,
              showConfirmButton: false
            });

            $("#modalEx3").modal('hide');
          } else {
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Quantity tidak mencukupi !',
              timer: 3000,
              showCancelButton: false,
              showConfirmButton: false
            });

            $("#modalEx3").modal('hide');
          }
        },
      });
    });
  });
</script>