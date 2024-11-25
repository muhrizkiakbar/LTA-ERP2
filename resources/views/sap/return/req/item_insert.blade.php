<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Tambahkan Item</h5>
    </div>
		<div class="modal-body">
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
          <label class="col-sm-3 col-form-label fw-bolder">Tax Code</label>
          <div class="col-sm-3">{!! Form::select('TaxCode',$tax,[],['class'=>'form-control form-control-sm','id'=>'TaxCode']) !!}</div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Warehouse</label>
          <div class="col-sm-6">
            {!! Form::select('Warehouse',$whsList,[],['class'=>'form-control form-control-sm','id'=>'warehouse']) !!}
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Satuan</label>
          <div class="col-sm-3">{!! Form::select('Satuan',$satuan,[],['class'=>'form-control form-control-sm','id'=>'Satuan']) !!}</div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder">Quantity</label>
          <div class="col-sm-2">
            {!! Form::text('Quantity',null,['class'=>'form-control form-control-sm','id'=>'quantity']) !!}
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-sm-3 col-form-label fw-bolder"></label>
          <div class="col-sm-4">
            <input type="hidden" name="CardCode" value="{{ $CardCode }}">
            {{ csrf_field() }}
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary add">Add Item</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $(".add").click( function(e) {
      e.preventDefault();
      $.ajax({
        url: "{!! route('return_request.temp_store') !!}",
        type: "POST",
        data: $("#postx").serialize(),
        dataType: 'JSON',
        success:function(response){
          if (response.message == "sukses") {
            $("#modalEx3").modal('hide');
            loadTable();
            $("#totalBeforeDisc").val(response.totalBefore);
            $("#vatSum").val(response.vatSum);
            $("#total").val(response.total);
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
            loadTable();
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
            loadTable();
          }
        },
      });
    });
  });
</script>