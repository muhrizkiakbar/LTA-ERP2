<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="feather-briefcase mr-2"></i>Sales A/R <div class="arrow-down"></div>
  </a>
  <div class="dropdown-menu" aria-labelledby="topnav-components">
    <a href="{{ route('sales') }}" class="dropdown-item">Sales Order</a>
    <a href="{{ route('delivery') }}" class="dropdown-item">Delivery</a>
    <a href="{{ route('invoice') }}" class="dropdown-item">A/R Invoice</a>
    <a href="{{ route('return') }}" class="dropdown-item">Return (Print Only)</a>
    <a href="{{ route('arcm') }}" class="dropdown-item">A/R Credit Note (Print Only)</a>
		<a href="{{ route('voucher_release') }}" class="dropdown-item">Voucher Release</a>
		<a href="{{ route('voucher_pairing') }}" class="dropdown-item">Voucher Pairing</a>
  </div>
</li>
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="feather-briefcase mr-2"></i>Syncronize Data <div class="arrow-down"></div>
  </a>
  <div class="dropdown-menu" aria-labelledby="topnav-components">
    <a href="{{ route('sfapng') }}" class="dropdown-item">SFA P&G</a>
    <a href="{{ route('sfamix') }}" class="dropdown-item">SFA MIX</a>
    <div class="dropdown">
      <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-tables" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        SFA Vdist <div class="arrow-down"></div>
      </a>
      <div class="dropdown-menu" aria-labelledby="topnav-tables">
        <a href="{{ route('vdist') }}" class="dropdown-item">Sync Vdist</a>
        <a href="{{ route('vdist.unserved') }}" class="dropdown-item">Unserved Vdist</a>
      </div>
    </div>
  </div>
</li>
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="feather-briefcase mr-2"></i>Report <div class="arrow-down"></div>
  </a>
  <div class="dropdown-menu" aria-labelledby="topnav-components">
    <div class="dropdown">
      <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-tables" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Logistik <div class="arrow-down"></div>
      </a>
      <div class="dropdown-menu" aria-labelledby="topnav-tables">
        <a href="{{ route('report.globalan') }}" class="dropdown-item">Packing List Globalan</a>
        <a href="{{ route('report.delivery_sales') }}" class="dropdown-item">Daily Delivery By Sales</a>
        <a href="{{ route('report.delivery_plat') }}" class="dropdown-item">Daily Delivery By Plat</a>
        <a href="{{ route('report.rekap_so') }}" class="dropdown-item">Rekap SO By Sales</a>
        <a href="{{ route('report.rekap_so_plat') }}" class="dropdown-item">Rekap SO By Plat</a>
        <a href="{{ route('report.rekap_do_plat') }}" class="dropdown-item">Rekap DO By Plat</a>
				<a href="{{ route('report.cek_penjualan_do') }}" class="dropdown-item">Cek Penjualan Delivery</a>
      </div>
    </div>
  </div>
</li>