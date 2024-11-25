<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
		<div class="modal-body">
			{!! Form::open(['route'=>['voucher_pairing.generate'],'method'=>'POST','files' => true]) !!}
			<table class="table table-sm table-bordered table-striped table-hover" width="100%">
				<thead>
					<tr>
						<th class="text-center">
							<input type="checkbox" id="select-all">
						</th>
						<th class="text-center">Document Number</th>
						<th class="text-center" width='100px'>Date</th>
            <th class="text-center">Referensi Number</th>
            <th class="text-center">Description</th>
            <th class="text-center">Doc Total</th>
					</tr>
				</thead>
				<tbody>
					@php
						$no = 0;
					@endphp
					@foreach ($row as $item)
					<tr>
						<td class="text-center">
							<input type="checkbox" class="item" name="check[{{ $no }}]" value="{{ $item['DocTotal'] }}">
							<input type="hidden" name="DocEntry[]" value="{{ $item['DocEntry'] }}">
							<input type="hidden" name="DocNum[]" value="{{ $item['DocNum'] }}">
							<input type="hidden" name="DocDate[]" value="{{ $item['DocDate'] }}">
							<input type="hidden" name="DocDueDate[]" value="{{ $item['DocDueDate'] }}">
							<input type="hidden" name="CardCode[]" value="{{ $item['CardCode'] }}">
							<input type="hidden" name="CardName[]" value="{{ $item['CardName'] }}">
							<input type="hidden" name="Comments[]" value="{{ $item['Comments'] }}">
							<input type="hidden" name="NumAtCard[]" value="{{ $item['NumAtCard'] }}">
							<input type="hidden" name="DocTotal[]" value="{{ $item['DocTotal'] }}">
							<input type="hidden" name="PaidToDate[]" value="{{ $item['PaidToDate'] }}">
							<input type="hidden" name="BalanceDue[]" value="{{ $item['BalanceDue'] }}">
							<input type="hidden" name="OcrCode2[]" value="{{ $item['OcrCode2'] }}">
						</td>
						<td class="text-center">{{ $item['DocNum'] }}</td>
						<td class="text-center">{{ $item['DocDate'] }}</td>
						<td>{{ $item['NumAtCard'] }}</td>
						<td>{{ $item['Comments'] }}</td>
						<td class="text-right">{{ rupiahnon2($item['DocTotal']) }}</td>
					</tr>
					@php
						$no++;
					@endphp
					@endforeach
				</tbody>
			</table>
			<div class="row">
				<div class="col-9">
					<div class="form-group row">
            <label class="col-form-label col-md-2">Delivery Number</label>
            <div class="col-md-4">{!! Form::text('DocNumDelivery',null,['class'=>'form-control','required'=>true]) !!}</div>
          </div>
				</div>
				<div class="col-3">
					<div class="text-right">
						<button type="submit" class="btn btn-primary">Simpan</button>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>