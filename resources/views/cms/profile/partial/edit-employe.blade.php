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

                <div class="form-group">
                    <label for="identity_card">Kartu Tanda Penduduk</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inpoIdentityCard">Upload</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="edit_identity_card" name="identity_card"
                                aria-describedby="inpoIdentityCard" accept="image/*">
                            <label class="custom-file-label" for="identity_card">Choose file</label>
                        </div>
                    </div>
                    <div class="mt-3" id="editIdentityCardPreviewContainer">
                        @if (!empty($data->employeDetails->identity_card))
                            <img id="editIdentityCardPreview"
                                src="{{ route('getImage', ['path' => 'identity_card', 'imageName' => $data->employeDetails->identity_card]) }}"
                                alt="KTP" class="img-fluid rounded" style="max-width: 100px;">
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <button class="btn btn-warning" type="submit">Simpan</button>
            </div>
        </form>
    </div>
@endsection


@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function updatePreview(inputId, previewContainerId) {
                const inputFile = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewContainerId);

                inputFile.addEventListener('change', function() {
                    const file = this.files[0];

                    const label = this.nextElementSibling;
                    label.textContent = file ? file.name : 'Choose file';

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            let previewImage = previewContainer.querySelector('img');
                            if (!previewImage) {
                                previewImage = document.createElement('img');
                                previewImage.className = 'img-fluid rounded';
                                previewImage.style.maxWidth = '100px';
                                previewContainer.appendChild(previewImage);
                            }
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewContainer.innerHTML = '';
                    }
                });
            }

            updatePreview('edit_identity_card', 'editIdentityCardPreviewContainer');
        });
    </script>
@endpush