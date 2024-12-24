@extends('cms.layouts.main', ['title' => 'Edit Majikan'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Edit Majikan</h1>
        <a href="{{ route('profile', $data->id) }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
    </div>

    <div class="card shadow mb">
        <form method="POST" action="{{ route('profile-employe.update', $data->id) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $data->name) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="{{ old('username', $data->username) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $data->email) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="phone" name="phone" maxlength="13"
                            value="{{ old('phone', $data->employeDetails->phone ?? '') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="address" id="address" rows="3" required>{{ old('address', $data->employeDetails->address ?? '') }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <button class="btn btn-warning" type="submit">Simpan</button>
            </div>
        </form>
    </div>
@endsection
