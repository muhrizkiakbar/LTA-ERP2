<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      <table class="table table-sm table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="5%"">No</th>
            <th class="text-center">Item Code</th>
            <th class="text-center">Item Name</th>
            <th class="text-center">Notes</th>
            <th class="text-center">Exp. Date</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Unit Price</th>
            <th class="text-center">Total Price</th>
          </tr>
        </thead>
        <tbody>
          @php
              $no=1;
          @endphp
          @foreach ($row['lines'] as $lines)
          <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $lines->ItemCode }}</td>
            <td>{{ $lines->ItemName }}</td>
            <td>{{ $lines->note }}</td>
            <td class="text-center">{{ $lines->ExpDate }}</td>
            <td class="text-center">{{ $lines->Quantity }}</td>
            <td class="text-right">{{ rupiah($lines->UnitPrice) }}</td>
            <td class="text-right">{{ rupiah($lines->LineTotal) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="7" class="text-center"><strong>Grand Total</strong></td>
            <td style="text-align: right;"><strong>{{ rupiah($total) }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      @if ($row['header']['approval_spv_st']==1 && $row['header']['approval_sbh_st']==1)
      {{-- <a href="{{ route('return.approval.view',$id) }}" class="btn btn-primary" data-id="{{ $id }}">Proses</a>  --}}
      <a href="{{ route('return.approval.print',$id) }}" target="_blank" class="btn btn-success">Print Surat Jalan</a>    
      @endif
    </div>
  </div>
</div>