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
  <div class="page">
    <div style="margin-top: 0px; margin-bottom: -15px;">
      <p class="text-right" style="font-size: 10px; margin-right: 20px; margin-bottom:-10px;">{{ $print }}</p>
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
                <td  colspan="2">
                  {!! DNS1D::getBarcodeHTML('4445645656', 'PHARMA') !!}
                  <strong style="font-size: 14px;">FAKTUR PENJUALAN</strong>
                </td>
              </tr>
              <tr>
                <td width="80px" height="15px">No Nota</td>
                <td><strong style="font-size : 11px">{{ $series.' / '.$docnum }}</strong></td>
              </tr>
              <tr>
                <td height="15px">Customer</td>
                <td style="font-weight: 600;">{{ $cardCode }}</td>
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
                <td height="15px">User</td>
                <td><strong>{{ $USER_CODE }}, {{ $U_NAME }}</strong></td>
              </tr>
            </table>
          </td>
          <td class="text-center">
            <span style="font-size: 12px;">{{ $customer['cseg4'] }}</span>
          </td>
          <td valign="top" width="30%">
            <table>
              <tr>
                <td width="80px" height="15px">Izin PBF No</td>
                <td>{{ $pbf }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">CDOB</td>
                <td>{{ $cdob }}</td>
              </tr>
              <tr>
                <td height="15px">NPWP</td>
                <td style="font-weight: 600;">{{ $customer['Tax'] }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Kepada Yth</td>
                <td>{{ isset($customer['CardName']) ? $customer['CardName'] : '-' }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Tanggal</td>
                <td>{{ $DocDate }}</td>
              </tr>
              <tr>
                <td width="80px" height="15px">Jatuh Tempo</td>
                <td>{{ $DocDueDate }}</td>
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
      <table class="tablex" border="0" width="97%" {!! $nox==1 ? count($separate['lines']) <= 8 ? 'style="margin-bottom:0px; margin-top:-15px;"' : 'style="margin-bottom:30px;"' : 'style="margin-bottom:20px;"'!!}>
        <thead>
          <tr>
            <th style="border:1px solid;" class="text-center" width="20px" height="20px" rowspan="2">No</th>
            <th style="border:1px solid;" class="text-center" rowspan="2">Kode Barang</th>
            <th style="border:1px solid;" class="text-center" rowspan="2">Nama Barang</th>
            <th style="border:1px solid;" class="text-center" width="75px" colspan="2">Qty</th>
            <th style="border:1px solid;" class="text-center" rowspan="2">No. Batch</th>
            <th style="border:1px solid;" class="text-center" rowspan="2">ED</th>
            <th style="border:1px solid;" class="text-center" rowspan="2">Price</th>
            <th style="border:1px solid;" class="text-center" width="160px" rowspan="2">Disc %</th>
            <th style="border:1px solid;" class="text-center" width="60px" rowspan="2">Jumlah</th>
          </tr>
          <tr>
            <th style="border:1px solid;" class="text-center">Jml</th>
            <th style="border:1px solid;" class="text-center">Sat</th>
          </tr>
        </thead>
        <tbody>
          @php
            $no=1 + $separate['skip'];
          @endphp
          @foreach ($separate['lines'] as $item)
          <tr valign="top" bordercolor="#000000" height="25px">
            <td class="text-center" style="border-right: 1px solid; border-left: 1px solid;">{{ $no++  }}</td>
            <td style="border-right: 1px solid;">{{ $item['itemCode'] }}</td>
            <td style="border-right: 1px solid; font-size:10px;">{{ $item['itemDesc'] }}</td>
            <td style="border-right: 1px solid;" class="text-center">{{ round($item['qty'],0) }} </td>
            <td style="border-right: 1px solid;" class="text-center">{{ $item['unitMsr'] }}</td>
            <td style="border-right: 1px solid; font-size:10px;" class="text-center">{{ $item['Batch'] }}</td>
            <td style="border-right: 1px solid; font-size:10px;" class="text-center">{{ $item['ExpDate'] }}</td>
            <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['unitPrice']) }}</td>
            <td style="border-right: 1px solid; font-size:10px;" class="text-center">
              {{ $item['disc1'].' + '.$item['disc2'].' + '.$item['disc3'].' + '.$item['disc4'].' + '.$item['disc5'].' + '.$item['disc6'].' + '.$item['disc7'].' + '.$item['disc8'] }}
            </td>
            <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['docTotal']) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>   
      @endforeach
      <br>
      <table class="tablex" border="0" width="97%" {!! $nox > 1 ? 'style="margin-top:20px"' : ''!!}>
        <tr valign="top" style="border: none !important">
          <td colspan="5" rowspan="6">
            <table class="tablex">
              <tr>
                <td width="150px" style="padding-right: 30px">
                  Yang Menerima, <br><br><br><br><br>
                  <hr style="background-color: #000; height:1px; margin-bottom:2px;">
                  SIPA No.
                </td>
                <td width="150px" style="padding-right: 30px">
                  Kepala Gudang, <br><br><br><br><br>
                  <hr style="background-color: #000; height:1px; margin-bottom:2px;">
                  &nbsp;
                </td>
                <td width="170px">
                  Penanggung Jawab Farmasi, <br><br><br><br><br>
                  <hr style="background-color: #000; height:1px; margin-bottom:2px;">
                  SIPA No.
                </td>
              </tr>
            </table>
          </td>
          <td class="text-right">SUBTOTAL</td>
          <td class="text-right">{{ $DocTotal }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">Potongan</td>
          <td class="text-right">0</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">Pajak</td>
          <td class="text-right">{{ $VatSum }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">Diskon</td>
          <td class="text-right">0</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right">Bea Materai</td>
          <td class="text-right">{{ rupiahnon($uang_materai) }}</td>
        </tr>
        <tr valign="top" style="border: none !important;">
          <td class="text-right"><strong>Total</strong></td>
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
  </script>
</body>
</html>
