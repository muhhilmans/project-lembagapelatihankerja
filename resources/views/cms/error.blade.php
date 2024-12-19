@extends('cms.layouts.main', ['title' => 'Error'])

@section('content')
    <!-- Error Text -->
    <div class="text-center">
        <div class="error mx-auto" data-text="{{ $data['status'] }}">{{ $data['status'] }}</div>
        <p class="lead text-gray-800 mb-5">Terjadi Kesalahan</p>
        <p class="text-gray-500 mb-0">{{ $data['message'] }}</p>
        <a href="{{ url()->previous() }}">&larr; Kembali ke halaman sebelumnya</a>
    </div>
@endsection