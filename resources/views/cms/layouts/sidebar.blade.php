<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    @hasrole('superadmin|admin|owner')
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
            {{-- <div class="sidebar-brand-icon rotate-n-15">
        </div> --}}
            <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="" style="max-width: 30px;">
            <div class="sidebar-brand-text mx-3">SIPEMBANTU</div>
        </a>
    @endhasrole

    <!-- Sidebar - Brand -->
    @hasrole('majikan')
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard-employe') }}">
            <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="" style="max-width: 30px;">
            <div class="sidebar-brand-text mx-3">SIPEMBANTU</div>
        </a>
    @endhasrole

    <!-- Sidebar - Brand -->
    @hasrole('pembantu')
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard-servant') }}">
            <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="" style="max-width: 30px;">
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
    <hr class="sidebar-divider my-0">

    @if (auth()->user()->is_active == 1)
        @hasrole('superadmin|admin|pembantu')
            <!-- Nav Item - Cari Lowongan -->
            <li class="nav-item {{ Route::is('all-vacancy', 'show-vacancy') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('all-vacancy', 'show-vacancy') ? 'active' : '' }}"
                    href="{{ route('all-vacancy') }}">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Cari Lowongan</span></a>
            </li>

            <!-- Nav Item - Lamaran -->
            <li class="nav-item {{ Route::is('application-hire', 'application-indie') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('application-hire', 'application-indie') ? '' : 'collapsed' }}"
                    href="#" data-toggle="collapse" data-target="#collapseApplication"
                    aria-expanded="{{ Route::is('application-hire', 'application-indie') ? 'true' : 'false' }}"
                    aria-controls="collapseApplication">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Lamaran</span>
                </a>
                <div id="collapseApplication"
                    class="collapse {{ Route::is('application-hire', 'application-indie') ? 'show' : '' }}"
                    aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Route::is('application-hire') ? 'active' : '' }}"
                            href="{{ route('application-hire') }}">Hire</a>
                        <a class="collapse-item {{ Route::is('application-indie') ? 'active' : '' }}"
                            href="{{ route('application-indie') }}">Mandiri</a>
                    </div>
                </div>
            </li>
        @endhasrole

        @hasrole('superadmin|admin|majikan')
            <!-- Nav Item - Pembantu -->
            <li class="nav-item {{ Route::is('all-servant', 'show-servant') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('all-servant', 'show-servant') ? 'active' : '' }}"
                    href="{{ route('all-servant') }}">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Cari Pembantu</span></a>
            </li>

            <!-- Nav Item - Lowongan -->
            <li class="nav-item {{ Route::is('vacancies.*') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('vacancies.*') ? 'active' : '' }}" href="{{ route('vacancies.index') }}">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Lowongan</span></a>
            </li>

            <!-- Nav Item - Pelamar -->
            <li class="nav-item {{ Route::is('applicant-hire', 'applicant-indie') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('applicant-hire', 'applicant-indie') ? '' : 'collapsed' }}" href="#"
                    data-toggle="collapse" data-target="#collapseApplicant"
                    aria-expanded="{{ Route::is('applicant-hire', 'applicant-indie') ? 'true' : 'false' }}"
                    aria-controls="collapseApplicant">
                    <i class="fas fa-fw fa-user-tie"></i>
                    <span>Pelamar</span>
                </a>
                <div id="collapseApplicant"
                    class="collapse {{ Route::is('applicant-all', 'applicant-hire', 'applicant-indie') ? 'show' : '' }}"
                    aria-labelledby="headingApplicant" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Route::is('applicant-hire') ? 'active' : '' }}"
                            href="{{ route('applicant-hire') }}">Hire</a>
                        <a class="collapse-item {{ Route::is('applicant-indie') ? 'active' : '' }}"
                            href="{{ route('applicant-indie') }}">Mandiri</a>
                    </div>
                </div>
            </li>
        @endhasrole
    @endif

    <!-- Nav Item - Pengaduan -->
    <li class="nav-item {{ Route::is('complaints.*') ? 'active' : '' }}">
        <a class="nav-link {{ Route::is('complaints.*') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Pengaduan</span></a>
    </li>

    @hasrole('superadmin|admin|owner|majikan')
        <!-- Nav Item - Pekerja -->
        <li class="nav-item {{ Route::is('worker-all') ? 'active' : '' }}">
            <a class="nav-link {{ Route::is('worker-all') ? 'active' : '' }}" href="{{ route('worker-all') }}">
                <i class="fas fa-fw fa-id-badge"></i>
                <span>Pekerja</span></a>
        </li>
    @endhasrole

    @hasrole('majikan|pembantu')
        <li class="nav-item {{ Route::is('profile') ? 'active' : '' }}">
            <a class="nav-link {{ Route::is('profile') ? 'active' : '' }}"
                href="{{ route('profile', Auth::user()->id) }}">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Profil</span></a>
        </li>
    @endhasrole

    @if (auth()->user()->is_active == 1)
        @hasrole('superadmin|admin|owner')
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Master
            </div>

            <!-- Nav Item - Profesi -->
            <li class="nav-item {{ Route::is('professions.*') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('professions.*') ? 'active' : '' }}"
                    href="{{ route('professions.index') }}">
                    <i class="fas fa-fw fa-layer-group"></i>
                    <span>Profesi</span></a>
            </li>

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

        @hasrole('superadmin|admin')
            <!-- Nav Item - Blog -->
            <li class="nav-item {{ Route::is('blogs.*') ? 'active' : '' }}">
                <a class="nav-link {{ Route::is('blogs.*') ? 'active' : '' }}"
                    href="{{ route('blogs.index') }}">
                    <i class="fas fa-fw fa-newspaper"></i>
                    <span>Blog</span></a>
            </li>
        @endhasrole
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
