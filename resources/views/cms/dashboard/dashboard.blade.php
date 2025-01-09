@extends('cms.layouts.main', ['title' => 'Dashboard'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
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
    </div>
@endsection

@push('custom-script')
    <script>
        const workerData = @json($chartWorkerCount);
        const workerLabels = Object.keys(workerData);
        const workerCounts = Object.values(workerData);

        const generateRandomColors = (numColors) => {
            const colors = [];
            for (let i = 0; i < numColors; i++) {
                const randomColor = `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`;
                colors.push(randomColor);
            }
            return colors;
        };

        const darkenColor = (color, amount) => {
            const num = parseInt(color.slice(1), 16);
            const r = Math.max(0, (num >> 16) - amount);
            const g = Math.max(0, ((num >> 8) & 0x00FF) - amount);
            const b = Math.max(0, (num & 0x0000FF) - amount);
            return `#${(r << 16 | g << 8 | b).toString(16).padStart(6, '0')}`;
        };

        if (workerCounts.length > 0) {
            const workerBackgroundColors = generateRandomColors(workerLabels.length);
            const workerHoverBackgroundColors = workerBackgroundColors.map(color => darkenColor(color, 30));

            new Chart(document.getElementById('workerPieChart'), {
                type: 'doughnut',
                data: {
                    labels: workerLabels,
                    datasets: [{
                        data: workerCounts,
                        backgroundColor: workerBackgroundColors,
                        hoverBackgroundColor: workerHoverBackgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }

        const servantData = @json($chartServantCount);
        const servantLabels = Object.keys(servantData);
        const servantCounts = Object.values(servantData);

        if (servantCounts.length > 0) {
            const servantBackgroundColors = generateRandomColors(servantLabels.length);
            const servantHoverBackgroundColors = servantBackgroundColors.map(color => darkenColor(color, 30));

            new Chart(document.getElementById('servantPieChart'), {
                type: 'doughnut',
                data: {
                    labels: servantLabels,
                    datasets: [{
                        data: servantCounts,
                        backgroundColor: servantBackgroundColors,
                        hoverBackgroundColor: servantHoverBackgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }

        const vacancyData = @json($chartVacancyCount);
        const vacancyLabels = Object.keys(vacancyData);
        const vacancyCounts = Object.values(vacancyData);

        if (vacancyCounts.length > 0) {
            const vacancyBackgroundColors = generateRandomColors(vacancyLabels.length);
            const vacancyHoverBackgroundColors = vacancyBackgroundColors.map(color => darkenColor(color, 30));

            new Chart(document.getElementById('vacancyPieChart'), {
                type: 'doughnut',
                data: {
                    labels: vacancyLabels,
                    datasets: [{
                        data: vacancyCounts,
                        backgroundColor: vacancyBackgroundColors,
                        hoverBackgroundColor: vacancyHoverBackgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }
    </script>
@endpush
