<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      <table class="table table-sm table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="5%"">No</th>
            <th class="text-center">Item Code</th>
            <th class="text-center">Item Code Vdist</th>
            <th class="text-center">Item Name</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Unit Price</th>
            <th class="text-center">Discount</th>
            <th class="text-center">Total Price</th>
          </tr>
        </thead>
        <tbody>
          @php
              $no=1;
          @endphp
          @foreach ($row as $item)
          <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $item->ItemCode }}</td>
            <td>{{ $item->ItemCodeVdist }}</td>
            <td>{{ $item->ItemName }}</td>
            <td class="text-right">{{ $item->Quantity }}</td>
            <td class="text-center">{{ $item->UoMCode }}</td>
            <td class="text-right">{{ rupiah($item->UnitPrice) }}</td>
            <td class="text-right">{{ $item->DiscountPercent }}</td>
            <td class="text-right">{{ rupiah($item->LineTotal) }}</td>
          </tr>   
          @endforeach
          <tr>
            <td colspan="8" class="text-center">
              <strong>GRAND TOTAL</strong>
            </td>
            <td class="text-right">
              <strong>{{ rupiah($total) }}</strong>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
      <a href="{{ route('vdist.delete',$id) }}" class="btn btn-danger">Delete</a>  
      <a href="javascript:void(0);" class="btn btn-primary push" data-id="{{ $id }}">Push To Delivery</a>   
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
      var url = '{{ route('vdist.push') }}';
      var token = '{{ csrf_token() }}';
      $("#modalEx").modal('hide');
      $('#overlay').fadeIn();
      $.ajax({
        url: url,
        type: "POST",
        data : { id:id, _token:token },
        dataType: 'JSON',
        success:function(response){
          if (response.message == "sukses") {
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Delivery Order berhasil !',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            }).then (function() {
              window.location.href = "{!! route('vdist') !!}";
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