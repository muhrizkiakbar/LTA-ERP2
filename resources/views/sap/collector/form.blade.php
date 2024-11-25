<div class="row mb-2">
  <label class="col-sm-1 col-form-label fw-bolder">Generate</label>
  <div class="col-sm-2">{!! Form::select('branch_code',$branch,null,['class'=>'form-control','id'=>'branch_code','placeholder'=>'-- Pilih Branch --']) !!}</div>
  <div class="col-sm-2">{!! Form::select('users_id',[],null,['class'=>'form-control','id'=>'users_id','placeholder'=>'-- Pilih Collector --']) !!}</div>
  <div class="col-sm-2">{!! Form::text('date',null,['class'=>'form-control datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd']) !!}</div>
</div>
