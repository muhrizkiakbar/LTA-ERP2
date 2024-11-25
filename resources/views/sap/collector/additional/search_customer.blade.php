<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Pilih Customer...</h5>
    </div>
		<div class="modal-body">
      <table class="table table-xxs table-striped table-bordered datatable-basic">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th class="text-center">Kode Customer</th>
            <th class="text-center">Nama Customer</th>
            <th class="text-center" width="300px">Alamat</th>
          </tr>
        </thead>
        <tbody>
          @if (!empty($row['data']))
            @foreach ($row['data'] as $item)
            <tr>
              <td class="text-center">
                <a href="#" class="check" data-id="{{ $item['CardCode'] }}">
                  <span class="badge badge-success">Pilih</span>
                </a>
              </td>
              <td>{{ $item['CardCode'] }}</td>
              <td>{{ $item['CardName'] }}</td>
              <td>{{ $item['Address'] }}</td>
            </tr>
            @endforeach
          @endif
        </tbody>
      </table>
      <input type="hidden" name="kd" id="kd" value="{{ $kd }}">
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var oTable = $('.datatable-basic').DataTable({
    	"select": "single",
    	"serverSide": false,
    	drawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

        $(".check").unbind();
        $(".check").click(function(e) {
          var id = $(this).data('id');
          var kd = $("#kd").val();
          var csrf = '{{ csrf_token() }}';
          var url = '{{ route('collector.additional_customer_search') }}';
          $.ajax({
            url: url,
            type: "POST",
            data : { _token: csrf,id:id,kd:kd },
            dataType: 'JSON',
            success: function (response){
              getInvoice(response.Company, response.CardCode, response.Kategori, response.SlpName, response.kd);
              $('#cardCode').val(response.CardCode);
              $('#cardName').val(response.CardName);
              $("#modalEx").modal('hide');
            }
          });
        });
      },
      preDrawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
      } 
    });

    $('.datatable-basic').each(function() {
      var datatable = $(this);
      // SEARCH - Add the placeholder for Search and Turn this into in-line form control
      var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
      search_input.attr('placeholder', 'Search');
      search_input.removeClass('form-control-sm');
      // LENGTH - Inline-Form control
      var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
      length_sel.removeClass('form-control-sm');
    });
  });
</script>