<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <form method="POST" id="discx">
    <div class="modal-body">
      <table class="table table-xxs table-striped table-bordered">
        <thead>
          <tr>
            <th class="text-center">Item No.</th>
            <th class="text-center" width="30%">Item Description</th>
						<th class="text-center">NISIB</th>
            <th class="text-center">DISC 1</th>
            <th class="text-center">DISC 2</th>
            <th class="text-center">DISC 3</th>
            <th class="text-center">DISC 4</th>
            <th class="text-center">DISC 5</th>
            <th class="text-center">DISC 6</th>
            <th class="text-center">DISC 7</th>
            <th class="text-center">DISC 8</th>
          </tr>
        </thead>
        <tbody>
          @php $no=0; @endphp
          @foreach ($lines as $item)
          <tr>
            <td>{{ $item['itemCode'] }}</td>
            <td>{{ $item['itemDesc'] }}</td>
						<td class="text-right">{{ round($item['nisib']) }}</td>
            <td>{!! Form::text('disc1[]',$item['disc1'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc2[]',$item['disc2'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc3[]',$item['disc3'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc4[]',$item['disc4'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc5[]',$item['disc5'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc6[]',$item['disc6'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc7[]',$item['disc7'],['class'=>'form-control form-control-sm']) !!}</td>
            <td>{!! Form::text('disc8[]',$item['disc8'],['class'=>'form-control form-control-sm']) !!}</td>
          </tr>
          <input type="hidden" name="id[]" value="{{ $item['id'] }}">
          <input type="hidden" name="total[]" value="{{ $item['beforeDiscount'] }}">
					<input type="hidden" name="disc1_rp[]" value="{{ $item['disc1_rp'] }}">
					<input type="hidden" name="disc2_rp[]" value="{{ $item['disc2_rp'] }}">
					<input type="hidden" name="disc3_rp[]" value="{{ $item['disc3_rp'] }}">
					<input type="hidden" name="disc4_rp[]" value="{{ $item['disc4_rp'] }}">
					<input type="hidden" name="disc5_rp[]" value="{{ $item['disc5_rp'] }}">
					<input type="hidden" name="disc6_rp[]" value="{{ $item['disc6_rp'] }}">
					<input type="hidden" name="disc7_rp[]" value="{{ $item['disc7_rp'] }}">
					<input type="hidden" name="disc8_rp[]" value="{{ $item['disc8_rp'] }}">
          <input type="hidden" name="idx[{{ $no }}]" value="{{ $no }}">
          @php $no++;@endphp
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      {{ csrf_field() }}
      <input type="hidden" name="numAtCard" value="{{ $id }}">
			<input type="hidden" name="cardCode" value="{{ $cardCode }}">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Update Discount</button>
    </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $("#discx").on("submit",function(e){
      e.preventDefault();
      $("#modalEx").modal('hide');
      $('#overlay').fadeIn();
      var url = '{{ route('return_request.discount_update') }}';
      $.ajax({
        url: url,
        type: "POST",
        data: $("#discx").serialize(),
        dataType: 'JSON',
        success: function (response){
          if (response.message=="sukses") {
						$("#modalEx3").modal('hide');
            loadTable();
            $("#totalBeforeDisc").val(response.totalBefore);
            $("#vatSum").val(response.vatSum);
            $("#total").val(response.total);
						$('#overlay').hide();
          } else {
            $("#modalEx3").modal('hide');
            loadTable();
						$('#overlay').hide();
          }
        }
      });
    });
  });
</script>