<div class="col-md-7">
  <div class="row">
    <label class="col-sm-3 col-form-label fw-bolder">11. Sales Employee</label>
    <div class="col-sm-4">{!! Form::text('SalesPersonCode',$header->SlpName,['class'=>'form-control form-control-sm','id'=>'sales_employee','readonly'=>true]) !!}</div>
  </div>
  <div class="row mb-4">
    <label class="col-sm-3 col-form-label">Owner</label>
    <div class="col-sm-4">{!! Form::text('owner',null,['class'=>'form-control form-control-sm','id'=>'owner']) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-3 col-form-label">Remarks</label>
    <div class="col-sm-4">{!! Form::textarea('remarks',null,['class'=>'form-control form-control-sm','rows'=>3,'id'=>'remarks','readonly'=>true]) !!}</div>
  </div>
</div>
<div class="col-md-5">
  <div class="row">
    <label class="col-sm-5 col-form-label">Total Before Discount</label>
    <div class="col-sm-6">{!! Form::text('totalBefDi',rupiah($header->Bruto),['class'=>'form-control form-control-sm text-right','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-3 col-form-label">Discount</label>
    <div class="col-sm-2">{!! Form::text('discount',null,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
    <div class="col-sm-6">{!! Form::text('discountAft',null,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Freight</label>
    <div class="col-sm-6">{!! Form::text('freight',null,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">
          Rounding
        </label>
      </div>
    </label>
    <div class="col-sm-6">{!! Form::text('rounding',null,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Tax</label>
    <div class="col-sm-6">{!! Form::text('tax',rupiah($header->VatSum),['class'=>'form-control form-control-sm text-right','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Total</label>
    <div class="col-sm-6">{!! Form::text('total',rupiah($header->Netto),['class'=>'form-control form-control-sm text-right','readonly'=>true]) !!}</div>
  </div>
</div>