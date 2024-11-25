<div class="card">
  <table class="table table-xxs table-striped table-bordered dataTable">
    <thead>
      <tr>
        <th class="text-center" width="2%"">No</th>
        <th class="text-center">Customer</th>
        <th class="text-center">Segment</th>
        <th class="text-center">Date</th>
        <th class="text-center">Sales</th>
        <th class="text-center">Off Route</th>
        <th class="text-center">Compliance</th>
        <th class="text-center">Time In</th>
        <th class="text-center">Time Out</th>
        <th class="text-center">Sales Act</th>
        <th class="text-center">Image</th>
        <th class="text-center">#</th>
      </tr>
    </thead>
    <tbody>
      @php
          $no=1;
      @endphp
      @foreach ($row as $item)
      <tr {!! $item['temuan']==1 ? 'class="table-danger"' : '' !!}>
        <td class="text-center">{{ $no++ }}</td>
        <td>{{ $item['store_name'] }}</td>
        <td class="text-center">{{ $item['store_chanel'] }}</td>
        <td>{{ $item['visit_date'] }}</td>
        <td>{{ $item['seller_name'] }}</td>
        <td class="text-center">{{ $item['off_route'] }}</td>
        <td class="text-center">{{ $item['compliance'] }}</td>
        <td class="text-center">{{ $item['time_in'] }}</td>
        <td class="text-center">{{ $item['time_out'] }}</td>
        <td class="text-right">{{ rupiah($item['sales_act']) }}</td>
        <td class="text-center">
          <div class="gallery">
            <a href="{{ $item['file'] }}">
              <img src="{{ $item['file'] }}" height="80px" alt="">
            </a>
          </div>
        </td>
        <td class="text-center">
          @if ($item['temuan']==0)
          <a href="javascript:void(0);" class="temuan" data-id="{{ $item['id'] }}">
            <span class="badge badge-warning">Temuan</span>
          </a>   
          @endif
          <span class="badge badge-info">Detail</span>
        </td> 
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var gallery = $('.gallery a').simpleLightbox();

    $.extend( $.fn.dataTable.defaults, {
			iDisplayLength:25,        
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

    var oTable = $('.dataTable').DataTable({
    	"select": "single",
    	"serverSide": false,
    	drawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

        $(".temuan").unbind();
        $(".temuan").click(function(e) {
          var id = $(this).data('id');
          var token = '{{ csrf_token() }}';
          var url = '{{ route('gps_compliance.temuan') }}';
          $('#overlay').fadeIn();
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id,_token:token },
            dataType : "JSON",
            success:function(response){
              if (response.message == "sukses") {
                $('#overlay').hide();
                Swal.fire({
                  icon: 'success',
                  type: 'success',
                  text: 'Temuan berhasil di input',
                  timer: 1500,
                  showCancelButton: false,
                  showConfirmButton: false
                });
              }
            },
            complete : function(response){
              loadView();
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