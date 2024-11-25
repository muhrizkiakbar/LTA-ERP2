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
              <li class="breadcrumb-item">Sales A/R</li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <form method="POST" id="sync">
              <div class="form-group row">
                <label class="col-form-label col-md-3">Date From</label>
                <div class="col-md-3">{!! Form::text('dateFrom',null,['class'=>'form-control form-control-sm datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateFrom','required'=>true]) !!}</div>
                <label class="col-form-label col-md-1">To</label>
                <div class="col-md-3">{!! Form::text('dateTo',null,['class'=>'form-control form-control-sm datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'dateTo','required'=>true]) !!}</div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">Time From</label>
                <div class="col-md-2">{!! Form::text('timeFrom',null,['class'=>'form-control form-control-sm','id'=>'timeFrom','required'=>true]) !!}</div>
                <label class="col-form-label col-md-1">To</label>
                <div class="col-md-2">{!! Form::text('timeTo',null,['class'=>'form-control form-control-sm','id'=>'timeTo','required'=>true]) !!}</div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">Sales</label>
                <div class="col-md-6">
                  <select name="sales" id="sales" class="form-control form-control-sm select2" required>
                    <option value="">-- Pilih Sales --</option>
                    @foreach ($sales as $sales)
                    <option value="{{ $sales['title'] }}">{{ $sales['title'] }}</option>    
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">Cabang</label>
                <div class="col-md-4">
                  <select name="cabang" id="cabang" class="form-control form-control-sm select2" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach ($cabang as $cabang)
                    <option value="{{ $cabang['Cabang'] }}">{{ $cabang['Cabang'] }}</option>    
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">Tipe Barang</label>
                <div class="col-md-4">
                  <select name="tipe" id="tipe" class="form-control form-control-sm select2" required>
                    <option value="">-- Pilih tipe --</option>
                    @foreach ($tipe as $tipe)
                    <option value="{{ $tipe['Tipe'] }}">{{ $tipe['Tipe'] }}</option>    
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">Kirim Luar Kota</label>
                <div class="col-md-3">{!! Form::select('status',$status,[],['class'=>'form-control form-control-sm','id'=>'status','required'=>true,'placeholder'=>'-- Pilih Status --','required'=>true]) !!}</div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3">User</label>
                <div class="col-md-4">
                  <select name="user" id="user" class="form-control form-control-sm select2" required>
                    <option value="">-- Pilih user --</option>
                    @foreach ($user as $user)
                    <option value="{{ $user['Username'] }}">{{ $user['Username'] }}</option>    
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-md-3"></label>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-primary">Cari</button>
                  <a href="javascript:void(0);" class="btn btn-info print mr-2">
                    Cetak
                  </a>
                </div>
              </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div id="loadView"></div>
      </div>
    </div>
    
  </div>
</div>
@endsection
@section('customjs')
<script type="text/javascript">
  $(document).ready(function(){

    $(".print").bind("click", function(event) {
	  	$('#print_page').printArea();
	  });

    $('.datepick').datepicker({
    	autoClose:true
    });

    $('.select2').select2();

    $("#sync").on("submit",function(e){
      e.preventDefault();
      $('#overlay').show();
      var dateFrom = $("#dateFrom").val();
      var dateTo = $("#dateTo").val();
      var timeFrom = $("#timeFrom").val();
      var timeTo = $("#timeTo").val();
      var tipe = $("#tipe").val();
      var user = $("#user").val();
      var cabang = $("#cabang").val();
      var status = $("#status").val();
      var sales = $("#sales").val();
      var csrf = "{!! csrf_token() !!}";
      var url = '{{ route('report.delivery_sales_search') }}';
      $.ajax({
        url : url,
        data  : {timeFrom:timeFrom,timeTo:timeTo,tipe:tipe,dateFrom:dateFrom,dateTo:dateTo,user:user,cabang:cabang,status:status,sales:sales,_token:csrf},
        type : "POST",
        success : function(data){
          $('#loadView').html(data);
        },
        complete : function(data){
          $('#overlay').hide();
        }
      });
    });
  });
</script>
@endsection
