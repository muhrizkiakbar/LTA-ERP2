<div class="card-body">
  <div id="print_page">
    <table class="table table-sm table-bordered table-striped" width="100%">
      <tr>
        <th class="text-center">No</th>
        <th class="text-center">Nama Toko</th>
        <th class="text-center">Check In</th>
        <th class="text-center">Check Out</th>
        <th class="text-center">Durasi</th>
        <th class="text-center">Call Obj</th>
        <th class="text-center">File</th>
      </tr>
      @php
        $no=1;
      @endphp
      @foreach ($row as $item)
      <tr>
        <td class="text-center">{{ $no++ }}</td>
        <td>{{ $item['CardName'] }}</td>
        <td class="text-center">{{ $item['time_in'] }}</td>
        <td class="text-center">{{ $item['time_out'] }}</td>
        <td class="text-right">{{ $item['durasi'] }}</td>
        <td class="text-right">{{ $item['call'] }}</td>
        <td class="text-center">
          <img src="{{ $item['file'] }}" height="70px" alt="">
        </td>
      </tr>   
      @endforeach
    </table>
  </div>
</div>