<div class="card">
  <div class="card-body">
    <div id="print_page">
      <div class="table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th class="text-center">NO SO</th>
              <th class="text-center">TGL SO</th>
              <th class="text-center">NO DO</th>
              <th class="text-center">STATUS DO</th>
              <th class="text-center">NO AR</th>
              <th class="text-center">STATUS AR</th>
              <th class="text-center">CUSTOMER CODE</th>
              <th class="text-center">CUSTOMER NAME</th>
              <th class="text-center">SALES NAME</th>
              <th class="text-center">CUSTOMER REF NO</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($row as $item)
            <tr>
              <td>{{ $item['DocNumSO'] }}</td>
              <td>{{ $item['DocDateSO'] }}</td>
              <td>{{ $item['DocNumDO'] }}</td>
              <td>{{ $item['DocStatusDO'] }}</td>
              <td>{{ $item['DocNumAR'] }}</td>
              <td>{{ $item['DocStatusAR'] }}</td>
              <td>{{ $item['CardCode'] }}</td>
              <td>{{ $item['CardName'] }}</td>
              <td>{{ $item['SlpName'] }}</td>
              <td>{{ $item['NumAtCardSO'] }}</td>
            </tr>
            @endforeach
            
          </tbody>
          
        </table>
      </div>
    </div>
  </div>
</div>