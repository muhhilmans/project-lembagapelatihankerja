@extends('cms.layouts.main', ['title' => 'Tracking Lokasi Pembantu'])

@section('content')
    <div class="row m-0 tracking-container">
        <!-- Sidebar List -->
        <div class="col-md-3 col-12 p-0 bg-white border-right shadow-sm d-flex flex-column sidebar-container order-2 order-md-1">
            <!-- Header & Filter -->
            <div class="p-3 border-bottom bg-light">
                <h5 class="font-weight-bold text-gray-800 mb-3">Live Tracking</h5>
                
                <div class="input-group mb-3">
                    <input type="text" id="searchWorker" class="form-control" placeholder="Cari pembantu...">
                    <div class="input-group-append">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-gray-400"></i></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-sm btn-primary active filter-btn" data-filter="all">Semua</button>
                    <button class="btn btn-sm btn-outline-success filter-btn" data-filter="online">Online</button>
                    <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="offline">Offline</button>
                </div>
            </div>

            <!-- Worker List -->
            <div class="worker-list flex-grow-1 overflow-auto p-2" id="workerListContainer">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-circle-notch fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>

            <!-- Footer Stats -->
            <div class="p-2 border-top bg-light text-center small text-muted">
                <span id="totalWorkers">0</span> Pembantu Aktif &bull; Update tiap 10dtk
            </div>
        </div>

        <!-- Map -->
        <div class="col-md-9 col-12 p-0 bg-secondary position-relative map-container order-1 order-md-2">
            <div id="map" style="height: 100%; width: 100%;"></div>
            
            <!-- Map Controls / Legend -->
            <div class="position-absolute bg-white p-2 rounded shadow-sm" style="top: 10px; right: 10px; z-index: 1000;">
                <small class="font-weight-bold d-block mb-1">Legenda:</small>
                <div class="d-flex align-items-center mb-1"><span class="badge badge-success mr-2">&nbsp;</span> Available</div>
                <div class="d-flex align-items-center"><span class="badge badge-secondary mr-2">&nbsp;</span> Working</div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow m-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Pembantu dengan Lokasi
            </h6>
        </div>
        <div class="card-body">
            @if ($servants->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data lokasi pembantu</p>
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
                                            data-id="{{ $servant->id }}">
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        /* Responsive Layout */
        .tracking-container {
            height: calc(100vh - 70px);
            position: relative;
            overflow: hidden;
        }

        .sidebar-container {
            height: 100%;
            z-index: 10;
        }

        .map-container {
            height: 100%;
        }

        @media (max-width: 767.98px) {
            .tracking-container {
                height: auto !important;
                /* Allow natural height */
                overflow: visible !important;
                /* Disable hidden overflow */
            }

            .sidebar-container {
                height: 50vh !important;
                /* Fixed height for list on mobile */
                border-right: none !important;
                border-top: 5px solid #eaecf4;
                /* Separator */
            }

            .map-container {
                height: 50vh !important;
                /* Fixed height for map on mobile */
            }
        }

        /* Custom Scrollbar */
        .worker-list::-webkit-scrollbar {
            width: 6px;
        }

        .worker-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .worker-list::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .worker-list::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        /* Worker Card */
        .worker-card {
            cursor: pointer;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .worker-card:hover {
            background-color: #f8f9fc;
            transform: translateX(2px);
        }

        .worker-card.active {
            background-color: #eef2ff;
            border-left-color: #4e73df;
        }

        /* Map Marker */
        .custom-marker-pin {
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            background-size: cover;
            background-position: center;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .custom-marker-pin:hover {
            transform: scale(1.2);
            z-index: 1000 !important;
        }

        /* Pulse Animation for active workers */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.33);
            }

            80%,
            100% {
                opacity: 0;
            }
        }

        .pulse-ring {
            position: absolute;
            height: 100%;
            width: 100%;
            border-radius: 50%;
            border: 3px solid #4e73df;
            animation: pulse-ring 3s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        /* Marker Colors by Profession */
        .border-sopir {
            border-color: #4e73df !important;
        }

        .border-art {
            border-color: #1cc88a !important;
        }

        .border-babysitter {
            border-color: #e74a3b !important;
        }
    </style>
@endpush

@push('custom-script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map Initialization
            const map = L.map('map', {
                zoomControl: false
            }).setView([-2.5, 118], 5);
            
            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            // Dark/Modern Tile Layer (CartoDB Voyager)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // State
            let workersData = [];
            let markers = {};
            let currentFilter = 'all';
            const fetchInterval = 10000; // 10 seconds

            // DOM Elements
            const workerListContainer = document.getElementById('workerListContainer');
            const searchInput = document.getElementById('searchWorker');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const totalWorkersSpan = document.getElementById('totalWorkers');

            // --- FUNCTIONS ---

            // Fetch Data
            async function fetchLocations() {
                try {
                    const response = await fetch("{{ route('tracking.locations') }}");
                    const data = await response.json();
                    workersData = data;
                    updateUI();
                } catch (error) {
                    console.error('Error fetching locations:', error);
                }
            }

            // Update UI (Map & Sidebar)
            function updateUI() {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredWorkers = workersData.filter(worker => {
                    const matchName = worker.name.toLowerCase().includes(searchTerm);
                    let matchStatus = true;
                    if (currentFilter === 'online') matchStatus = worker.is_online;
                    if (currentFilter === 'offline') matchStatus = !worker.is_online;
                    return matchName && matchStatus;
                });

                updateSidebar(filteredWorkers);
                updateMap(filteredWorkers);
                totalWorkersSpan.textContent = filteredWorkers.length;
            }

            // Update Sidebar List
            function updateSidebar(workers) {
                workerListContainer.innerHTML = '';
                
                if (workers.length === 0) {
                    workerListContainer.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-search mb-2"></i><br>Tidak ditemukan
                        </div>`;
                    return;
                }

                workers.forEach(worker => {
                    const card = document.createElement('div');
                    card.className = 'card worker-card mb-2 p-2 shadow-sm border-0';
                    card.setAttribute('data-id', worker.id);
                    
                    // Profession Color Class
                    let profClass = 'text-primary';
                    if (worker.profession.toLowerCase().includes('art')) profClass = 'text-success';
                    if (worker.profession.toLowerCase().includes('baby')) profClass = 'text-danger';

                    card.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="mr-3 position-relative">
                                ${worker.photo ? 
                                    `<img src="${worker.photo}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">` : 
                                    `<div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center text-gray-600 font-weight-bold" style="width: 40px; height: 40px;">${worker.name.charAt(0)}</div>`
                                }
                                <div class="position-absolute ${worker.is_online ? 'bg-success' : 'bg-secondary'} rounded-circle border border-white" 
                                    style="width: 10px; height: 10px; bottom: 0; right: 0;"></div>
                            </div>
                            <div class="overflow-hidden w-100">
                                <h6 class="mb-0 text-truncate font-weight-bold text-dark" style="font-size: 0.9rem;">${worker.name}</h6>
                                <div class="d-flex align-items-center small justify-content-between mb-1">
                                    <span class="${profClass} font-weight-bold mr-2">${worker.profession}</span>
                                    <span class="${worker.is_online ? 'text-success' : 'text-muted'}" style="font-size: 0.75rem;">
                                        ${worker.is_online ? '<i class="fas fa-circle" style="font-size: 8px;"></i> Online' : worker.last_seen}
                                    </span>
                                </div>
                                <div class="text-xs text-muted text-truncate"><i class="fas fa-map-marker-alt"></i> ${worker.address}</div>
                            </div>
                        </div>
                    `;

                    // Click Event: Focus on Map
                    card.addEventListener('click', () => {
                        focusOnWorker(worker);
                        // Highlight active card
                        document.querySelectorAll('.worker-card').forEach(c => c.classList.remove('active'));
                        card.classList.add('active');
                    });

                    workerListContainer.appendChild(card);
                });
            }

            // Update Map Markers
            function updateMap(workers) {
                // Remove markers that are not in the new list
                Object.keys(markers).forEach(id => {
                    if (!workers.find(w => w.id == id)) {
                        map.removeLayer(markers[id]);
                        delete markers[id];
                    }
                });

                workers.forEach(worker => {
                    const lat = parseFloat(worker.lat);
                    const lng = parseFloat(worker.lng);

                    // Determine Border Color based on Profession
                    let borderClass = 'border-sopir';
                    if (worker.profession.toLowerCase().includes('art')) borderClass = 'border-art';
                    if (worker.profession.toLowerCase().includes('baby')) borderClass = 'border-babysitter';

                    // Create Icon
                    const iconHtml = `
                        <div class="custom-marker-pin ${borderClass}" 
                            style="width: 40px; height: 40px; background-image: url('${worker.photo || 'https://ui-avatars.com/api/?name='+worker.name}'); background-color: white;">
                        </div>
                    `;

                    const customIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: iconHtml,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        popupAnchor: [0, -20]
                    });

                    // Add or Update Marker
                    if (markers[worker.id]) {
                        // Smooth Animation (Optional: use plugin for real smooth)
                        markers[worker.id].setLatLng([lat, lng]);
                        markers[worker.id].setIcon(customIcon); // Update icon just in case
                    } else {
                        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
                        
                        // Popup
                        const popupContent = `
                            <div class="text-center p-2">
                                <img src="${worker.photo || 'https://ui-avatars.com/api/?name='+worker.name}" class="rounded-circle mb-2 shadow-sm" width="60" height="60" style="object-fit:cover;">
                                <h6 class="font-weight-bold mb-0">${worker.name}</h6>
                                <div class="mb-2">
                                    <span class="badge ${worker.is_online ? 'badge-success' : 'badge-secondary'} mr-1">${worker.is_online ? 'Online' : 'Offline'}</span>
                                    <span class="badge badge-light border">${worker.profession}</span>
                                </div>
                                <div class="text-muted small mb-2 text-center" style="font-size: 0.8rem;">
                                    ${worker.is_online ? 'Sedang Aktif' : 'Terakhir: ' + worker.last_seen}
                                </div>
                                <div class="text-left small mt-2 border-top pt-2">
                                    <div><i class="fas fa-phone fa-fw text-muted"></i> ${worker.phone || '-'}</div>
                                    <div><i class="fas fa-map-marker-alt fa-fw text-muted"></i> ${worker.address}</div>
                                </div>
                            </div>
                        `;
                        marker.bindPopup(popupContent);
                        
                        markers[worker.id] = marker;
                    }
                });
            }

            // Focus Function
            function focusOnWorker(worker) {
                const lat = parseFloat(worker.lat);
                const lng = parseFloat(worker.lng);
                map.flyTo([lat, lng], 16, {
                    animate: true,
                    duration: 1.5
                });
                
                if (markers[worker.id]) {
                    setTimeout(() => markers[worker.id].openPopup(), 1500);
                }
            }

            // --- EVENT LISTENERS ---

            // Search Filter
            searchInput.addEventListener('input', updateUI);

            // Button Filter
            filterButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterButtons.forEach(b => {
                        b.classList.remove('active', 'btn-primary', 'btn-success');
                        if (b.dataset.filter === 'online') {
                            b.classList.add('btn-outline-success');
                        } else {
                            b.classList.add('btn-outline-secondary');
                        }
                    });
                    
                    if (btn.dataset.filter === 'online') {
                        btn.classList.remove('btn-outline-success');
                        btn.classList.add('active', 'btn-success');
                    } else {
                        btn.classList.remove('btn-outline-secondary');
                        btn.classList.add('active', 'btn-primary');
                    }
                    
                    currentFilter = btn.dataset.filter;
                    updateUI();
                });
            });

            // Initial Fetch & Polling
            fetchLocations();
            setInterval(fetchLocations, fetchInterval);

            // Table "Lihat di Peta" Button Handler
            document.addEventListener('click', function(e) {
                if(e.target && e.target.classList.contains('focus-marker')) {
                    const servantId = e.target.getAttribute('data-id');
                    const worker = workersData.find(w => w.id == servantId);
                    
                    if(worker) {
                        // Scroll to map top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        
                        // Focus on map
                        focusOnWorker(worker);
                    }
                }
            });
        });
    </script>
@endpush
