{!! Form::open(['route'=>['collector.additional_generate'],'method'=>'POST']) !!}
<table class="table table-sm table-bordered table-striped" width="100%">
  <tr>
    <th class="text-center">#</th>
    <th class="text-center">Document Number</th>
    <th class="text-center">Referensi Number</th>
    <th class="text-center">Due Date</th>
    <th class="text-center">Jadwal</th>
    <th class="text-center">Total</th>
    <th class="text-center">Installment</th>
    <th class="text-center">Balance</th>
  </tr>
  @if (count($row)==0)
  <tr>
    <td class="text-center" colspan="8">
      Data Tidak di Temukan !!!
    </td>
  </tr>
  @else
    @php
      $no=0;
    @endphp
    @foreach ($row as $item)
    @php
      $date_exp = explode(' ',$item['DocDueDate']);
    @endphp
    <tr>
      <td class="text-center">
        <input type="checkbox" name="check[{{ $no }}]" class="checkbox" value="{{ $no }}">
        <input type="hidden" name="DocEntry[]" value="{{ $item['DocEntry'] }}">
        <input type="hidden" name="DocNum[]" value="{{ $item['DocNum'] }}">
        <input type="hidden" name="DocNumDO[]" value="{{ $item['DocNumDO'] }}">
        <input type="hidden" name="CardCode[]" value="{{ $item['CardCode'] }}">
        <input type="hidden" name="CardName[]" value="{{ $item['CardName'] }}">
        <input type="hidden" name="GroupCode[]" value="{{ $item['GroupCode'] }}">
        <input type="hidden" name="DocDueDate[]" value="{{ $item['DocDueDate'] }}">
        <input type="hidden" name="DocDate[]" value="{{ $item['DocDate'] }}">
        <input type="hidden" name="NumAtCard[]" value="{{ $item['NumAtCard'] }}">
        <input type="hidden" name="OcrCode[]" value="{{ $item['OcrCode'] }}">
        <input type="hidden" name="OcrCode2[]" value="{{ $item['OcrCode2'] }}">
        <input type="hidden" name="Alamat[]" value="{{ $item['Alamat'] }}">
        <input type="hidden" name="Netto[]" value="{{ $item['Netto'] }}">
        <input type="hidden" name="BalanceDue[]" value="{{ $item['BalanceDue'] }}">
        <input type="hidden" name="Lat[]" value="{{ $item['Lat'] }}">
        <input type="hidden" name="Long[]" value="{{ $item['Long'] }}">
      </td>
      <td>{{ $item['DocNum'] }}</td>
      <td>{{ $item['NumAtCard'] }}</td>
      <td class="text-center">{{ $date_exp[0] }}</td>
      <td class="text-center">{{ $item['Jadwal'] }}</td>
      <td class="text-right">{{ rupiahnon2($item['Netto']) }}</td>
      <td class="text-right">{{ rupiahnon2($item['BalanceDue']) }}</td>
      <td class="text-right">{{ rupiahnon2($item['Netto'] - $item['BalanceDue']) }}</td>
    </tr>
    @php
      $no++;
    @endphp   
    @endforeach
  @endif
</table>
<input type="hidden" name="kd" value="{{ $kd }}">
<button class="btn btn-info btn-sm mt-2" type="submit">Update</button>
{!! Form::close() !!}