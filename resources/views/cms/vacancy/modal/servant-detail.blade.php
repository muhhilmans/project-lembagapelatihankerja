<!-- Modal -->
<div class="modal fade" id="servantDetailsModal-{{ $d->id }}" tabindex="-1" role="dialog"
    aria-labelledby="servantDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="servantDetailsModalLabel">Detail Informasi Pembantu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">

                <div class="card shadow mb-3">
                    <div class="card-header">
                        <h5 class="font-weight-bold">Detail Informasi</h5>
                    </div>
                    <div class="card-body">
                        @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                            <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;"
                                alt="Pembantu {{ $d->servant->name }}">
                        @else
                            <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                                class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;"
                                alt="Pembantu {{ $d->servant->name }}">
                        @endif
                        <table class="table table-borderless table-responsive">
                            <tbody>
                                <tr>
                                    <th scope="row">Nama</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Username</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->username }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>:</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $d->servant->servantDetails->working_status ? 'success' : 'danger' }}">
                                            {{ $d->servant->servantDetails->working_status ? 'Bekerja' : 'Tidak Bekerja' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">TTL</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->place_of_birth }},
                                        {{ \Carbon\Carbon::parse($d->servant->servantDetails->date_of_birth)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Jenis Kelamin</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->gender == 'male' ? 'Laki-laki' : ($d->servant->servantDetails->gender == 'female' ? 'Perempuan' : '-') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Agama</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->religion }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->marital_status == 'married' ? 'Menikah' : ($d->servant->servantDetails->marital_status == 'single' ? 'Lajang' : ($d->servant->servantDetails->marital_status == 'divorced' ? 'Cerai' : '-')) }}
                                    </td>
                                </tr>
                                @if ($d->servant->servantDetails->marital_status != 'single')
                                    <tr>
                                        <th scope="row">Anak</th>
                                        <td>:</td>
                                        <td>{{ $d->servant->servantDetails->children == 0 ? 'Tidak Ada' : 'Ada' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th scope="row">Profesi</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->profession->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pendidikan Terakhir</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->last_education == 'not_filled' ? '-' : $d->servant->servantDetails->last_education }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Inval</th>
                                    <td>:</td>
                                    <td>
                                        <span
                                            class="p-2 badge badge-{{ $d->servant->servantDetails->is_inval == 1 ? 'success' : 'danger' }}">{{ $d->servant->servantDetails->is_inval == 1 ? 'Bersedia' : 'Tidak' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Pulang Pergi</th>
                                    <td>:</td>
                                    <td>
                                        <span
                                            class="p-2 badge badge-{{ $d->servant->servantDetails->is_stay == 1 ? 'success' : 'danger' }}">{{ $d->servant->servantDetails->is_stay == 1 ? 'Bersedia' : 'Tidak' }}</span>
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <th scope="row">Nomor Telepon</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Nomor Darurat</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->emergency_number ?? '-' }}</td>
                                </tr> --}}
                                <tr>
                                    <th scope="row">Asal Kota</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->regency }},
                                        {{ $d->servant->servantDetails->province }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pengalaman Kerja</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->experience }} Tahun</td>
                                </tr>
                                <tr>
                                    <th scope="row">Deskripsi</th>
                                    <td>:</td>
                                    <td>{{ $d->servant->servantDetails->description }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="font-weight-bold">Keahlian</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            @if ($d->servant->servantSkills->count() > 0)
                                @foreach ($d->servant->servantSkills as $dSkill)
                                    <li>{{ $dSkill->skill }} ({{ $dSkill->level }})</li>
                                @endforeach
                            @else
                                <li>Belum Ada Keahlian</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
