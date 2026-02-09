@extends('cms.layouts.main', ['title' => 'Tracking Lokasi Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tracking Lokasi Pembantu</h1>
    </div>

    <!-- Map Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-map-marked-alt"></i> Peta Lokasi Pembantu
            </h6>
        </div>
        <div class="card-body">
            <div id="map" style="height: 500px; width: 100%; border-radius: 8px;"></div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Pembantu dengan Lokasi
            </h6>
            <span class="badge badge-primary">{{ $servants->count() }} Pembantu</span>
        </div>
        <div class="card-body">
            @if ($servants->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data lokasi pembantu</p>
                    <small class="text-muted">Data lokasi akan muncul ketika pembantu login melalui aplikasi mobile</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Profesi</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($servants as $index => $servant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($servant->servantDetails->photo)
                                                <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $servant->servantDetails->photo]) }}"
                                                    class="rounded-circle mr-2" width="40" height="40"
                                                    alt="{{ $servant->name }}">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2"
                                                    style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($servant->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $servant->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $servant->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $servant->servantDetails->profession->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $servant->servantDetails->regency ?? '-' }},
                                        {{ $servant->servantDetails->province ?? '-' }}
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-map-pin text-danger"></i>
                                            {{ number_format($servant->servantDetails->latitude, 6) }},
                                            {{ number_format($servant->servantDetails->longitude, 6) }}
                                        </small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info focus-marker"
                                            data-lat="{{ $servant->servantDetails->latitude }}"
                                            data-lng="{{ $servant->servantDetails->longitude }}"
                                            data-name="{{ $servant->name }}">
                                            <i class="fas fa-crosshairs"></i> Lihat di Peta
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('custom-style')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-popup-content {
            min-width: 200px;
        }

        .popup-content {
            padding: 5px;
        }

        .popup-content h6 {
            margin-bottom: 8px;
            color: #4e73df;
        }

        .popup-content p {
            margin-bottom: 4px;
            font-size: 13px;
        }
    </style>
@endpush

@push('custom-script')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data pembantu dari server
            const servants = @json($servants);

            // Inisialisasi peta (Indonesia tengah)
            const map = L.map('map').setView([-2.5, 118], 5);

            // Tambahkan tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Object untuk menyimpan markers
            const markers = {};

            // Tambahkan marker untuk setiap pembantu
            servants.forEach(function(servant) {
                if (servant.servant_details && servant.servant_details.latitude && servant.servant_details
                    .longitude) {
                    const lat = parseFloat(servant.servant_details.latitude);
                    const lng = parseFloat(servant.servant_details.longitude);

                    // Custom icon
                    const customIcon = L.divIcon({
                        className: 'custom-marker',
                        html: `<div style="background-color: #4e73df; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                            ${servant.name.charAt(0).toUpperCase()}
                        </div>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15],
                        popupAnchor: [0, -15]
                    });

                    // Popup content
                    const popupContent = `
                        <div class="popup-content">
                            <h6><i class="fas fa-user"></i> ${servant.name}</h6>
                            <p><i class="fas fa-briefcase text-info"></i> <strong>Profesi:</strong> ${servant.servant_details.profession?.name || '-'}</p>
                            <p><i class="fas fa-map-marker-alt text-danger"></i> <strong>Lokasi:</strong> ${servant.servant_details.regency || '-'}, ${servant.servant_details.province || '-'}</p>
                            <p><i class="fas fa-phone text-success"></i> <strong>Telepon:</strong> ${servant.servant_details.phone || '-'}</p>
                        </div>
                    `;

                    const marker = L.marker([lat, lng], {
                        icon: customIcon
                    }).addTo(map);
                    marker.bindPopup(popupContent);

                    // Simpan marker dengan ID servant
                    markers[servant.id] = marker;
                }
            });

            // Fit bounds jika ada marker
            if (Object.keys(markers).length > 0) {
                const group = new L.featureGroup(Object.values(markers));
                map.fitBounds(group.getBounds().pad(0.1));
            }

            // Event listener untuk tombol "Lihat di Peta"
            document.querySelectorAll('.focus-marker').forEach(function(button) {
                button.addEventListener('click', function() {
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);
                    const name = this.dataset.name;

                    // Zoom ke lokasi
                    map.setView([lat, lng], 15);

                    // Scroll ke peta
                    document.getElementById('map').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Cari marker yang sesuai dan buka popup
                    Object.values(markers).forEach(function(marker) {
                        const markerLatLng = marker.getLatLng();
                        if (Math.abs(markerLatLng.lat - lat) < 0.0001 && Math.abs(markerLatLng
                                .lng - lng) < 0.0001) {
                            marker.openPopup();
                        }
                    });
                });
            });
        });
    </script>
@endpush
