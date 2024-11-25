<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Dashboard - SFA P&G | PT LAUT TIMUR ARDIPRIMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Dashboard SFA P&G" name="description" />
    <meta content="PT LAUT TIMUR ARDIPRIMA" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-lta-clear.png') }}">
    @include('layouts.backend.assets')
  </head>
  <body>
    <div id="layout-wrapper">
      <div class="main-content">
        @include('layouts.backend.navbar')
        @yield('content')            
        @include('layouts.backend.footer')
      </div>
    </div>
    @include('layouts.backend.script')
    @yield('customjs')
  </body>

</html>