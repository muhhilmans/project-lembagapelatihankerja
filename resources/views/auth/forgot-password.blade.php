@extends('auth.layout.main', ['title' => 'Lupa Password'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('login') }}" class="btn btn-secondary mb-3 shadow"><i class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <form method="POST" action="{{ route('forgot.password.post') }}">
                        @csrf
                        <div class="card-body text-left">
                            <label for="email">Email Address:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            <button type="submit" class="btn btn-primary mt-3 shadow">Kirim Link Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
