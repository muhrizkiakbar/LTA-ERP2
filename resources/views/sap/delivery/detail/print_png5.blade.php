<html>
<head>
	<link rel="stylesheet" href="{{ asset('assets/print/bootstrap.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/print/core.css') }}" />
  <script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>
	<style>
	  *{	
	  	font-family: Sans-Serif;
			color: #000;
      --pagedjs-margin-top : 1px !important;
      --pagedjs-margin-bottom : 1px !important;
	  }

    .pagedjs_pagebox > .pagedjs_area {
      padding: 0px !important;
    }

	  table{
	    font-size: 11px;
	  }

    .tablex {
      font-size: 12px;
      border-collapse: collapse; 
    }

    .tablex td {
      padding: 2px;
    }

      /* .tablex tr {
        border-bottom: none !important;
      } */


	  .spacingtd {
	    padding: 0px 0px 0px 10px !important;
	    margin: 0 !important;
	  }

	  .spacing tr td {
	    padding: 0 10px 0 0 !important;
	    margin: 0 !important;
	  }

		.page {
      width: 22cm;
      max-height: 13.5cm;
			background: white;
      /* border: 1px solid #000; */
		}

    footer {
      font-size: 9px;
      color: #f00;
      text-align: center;
      padding-top: 30px;
    }

		@page {
      size: 22cm 13.5cm;
      margin: 5mm;
      padding-bottom: 1mm;
      /* border: 1px solid #000; */
      
      /* .tablex tr td, .tablex tr th {
        page-break-inside: avoid;
      } */

      
    }

		@media print {
      html, body {
        display: block; 
        font-family: Sans-Serif;
      }

			.page {
				margin-bottom: 20px;
				border: initial;
				border-radius: initial;
				width: initial;
				min-height: initial;
				box-shadow: initial;
				background: initial;
				page-break-after: always;
			}

      footer {
        position: fixed;
        page-break-after: always;
        bottom: 0;
      }

      /* .tablex table { page-break-after:auto;}
      .tablex tr { page-break-inside:avoid; page-break-after:auto }
      .tablex td { page-break-inside:avoid; page-break-after:auto }
      .tablex thead { display:table-header-group; margin-top: 10px; } */

      /* table.tablex tr td, table.tablex tr th {
        page-break-inside: avoid;
      } */
		}

    

    /* .tablex table { page-break-after:auto;}
    .tablex tr { page-break-inside:avoid; page-break-after:auto }
    .tablex td { page-break-inside:avoid; page-break-after:auto }
    .tablex thead { display:table-header-group; margin-top: 10px; } */

		@media print and (color) {
      body {margin: 0;}
      /* table { page-break-after:auto;}
      tr    { page-break-inside:avoid; page-break-after:auto }
      td    { page-break-inside:avoid; page-break-after:auto }
      thead { display:table-header-group; margin-top: 10px; }
      tfoot { display:table-footer-group } */
		  * {
		      -webkit-print-color-adjust: exact;
		      print-color-adjust: exact;
		  }
		}

    /* Styles go here */

    .page-header, .page-header-space {
      height: 25px;
    }

    .page-footer, .page-footer-space {
      height: 20px;
      margin-left: 20px;
    }

    .page-footer {
        text-align: center;
        display: table-footer-group;
    }

    .page-footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      font-size: 10px;
      
    }

    .page-footer:after {
      content: counter(page) ' of ' counter(pages);
    }

    /* .page {
      page-break-after: always;
    } */
 	</style>
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
        <img src="{{ asset('assets/images/logo-lta-clear.png') }}" style="width: 35px; margin-right:5px;">
        <strong style="font-size: 14px;">PT LAUT TIMUR ARDIPRIMA</strong>
      </center>
    </div>
    <div style="margin:20px;">
      <table border="0" width="97%">
        <tr>
          <td width="40%">
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
          <td valign="top" width="40%">
            <table>
              <tr>
                <td width="80px" height="15px"></td>
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
    <div style="margin: -15px 20px 20px 20px;">
      @php
          $no=1;
      @endphp
      @foreach ($separate as $separate)
      @php
          $nox = $no++;
      @endphp
      @if ($nox==1) {{-- Cek Jika Baris Ke 1 --}}
        @if (count($separate['lines']) <= 5) {{-- Cek Jika Jumlah Item Kurang Dari 11 --}}
          @php
            $style = 'style="margin-top:-15px;"';
            $style2 = 'style="margin-top:0px;"';
          @endphp
        @else
          @php
            $style = 'style="margin-bottom:40px; margin-top:-15px;"';
            $style2 = 'style="margin-top:30px;"';
          @endphp
        @endif
      @else
        @if (count($separate['lines']) <= 5) {{-- Cek Jika Jumlah Item Kurang Dari 5 Untuk Perulangan Selanjutnya --}}
          @php
            $style = 'style="margin-bottom:15px; margin-top:15px;"';
            $style2 = 'style="margin-top:10px"';
          @endphp
        @else {{-- Cek Jika Jumlah Item Melebihi 5 Untuk Perulangan Selanjutnya --}}
          @php
            $style = 'style="margin-bottom:35px; margin-top:15px;"';
            $style2 = 'style="margin-top:20px"';
          @endphp
        @endif
      @endif

      <table class="tablex" border="0" width="97%" {!! $style !!}>
        <thead>
          <tr>
            <th rowspan="2" style="border:1px solid;" class="text-center" width="20px" height="20px">No</th>
            <th rowspan="2" style="border:1px solid;" class="text-center" width="200px">Nama Barang</th>
            <th rowspan="2" style="border:1px solid;" class="text-center" >Barcode</th>
            <th rowspan="2" style="border:1px solid;" class="text-center" width="55px">Qty</th>
            <th rowspan="2" style="border:1px solid;" class="text-center" width="50px">Harga Pcs</th>
            <th colspan="3" style="border:1px solid;" class="text-center" width="160px">Diskon Pcs</th>
            <th rowspan="2" style="border:1px solid;" class="text-center" width="75px">Harga Nett<br>Pcs + PPN</th>
            <th rowspan="2" style="border:1px solid;" class="text-center" width="85px">Jumlah <br> (Diskon + PPN)</th>
          </tr>
          <tr>
            <th style="border:1px solid;" class="text-center">Reguler</th>
            <th style="border:1px solid;" class="text-center">Lotsell</th>
            <th style="border:1px solid;" class="text-center">Volume</th>
          </tr>
        </thead>
        <tbody>
          @php
            $no=1 + $separate['skip'];
          @endphp
          @foreach ($separate['lines'] as $item)
          <tr valign="top" bordercolor="#000000" height="25px">
            <td class="text-center" style="border-right: 1px solid; border-left: 1px solid;">{{ $no++  }}</td>
            <td style="border-right: 1px solid; font-size:11px;" width="170px">{{ $item['itemDesc'] }}</td>
            <td style="border-right: 1px solid; font-size:11px;">{{ $item['barcode'] }}</td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-center">{{ round($item['qty'],0) }} {{ $item['unitMsr'] }}</td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-right">{{ rupiahnon($item['unitPrice']) }}</td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-right">
              {{ rupiahnon($item['disc_reg'] )}}
            </td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-right">
              {{ rupiahnon($item['disc_lot']) }}
            </td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-right">
              {{ rupiahnon($item['disc_vol']) }}
            </td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-right">{{ rupiahnon($item['harga_satuan_nett']) }}</td>
            <td style="border-right: 1px solid; font-size:11px;" class="text-right">{{ rupiahnon($item['docTotal2']) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>   
      @endforeach
      <table class="tablex" border="0" width="97%" {!! $style2 !!}>
        <tr valign="top" style="border: none !important">
          <td colspan="5" rowspan="7">
            Terbilang : <strong>{{ $voucher==0 ? penyebut($TotalSum2) : penyebut($TotalSum2) }}</strong> <br><br>
            <table class="tablex">
              <tr>
                <td width="130px">
                  Yang Menerima, <br><br><br><br>
                  {{ $customer['CardName'] }}
                </td>
                <td class="text-center" width="120px">
                  Hormat Kami, <br><br><br><br>
                  Supervisor
                </td>
                <td style="border:1px solid #000" width="170px" valign="top">
                  Pembayaran Transfer<br>
                  {{ $branch_norek }} <br>
                  {{ $branch_norek_name }}<br><br><br>
                  Berita <strong>{{ $cardCode }}</strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="text-right"><strong>TOTAL INVOICE + PPN</strong></td>
          <td class="text-right"><strong>{{ rupiahnon($TotalSum) }}</strong></td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">SUBTOTAL NON PPN</td>
          <td class="text-right">{{ $DocTotal }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">PPN</td>
          <td class="text-right">{{ $VatSum }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">BEA MATERAI</td>
          <td class="text-right">{{ rupiahnon($uang_materai) }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">Voucher CN / Potongan</td>
          <td class="text-right">- {{ rupiahnon($voucher) }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right"><strong>TOTAL BAYAR</strong></td>
          <td class="text-right"><strong>{{ rupiahnon($TotalSum2) }}</strong></td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right"></td>
          <td class="text-right"></strong></td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td colspan="7" class="text-left" style="font-size: 10px;">
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
