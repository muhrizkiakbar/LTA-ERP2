<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      @if (isset($closing))
        @if ($date==$closing)
        <div class="row">
          <label class="col-sm-2 col-form-label fw-bolder">Posting Date</label>
          <div class="col-sm-2">{!! Form::text('DocDate',$date,['class'=>'form-control form-control-sm datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'docDate']) !!}</div>
        </div> 
        @endif
      @endif
      <table class="table table-xs table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="5%"">No</th>
            <th class="text-center">Item Code</th>
            <th class="text-center">Item Name</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Unit Price</th>
            <th class="text-center">Total Price</th>
          </tr>
        </thead>
        <tbody>
          @php $no=1; $total=0;@endphp
          @foreach ($row as $item)
          <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $item['itemCode'] }}</td>
            <td>{{ $item['title'] }}</td>
            <td style="text-align: right;">{{ $item['quantity'] }}</td>
            <td class="text-center">{{ $item['unit'] }}</td>
            <td style="text-align: right;">{{ rupiah($item['price']) }}</td>
            <td style="text-align: right;">{{ rupiah($item['total_price']) }}</td>
          </tr> 
          @php $total += $item['total_price']; @endphp
          @endforeach
          <tr>
            <td colspan="6" class="text-center"><strong>Grand Total</strong></td>
            <td style="text-align: right;"><strong>{{ rupiah($total) }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      @if ($total==0)
      <a href="{{ route('sfamix.delete',$id) }}" class="btn btn-danger">Delete</a>  
      @else
      <a href="javascript:void(0);" class="btn btn-primary push" data-id="{{ $id }}">Push To Sales</a>   
      @endif
      
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });

    $(".push").click(function(e) {
      var id = $(this).data('id');
      var docDate = $("#docDate").val();
      var url = '{{ route('sfamix.push') }}';
      $("#modalEx").modal('hide');
      $('#overlay').fadeIn();
      $.ajax({
        url: url,
        type: "GET",
        data : { id:id, docDate:docDate },
        dataType: 'JSON',
        success:function(response){
          if (response.message == "sukses") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Sales Order berhasil !',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            }).then (function() {
              window.location.href = "{!! route('sfamix') !!}";
            });
          } else if (response.message == "error") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'terjadi kesalahan, silahkan cek di history anda !',
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