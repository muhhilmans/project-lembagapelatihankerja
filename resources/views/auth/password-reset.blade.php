@extends('auth.layout.main', ['title' => 'Reset Password'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('login') }}" class="btn btn-secondary mb-3 shadow"><i class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <form method="POST" action="{{ route('reset.password.post') }}">
                        @csrf
                        <div class="card-body text-left">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password Baru</label>
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
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-user" id="password_confirmation"
                                        name="password_confirmation" placeholder="Password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white border-left-0"
                                            onclick="togglePasswordConfirmation()" style="cursor: pointer;">
                                            <i class="fas fa-eye" id="togglePasswordConfirmationIcon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3 shadow">Ubah Password</button>
                        </div>
                    </form>
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

        function togglePasswordConfirmation() {
            const passwordInput = document.getElementById('password_confirmation');
            const icon = document.getElementById('togglePasswordConfirmationIcon');
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
@endpush
