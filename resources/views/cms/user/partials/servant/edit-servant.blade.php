@extends('cms.layouts.main', ['title' => 'Edit Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Edit Pembantu</h1>
        <a href="{{ route('users-servant.show', $user->id) }}" class="btn btn-secondary"><i
                class="fas fa-fw fa-arrow-left"></i></a>
    </div>

    <div class="card shadow">
        <form action="{{ route('users-servant.update', $user->id) }}" method="POST" enctype="multipart/form-data"
            class="needs-validation">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ old('username', $user->username) }}">
                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="place_of_birth">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="place_of_birth" name="place_of_birth"
                                value="{{ old('place_of_birth', $user->servantDetails->place_of_birth) }}">
                            @error('place_of_birth')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                value="{{ old('date_of_birth', $user->servantDetails->date_of_birth) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="custom-select" id="gender" name="gender" required>
                                <option selected disabled>Pilih Jenis Kelamin...</option>
                                <option value="male" {{ $user->servantDetails->gender == 'male' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="female" {{ $user->servantDetails->gender == 'female' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="religion">Agama <span class="text-danger">*</span></label>
                            <select class="custom-select" id="religion" name="religion" required>
                                <option selected disabled>Pilih Agama...</option>
                                <option value="Islam" {{ $user->servantDetails->religion == 'Islam' ? 'selected' : '' }}>
                                    Islam</option>
                                <option value="Kristen"
                                    {{ $user->servantDetails->religion == 'Kristen' ? 'selected' : '' }}>
                                    Kristen</option>
                                <option value="Katolik"
                                    {{ $user->servantDetails->religion == 'Katolik' ? 'selected' : '' }}>Katolik
                                </option>
                                <option value="Hindu" {{ $user->servantDetails->religion == 'Hindu' ? 'selected' : '' }}>
                                    Hindu</option>
                                <option value="Buddha" {{ $user->servantDetails->religion == 'Buddha' ? 'selected' : '' }}>
                                    Buddha
                                </option>
                                <option value="Konghucu"
                                    {{ $user->servantDetails->religion == 'Konghucu' ? 'selected' : '' }}>
                                    Konghucu</option>
                                <option value="Lainnya"
                                    {{ $user->servantDetails->religion == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                </option>
                            </select>
                            @error('religion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="marital_status">Status Pernikahan <span class="text-danger">*</span></label>
                            <select class="custom-select" id="marital_status" name="marital_status" required>
                                <option selected disabled>Pilih Status Pernikahan...</option>
                                <option value="married"
                                    {{ $user->servantDetails->marital_status == 'married' ? 'selected' : '' }}>
                                    Menikah</option>
                                <option value="single"
                                    {{ $user->servantDetails->marital_status == 'single' ? 'selected' : '' }}>
                                    Lajang</option>
                                <option value="divorced"
                                    {{ $user->servantDetails->marital_status == 'divorced' ? 'selected' : '' }}>
                                    Cerai</option>
                            </select>
                            @error('marital_status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="children">Memiliki Anak <span class="text-danger">*</span></label>
                            <select name="children" id="children" class="custom-select" required>
                                <option selected disabled>Pilih Kepemilikan Anak...</option>
                                <option value="1" {{ $user->servantDetails->children != 0 ? 'selected' : '' }}>Ada</option>
                                <option value="0" {{ $user->servantDetails->children == 0 ? 'selected' : '' }}>Tidak</option>
                            </select>
                            @error('children')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="profession_id">Profesi <span class="text-danger">*</span></label>
                            <select class="custom-select" id="profession_id" name="profession_id" required>
                                <option selected disabled>Pilih Profesi...</option>
                                @foreach ($professions as $profession)
                                    <option value="{{ $profession->id }}"
                                        {{ $user->servantDetails->profession_id == $profession->id ? 'selected' : '' }}>
                                        {{ $profession->name }}</option>
                                @endforeach
                            </select>
                            @error('profession_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_education">Pendidikan Terakhir <span class="text-danger">*</span></label>
                            <select class="custom-select" id="last_education" name="last_education" required>
                                <option selected disabled>Pilih Pendidikan Terakhir...</option>
                                <option value="SD"
                                    {{ $user->servantDetails->last_education == 'SD' ? 'selected' : '' }}>
                                    SD/Sederajat</option>
                                <option value="SMP"
                                    {{ $user->servantDetails->last_education == 'SMP' ? 'selected' : '' }}>
                                    SMP/Sederajat</option>
                                <option value="SMA"
                                    {{ $user->servantDetails->last_education == 'SMA' ? 'selected' : '' }}>
                                    SMA/Sederajat</option>
                                <option value="D1"
                                    {{ $user->servantDetails->last_education == 'D1' ? 'selected' : '' }}>
                                    D1</option>
                                <option value="D2"
                                    {{ $user->servantDetails->last_education == 'D2' ? 'selected' : '' }}>
                                    D2</option>
                                <option value="D3"
                                    {{ $user->servantDetails->last_education == 'D3' ? 'selected' : '' }}>
                                    D3</option>
                                <option value="S1"
                                    {{ $user->servantDetails->last_education == 'S1' ? 'selected' : '' }}>
                                    S1</option>
                                <option value="S2"
                                    {{ $user->servantDetails->last_education == 'S2' ? 'selected' : '' }}>
                                    S2</option>
                                <option value="S3"
                                    {{ $user->servantDetails->last_education == 'S3' ? 'selected' : '' }}>
                                    S3</option>
                            </select>
                            @error('last_education')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $user->servantDetails->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="emergency_number">Nomor Darurat <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="emergency_number" name="emergency_number"
                                value="{{ old('emergency_number', $user->servantDetails->emergency_number) }}" required>
                            @error('emergency_number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_bank"
                                        name="is_bank"
                                        {{ old('is_bank', $user->servantDetails->is_bank) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_bank">
                                        Memiliki Rekening
                                    </label>
                                </div>

                                <div id="bank-details"
                                    class="{{ old('is_bank', $user->servantDetails->is_bank) == 1 ? '' : 'd-none' }}">
                                    <div class="form-group">
                                        <label for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="bank_name" name="bank_name"
                                            value="{{ old('bank_name', $user->servantDetails->bank_name) }}"
                                            placeholder="Isi dengan nama bank...">
                                    </div>

                                    <div class="form-group">
                                        <label for="account_number">Nomor Rekening <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="account_number"
                                            name="account_number"
                                            value="{{ old('account_number', $user->servantDetails->account_number) }}"
                                            placeholder="Isi dengan nomor rekening...">
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_bpjs"
                                        name="is_bpjs"
                                        {{ old('is_bpjs', $user->servantDetails->is_bpjs) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_bpjs">
                                        Memiliki BPJS
                                    </label>
                                </div>

                                <div id="bpjs-details"
                                    class="{{ old('is_bpjs', $user->servantDetails->is_bpjs) == 1 ? '' : 'd-none' }}">
                                    <div class="form-group">
                                        <label for="type_bpjs">Jenis BPJS <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="type_bpjs" name="type_bpjs"
                                            value="{{ old('type_bpjs', $user->servantDetails->type_bpjs) }}"
                                            placeholder="Isi dengan jenis BPJS...">
                                    </div>

                                    <div class="form-group">
                                        <label for="number_bpjs">Nomor BPJS <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="number_bpjs" name="number_bpjs"
                                            value="{{ old('number_bpjs', $user->servantDetails->number_bpjs) }}"
                                            placeholder="Isi dengan nomor BPJS...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="address">Alamat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $user->servantDetails->address) }}"
                                placeholder="Isi dengan nama jalan/kampung sekarang..." required>
                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-lg-6">
                                <label for="rt">RT <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="rt" name="rt"
                                    value="{{ old('rt', $user->servantDetails->rt) }}">
                                @error('rt')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="rw">RW <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="rw" name="rw"
                                    value="{{ old('rw', $user->servantDetails->rw) }}">
                                @error('rw')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-lg-6">
                                <label for="province">Provinsi <span class="text-danger">*</span></label>
                                <select name="province" id="province" class="custom-select" required>
                                    <option selected disabled>Pilih Provinsi...</option>
                                </select>
                                @error('province')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="regency">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <select name="regency" id="regency" class="custom-select" required>
                                    <option selected disabled>Pilih Kota/Kabupaten...</option>
                                </select>
                                @error('regency')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-lg-6">
                                <label for="district">Kecamatan <span class="text-danger">*</span></label>
                                <select name="district" id="district" class="custom-select" required>
                                    <option selected disabled>Pilih Kecamatan...</option>
                                </select>
                                @error('district')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="village">Desa/Kelurahan <span class="text-danger">*</span></label>
                                <select name="village" id="village" class="custom-select" required>
                                    <option selected disabled>Pilih Desa/Kelurahan...</option>
                                </select>
                                @error('village')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="working_status">Status Kerja <span class="text-danger">*</span></label>
                            <select class="custom-select" id="working_status" name="working_status" required>
                                <option selected disabled>Pilih Status Kerja...</option>
                                <option value="1"
                                    {{ $user->servantDetails->working_status == '1' ? 'selected' : '' }}>
                                    Bekerja</option>
                                <option value="0"
                                    {{ $user->servantDetails->working_status == '0' ? 'selected' : '' }}>
                                    Tidak Bekerja</option>
                            </select>
                            @error('working_status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="experience">Pengalaman Kerja <span class="text-danger">*(Isi dengan tahun pengalaman kerja)</span></label>
                            <input type="number" class="form-control" id="experience" name="experience"
                                value="{{ old('experience', $user->servantDetails->experience) }}">
                            @error('experience')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="5"
                                placeholder="Isi dengan deskripsi singkat pribadi..">{{ old('description', $user->servantDetails->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="photo">Foto <span class="text-danger">*(Kosongkan jika tidak ingin diubah)</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inpoPhoto">Upload</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="photo" name="photo"
                                        aria-describedby="inpoPhoto" accept="image/*" {{ $user->servantDetails->photo ? null : 'required' }}>
                                    <label class="custom-file-label" for="photo">Choose file</label>
                                </div>
                            </div>
                            <div class="mt-3" id="photoPreviewContainer">
                                @if (!empty($user->servantDetails->photo))
                                    <img id="photoPreview"
                                        src="{{ route('getImage', ['path' => 'photo', 'imageName' => $user->servantDetails->photo]) }}"
                                        alt="Foto" class="img-fluid rounded" style="max-width: 100px;">
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="identity_card">Kartu Tanda Penduduk <span class="text-danger">*(Kosongkan jika tidak ingin diubah)</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inpoIdentityCard">Upload</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="identity_card"
                                        name="identity_card" aria-describedby="inpoIdentityCard" accept="image/*" {{ $user->servantDetails->identity_card ? null : 'required' }}>
                                    <label class="custom-file-label" for="identity_card">Choose file</label>
                                </div>
                            </div>
                            <div class="mt-3" id="identityCardPreviewContainer">
                                @if (!empty($user->servantDetails->identity_card))
                                    <img id="identityCardPreview"
                                        src="{{ route('getImage', ['path' => 'identity_card', 'imageName' => $user->servantDetails->identity_card]) }}"
                                        alt="KTP" class="img-fluid rounded" style="max-width: 100px;">
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="family_card">Kartu Keluarga</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inpoFamilyCard">Upload</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="family_card" name="family_card"
                                        aria-describedby="inpoFamilyCard" accept="image/*">
                                    <label class="custom-file-label" for="family_card">Choose file</label>
                                </div>
                            </div>
                            <div class="mt-3" id="familyCardPreviewContainer">
                                @if (!empty($user->servantDetails->family_card))
                                    <img id="familyCardPreview"
                                        src="{{ route('getImage', ['path' => 'family_card', 'imageName' => $user->servantDetails->family_card]) }}"
                                        alt="KK" class="img-fluid rounded" style="max-width: 100px;">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('users-servant.show', $user->id) }}" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@push('custom-script')
    <script>
        const dataVillage = "{{ optional($user->servantDetails)->village }}";
        const dataDistrict = "{{ optional($user->servantDetails)->district }}";
        const dataRegency = "{{ optional($user->servantDetails)->regency }}";
        const dataProvince = "{{ optional($user->servantDetails)->province }}";

        document.addEventListener('DOMContentLoaded', () => {
            const isBankCheckbox = document.getElementById('is_bank');
            const bankDetails = document.getElementById('bank-details');
            isBankCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    bankDetails.classList.remove('d-none');
                } else {
                    bankDetails.classList.add('d-none');
                }
            });
            
            const isBpjsCheckbox = document.getElementById('is_bpjs');
            const bpjsDetails = document.getElementById('bpjs-details');
            isBpjsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    bpjsDetails.classList.remove('d-none');
                } else {
                    bpjsDetails.classList.add('d-none');
                }
            });

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

            // Inisialisasi pratinjau gambar untuk setiap input
            updatePreview('photo', 'photoPreviewContainer');
            updatePreview('identity_card', 'identityCardPreviewContainer');
            updatePreview('family_card', 'familyCardPreviewContainer');

            const provinceSelect = document.getElementById('province');
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            // Fetch provinces
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(data => {
                    data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        option.dataset.id = province.id;
                        if (province.name == dataProvince) {
                            option.selected = true;
                        }
                        provinceSelect.appendChild(option);
                    });
                    // Trigger change event to load regencies
                    handleProvinceChange({
                        target: {
                            value: dataProvince
                        }
                    });
                })
                .catch(error => console.error('Error fetching provinces:', error));

            // Event listeners
            provinceSelect.addEventListener('change', handleProvinceChange);
            regencySelect.addEventListener('change', handleRegencyChange);
            districtSelect.addEventListener('change', handleDistrictChange);

            // Functions
            function handleProvinceChange(event) {
                const selectedProvince = provinceSelect.selectedOptions[0];
                const provinceId = selectedProvince.dataset.id;
                regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

                if (!provinceId) return;

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(regency => {
                            const option = document.createElement('option');
                            option.value = regency.name;
                            option.textContent = regency.name;
                            option.dataset.id = regency.id;
                            if (regency.name == dataRegency) {
                                option.selected = true;
                            }
                            regencySelect.appendChild(option);
                        });
                        // Trigger change event to load districts
                        handleRegencyChange({
                            target: {
                                value: dataRegency
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching regencies:', error));
            }

            function handleRegencyChange(event) {
                const selectedRegency = regencySelect.selectedOptions[0];
                const regencyId = selectedRegency.dataset.id;
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

                if (!regencyId) return;

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.name;
                            option.textContent = district.name;
                            option.dataset.id = district.id;
                            if (district.name == dataDistrict) {
                                option.selected = true;
                            }
                            districtSelect.appendChild(option);
                        });
                        // Trigger change event to load villages
                        handleDistrictChange({
                            target: {
                                value: dataDistrict
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching districts:', error));
            }

            function handleDistrictChange(event) {
                const selectedDistrict = districtSelect.selectedOptions[0];
                const districtId = selectedDistrict.dataset.id;
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

                if (!districtId) return;

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(village => {
                            const option = document.createElement('option');
                            option.value = village.name;
                            option.textContent = village.name;
                            option.dataset.id = village.id;
                            if (village.name == dataVillage) {
                                option.selected = true;
                            }
                            villageSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching villages:', error));
            }
        });
    </script>
@endpush
