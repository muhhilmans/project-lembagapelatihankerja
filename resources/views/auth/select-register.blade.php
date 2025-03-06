@extends('auth.layout.main', ['title' => 'Register'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('login') }}" class="btn btn-secondary mb-3 shadow"><i class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-6">
                <a href="{{ route('register-employe') }}" class="text-secondary">
                    <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0">
                        <div class="card-body">
                            <h1 class="display-1 card-title"><i class="fas fa-fw fa-building"></i></h1>
                            <p class="card-text"><strong>Client</strong></p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-6">
                <a href="{{ route('register-servant') }}" class="text-secondary">
                    <div class="card o-hidden border-0 shadow-lg">
                        <div class="card-body">
                            <h1 class="display-1 card-title"><i class="fas fa-fw fa-user-tie"></i></h1>
                            <p class="card-text"><strong>Mitra</strong></p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
