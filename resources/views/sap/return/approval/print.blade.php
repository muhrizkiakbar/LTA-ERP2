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
      font-size: 11px;
      border-collapse: collapse; 
    }

    .tablex th {
      font-size: 10px;
      font-weight: 600;
    }

    .tablex td {
      vertical-align: top;
      font-size: 11px;
      padding: 5px 3px;
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
      width: 21cm;
      max-height: 29.7cm;
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
      size: 21cm 29.7cm;
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
    {{ auth()->user()->username_sap }} | {{ $header->number }} | {{ $header->SlpName }} | {{ date('d-m-Y H:i:s') }} |
  </div>
  <div class="page-header-space"></div>
  <div class="page">
    <div style="margin-top: -20px; margin-bottom: 15px;">
      <p class="text-right" style="font-size: 10px; margin-right: 20px; margin-bottom:-10px;">{{ $header->print==1 ? 'Copied' : '' }}</p>
      <center>
        <strong style="font-size: 16px;">SURAT JALAN PENARIKAN BARANG RETUR</strong> <br>
        <strong style="font-size: 14px;">Nomor : {{ $header->number }}</strong>
      </center>
    </div>
    <div style="margin:10px;">
      <table border="0" width="97%">
        <tr>
          <td width="80%">
            <table style="font-size : 12px;">
              <tr>
                <td width="100px" height="20px" class="text-right">Nama Toko :</td>
                <td style="padding-left: 10px;"><strong>{{ $header->CardName }}</strong></td>
              </tr>
              <tr>
                <td height="20px" class="text-right">Kode Toko :</td>
                <td style="padding-left: 10px;"><strong>{{ $header->CardCode }}</strong></td>
              </tr>
              <tr>
                <td height="20px" class="text-right">Tgl Pengajuan :</td>
                <td style="padding-left: 10px;"><strong>{{ $header->date }}</strong></td>
              </tr>
              <tr>
                <td height="20px" class="text-right">Tgl Penarikan :</td>
                <td style="padding-left: 10px;"><strong></strong></td>
              </tr>
              <tr>
                <td height="20px" class="text-right">PIC Toko :</td>
                <td style="padding-left: 10px;"><strong>{{ $header->NamaPicToko }} {{ isset($header->NumberPicToko) ? '('.$header->NumberPicToko.')' : '' }}</strong></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
    <div style="margin: 10px;">
      <table class="tablex" border="1" width="100%">
        <thead>
          <tr>
            <th class="text-center" width="100px">KODE BARANG</th>
            <th class="text-center">NAMA BARANG</th>
            <th class="text-center" width="75px">JUMLAH PENARIKAN YG DISETUJUI (PCS)</th>
            <th class="text-center" width="75px">*JUMLAH PENARIKAN YG DILAKUKAN (PCS)</th>
            <th class="text-center" width="75px">**Estimasi Rp. SESUAI PENARIKAN YG DISETUJUI</th>
            <th class="text-center" width="130px">CATATAN</th>
            <th class="text-center" width="30px">***DOK FOTO(1/0)</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($lines as $item)
          <tr>
            <td>{{ $item->ItemCode }}</td>
            <td>{{ $item->ItemName }}</td>
            <td class="text-center">{{ $item->Quantity }}</td>
            <td class="text-center"></td>
            <td class="text-right">{{ rupiahnon2($item->Quantity * $item->UnitPrice) }}</td>
            <td>{{ isset($item->ExpDate) ? $item->note .' ('.$item->ExpDate.')' : $item->note }}</td>
            <td></td>
          </tr> 
          @endforeach
        </tbody>
      </table>
      <p class="text-left" style="font-size: 10px; margin-right: 20px; margin-bottom:-10px;">
        * Jumlah 'Penarikan yg Dilakukan' maksimal sebanyak 'Jumlah Penarikan yg Disetujui'. <br>
        ** Rupiah tidak mengikat dan dapat berubah sewaktu-waktu. <br>
        *** Dokumentasi foto wajib dilakukan saat melakukan penarikan barang retur.
      </p>
      <table style="margin-top: 10px; font-size: 10px;" border="0" width=100% >
        <tr>
          <td class="text-center" width="25%">
            Menerbitkan, <br><br><br><br><br><br>
            ( {{ auth()->user()->name }} )
          </td>
          <td class="text-center" width="20%">
            Melakukan Serah Terima, <br><br><br><br><br><br>
            ( &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; )
          </td>
          <td class="text-center" width="35%">
            <br><br><br><br><br><br><br>
            ( {{ $header->CardName }} )
          </td>
          <td class="text-center" width="20%">
            Memvalidasi, <br><br><br><br><br><br>
            ( &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; )
          </td>
        </tr>
        <tr>
          <td class="text-left" style="padding-left: 20px;">
            Tgl TTD : {{ $header->date }}
          </td>
          <td class="text-left" style="padding-left: 20px;">
            Tgl TTD :
          </td>
          <td class="text-left" style="padding-left: 20px;">
            Tgl TTD :
          </td>
          <td class="text-left" style="padding-left: 20px;">
            Tgl TTD :
          </td>
        </tr>
      </table>
    </div>
  </div>
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
