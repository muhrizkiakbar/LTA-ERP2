<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    {!! Form::model($row,['route'=>['return.temp_lines_update',$row->id],'method'=>'PUT','files' => true]) !!}
    <div class="modal-body">
      <div class="row">
        <label class="col-sm-3 col-form-label fw-bolder">Item Code</label>
        <div class="col-sm-7">{!! Form::text('ItemCode',null,['class'=>'form-control form-control-sm','readonly'=>true]) !!}</div>
      </div>
      <div class="row">
        <label class="col-sm-3 col-form-label fw-bolder">Quantity</label>
        <div class="col-sm-3">{!! Form::text('Quantity',null,['class'=>'form-control form-control-sm']) !!}</div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>