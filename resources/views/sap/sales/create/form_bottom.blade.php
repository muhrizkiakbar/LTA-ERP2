<div class="col-md-7">
  <div class="row">
    <label class="col-sm-3 col-form-label fw-bolder">11. Sales Employee</label>
    <div class="col-sm-4">
      <select name="sales" id="SalesPersonCode" class="form-control form-control-sm select2" required>
        <option value="">-- Pilih Sales --</option>
        @foreach ($sales as $sales)
        <option value="{{ $sales['id'] }}">{{ $sales['title'] }}</option>    
        @endforeach
      </select>
    </div>
  </div>
  <div class="row mb-4">
    <label class="col-sm-3 col-form-label">Owner</label>
    <div class="col-sm-4">{!! Form::text('owner',null,['class'=>'form-control form-control-sm','id'=>'owner']) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-3 col-form-label">Remarks</label>
    <div class="col-sm-4">{!! Form::textarea('remarks',$remarks,['class'=>'form-control form-control-sm','rows'=>3,'id'=>'remarks','readonly'=>true]) !!}</div>
  </div>
</div>
<div class="col-md-5">
  <div class="row">
    <label class="col-sm-5 col-form-label">Total Before Discount</label>
    <div class="col-sm-6">{!! Form::text('totalBefDi',null,['class'=>'form-control form-control-sm text-right','id'=>'totalBeforeDisc','readonly'=>true]) !!}</div>
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
    <div class="col-sm-6">{!! Form::text('rounding',null,['class'=>'form-control form-control-sm text-right','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Tax</label>
    <div class="col-sm-6">{!! Form::text('tax',null,['class'=>'form-control form-control-sm text-right','id'=>'vatSum','readonly'=>true]) !!}</div>
  </div>
  <div class="row">
    <label class="col-sm-5 col-form-label">Total</label>
    <div class="col-sm-6">{!! Form::text('total',null,['class'=>'form-control form-control-sm text-right','id'=>'total','readonly'=>true]) !!}</div>
  </div>
</div>
<input type="hidden" id="BplId">
<input type="hidden" id="Nopol1">
<input type="hidden" id="Nopol2">