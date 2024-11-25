<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Pilih Item...</h5>
    </div>
		<div class="modal-body">
      <table class="table table-xs table-bordered table-hover datatable-basic2">
        <thead>
          <tr>
            <th>#</th>
            <th class="text-center">Kode Item</th>
            <th class="text-center">Deskripsi</th>
            <th class="text-center">Barcode</th>
            <th class="text-center">SKU</th>
            <th class="text-center">NISIB</th>
            <th class="text-center">Satuan Kecil</th>
          </tr>
        </thead>
        <tbody>
          @if (!empty($row['data']))
            @foreach ($row['data'] as $item)
            <tr>
              <td class="text-center">
                <a href="#" class="check_item" data-id="{{ $item['ItemCode'] }}">
                  <span class="badge badge-success">Pilih</span>
                </a>
              </td>
              <td>{{ $item['ItemCode'] }}</td>
              <td>{{ $item['ItemName'] }}</td>
              <td>{{ $item['BarCode'] }}</td>
              <td>{{ $item['SKU'] }}</td>
              <td class="text-center">{{ round($item['NISIB'],2) }}</td>
              <td class="text-center">{{ $item['SATUAN_KECIL'] }}</td>
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
    $.extend( $.fn.dataTable.defaults, {
			iDisplayLength:10,        
      autoWidth: false,
			columnDefs: [{ 
				orderable: false,
				targets: [  ]
			}],
      dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
      language: {
        search: '<span>Filter:</span> _INPUT_',
        searchPlaceholder: 'Type to filter...',
        lengthMenu: '<span>Show:</span> _MENU_',
        paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
      }
    });

    var oTable = $('.datatable-basic2').DataTable({
    	"select": "single",
    	"serverSide": false,
    	drawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

        $(".check_item").unbind();
        $(".check_item").click(function(e) {
          var id = $(this).data('id');
          var cardcode = $("#cardCode").val();
          var csrf = '{{ csrf_token() }}';
          var url = '{{ route('dashboard.getItemDetail') }}';
          $.ajax({
            url: url,
            type: "POST",
            data : { _token: csrf,id:id,cardcode:cardcode },
            success: function (response){ 
              $("#modalEx2").modal('hide');
              $("#modalEx3").html(response);
              $("#modalEx3").modal('show',{backdrop: 'true'});
            }
          });
        });
      },
      preDrawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
      } 
    });
  });
</script>