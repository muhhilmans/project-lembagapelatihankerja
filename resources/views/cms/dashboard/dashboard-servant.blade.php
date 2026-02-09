@extends('cms.layouts.main', ['title' => 'Dashboard Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Pembantu</h1>
        {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Lamaran (Pending)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Lamaran (Proses)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['process'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Lamaran (Diterima)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['accepted'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Lamaran (Ditolak)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jadwal Interview</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Majikan</th>
                            <th>Nama Lowongan</th>
                            <th>Status</th>
                            <th>Tanggal Interview</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datasApp as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->vacancy_id ? $data->vacancy->user->name : $data->employe->name }}</td>
                                <td class="text-center">{{ $data->vacancy_id ? $data->vacancy->title : '-' }}</td>
                                <td class="text-center">{{ $data->vacancy_id ? 'Mandiri' : 'Hire' }}</td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($data->interview_date ? $data->interview_date : '')->format('d-M-Y') }}
                                </td>
                                <td class="text-center">{!! $data->notes_interview !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah browser mendukung geolocation
            if ('geolocation' in navigator) {
                // Request permission dan dapatkan lokasi
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Berhasil mendapatkan lokasi
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Kirim lokasi ke server
                        fetch('{{ route("update-location") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    latitude: latitude,
                                    longitude: longitude
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    console.log('Lokasi berhasil diperbarui:', latitude, longitude);
                                }
                            })
                            .catch(error => {
                                console.error('Gagal memperbarui lokasi:', error);
                            });
                    },
                    function(error) {
                        // Gagal mendapatkan lokasi
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                console.log('Pengguna menolak akses lokasi.');
                                break;
                            case error.POSITION_UNAVAILABLE:
                                console.log('Informasi lokasi tidak tersedia.');
                                break;
                            case error.TIMEOUT:
                                console.log('Request lokasi timeout.');
                                break;
                            default:
                                console.log('Terjadi kesalahan saat mengambil lokasi.');
                                break;
                        }
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000 // Cache lokasi selama 5 menit
                    }
                );
            } else {
                console.log('Browser tidak mendukung geolocation.');
            }
        });
    </script>
@endpush

