@extends('auth.layout.main', ['title' => 'Verifikasi OTP'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('login') }}" class="btn btn-secondary mb-3 shadow"><i class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <div class="card-body text-left">
                        <form method="POST" action="{{ route('verify-otp') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <label for="otp">Kode OTP:</label>
                            <input type="text" name="otp" id="otp" class="form-control" required>
                            <button type="submit" class="btn btn-primary mt-3 shadow">Verifikasi Email</button>
                        </form>
                        <p class="card-text mt-3">
                            Jika Anda tidak menerima email, silakan klik
                            <a href="{{ route('verification.resend') }}"
                                onclick="event.preventDefault(); document.getElementById('resend-form').submit();"
                                class="text-primary font-weight-bold">disini</a>
                            untuk mengirim ulang verifikasi email.
                        </p>

                        <form id="resend-form" action="{{ route('verification.resend') }}" method="post" class="d-none">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
