@extends('cms.layouts.main', ['title' => 'Profile'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <div class="d-flex flex-row">
            <h1 class="h3 mb-4 text-gray-800">Profile Saya</h1>
            <sup class="ml-2"><span class="badge bg-{{ $data->is_active ? 'success' : 'warning' }} text-{{ $data->is_active ? 'light' : 'dark' }} p-2"><strong>{{ $data->is_active ? 'Terverifikasi' : 'Belum Diverifikasi' }}</strong></span></sup>
        </div>
        <a href="{{ route('profile.edit', $data->id) }}" class="btn btn-warning mr-1"><i
                class="fas fa-fw fa-user-edit"></i></a>
    </div>

    @if ($data->roles->first()->name === 'pembantu')
        @include('cms.profile.partial.servant')
    @else
        @include('cms.profile.partial.employe')
    @endif
@endsection
