<div class="card-body">
  <div id="print_page">
    <table class="table table-sm table-bordered table-striped" width="100%">
      <thead>
        <tr>
          <th class="text-center" width="10px">No</th>
          <th class="text-center">Code</th>
          <th class="text-center">Item Code SAP</th>
          <th class="text-center">Item Code Vdist</th>
          <th class="text-center">Item Name</th>
          <th class="text-center">Quantity</th>
        </tr>
      </thead>
      <tbody>
        @php
          $no=1;
        @endphp
        @foreach ($row as $item)
        <tr>
          <td class="text-center">{{ $no++ }}</td>
          <td>{{ $item->NumAtCard }}</td>
          <td>{{ $item->ItemCode }}</td>
          <td>{{ $item->ItemCodeVdist }}</td>
          <td>{{ $item->ItemName }}</td>
          <td class="text-right">{{ $item->QuantityVdist }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>