<div class="col-md-6">
  <div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">1. Customer</label>
    <div class="col-sm-7">{!! Form::text('CardCode',null,['class'=>'form-control form-control-sm','id'=>'cardCode','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Name</label>
    <div class="col-sm-7">{!! Form::text('CardName',null,['class'=>'form-control form-control-sm','id'=>'cardName']) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">5. Customer Ref No</label>
    <div class="col-sm-7">{!! Form::text('NumAtCard',null,['class'=>'form-control form-control-sm','id'=>'numAtCard']) !!}</div>
  </div>
  <div class="row mb-2">
    <div class="col-sm-4">{!! Form::select('local_currency',$local_currency,null,['class'=>'form-control form-control-sm']) !!}</div>
    <div class="col-sm-6">{!! Form::text('segment',null,['class'=>'form-control form-control-sm','id'=>'subSegment','readonly'=>true]) !!}</div>
  </div>
	<div class="row mb-4">
    <label class="col-sm-4 col-form-label fw-bolder">Alasan Retur</label>
    <div class="col-sm-7">{!! Form::select('U_ALASANRETUR',$alasan,null,['class'=>'form-control form-control-sm','id'=>'U_ALASANRETUR']) !!}</div>
  </div>
</div>
<div class="col-md-6">
  <div class="row">
    <label class="col-sm-3 col-form-label fw-bolder">2. Posting Date</label>
    <div class="col-sm-6">
      {!! Form::text('DocDate',$date,['class'=>'form-control form-control-sm datepick-min','id'=>'docDate','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','required'=>true]) !!}
    </div>
  </div>
  <div class="row">
    <label class="col-sm-3 col-form-label fw-bolder">3. Due Date</label>
    <div class="col-sm-6">{!! Form::text('DocDueDate',$dueDate,['class'=>'form-control form-control-sm','id'=>'docDueDate','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-3 col-form-label fw-bolder">4. Document Date</label>
    <div class="col-sm-6">{!! Form::text('document_date',$date,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
	<div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-bolder">Price List</label>
    <div class="col-sm-6">{!! Form::text('priceList',null,['class'=>'form-control form-control-sm','id'=>'priceList','readonly'=>true]) !!}</div>
  </div>
</div>