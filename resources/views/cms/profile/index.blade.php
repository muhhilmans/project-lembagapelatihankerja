@extends('cms.layouts.main', ['title' => 'Profile'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Profile Saya</h1>
        <a href="{{ route('profile.edit', $data->id) }}" class="btn btn-warning mr-1"><i
                class="fas fa-fw fa-user-edit"></i></a>
    </div>
    @if (session('error'))
        <h5 class="text-danger">{{ session('error') }}</h5>
    @endif

    @if (session('success'))
        <h5 class="text-success">{{ session('success') }}</h5>
    @endif

    @if ($data->roles->first()->name === 'pembantu')
        @include('cms.profile.partial.servant')
    @else
        @include('cms.profile.partial.employe')
    @endif
@endsection
