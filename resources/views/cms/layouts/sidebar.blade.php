<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    @hasrole('superadmin|admin|owner')
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        {{-- <div class="sidebar-brand-icon rotate-n-15">
        </div> --}}
        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="img-fluid" alt="" style="max-width: 30px;">
        <div class="sidebar-brand-text mx-3">SIPEMBANTU</div>
    </a>
    @endhasrole

    <!-- Sidebar - Brand -->
    @hasrole('majikan')
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard-employe') }}">
        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="img-fluid" alt="" style="max-width: 30px;">
        <div class="sidebar-brand-text mx-3">SIPEMBANTU</div>
    </a>
    @endhasrole

    <!-- Sidebar - Brand -->
    @hasrole('pembantu')
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard-servant') }}">
        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="img-fluid" alt="" style="max-width: 30px;">
        <div class="sidebar-brand-text mx-3">SIPEMBANTU</div>
    </a>
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    @hasrole('superadmin|admin|owner')
        <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>
    @endhasrole

    @hasrole('majikan')
        <li class="nav-item {{ Route::is('dashboard-employe') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard-employe') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>
    @endhasrole

    @hasrole('pembantu')
        <li class="nav-item {{ Route::is('dashboard-servant') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard-servant') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div>

    <!-- Nav Item - Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-folder"></i>
            <span>Menu</span></a>
    </li>

    @hasrole('superadmin|admin|owner')
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Master
        </div>

        <!-- Nav Item - Kategori -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-layer-group"></i>
                <span>Kategori</span></a>
        </li>

        @hasrole('superadmin|owner|admin')
            <!-- Nav Item - Users Collapse Menu -->
            <li class="nav-item {{ Route::is('users-admin.*', 'users-employe.*', 'users-servant.*') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('users-admin.*', 'users-employe.*', 'users-servant.*') ? '' : 'collapsed' }}"
                    href="#" data-toggle="collapse" data-target="#collapseUsers"
                    aria-expanded="{{ Route::is('users-admin.*', 'users-employe.*', 'users-servant.*') ? 'true' : 'false' }}"
                    aria-controls="collapseUsers">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Users</span>
                </a>
                <div id="collapseUsers"
                    class="collapse {{ Route::is('users-admin.*', 'users-employe.*', 'users-servant.*') ? 'show' : '' }}"
                    aria-labelledby="headingUsers" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Route::is('users-employe.*') ? 'active' : '' }}"
                            href="{{ route('users-employe.index') }}">Majikan</a>
                        <a class="collapse-item {{ Route::is('users-servant.*') ? 'active' : '' }}"
                            href="{{ route('users-servant.index') }}">Pembantu</a>
                        @hasrole('superadmin|owner')
                            <a class="collapse-item {{ Route::is('users-admin.*') ? 'active' : '' }}"
                                href="{{ route('users-admin.index') }}">Admin</a>
                        @endhasrole
                    </div>
                </div>
            </li>
        @endhasrole
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
