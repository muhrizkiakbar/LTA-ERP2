<header id="page-topbar">
  <div class="navbar-header">
    <div class="navbar-brand-box d-flex align-items-left">
      <a href="#" class="logo">
        <img src="{{ asset('assets/images/lta-logo-text.png') }}" alt="">
      </a>

      <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light" data-toggle="collapse" data-target="#topnav-menu-content">
        <i class="fa fa-fw fa-bars"></i>
      </button>
    </div>

    <div class="d-flex align-items-center">
      <div class="dropdown d-inline-block ml-2">
        <button type="button" class="btn header-item waves-effect waves-light" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/user.png') }}" alt="Header Avatar">
          <span class="d-none d-sm-inline-block ml-1">{{ auth()->user()->name }}</span>
          <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          @if (auth()->user()->users_role_id==1)
          <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('users') }}">
            <span>Users Management</span>
          </a>
          @elseif(auth()->user()->users_role_id==2)
          <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('users') }}">
            <span>Users Management</span>
          </a>
          @endif
          <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('dashboard.history') }}">
            <span>History</span>
          </a>
          <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('dashboard.logout') }}">
            <span>Log Out</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</header>
<div class="topnav">
  <div class="container-fluid">
    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
      <div class="collapse navbar-collapse" id="topnav-menu-content">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
              <i class="feather-home mr-2"></i>Dashboard
            </a>
          </li>
          @if (auth()->user()->users_role_id==1)
            @include('layouts.backend.menu_root')
          @elseif (auth()->user()->users_role_id==2)
          @include('layouts.backend.menu_admin')
          @elseif (auth()->user()->users_role_id==3 || auth()->user()->users_role_id==9)
            @include('layouts.backend.menu_fakturis')
          @elseif (auth()->user()->users_role_id==5)
            @include('layouts.backend.menu_collector') 
          @elseif (auth()->user()->users_role_id==10)
            @include('layouts.backend.menu_png') 
          @endif
        </ul>
      </div>
    </nav>
  </div>
</div>   