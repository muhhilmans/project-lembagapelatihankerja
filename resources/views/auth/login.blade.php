@extends('auth.layout.main', ['title' => 'Login'])

@section('main')
    <div class="col-xl-5 col-lg-6">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <a href="{{ route('home') }}"><img src="{{ asset('assets/img/logo.png') }}" alt="Logo"
                                        class="img-fluid mb-2" style="max-height: 100px; width: auto;"></a>
                                <h1 class="h4 text-gray-900 mb-4">Silahkan Login!</h1>
                            </div>
                            <form class="user" action="{{ route('authenticate') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="account"
                                        name="account" aria-describedby="emailHelp"
                                        placeholder="Enter Username/Email Address..." required>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-user" id="password"
                                            name="password" placeholder="Password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-white border-left-0" onclick="togglePassword()"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('forgot.password') }}">Lupa Password?</a>
                            </div>
                            <div class="text-center small">
                                Belum punya akun? <a href="{{ route('select-register') }}">Daftar Sekarang!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('custom-script')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
@endpush
