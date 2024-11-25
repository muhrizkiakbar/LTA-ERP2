<div class="modal-dialog modal-md">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      {!! Form::open(['route'=>['vdist.import_store'],'method'=>'POST','files'=>true]) !!}
      <div class="row mb-2">
        <label class="col-sm-3 col-form-label fw-bolder">Tanggal</label>
        <div class="col-sm-7">{!! Form::text('date',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd']) !!}</div>
      </div>
      <div class="row mb-2">
        <label class="col-sm-3 col-form-label fw-bolder">Branch</label>
        <div class="col-sm-7">{!! Form::select('branch',$branch,null,['class'=>'form-control','placeholder'=>'-- Pilih Branch --','required'=>true]) !!}</div>
      </div>
      <div class="row mb-2">
        <label class="col-sm-3 col-form-label fw-bolder">File</label>
        <div class="col-sm-7">{!! Form::file('file',['required'=>true]) !!}</div>
      </div>
      <div class="row mb-2">
        <label class="col-sm-3 col-form-label fw-bolder"></label>
        <div class="col-sm-4">
          <button type="submit" class="btn btn-primary">Import</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });
  });
</script>