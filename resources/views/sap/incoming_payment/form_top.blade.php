<div class="col-md-6">
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Company</label>
    <div class="col-sm-7">{!! Form::text('CardCode',null,['class'=>'form-control form-control-sm','id'=>'cardCode','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Branch</label>
    <div class="col-sm-7">{!! Form::text('CardCode',null,['class'=>'form-control form-control-sm','id'=>'cardCode','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Customer Code</label>
    <div class="col-sm-7">{!! Form::text('CardCode',null,['class'=>'form-control form-control-sm','id'=>'cardCode','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label">Customer Name</label>
    <div class="col-sm-7">{!! Form::text('CardName',null,['class'=>'form-control form-control-sm','id'=>'cardName']) !!}</div>
  </div>
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Bill To</label>
    <div class="col-sm-7">{!! Form::textarea('Address',null,['class'=>'form-control form-control-sm','id'=>'Address','rows'=>3,'readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Contact Person</label>
    <div class="col-sm-7">{!! Form::text('ContactPerson',null,['class'=>'form-control form-control-sm','id'=>'ContactPerson','readonly'=>true]) !!}</div>
  </div>
</div>
<div class="col-md-6">
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Posting Date</label>
    <div class="col-sm-6">
      {!! Form::text('DocDate',$date,['class'=>'form-control form-control-sm','id'=>'docDate','readonly'=>true]) !!}
    </div>
  </div>
  <div class="row mb-2">
    <label class="col-sm-3 col-form-label fw-bolder">Due Date</label>
    <div class="col-sm-6">{!! Form::text('DocDueDate',$date,['class'=>'form-control form-control-sm','id'=>'docDueDate','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-bolder">Document Date</label>
    <div class="col-sm-6">{!! Form::text('document_date',$date,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
</div>