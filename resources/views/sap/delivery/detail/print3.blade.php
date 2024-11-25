<html>
<head>
	<link rel="stylesheet" href="{{ asset('assets/print/bootstrap.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/print/core.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/print/print.css') }}" media="print"/>
  <script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>
</head>
<body style="background: #fff;">
  <div class="page-footer">
    {{ auth()->user()->username_sap }} | {{ $docnum }} | {{ $plat }} | {{ date('d-m-Y H:i:s') }} | 
  </div>
  <div class="page-header-space"></div>
  <div class="page">
    <div style="margin-top: -20px; margin-bottom: -15px;">
      <p class="text-right" style="font-size: 10px; margin-right: 20px; margin-bottom:-10px;">{{ $print }}</p>
      <center>
        <img src="{{ asset('assets/images/logo-lta-clear.png') }}" style="width: 30px; margin-right:5px;">
        <strong style="font-size: 16px;">PT LAUT TIMUR ARDIPRIMA</strong>
      </center>
    </div>
    <div style="margin:20px;">
      <table border="0" width="98%">
        <tr>
          <td width="45%">
            <table>
              <tr>
                <td  colspan="2" height="15px">{{ $cabang }}, {{ date('d-M-Y',strtotime($DocDate)) }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">No Nota</td>
                <td><strong style="font-size : 11px">{{ $series.' / '.$docnum }}</strong></td>
              </tr>
              <tr>
                <td height="15px">TOP</td>
                <td><strong>{{ $top==0 ? 'CASH' : $top.' Hari' }}</strong></td>
              </tr>
              <tr>
                <td height="15px">No PO</td>
                <td><strong>{{ $numAtCard }}</strong></td>
              </tr>
              <tr>
                <td height="15px">Sales</td>
                <td><strong>{{ $sales }}</strong></td>
              </tr>
              <tr>
                <td height="15px">No SO</td>
                <td><strong>{{ $DocNumSO }}</strong> || <strong>{{ $segment }}</strong></td>
              </tr>
            </table>
          </td>
          <td class="text-center">
            <strong style="font-size: 14px;">NOTA</strong>
          </td>
          <td valign="top" width="45%">
            <table style="margin-left: 40px">
              <tr>
                <td width="100px" height="15px"></td>
                <td style="font-weight: 600;"></td>
              </tr>
              <tr>
                <td width="80px" height="15px">Kepada Yth</td>
                <td style="font-weight: 600;">{{ isset($customer['CardName']) ? $customer['CardName'] : '-' }}</td>
              </tr>
              <tr>
                <td height="15px">Kode Customer</td>
                <td style="font-weight: 600;">{{ $cardCode }}</td>
              </tr>
              {{-- <tr>
                <td height="15px">Nama NPWP</td>
                <td style="font-weight: 600;">
                  {{ $customer['tax_name'] }}
                </td>
              </tr> --}}
              <tr>
                <td height="15px">Nomor NPWP</td>
                <td style="font-weight: 600;">{{ $customer['Tax'] }}</td>
              </tr>
              <tr>
                <td height="15px" valign="top">Address</td>
                <td style="font-weight: 600;">
                  {{ $customer['Address'] }} <br></br>
                  <div class="text-right pr-2">{{ $plat }}</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
    <div style="margin:20px;">
      @php
          $no=1;
      @endphp
      @foreach ($separate as $separate)
      @php
          $nox = $no++;
      @endphp
      <table class="tablex" border="0" width="98%" {!! $nox==1 ? count($separate['lines']) <= 8 ? 'style="margin-bottom:0px; margin-top:-15px;"' : 'style="margin-bottom:30px;"' : 'style="margin-bottom:20px;"'!!}>
        <thead>
          <tr>
            <th style="border:1px solid;" class="text-center" width="20px" height="20px">No</th>
            <th style="border:1px solid;" class="text-center">Kode Barang</th>
            <th style="border:1px solid;" class="text-center">Nama Barang</th>
            <th style="border:1px solid;" class="text-center" width="75px">Qty</th>
            <th style="border:1px solid;" class="text-center" width="60px">Price</th>
            <th style="border:1px solid;" class="text-center" width="200px">Disc %</th>
            <th style="border:1px solid;" class="text-center" width="70px">Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @php
            $no=1 + $separate['skip'];
          @endphp
          @foreach ($separate['lines'] as $item)
          <tr valign="top" bordercolor="#000000" height="25px" style="font-weight: 100;">
            <td class="text-center" style="border-right: 1px solid; border-left: 1px solid;">{{ $no++  }}</td>
            <td style="border-right: 1px solid;">{{ $item['itemCode'] }}</td>
            <td style="border-right: 1px solid; font-size:11px;">{{ $item['itemDesc'] }}</td>
            <td style="border-right: 1px solid;" class="text-center">{{ round($item['qty'],0) }} {{ $item['unitMsr'] }}</td>
            <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['unitPrice']) }}</td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-center">
              {{ $item['disc1'].' + '.$item['disc2'].' + '.$item['disc3'].' + '.$item['disc4'].' + '.$item['disc5'].' + '.$item['disc6'].' + '.$item['disc7'].' + '.$item['disc8'] }}
            </td>
            <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['docTotal']) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>   
      @endforeach
      <table class="tablex" border="0" width="98%" {!! $nox > 1 ? 'style="margin-top:20px"' : ''!!}>
        <tr valign="top" style="border: none !important; font-weight: 100;">
          <td colspan="5" rowspan="6">
            Terbilang : <strong>{{ $voucher==0 ? penyebut($TotalSum) : penyebut($TotalSum2) }}</strong> <br><br>
            <table class="tablex">
              <tr>
                <td width="150px">
                  Yang Menerima, <br><br><br><br>
                  {{ $customer['CardName'] }}
                </td>
                <td class="text-center" width="150px">
                  Hormat Kami, <br><br><br><br>
                  Supervisor
                </td>
                <td style="border:1px solid #000" width="200px" valign="top">
                  Pembayaran Transfer<br>
                  {{ $branch_norek }} <br>
                  {{ $branch_norek_name }}<br><br><br>
                  Berita <strong>{{ $cardCode }}</strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="text-right">SUBTOTAL</td>
          <td class="text-right">{{ $DocTotal }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">PPN</td>
          <td class="text-right">{{ $VatSum }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">TOTAL INVOICE</td>
          <td class="text-right">{{ rupiahnon($TotalSum) }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">Voucher CN / Potongan</td>
          <td class="text-right">- {{ rupiahnon($voucher) }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">TOTAL BAYAR</td>
          <td class="text-right">{{ rupiahnon($TotalSum2) }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right"></td>
          <td class="text-right"></td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td colspan="7" class="text-left" style="font-size: 9px;">
            Putih - Tunai: Customer, Kredit: Admin / Merah - Admin / Kuning - Customer | Permintaan Faktur Pajak dapat melalui email : fakturpajak@laut-timur.com
          </td>
        </tr>
      </table>
    </div>
  </div>
  @if ($voucher != 0)
  <div class="page">
    <span style="font-size:12px; margin-top:20px; font-weight:600;">Voucher CN / Potongan</span>
    <table class="tablex" border="0" width="95%" >
      <thead>
        <tr>
          <th style="border:1px solid;" class="text-center" width="20px" height="20px">No</th>
          <th style="border:1px solid;" class="text-center">Document Number</th>
          <th style="border:1px solid;" class="text-center">Referensi Number</th>
          <th style="border:1px solid;" class="text-center">Description</th>
          <th style="border:1px solid;" class="text-center">Posting Date</th>
          <th style="border:1px solid;" class="text-center" width="100px">Balance</th>
        </tr>
      </thead>
      <tbody>
        @php
          $no=1;
        @endphp
        @foreach ($voucherList as $list)
        <tr valign="top" bordercolor="#000000">
          <td class="text-center" style="border-right: 1px solid; border-left: 1px solid; border-bottom: 1px solid;">{{ $no++ }}</td>
          <td style="border:1px solid;">{{ $list['DocNum'] }}</td>
          <td style="border:1px solid;">{{ $list['NumAtCard'] }}</td>
          <td style="border:1px solid;">{{ $list['Comments'] }}</td>
          <td class="text-center" style="border:1px solid;">{{ $list['DocDate'] }}</td>
          <td class="text-right" style="border:1px solid;">{{ rupiahnon($list['BalanceDue']) }}</td>
        </tr>  
        @endforeach
        <tr>
          <td class="text-center" style="border-right: 1px solid; border-left: 1px solid; border-bottom: 1px solid;" colspan="5">Total Voucher CN</td>
          <td class="text-right" style="border:1px solid;">{{ rupiahnon($voucher) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  @endif
  {{-- <div class="page-footer-space"></div> --}}

  {{-- <script type="text/javascript">
    $(document).ready(function(){
      window.print();
      window.onafterprint = window.close;
    });
  </script> --}}

  <script type="text/javascript">
    $(document).ready(function(){
      window.print();
      window.onafterprint = window.close;
    });

    class RepeatingTableHeaders extends Paged.Handler {
      constructor(chunker, polisher, caller) {
        super(chunker, polisher, caller);
      }

      afterPageLayout(pageElement, page, breakToken, chunker) {
        // Find all split table elements
        let tables = pageElement.querySelectorAll("table[data-split-from]");
        tables.forEach((table) => {
          // There is an edge case where the previous page table 
          // has zero height (isn't visible).
          // To avoid double header we will only add header if there is none.
          let tableHeader = table.querySelector("thead");
          if (tableHeader) {
            return;
          }

          // Get the reference UUID of the node
          let ref = table.dataset.ref;
          // Find the node in the original source
          let sourceTable = chunker.source.querySelector("[data-ref='" + ref + "']");

          // Find if there is a header
          let sourceHeader = sourceTable.querySelector("thead");
          if (sourceHeader) {
            console.log("Table header was cloned, because it is splitted.");
            // Clone the header element
            let clonedHeader = sourceHeader.cloneNode(true);
            // Insert the header at the start of the split table
            table.insertBefore(clonedHeader, table.firstChild);
          }
        });

        // Find all tables
        tables = pageElement.querySelectorAll("table");

        // special case which might not fit for everyone
        tables.forEach((table) => {
          // if the table has no rows in body, hide it.
          // This happens because my render engine creates empty tables.
          let sourceBody = table.querySelector("tbody > tr");
          if (!sourceBody) {
            console.log("Table was hidden, because it has no rows in tbody.");
            table.style.visibility = "hidden";
            table.style.position = "absolute";

            var lineSpacer = table.nextSibling;
            if (lineSpacer) {
              lineSpacer.style.visibility = "hidden";
              lineSpacer.style.position = "absolute";
            }
          }
        });
        // setting the page to show overflowing content
        let contents = pageElement.querySelectorAll(".pagedjs_page_content");
        contents.forEach((content) => {
          content.style.height = 'max-content';
        });
      }
    }

    Paged.registerHandlers(RepeatingTableHeaders);
  </script>
</body>
</html>
