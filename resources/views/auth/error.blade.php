@extends('auth.layout.main', ['title' => 'Error'])

@section('main')
    <div class="col-xl-5 col-lg-8">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="error mx-auto" data-text="{{ $data['status'] }}">{{ $data['status'] }}</div>
                            <p class="lead text-gray-800 mb-5">Terjadi Kesalahan</p>
                            <p class="text-gray-500 mb-0">{{ $data['message'] }}</p>
                            <a href="{{ url()->previous() }}">&larr; Kembali ke halaman sebelumnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
