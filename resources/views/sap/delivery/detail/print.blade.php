<html>
<head>
	<!-- Fonts  -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="{{ asset('assets/print/bootstrap.css') }}" />
	<!-- Base Styling  -->
	<link rel="stylesheet" href="{{ asset('assets/print/core.css') }}" />
	<style>
	  *{	
	  	font-family: sans-serif;
			color: #000;
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
			min-height: 14cm;
			padding: 0.75cm;
			margin: 0.1cm auto;
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
      size: 22cm 14cm;
      margin: 2mm;
      padding: 0.75cm;
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
		}

		@media print and (color) {
      table { page-break-after:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
      td    { page-break-inside:avoid; page-break-after:auto }
      thead { display:table-header-group; margin-top: 10px; }
      tfoot { display:table-footer-group }
		  * {
		      -webkit-print-color-adjust: exact;
		      print-color-adjust: exact;
		  }
		}
 	</style>
</head>
<body style="background: #fff;" onload="window.print()">
	<div class="page">
		<div class="row">
			<div class="col-md-12">
				<div style="margin-top: -10px; margin-bottom: -15px;">
          <p class="text-right" style="font-size: 10px; margin-right: 20px;">{{ $print }}</p>
					<center>
            <img src="{{ asset('assets/images/logo-lta-clear.png') }}" style="width: 35px; margin-right:5px;">
            <strong style="font-size: 14px;">PT LAUT TIMUR ARDIPRIMA</strong>
					</center>
				</div>
			</div>
		</div>
    <div class="row" style="margin-top:20px;">
      <div class="col-md-12">
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
                  <td><strong>{{ $row['NumAtCard'] }}</strong></td>
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
                  <td width="80px" height="15px">Kepada Yth</td>
                  <td style="font-weight: 600;">{{ isset($customer['CardName']) ? $customer['CardName'] : '-' }}</td>
                </tr>
                <tr>
                  <td height="15px">Kode Customer</td>
                  <td style="font-weight: 600;">{{ $row['CardCode'] }}</td>
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
                  <td height="15px">Address</td>
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
    </div>
    <div class="row" style="margin-top:20px;">
      <div class="col-md-12">
        <table class="tablex" border="0" width="97%">
          <thead>
            <tr>
              <th style="border:1px solid;" class="text-center" width="20px" height="20px">No</th>
              <th style="border:1px solid;" class="text-center">Kode Barang</th>
              <th style="border:1px solid;" class="text-center">Nama Barang</th>
              <th style="border:1px solid;" class="text-center" width="50px">Qty</th>
              <th style="border:1px solid;" class="text-center" width="60px">Price</th>
              <th style="border:1px solid;" class="text-center" width="250px">Disc %</th>
              <th style="border:1px solid;" class="text-center" width="60px">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            @php
              $no=1;
            @endphp
            @foreach ($lines as $item)
            <tr valign="top" bordercolor="#000000">
              <td height="20px" class="text-center" style="border-right: 1px solid; border-left: 1px solid;">{{ $no++ }}</td>
              <td style="border-right: 1px solid;">{{ $item['itemCode'] }}</td>
              <td style="border-right: 1px solid;">{{ $item['itemDesc'] }}</td>
              <td style="border-right: 1px solid;" class="text-center">{{ round($item['qty'],0) }} {{ $item['unitMsr'] }}</td>
              <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['unitPrice']) }}</td>
              <td style="border-right: 1px solid;" class="text-center">
                {{ $item['disc1'].' + '.$item['disc2'].' + '.$item['disc3'].' + '.$item['disc4'].' + '.$item['disc5'].' + '.$item['disc6'].' + '.$item['disc7'].' + '.$item['disc8'] }}
              </td>
              <td style="border-right: 1px solid;" class="text-right">{{ rupiahnon($item['docTotal']) }}</td>
            </tr>
            @endforeach
            <tr valign="top" style="border: none !important;">
              <td colspan="5" rowspan="6" style="border-top: 1px solid;">
                Terbilang : <strong>{{ penyebut($TotalSum) }}</strong> <br><br>
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
                    <td style="border:1px solid #000" width="220px" valign="top">
                      Pembayaran Transfer<br>
                      {{ $branch_norek }} <br>
                      {{ $branch_norek_name }}<br><br><br>
                      Berita <strong>{{ $row['CardCode'] }}</strong>
                    </td>
                  </tr>
                </table>
              </td>
              <td style="border-top: 1px solid;" class="text-right">SUBTOTAL</td>
              <td style="border-top: 1px solid;" class="text-right">{{ $DocTotal }}</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right">PPN</td>
              <td class="text-right">{{ $VatSum }}</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right">POTONGAN</td>
              <td class="text-right">0</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right">DISKON RUPIAH</td>
              <td class="text-right">0</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right">DISKON 0%</td>
              <td class="text-right">0</td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td class="text-right"><strong>TOTAL</strong></td>
              <td class="text-right"><strong>{{ rupiahnon($TotalSum) }}</strong></td>
            </tr>
            <tr valign="top" style="border: none !important;">
              <td colspan="7" class="text-left" style="font-size: 10px;">
                Putih - Tunai: Customer, Kredit: Admin / Merah - Admin / Kuning - Customer | Permintaan Faktur Pajak dapat melalui email : fakturpajak@laut-timur.com
              </td>
            </tr>
            {{-- <tr valign="top" style="border: none !important;">
              
            </tr> --}}
          </tbody>
        </table>
      </div>
    </div>
	</div>
  {{-- <script type="text/javascript">
    $(document).ready(function(){
      window.print();
      window.onafterprint = window.close;
    });
  </script> --}}
</body>
</html>
