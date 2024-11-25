<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
      @foreach ($row as $item)
      <table class="table table-xs table-striped table-borderless table-hover">
        <tbody>
          <tr>
            <td>
              {{ $item['CardCode'] }} {!! $item['status'] !!} <br> 
              <h5>{{ $item['CardName'] }}</h5>
              {{ $item['Alamat'] }} <br>
              Total Invoice : {{ $item['TotalInv'] }} <br>
              Total Tagihan : {{ rupiah($item['TotalPrice']) }} <br>
            </td>
          </tr> 
        </tbody>
      </table>
      <a class="d-block pt-2 pb-2 text-dark" data-toggle="collapse" href="#detailInv{{ $item['CardCode2'] }}" aria-expanded="false">
        <strong>DETAIL INVOICE {{ $item['CardName'] }}</strong> <span class="float-right"><i class="mdi mdi-chevron-down accordion-arrow"></i></span>
      </a>
      <div id="detailInv{{ $item['CardCode2'] }}" class="collapse">
        <table class="table table-xs table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th class="text-center" width="2%">No</th>
              <th class="text-center">A/R Number</th>
              <th class="text-center">Due Date</th>
              <th class="text-center">Total</th>
              <th class="text-center">Payment</th>
              <th class="text-center">Balance</th>
            </tr>
          </thead>
          <tbody>
            @php
              $no=1;
            @endphp
            @foreach ($item['Lines'] as $lines)
            <tr {!! $lines['sts']=='1' ? 'class="table-dark"' : '' !!}>
              <td>{{ $no++ }}</td>
              <td>{{ $lines['DocNum'] }}</td>
              <td>{{ $lines['DocDueDate'] }}</td>
              <td class="text-right">{{ rupiahnon2($lines['Price']) }}</td>
              <td class="text-right">{{ $lines['Payment'] }}</td>
              <td class="text-right">{{ rupiahnon2($lines['Balance']) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{-- <a class="d-block pt-2 pb-2 text-dark" data-toggle="collapse" href="#detailCN{{ $item['CardCode2'] }}" aria-expanded="false">
        <strong>DETAIL CN {{ $item['CardName'] }}</strong> <span class="float-right"><i class="mdi mdi-chevron-down accordion-arrow"></i></span>
      </a>
      <div id="detailCN{{ $item['CardCode2'] }}" class="collapse">
        <table class="table table-xs table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th class="text-center" width="2%">No</th>
              <th class="text-center">CN Number</th>
              <th class="text-center">Card Code</th>
              <th class="text-center">Balance</th>
            </tr>
          </thead>
          <tbody>
            @php
              $nox=1;
            @endphp
            @foreach ($item['CN'] as $cn)
            <tr {!! $cn['sts']=='1' ? 'class="table-dark"' : '' !!}>
              <td>{{ $nox++ }}</td>
              <td>{{ $cn['DocNum'] }}</td>
              <td>{{ $cn['DocDueDate'] }}</td>
              <td class="text-right">{{ rupiahnon2($cn['Balance']) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div> --}}
      <a class="d-block pt-2 pb-2 text-dark" data-toggle="collapse" href="#detailTitip{{ $item['CardCode2'] }}" aria-expanded="false">
        <strong>TITIP NOTA {{ $item['CardName'] }}</strong> <span class="float-right"><i class="mdi mdi-chevron-down accordion-arrow"></i></span>
      </a>
      <div id="detailTitip{{ $item['CardCode2'] }}" class="collapse">
        <table class="table table-xs table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th class="text-center" width="2%">No</th>
              <th class="text-center">A/R Number</th>
              <th class="text-center">Due Date</th>
              <th class="text-center">Balance</th>
            </tr>
          </thead>
          <tbody>
            @php
              $no=1;
            @endphp
            @foreach ($item['titip'] as $titip)
            <tr {!! $titip['sts']=='1' ? 'class="table-dark"' : '' !!}>
              <td>{{ $no++ }}</td>
              <td>{{ $titip['DocNum'] }}</td>
              <td>{{ $titip['DocDueDate'] }}</td>
              <td class="text-right">{{ rupiahnon2($titip['Balance']) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <hr>
      @endforeach
      
    </div>
  </div>
</div>