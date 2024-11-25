<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      {!! Form::model($row,['route'=>['master.item_kino_update',$row->id],'method'=>'PUT','files' => true]) !!}
      <div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Item Code</label>
				<div class="col-sm-6">{!! Form::text('code',null,['class'=>'form-control']) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Title</label>
				<div class="col-sm-9">{!! Form::text('title',null,['class'=>'form-control']) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Barcode</label>
				<div class="col-sm-6">{!! Form::text('barcode',null,['class'=>'form-control']) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">CSN</label>
				<div class="col-sm-6">{!! Form::text('csn',null,['class'=>'form-control']) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Flag Bonus</label>
				<div class="col-sm-3">{!! Form::select('flag_bonus',$bool,null,['class'=>'form-control','placeholder'=>'-- Pilih --']) !!}</div>
			</div>
			<div class="row mb-2">
				<label class="col-sm-3 col-form-label fw-bolder">Flag Active</label>
				<div class="col-sm-3">{!! Form::select('flag_active',$bool,null,['class'=>'form-control','placeholder'=>'-- Pilih --']) !!}</div>
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
