@extends('auth.layout.main', ['title' => 'Register Pembantu'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('register-tc-servant') }}" class="btn btn-secondary mb-3 shadow"><i
                class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <a href="{{ route('home') }}"><img src="{{ asset('assets/img/logo.png') }}" alt="Logo"
                            class="img-fluid mb-2" style="max-height: 100px; width: auto;"></a>
                    <h3 class="card-title font-weight-bold">Registrasi Akun</h3>
                    <div class="card-body text-start">
                        <form action="{{ route('store-servant-register') }}" method="POST">
                            @csrf
                            <div class="mb-3 text-left">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="username">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="form-row p-1">
                                    <label for="profession_id">Profesi <span class="text-danger">*</span></label>
                                    <select class="custom-select" id="profession_id" name="profession_id" required>
                                        <option selected>Pilih Profesi...</option>
                                        @foreach ($professions as $profession)
                                            <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
