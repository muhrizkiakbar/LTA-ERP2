<div class="col-md-6">
  <div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">1. Customer</label>
    <div class="col-sm-7">{!! Form::text('customer',$row['CardCode'],['class'=>'form-control form-control-sm','id'=>'customer','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Name</label>
    <div class="col-sm-7">{!! Form::text('name',$customer['CardName'],['class'=>'form-control form-control-sm','id'=>'name','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Contact Person</label>
    <div class="col-sm-7">{!! Form::text('contact_person',null,['class'=>'form-control form-control-sm','id'=>'contact_person','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">5. Customer Ref No</label>
    <div class="col-sm-7">{!! Form::text('customer_ref',$row['NumAtCard'],['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-4">
    <div class="col-sm-4">{!! Form::text('local_currency',$local_currency,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
	<div class="row mb-4">
    <label class="col-sm-4 col-form-label fw-bolder">Alasan Retur</label>
    <div class="col-sm-7">{!! Form::select('U_ALASANRETUR',$alasan,null,['class'=>'form-control form-control-sm','id'=>'U_ALASANRETUR']) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Branch</label>
    <div class="col-sm-7">{!! Form::text('branch',$branch_title,['class'=>'form-control form-control-sm','id'=>'branch','readonly'=>true]) !!}</div>
  </div>
</div>
<div class="col-md-6">
  <div class="row">
    <label class="col-sm-2 col-form-label">No.</label>
    <div class="col-sm-3">
      {!! Form::text('series',$series,['class'=>'form-control form-control-sm','readonly'=>true]) !!}
      </select>
    </div>
    <div class="col-sm-6">{!! Form::text('docnum',$docnum,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Status</label>
    <div class="col-sm-6">{!! Form::text('status',null,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label fw-bolder">2. Posting Date</label>
    <div class="col-sm-6">{!! Form::text('posting_date',$docDate,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label fw-bolder">3. Due Date</label>
    <div class="col-sm-6">{!! Form::text('due_date',$docDueDate,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-3">
    <label class="col-sm-5 col-form-label fw-bolder">4. Document Date</label>
    <div class="col-sm-6">{!! Form::text('document_date',$docDate,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Branch Reg. No</label>
    <div class="col-sm-6">{!! Form::text('branch_reg_no',$branch_reg,['class'=>'form-control form-control-sm','id'=>'branch','readonly'=>true]) !!}</div>
  </div>
</div>