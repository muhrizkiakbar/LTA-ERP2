<div class="col-md-6">
  <div class="row">
    <label class="col-sm-4 col-form-label fw-bolder">1. Customer</label>
    <div class="col-sm-7">{!! Form::text('customer',$row['CardCode'],['class'=>'form-control form-control-sm','id'=>'CardCode','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-4 col-form-label">Name</label>
    <div class="col-sm-7">{!! Form::text('name',isset($custx['CardName']) ? $custx['CardName'] : '-',['class'=>'form-control form-control-sm','id'=>'name','readonly'=>true]) !!}</div>
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
    <div class="col-sm-6">{!! Form::text('customer_ref',isset($custx['cseg4']) ? $custx['cseg4'] : '-',['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
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
    <label class="col-sm-2 col-form-label">Status</label>
    <div class="col-sm-3 col-form-label">
      <a href="javascript:void(0);" class="relation_maps" data-id="{{ $docnum }}">Relation Maps</a>
    </div>
    <div class="col-sm-6">{!! Form::text('status',$DocStatus,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label fw-bolder">2. Posting Date</label>
    <div class="col-sm-6">{!! Form::text('posting_date',$row['DocDate'],['class'=>'form-control form-control-sm datepick','data-language'=>'en','data-date-format'=>'yyyy-mm-dd','id'=>'PostingDate']) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label fw-bolder">3. Due Date</label>
    <div class="col-sm-6">{!! Form::text('due_date',$row['DocDueDate'],['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-3">
    <label class="col-sm-5 col-form-label fw-bolder">4. Document Date</label>
    <div class="col-sm-6">{!! Form::text('document_date',$row['DocDate'],['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Branch Reg. No</label>
    <div class="col-sm-6">{!! Form::text('branch_reg_no',$branch_reg,['class'=>'form-control form-control-sm','id'=>'branch','readonly'=>true]) !!}</div>
  </div>
	<div class="row mb-3">
    <label class="col-sm-5 col-form-label fw-bolder">Price List</label>
    <div class="col-sm-6">{!! Form::text('priceList',$custx['PriceList'],['class'=>'form-control form-control-sm','id'=>'priceList','readonly'=>true]) !!}</div>
  </div>
</div>