@extends('cms.layouts.main', ['title' => 'Dashboard'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>

        <!-- Filter Form -->
        <form action="{{ route('dashboard') }}" method="GET" class="form-group shadow">
            <select name="filter" class="form-control" onchange="this.form.submit()">
                @hasrole('superadmin|admin')
                    <option value="weekly" {{ $filter === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                @endhasrole
                <option value="monthly" {{ $filter === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                <option value="yearly" {{ $filter === 'yearly' ? 'selected' : '' }}>Tahunan</option>
            </select>
        </form>
    </div>

    @hasrole('superadmin')
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Admin</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['admins'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users-cog fa-2x text-gray-300"></i>
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
                                    Majikan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['employes'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-friends fa-2x text-gray-300"></i>
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
                                    Pembantu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['servants'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pelamar (Pending)</div>
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
                                Pelamar (Proses)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['process'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Pelamar (Diterima)</div>
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
                                Pelamar (Ditolak)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Lowongan (Aktif)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['vacancy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file fa-2x text-gray-300"></i>
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
                                Pekerja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['worker'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-badge fa-2x text-gray-300"></i>
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
                                Pengaduan (Ditolak)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['rejectedComp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
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
                                Pengaduan (Diterima)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['acceptedComp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        {{-- <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}

        @hasrole('superadmin|admin')
            <div class="col-xl-8 col-lg-8">
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
                                        <th>Nama Pelamar</th>
                                        <th>Tanggal Interview</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datasApp as $data)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $data->servant->name }}</td>
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
            </div>
        @endhasrole

        <!-- Worker Chart -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pekerja (Berdasarkan Profesi)</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pb-2">
                        @if (count($chartWorkerCount) > 0)
                            <canvas id="workerPieChart"></canvas>
                        @else
                            <p class="text-center text-muted">Tidak ada data pekerja.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Servant Chart -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pelamar (Berdasarkan Profesi)</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pb-2">
                        @if (count($chartServantCount) > 0)
                            <canvas id="servantPieChart"></canvas>
                        @else
                            <p class="text-center text-muted">Tidak ada data pelamar.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Vacancy Chart -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lowongan (Berdasarkan Profesi)</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pb-2">
                        @if (count($chartVacancyCount) > 0)
                            <canvas id="vacancyPieChart"></canvas>
                        @else
                            <p class="text-center text-muted">Tidak ada data lowongan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @hasrole('owner')
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Pekerja</h6>

                        <form action="{{ route('dashboard') }}" method="GET" class="form-group shadow">
                            <select name="filterBar" class="form-control" onchange="this.form.submit()">
                                <option value="weekly" {{ $filterBar === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                <option value="monthly" {{ $filterBar === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ $filterBar === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <canvas id="workerBarChart"></canvas>
                    </div>
                </div>
            </div>
        @endhasrole
    </div>
@endsection

@push('custom-script')
    <script>
        const fixedColors = ["#28a745", "#004085", "#8B4513", "#8B0000"]; // Hijau, Biru Tua, Coklat, Merah Tua

        function getFixedColors(length) {
            return Array.from({
                length
            }, (_, i) => fixedColors[i % fixedColors.length]);
        }

        function createChart(chartId, data, labels) {
            if (data.length > 0) {
                const backgroundColors = getFixedColors(labels.length);
                const hoverBackgroundColors = backgroundColors.map(color => darkenColor(color, 30));

                new Chart(document.getElementById(chartId), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColors,
                            hoverBackgroundColor: hoverBackgroundColors,
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                bottom: 20,
                            },
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                align: 'start',
                                fullSize: true,
                                labels: {
                                    padding: 15,
                                    boxWidth: 15
                                }
                            },
                            tooltip: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: true,
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return `${labels[tooltipItem.dataIndex]}: ${data[tooltipItem.dataIndex]}`;
                                    }
                                }
                            }
                        },
                        cutout: '80%',
                    },
                });
            }
        }

        function darkenColor(color, amount) {
            const num = parseInt(color.slice(1), 16);
            const r = Math.max(0, (num >> 16) - amount);
            const g = Math.max(0, ((num >> 8) & 0x00FF) - amount);
            const b = Math.max(0, (num & 0x0000FF) - amount);
            return `#${(r << 16 | g << 8 | b).toString(16).padStart(6, '0')}`;
        }

        const workerData = @json($chartWorkerCount);
        createChart('workerPieChart', Object.values(workerData), Object.keys(workerData));

        const servantData = @json($chartServantCount);
        createChart('servantPieChart', Object.values(servantData), Object.keys(servantData));

        const vacancyData = @json($chartVacancyCount);
        createChart('vacancyPieChart', Object.values(vacancyData), Object.keys(vacancyData));

        const activeWorkersData = @json($activeWorkers);
        const activeWorkersLabels = Object.keys(activeWorkersData);
        const activeWorkersCounts = Object.values(activeWorkersData);
        const labels = @json($labelsBar);

        if (activeWorkersCounts.length > 0) {
            new Chart(document.getElementById('workerBarChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Jumlah Pekerja Aktif",
                        data: activeWorkersCounts,
                        backgroundColor: getFixedColors(activeWorkersLabels.length),
                        hoverBackgroundColor: getFixedColors(activeWorkersLabels.length).map(color =>
                            darkenColor(color, 30)),
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            time: {
                                unit: "month",
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                maxTicksLimit: 6,
                            },
                            maxBarThickness: 25,
                        }, ],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 1,
                                padding: 10,
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2],
                            },
                        }, ],
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    }
                },
            });
        }
    </script>
@endpush
