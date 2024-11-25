<div class="card-body">
  <div id="print_page">
    <table border="1" width="100%">
      <tr>
        <td class="text-center">No</td>
        <td class="text-center">Kode Sales</td>
        <td class="text-center">Nama Sales</td>
        <td class="text-center">SFA Order</td>
        <td class="text-center">ERP Order</td>
        <td class="text-center">Deviasi</td>
      </tr>
      @php
        $no=1;
      @endphp
      @foreach ($row as $item)
      <tr>
        <td class="text-center">{{ $no++ }}</td>
        <td>{{ $item['sales_code_sfa'] }}</td>
        <td>{{ $item['sales_name'] }}</td>
        <td class="text-center">{{ $item['sfa_order'] }}</td>
        <td class="text-center">{{ $item['erp_order'] }}</td>
        <td class="text-center">{{ $item['deviasi'] }}</td>
      </tr>
      @endforeach
    </table>
  </div>
</div>