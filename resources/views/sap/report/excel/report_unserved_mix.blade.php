<div >
  <div id="print_page" >
    <table class="table table-sm table-bordered table-striped">
      <tr>
       <th class="text-center" rowspan="1">No</th>
        <th class="text-center" rowspan="1">Date</th>
        <th class="text-center" rowspan="1">Item Code</th>
        <th class="text-center" rowspan="1">Item Name</th>
        <th class="text-center" rowspan="1">Customer</th>
        <th class="text-center" rowspan="1">Customer Name</th>
        <th class="text-center" rowspan="1">NISIB</th>
        <th class="text-center" rowspan="1">Harga</th>
        <th class="text-center" colspan="1">Total Order</th>
        <th class="text-center" rowspan="1">Success</th>
        <th class="text-center" rowspan="1">Unserved</th>
        <th class="text-center" rowspan="1">Total Order Rp</th>
        <th class="text-center" rowspan="1">Total Unserved Rp</th>
        <th class="text-center" rowspan="1">Total Success Rp</th>
        <th class="text-center" rowspan="1">Percentage Unserved</th>
      </tr>
      @php $no=1; @endphp
      @foreach ($data as $item)
      <tr>
        <td class="text-center">{{ $no++ }}</td>
        <td class="text-center">{{ $item['DocDate'] }}</td>
        <td>{{ $item['ItemCode'] }}</td>
        <td>{{ $item['ItemName'] }}</td>
        <td>{{ $item['CardCode'] }}</td>
        <td>{{ $item['CardName'] }}</td>
        <td class="text-right">0</td>
        <td class="text-right">{{ rupiahnon2($item['Harga']) }}</td>
        <td class="text-right">{{ round($item['SfaQtyTotal'],0) }}</td>
        <td class="text-right">{{ round($item['SfaQtySuccess'],0) }}</td>
        <td class="text-right">{{ round($item['SfaQtyUnserve'],0) }}</td>
        <td class="text-right">{{ rupiahnon2($item['Total']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['TotalUnserved']) }}</td>
        <td class="text-right">{{ rupiahnon2($item['TotalSuccess']) }}</td>
        <td class="text-right">{{ round($item['percentage'],2).' %'}}</td>
      </tr>  
      @endforeach
    </table>
  </div>
</div>