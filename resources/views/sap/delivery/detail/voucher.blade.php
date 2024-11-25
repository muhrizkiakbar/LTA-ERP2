<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <form method="POST" action="{{ route('delivery.voucher_update') }}">
    <div class="modal-body">
      <table class="table table-xxs table-striped table-bordered">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th class="text-center">Document Number</th>
            <th class="text-center">Referensi Number</th>
            <th class="text-center">Description</th>
            <th class="text-center">Date</th>
            <th class="text-center">Balance</th>
          </tr>
        </thead>
        <tbody>
          @if (count($row)==0)
          <tr>
            <td class="text-center" colspan="10">
              Data Tidak di Temukan !!!
            </td>
          </tr>
          @else
            @php
              $no=1;
            @endphp
            @foreach ($row as $item)
            <tr>
              <td class="text-center">{{ $no++ }}</td>
              <td>{{ $item['DocNum'] }}</td>
              <td>{{ $item['NumAtCard'] }}</td>
              <td>{{ $item['Comments'] }}</td>
              <td class="text-center">{{ $item['DocDate'] }}</td>
              <td class="text-right">{{ rupiahnon2($item['BalanceDue']) }}</td>
            </tr>  
            @endforeach
            <tr>
              <td colspan="5" class="text-center"><strong>GRAND TOTAL</strong></td>
              <td class="text-right"><strong>{{ rupiahnon2($docTotal) }}</strong></td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <input type="hidden" name="kd" value="{{ $kd }}">
      <input type="hidden" name="docTotalCN" value="{{ $docTotal }}">
      {{ csrf_field() }}
      {{-- <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> --}}
      <button type="submit" class="btn btn-primary">Update</button>
    </div>
    </form>
  </div>
</div>