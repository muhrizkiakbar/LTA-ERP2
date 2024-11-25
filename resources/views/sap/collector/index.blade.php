@extends('layouts.backend.app')
@section('content')
<div id="overlay" style="display:none;">
  <div class="spinner-border text-primary m-2" role="status">
    <span class="sr-only">Loading...</span>
  </div>  
</div>
<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">{{ $title }}</h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item">Dashboard</li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        @include('sap.template.alert')
        <div class="card">
          <div class="card-body">
          {!! Form::open(['route'=>['collector.generate'],'method'=>'POST']) !!}
            <div class="row mb-2">
              <label class="col-sm-1 col-form-label fw-bolder">Generate</label>
              <div class="col-sm-2">{!! Form::select('company_id',$company,null,['class'=>'form-control','id'=>'company_id','placeholder'=>'-- Pilih Company --']) !!}</div>
              <div class="col-sm-2">{!! Form::select('branch_code',$branch,null,['class'=>'form-control','id'=>'branch_code','placeholder'=>'-- Pilih Branch --']) !!}</div>
              <div class="col-sm-2">{!! Form::select('users_id',[],null,['class'=>'form-control','id'=>'users_id','placeholder'=>'-- Pilih Collector --']) !!}</div>
              <div class="col-sm-2">{!! Form::text('date',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','placeholder'=>'-- Pilih Tanggal --']) !!}</div>
              <div class="col-sm-2">
                <button class="btn btn-primary btn-sm" type="submit">Simpan</button>
              </div>
            </div>
          {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          {{-- {!! dd($row) !!} --}}
          <table class="table table-xxs table-striped table-bordered datatable-basic">
            <thead>
              <tr>
                <th class="text-center" width="2%"">No</th>
                <th class="text-center">Company</th>
                <th class="text-center">Branch</th>
                <th class="text-center">Kategori</th>
                <th class="text-center">Date</th>
                <th class="text-center">Collector</th>
                <th class="text-center">Total Customer</th>
                <th class="text-center">Status</th>
                <th class="text-center">#</th>
              </tr>
            </thead>
            <tbody>
              @php
                  $no=1;
              @endphp
              @foreach ($row as $item)
              <tr>
                <td class="text-center">{{  $no++ }}</td>
                <td class="text-center">{{ $item['company'] }}</td>
                <td>{{ $item['branch'] }}</td>
                <td>{{ $item['category'] }}</td>
                <td>{{ $item['date'] }}</td>
                <td>{{ $item['user'] }}</td>
                <td class="text-center">{{ $item['toko'] }}</td>
                <td class="text-center">{!! $item['status'] !!}</td>
                <td class="text-center">
                  {{-- <a href="javascript:void(0)" class="detail" data-id="{{ $item['kd'] }}">
                    <span class="badge badge-info">Detail</span>
                  </a>
                  @if ($item['users_admin_st']==0)
                  <a href="{{ route('collector.additional',$item['kd']) }}" onclick="confirm('Anda yakin melakukan addtional task ?')">
                    <span class="badge badge-primary">Add Task</span>
                  </a> 
                  <a href="{{ route('collector.start_day',$item['kd']) }}" onclick="confirm('Anda yakin lakukan serah terima dokumen ?')">
                    <span class="badge badge-success">Serah Terima</span>
                  </a> 
                  <a href="{{ route('collector.additional2',$item['kd']) }}">
                    <span class="badge badge-primary">Titip Nota</span>
                  </a>
                  @endif
                  @if ($item['st']!=2)
                  <a href="{{ route('collector.close',$item['kd']) }}">
                    <span class="badge badge-warning">Close</span>
                  </a>
                  @endif
                  @if ($role==1 || $role==2)
                  <a href="{{ route('collector.delete',$item['kd']) }}" onclick="confirm('Anda yakin lakukan delete ?')">
                    <span class="badge badge-danger">Delete</span>
                  </a>   
                  @endif --}}
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Action <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a href="javascript:void(0)" class="dropdown-item waves-effect waves-light detail text-info" data-id="{{ $item['kd'] }}">
                        Detail
                      </a>
                      @if ($item['users_admin_st']==0)
                      <a class="dropdown-item waves-effect waves-light text-primary" href="{{ route('collector.additional',$item['kd']) }}" onclick="confirm('Anda yakin melakukan addtional task ?')">
                        Add Task
                      </a> 
                      <a class="dropdown-item waves-effect waves-light  text-success" href="{{ route('collector.start_day',$item['kd']) }}" onclick="confirm('Anda yakin lakukan serah terima dokumen ?')">
                        Serah Terima
                      </a> 
                      <a class="dropdown-item waves-effect waves-light text-primary" href="{{ route('collector.additional2',$item['kd']) }}">
                        Titip Nota
                      </a>
                      @endif
                      @if ($item['st']!=2)
                      <a class="dropdown-item waves-effect waves-light text-warning" href="{{ route('collector.close',$item['kd']) }}">
                        Close
                      </a>
                      @endif
                      @if ($role==1 || $role==2)
                      <a class="dropdown-item waves-effect waves-light text-danger" href="{{ route('collector.delete',$item['kd']) }}" onclick="confirm('Anda yakin lakukan delete ?')">
                        Delete
                      </a>   
                      @endif
                    </div>
                  </div>
                </td>
              </tr>    
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
    </div>
  </div>
</div>
<div class="modal fade" id="modalEx" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  var start_date;
  var end_date;
  var DateFilterFunction = (function (oSettings, aData, iDataIndex) {
    var dateStart = start_date;
    var dateEnd = end_date;
    //Kolom tanggal yang akan kita gunakan berada dalam urutan 2, karena dihitung mulai dari 0
    //nama depan = 0
    //nama belakang = 1
    //tanggal terdaftar =2
    var evalDate=aData[3];
    if((isNaN(dateStart) && isNaN(dateEnd)) || (isNaN(dateStart) && evalDate <= dateEnd) || (dateStart <= evalDate && isNaN(dateEnd)) || (dateStart <= evalDate && evalDate <= dateEnd)){
      return true;
    }
    return false;
  });


  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });
    
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

    var oTable = $('.datatable-basic').DataTable({
      "dom" : "<'row mt-4'<'col-sm-3'f><'col-sm-3' <'datesearchbox'>>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    	"select": "single",
    	"serverSide": false,
    	drawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

        $(".detail").unbind();
        $(".detail").click(function(e) {
          var id = $(this).data('id');
          var csrf = "{{ csrf_token() }}";
          var url = '{{ route('collector.detail') }}';
          $.ajax({
            url: url,
            type: "POST",
            data : { id:id,_token:csrf },
            success: function (ajaxData){
              $("#modalEx").html(ajaxData);
              $("#modalEx").modal('show',{backdrop: 'true'});
            }
          });
        });
      },
      preDrawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
      } 
    });

    //menambahkan daterangepicker di dalam datatables
    $("div.datesearchbox").html('<input type="text" class="form-control" id="datesearch" data-range="true" data-multiple-dates-separator=" - " placeholder="Pilih range tanggal..">');

    //konfigurasi daterangepicker pada input dengan id datesearch
    $('#datesearch').datepicker({
    	autoClose:true,
      dateFormat: 'yyyy-mm-dd',
      language: 'en',
      onSelect: function(ev, picker) {
        var datepicker = $('#datesearch').datepicker().data('datepicker');
        // var dateRange = $(this).val(picker.startDate + ' - ' + picker.endDate);
        // start_date=picker.startDate;
        // end_date=picker.endDate;
        // $.fn.dataTableExt.afnFiltering.push(DateFilterFunction);
        // oTable.draw();
        console.log(datepicker.selectDate());
      }
    });
    //menangani proses saat apply date range
    // $('#datesearch').on('click.datepicker', function(ev, picker) {
    //    $(this).val(picker.startDate.format('yyyy-mm-dd') + ' - ' + picker.endDate.format('yyyy-mm-dd'));
    //    start_date=picker.startDate.format('yyyy-mm-dd');
    //    end_date=picker.endDate.format('yyyy-mm-dd');
    //    $.fn.dataTableExt.afnFiltering.push(DateFilterFunction);
    //    $dTable.draw();
    // });

    $("#branch_code").on('change', function (e) {
      var branch_code = $("#branch_code").val();
      var csrf = "{{ csrf_token() }}";
      var url = '{{ route('collector.search_collector') }}';
      $.ajax({
        url : url,
        data  : {branch_code:branch_code,_token:csrf},
        type : "POST",
        dataType: "JSON",
        success: function (response){
          $("#users_id").html(response.listdoc);
        }
      });
    });
  }); 
</script>
@endsection