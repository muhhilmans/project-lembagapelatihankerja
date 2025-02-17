<div class="row mb-4">
    <div class="col-lg-4 mb-3 mb-lg-0">
        <div class="card shadow mb-3 p-3">
            @if ($data->servantDetails->photo)
                <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servantDetails->photo]) }}"
                    class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;" alt="...">
            @else
                <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                    class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;" alt="...">
            @endif

            <div class="card-body">
                <table class="table table-responsive table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row">Nama</th>
                            <td>:</td>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Username</th>
                            <td>:</td>
                            <td>{{ $data->username }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>:</td>
                            <td>{{ $data->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td>:</td>
                            <td><span
                                    class="p-2 badge badge-{{ $data->servantDetails->working_status == 1 ? 'success' : 'danger' }}">{{ $data->servantDetails->working_status == 1 ? 'Bekerja' : 'Tidak Bekerja' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Inval</th>
                            <td>:</td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        value="{{ $data->servantDetails->is_inval }}" id="is_inval" {{ $data->servantDetails->is_inval == 1 ? 'checked' : '' }} data-toggle="modal"
                                        data-target="#invalModal-{{ $data->id }}">
                                    <label class="form-check-label" for="is_inval">
                                        {{ $data->servantDetails->is_inval == 1 ? 'Bersedia' : 'Tidak' }}
                                    </label>
                                    @include('cms.profile.partial.modal.inval', ['data' => $data])
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Pulang Pergi</th>
                            <td>:</td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        value="{{ $data->servantDetails->is_stay }}" id="is_stay" {{ $data->servantDetails->is_stay == 1 ? 'checked' : '' }} data-toggle="modal"
                                        data-target="#stayModal-{{ $data->id }}">
                                    <label class="form-check-label" for="is_stay">
                                        {{ $data->servantDetails->is_stay == 1 ? 'Bersedia' : 'Tidak' }}
                                    </label>
                                    @include('cms.profile.partial.modal.stay', ['data' => $data])
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h5 font-weight-bold">Keahlian</h1>
                    <a class="btn btn-primary mb-2 mb-lg-0" href="#" data-toggle="modal"
                        data-target="#createSkillModal-{{ $data->id }}">
                        <i class="fas fa-plus"></i>
                    </a>
                    @include('cms.profile.partial.skill.create', [
                        'data' => $data,
                    ])
                </div>
            </div>
            <div class="card-body">
                <ul>
                    @if ($data->servantSkills->count() > 0)
                        @foreach ($data->servantSkills as $dataSkill)
                            <li>
                                <a class="text-capitalize" href="#" data-toggle="modal"
                                    data-target="#updateSkillModal-{{ $dataSkill->id }}">
                                    {{ $dataSkill->skill }} ({{ $dataSkill->level }})
                                </a>
                                @include('cms.profile.partial.skill.edit', [
                                    'data' => $data,
                                ])
                            </li>
                        @endforeach
                    @else
                        <li>
                            Belum Ada Keahlian
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-3">
            <div class="card-header">
                <h1 class="h5 font-weight-bold">Detail Informasi</h1>
            </div>
            <div class="card-body">
                <table class="table table-responsive table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row">TTL</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->place_of_birth }},
                                {{ $data->servantDetails->date_of_birth }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Jenis Kelamin</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->gender == 'male')
                                    Laki-laki
                                @elseif ($data->servantDetails->gender == 'female')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Agama</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->religion }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->marital_status == 'married')
                                    Menikah
                                @elseif ($data->servantDetails->marital_status == 'single')
                                    Lajang
                                @elseif ($data->servantDetails->marital_status == 'divorced')
                                    Cerai
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if ($data->servantDetails->marital_status != 'single')
                            <tr>
                                <th scope="row">Anak</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->children == 0 ? 'Tidak Ada' : 'Ada' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th scope="row">Profesi</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->profession->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pendidikan Terakhir</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->last_education == 'not_filled')
                                    -
                                @else
                                    {{ $data->servantDetails->last_education }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Rekening</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->is_bank == 1)
                                    {{ $data->servantDetails->account_number }}
                                    ({{ $data->servantDetails->bank_name }})
                                @else
                                    Belum memiliki rekening
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">BPJS</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->is_bpjs == 1)
                                    {{ $data->servantDetails->number_bpjs }} ({{ $data->servantDetails->type_bpjs }})
                                @else
                                    Belum memiliki BPJS
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Nomor Telepon</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Nomor Darurat</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->emergency_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Alamat</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->address }} RT {{ $data->servantDetails->rt }} RW
                                {{ $data->servantDetails->rw }}, {{ $data->servantDetails->village }},
                                {{ $data->servantDetails->district }}, {{ $data->servantDetails->regency }},
                                {{ $data->servantDetails->province }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pengalaman Kerja</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->experience }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Deskripsi</th>
                            <td>:</td>
                            <td>{{ $data->servantDetails->description }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h5 font-weight-bold">Berkas Kelengkapan</h1>
                    @hasrole('superadmin|admin')
                        <a class="btn btn-warning mb-2 mb-lg-0" href="#" data-toggle="modal"
                            data-target="#changeModal-{{ $data->id }}">
                            @if ($data->is_active == 1)
                                <i class="fas fa-fw fa-toggle-off"></i>
                            @else
                                <i class="fas fa-fw fa-toggle-on"></i>
                            @endif
                        </a>
                        @include('cms.data.partials.servant.change-servant', [
                            'data' => $data,
                        ])
                    @endhasrole
                </div>
            </div>

            <div class="card-body">
                <table class="table table-responsive table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row">KTP</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->identity_card == null)
                                    -
                                @else
                                    <img src="{{ route('getImage', ['path' => 'identity_card', 'imageName' => $data->servantDetails->identity_card]) }}"
                                        alt="Kartu Tanda Penduduk" class="img-fluid rounded zoomable-image"
                                        style="max-height: 150px;">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Kartu Keluarga</th>
                            <td>:</td>
                            <td>
                                @if ($data->servantDetails->family_card == null)
                                    -
                                @else
                                    <img src="{{ route('getImage', ['path' => 'family_card', 'imageName' => $data->servantDetails->family_card]) }}"
                                        alt="Kartu Tanda Penduduk" class="img-fluid rounded zoomable-image"
                                        style="max-height: 150px;">
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
