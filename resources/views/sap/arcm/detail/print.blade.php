<html>
<head>
	<link rel="stylesheet" href="{{ asset('assets/print/bootstrap.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/print/core.css') }}" />
	<style>
	  *{	
	  	font-family: sans-serif;
			color: #000;
      --pagedjs-margin-top : 1px !important;
      --pagedjs-margin-bottom : 1px !important;
	  }

    .pagedjs_pagebox > .pagedjs_area {
      padding: 0px !important;
    }

	  table{
	    font-size: 10px;
	  }

    .tablex {
      font-size: 11px;
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
  <input type="hidden" id="docnum" value="{{ $header->DocNum }}">
  <div class="page-footer">
    {{ auth()->user()->username_sap }} | {{ $header->DocNum }} | {{ date('d-m-Y H:i:s') }} | 
  </div>
  <div class="page-header-space"></div>
  <div class="page">
    <div style="margin-top: -20px; margin-bottom: -15px;">
      <p class="text-right" style="font-size: 10px; margin-right: 20px; margin-bottom:-10px;">
        {{ $header->Printed=='Y' ? 'Copy - Printed' : '' }}
      </p>
      <center>
        <img src="{{ asset('assets/images/logo-lta-clear.png') }}" style="width: 35px; margin-right:5px;">
        <strong style="font-size: 14px;">PT LAUT TIMUR ARDIPRIMA</strong>
      </center>
    </div>
    <div style="margin:20px;">
      <table border="0" width="97%">
        <tr>
          <td width="30%">
            <table>
              <tr>
                <td width="20px" height="15px">To</td>
                <td><strong style="font-size : 11px">{{ $header->CardCode }}</strong></td>
              </tr>
              <tr>
                <td height="15px"></td>
                <td><strong>{{ $header->CardName }}</strong></td>
              </tr>
              <tr>
                <td height="15px"></td>
                <td><br>{{ $header->Address }}</td>
              </tr>
            </table>
          </td>
          <td class="text-center">
            <strong style="font-size: 14px;" style="top:20px;">A/R Credit Note</strong>
          </td>
          <td valign="top" width="35%">
            <table>
              <tr>
                <td width="80px" height="15px">Date</td>
                <td>{{ date('d-F-Y',strtotime($header->DocDate)) }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Due Date</td>
                <td>{{ date('d-F-Y',strtotime($header->DocDueDate)) }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Document No</td>
                <td>{{ $header->DocNum }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Reff No</td>
                <td>{{ $header->NumAtCard }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Sales Name</td>
                <td>{{ $header->SlpName }} || {{ $header->OcrCode2=='P&G' ? $header->PLAT_PNG : $header->PLAT_MIX }}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
    <div style="margin:20px;">
      <table class="tablex" border="0" width="97%">
        <thead>
          <tr>
            <th style="border:1px solid;" class="text-center" width="20px" height="20px">No</th>
            <th style="border:1px solid;" class="text-center">Description</th>
            <th style="border:1px solid;" class="text-center" width="60px">Quantity</th>
            <th style="border:1px solid;" class="text-center" width="60px">Price</th>
            <th style="border:1px solid;" class="text-center" width="60px">Total</th>
          </tr>
        </thead>
        <tbody>
          @php
            $no=1;
          @endphp
          @foreach ($lines as $item)
          <tr valign="top" bordercolor="#000000" height="25px">
            <td class="text-center" style="border-right: 1px solid; border-left: 1px solid;">{{ $no++  }}</td>
            <td style="border-right: 1px solid; font-size:10px;">{{ $item['Dscription'] }}</td>
            <td style="border-right: 1px solid;" class="text-center">{{ $item['Quantity'].' '.$item['UnitMsr'] }}</td>
            <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['Price']) }}</td>
            <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['lineTotal']) }}</td>
          </tr>
          @endforeach
          <table class="tablex" border="0" width="97%">
            <tr valign="top" style="border: none !important">
              <td colspan="5" rowspan="6" style="border-top: 1px solid;">
                @php
                  $totalSum = $header->Bruto + $header->VatSum;
                @endphp
                Terbilang : <strong>{{ penyebut($totalSum) }}</strong> <br><br>
                <table class="tablex">
                  <tr>
                    <td width="150px">
                      Menyetujui, <br>
                      Finance Head <br><br><br><br><br>
                      (........................)
                    </td>
                    <td width="150px">
                      Mengetahui, <br>
                      PIhak Toko <br><br><br><br><br>
                      (........................)
                    </td>
                  </tr>
                </table>
              </td>
              <td style="border-top: 1px solid;" class="text-right">SUBTOTAL</td>
              <td style="border-top: 1px solid;" class="text-right">{{ rupiahnon($header->Bruto) }}</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right">PPN</td>
              <td class="text-right">{{ rupiahnon($header->VatSum) }}</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right"><strong>TOTAL</strong></td>
              <td class="text-right"><strong>{{ rupiahnon($totalSum) }}</strong></td>
            </tr>
          </table>
        </tbody>
      </table>   
    </div>
  </div>
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      updatedPrinted();
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

    function updatedPrinted(){
      var docnum = '{{ $header->DocNum }}';
      var url = '{{ route('arcm.update_printed') }}';
      var token = '{{ csrf_token() }}';
      $.ajax({
        url: url,
        type: "POST",
        data : { docnum:docnum, _token:token }
      });
    }
  </script>
</body>
</html>
