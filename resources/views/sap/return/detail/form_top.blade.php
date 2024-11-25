<div class="col-md-6">
  <div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">1. Customer</label>
    <div class="col-sm-7">{!! Form::text('customer',$header->CardCode,['class'=>'form-control form-control-sm','id'=>'customer','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Name</label>
    <div class="col-sm-7">{!! Form::text('name',$header->CardName,['class'=>'form-control form-control-sm','id'=>'name','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Contact Person</label>
    <div class="col-sm-7">{!! Form::text('contact_person',null,['class'=>'form-control form-control-sm','id'=>'contact_person','readonly'=>true]) !!}</div>
  </div>
	<div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">5. Customer Ref No</label>
    <div class="col-sm-7">{!! Form::text('NumAtCard',$header->NumAtCard,['class'=>'form-control form-control-sm','id'=>'numAtCard']) !!}</div>
  </div>
  <div class="row mb-4">
    <div class="col-sm-4">{!! Form::text('local_currency',$local_currency,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
</div>
<div class="col-md-6">
  <div class="row">
    <label class="col-sm-2 col-form-label">No.</label>
    <div class="col-sm-3">
      {!! Form::text('series',$header->Series,['class'=>'form-control form-control-sm','readonly'=>true]) !!}
      </select>
    </div>
    <div class="col-sm-6">{!! Form::text('docnum',$header->DocNum,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label fw-bolder">2. Posting Date</label>
    <div class="col-sm-6">{!! Form::text('posting_date',$header->DocDate,['class'=>'form-control form-control-sm datepick2','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'PostingDate','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label fw-bolder">3. Due Date</label>
    <div class="col-sm-6">{!! Form::text('due_date',$header->DocDueDate,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-3">
    <label class="col-sm-5 col-form-label fw-bolder">4. Document Date</label>
    <div class="col-sm-6">{!! Form::text('document_date',$header->DocDate,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
</div>