<div class="table-responsive">
  <table class="table table-xxs table-striped table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th class="text-center">Item No.</th>
        <th class="text-center">Item Description</th>
        <th class="text-center">Quantity</th>
        <th class="text-center">Satuan</th>
        <th class="text-center">Unit Price</th>
        <th class="text-center">Tax Code</th>
        <th class="text-center">Gudang</th>
        <th class="text-center">Distr. Rule</th>
				<th class="text-center">Discount %</th>
        <th class="text-center">Total (LC)</th>
      </tr>
    </thead>
		<tbody>
      @php
          $no=1;
      @endphp
      @foreach ($row as $item)
      <tr>
        <td>
          <a href="#" class="text-danger delete" data-id="{{ $item['id'] }}">
            Delete
          </a>
        </td>
        <td>{{ $item['itemCode'] }}</td>
        <td>{{ $item['itemDesc'] }}</td>
        <td class="text-right">{{ $item['qty'] }}</td>
        <td>{{ $item['unitMsr'] }}</td>
        <td class="text-right">{{ rupiah($item['unitPrice']) }}</td>
        <td>{{ $item['taxCode'] }}</td>
        <td>{{ $item['whsCode'] }}</td>
        <td>{{ $item['cogs'] }}</td>
				<td class="text-right">{{ rupiahnon2($item['disc']) }}</td>
        <td class="text-right">{{ rupiah($item['docTotal']) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $(".delete").click( function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      var csrf = '{{ csrf_token() }}';
      var url = '{{ route('return_request.temp_delete') }}';
      $.ajax({
        url: url,
        type: "POST",
        data : { _token: csrf,id:id},
        dataType: 'JSON',
        success:function(response){
          if (response.message == "sukses") {
            $("#modalEx3").modal('hide');
            loadTable();
            $("#totalBeforeDisc").val(response.totalBefore);
            $("#vatSum").val(response.vatSum);
            $("#total").val(response.total);
          }else {
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Terdapat kesalahan, harap cek kembali !',
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