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
          <label class="col-sm-3 col-form-label fw-bolder">Warehouse</label>
          <div class="col-sm-6">
            {!! Form::text('Warehouse',$warehouse,['class'=>'form-control form-control-sm','id'=>'warehouse','readonly'=>true]) !!}
            <span class="font-13 text-muted">Stok Gudang Utama : {{ $available }}</span>
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
            <input type="hidden" name="DocNum" value="{{ $DocNum }}">
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
        url: "{!! route('sales.lines_item_store') !!}",
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