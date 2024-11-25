<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Pilih Cabang...</h5>
    </div>
		<div class="modal-body">
      <table class="table table-sm table-striped table-bordered">
        @php $no = 1; @endphp
        @foreach ($bpl as $item)
        <tr>
          <td width="5%">{{  $no++ }}</td>
          <td>{{ $item['BPLName'] }}</td>
          <td class="text-center">
            <a href="#" class="check" data-id="{{ $item['BPLName'] }}">
              <span class="badge bg-success">Pilih</span>
            </a>
          </td>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $(".check").click(function(e) {
      var id = $(this).data('id');
      var csrf = '{{ csrf_token() }}';
      var url = '{{ route('dashboard.save_bpl') }}';
      $.ajax({
        url: url,
        type: "POST",
        data : { _token: csrf,id:id },
        dataType: 'JSON',
        success: function (response){
          if (response.message == "bpl_set") {
            $("#modalEx").modal('hide');
          }
        }
      });
    });
  });
</script>