@extends('auth.layout.main', ['title' => 'Verifikasi Email'])

@section('main')
    <div class="col-xl-5 col-lg-6">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Email Belum Diverifikasi!</h1>
                            </div>
                            <p class="card-text">Silakan cek email Anda untuk melihat kode verifikasi.</p>
                            <form method="POST" action="{{ route('verify-otp') }}">
                                @csrf
                                <div class="card-body text-left">
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <label for="otp">Kode OTP:</label>
                                    <input type="text" name="otp" id="otp" class="form-control" required>
                                    <button type="submit" class="btn btn-primary mt-3 shadow">Verifikasi Email</button>
                                </div>
                            </form>
                            <p class="card-text">
                                Jika Anda tidak menerima email, silahkan klik tombol dibawah untuk mengirim ulank verifikasi email.
                            </p>
                            <form action="{{ route('verification.resend') }}" method="post" class="d-flex justify-content-center">
                                @csrf
                                <button type="submit" class="btn btn-primary">Kirim Ulang Verifikasi Email</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
