<div class="row mb-2">
  <label class="col-sm-3 col-form-label fw-bolder">Company</label>
  <div class="col-sm-6">{!! Form::select('company_id',$company,null,['class'=>'form-control','placeholder'=>'-- Pilih Company --']) !!}</div>
</div>
<div class="row mb-2">
  <label class="col-sm-3 col-form-label fw-bolder">Description</label>
  <div class="col-sm-9">{!! Form::text('desc',null,['class'=>'form-control']) !!}</div>
</div>
<div class="row mb-2">
  <label class="col-sm-3 col-form-label fw-bolder">Type</label>
  <div class="col-sm-6">{!! Form::select('type',$type,null,['class'=>'form-control','placeholder'=>'-- Pilih Type --']) !!}</div>
</div>
<div class="row mb-2">
  <label class="col-sm-3 col-form-label fw-bolder">Function</label>
  <div class="col-sm-9">{!! Form::text('title',null,['class'=>'form-control']) !!}</div>
</div>
<div class="row mb-2">
  <label class="col-sm-3 col-form-label fw-bolder">Url</label>
  <div class="col-sm-9">{!! Form::text('url',null,['class'=>'form-control']) !!}</div>
</div>