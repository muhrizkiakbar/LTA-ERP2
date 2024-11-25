<div class="modal-dialog modal-md">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Update - Sales Collector</h5>
    </div>
    <div class="modal-body">
      {!! Form::open(['route'=>['users.mapping_update'],'method'=>'POST']) !!}
      <div class="row mb-2">
        <label class="col-sm-2 col-form-label fw-bolder">Sales</label>
        <div class="col-sm-6">
          <select name="sales" class="form-control" required>
            <option value="">-- Pilih Sales --</option>
            @foreach ($sales as $item)
              <option value="{{ $item['U_SALESCODE'] }}">{{ $item['SlpName'] }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <input type="hidden" name="users_id" value="{{ $users_id }}">
      <div class="row mb-2">
        <label class="col-sm-2 col-form-label fw-bolder"></label>
        <div class="col-sm-4">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>