<!DOCTYPE html>
<html lang="id">

<head>
    <base href="https://sipembantu.com/">
    <!-- Meta Data Wajib -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <meta name="keywords"
        content="jasa pembantu rumah tangga, babysitter, perawat lansia, penyalur pembantu terpercaya, yayasan pembantu profesional, cari pembantu harian, sopir pribadi">
    <meta name="description"
        content="Sipembantu.com menyediakan jasa pembantu rumah tangga, babysitter, dan perawat lansia yang profesional dan terpercaya. Dapatkan tenaga kerja rumah tangga terbaik dengan mudah dan aman. Hubungi kami sekarang!">
    <meta name="author" content="Sipembantu">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sipembantu.com/">

    <!-- Open Graph Meta Tags (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Jasa Pembantu Rumah Tangga Profesional | Sipembantu.com">
    <meta property="og:description"
        content="Cari pembantu rumah tangga, babysitter, atau perawat lansia yang terpercaya dan profesional? Temukan tenaga kerja terbaik hanya di Sipembantu.com.">
    <meta property="og:image" content="https://sipembantu.com/images/logo.png">
    <meta property="og:url" content="https://sipembantu.com/">
    <meta property="og:site_name" content="Sipembantu">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Jasa Pembantu Rumah Tangga Profesional | Sipembantu.com">
    <meta name="twitter:description"
        content="Cari pembantu rumah tangga, babysitter, atau perawat lansia yang terpercaya dan profesional? Temukan tenaga kerja terbaik hanya di Sipembantu.com.">
    <meta name="twitter:image" content="https://sipembantu.com/images/logo.png">
    <meta name="twitter:site" content="@Sipembantu">

    <title>{{ $title }} | {{ env('APP_NAME') }}</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/logo.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/logo.png') }}">


    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <style>
        .zoomable-image {
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        .zoomable-image:hover {
            transform: scale(1.05);
        }

        .modal-content-img {
            background: rgba(0, 0, 0, 0.85);
            border: none;
            padding: 10px;
        }

        #fullscreenImage {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100vh;
            display: block;
            margin: auto;
        }
    </style>

    @stack('custom-style')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('cms.layouts.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('cms.layouts.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('cms.layouts.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Image Modal -->
    <div id="fullscreenModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content-img bg-dark text-center">
                <button type="button" class="close text-white position-absolute"
                    style="top: 10px; right: 15px; z-index: 1051;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <img id="fullscreenImage" src="" alt="Fullscreen Image" class="img-fluid rounded">
            </div>
        </div>
    </div>

    @include('sweetalert::alert')

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('assets/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('assets/js/demo/chart-pie-demo.js') }}"></script>
    <script src="{{ asset('assets/js/demo/datatables-demo.js') }}"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-93SYV6YRRW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-93SYV6YRRW');
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const zoomableImages = document.querySelectorAll('.zoomable-image');
            const fullscreenModal = document.getElementById('fullscreenModal');
            const fullscreenImage = document.getElementById('fullscreenImage');

            zoomableImages.forEach(image => {
                image.addEventListener('click', function() {
                    fullscreenImage.src = this.src;
                    $(fullscreenModal).modal('show');
                });
            });

            $('#fullscreenModal').on('hidden.bs.modal', function() {
                fullscreenImage.src = '';
            });
        });
    </script>

    @stack('custom-script')
</body>

</html>
