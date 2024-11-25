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
            <th class="text-center">Branch</th>
            <th class="text-center">Alamat</th>
            <th class="text-center">Sub Segment</th>
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
              <td class="text-center">{{ $item['U_CLASS'] }}</td>
              <td>{{ $item['Address'] }}</td>
              <td class="text-center">{{ $item['U_CLEVEL_SEG4'] }}</td>
            </tr>
            @endforeach
          @endif
        </tbody>
      </table>
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
          var csrf = '{{ csrf_token() }}';
          var url = '{{ route('return_request.select_customer') }}';
          $.ajax({
            url: url,
            type: "POST",
            data : { _token: csrf,id:id },
            dataType: 'JSON',
            success: function (response){
              $('#cardCode').val(response.customer);
              $('#cardName').val(response.name);
              $("#SalesPersonCode").val(response.sales);
              $("#subSegment").val(response.segment);
              $("#BplId").val(response.bplid);
              $("#Nopol1").val(response.nopol1);
              $("#Nopol2").val(response.nopol2);
							$("#priceList").val(response.price_list);
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