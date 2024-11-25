<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      {!! Form::model($row,['route'=>['master.sales_employee_update',$row->id],'method'=>'PUT','files' => true]) !!}
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Title</label>
				<div class="col-sm-9">{!! Form::text('title',null,['class'=>'form-control']) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Code SAP</label>
				<div class="col-sm-6">{!! Form::text('code_sap',null,['class'=>'form-control','readonly'=>true]) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Code SFA</label>
				<div class="col-sm-6">{!! Form::text('code',null,['class'=>'form-control','readonly'=>true]) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Code KINO</label>
				<div class="col-sm-6">{!! Form::text('code_kino',null,['class'=>'form-control']) !!}</div>
			</div>
      <div class="row mb-2">
        <label class="col-sm-3 col-form-label fw-bolder"></label>
        <div class="col-sm-4">
          <button type="submit" class="btn btn-primary">Update</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
