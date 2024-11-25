<div class="card-body" style="overflow-x: scroll;">
  <div id="print_page" style="max-height: 60vh;overflow-y: scroll;">
    <table class="table table-sm table-bordered table-striped" style="position: relative;">
      <thead style="position: sticky; top: 0; " class="thead-dark">
        <tr>
          <th class="text-center" rowspan="2">Date</th>
          <th class="text-center" rowspan="2">Customer</th>
          <th class="text-center" rowspan="2">Customer Name</th>
          <th class="text-center" rowspan="1" colspan="3">Sfa Order</th>
          <th class="text-center" rowspan="1" colspan="3">Proccess Rp</th>
          <th class="text-center" rowspan="2">Percentage</th>
        </tr>
        <tr>
          <th class="text-center">Order</th>
          <th class="text-center">Success</th>
          <th class="text-center">Unserved</th>
          <th class="text-center">Order</th>
          <th class="text-center">Success</th>
          <th class="text-center">Unserved</th>
        </tr>

      </thead>
      <tbody class="mt-5">
          @foreach($data as $item) 
         <tr>
            <td>{{$item['DocDate']}}</td>
            <td>{{$item['CardCode']}}</td>
            <td>{{$item['CardName']}}</td>
            <td class="text-center">{{$item['SfaQtyOrder']}}</td>
            <td class="text-center">{{$item['SfaQtySuccess']}}</td>
            <td class="text-center">{{$item['SfaQtyUnserve']}}</td>
            <td class="text-right">{{rupiahnon2($item['TotalOrderRp'])}}</td>
            <td class="text-right">{{rupiahnon2($item['TotalSuccessRp'])}}</td>
            <td class="text-right">{{rupiahnon2($item['TotalUnservedRp'])}}</td>
            <td class="text-right">{{round($item['UnservedPrecentage'], 2)}} %</td>
          </tr>
          @endforeach
      </tbody>
      <tfoot style="position: sticky; bottom: 0; " class="thead-dark">
        <tr>
          <th colspan="3">Grand Total</th>
          <th  class="text-center">{{rupiahnon2($sfa_total_order)}}</th>
          <th  class="text-center">{{rupiahnon2($sfa_success_order)}}</th>
          <th  class="text-center">{{rupiahnon2($sfa_unserved_order)}}</th>
          <th class="text-right">{{rupiahnon2($total_order_rp)}}</th>
          <th class="text-right">{{rupiahnon2($total_success_rp)}}</th>
          <th class="text-right">{{rupiahnon2($total_unserved_rp)}}</th>
          <th class="text-right">{{round($percentage, 2)}} %</th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>