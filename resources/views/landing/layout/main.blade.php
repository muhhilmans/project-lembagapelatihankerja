<!DOCTYPE html>
<html lang="id">

<head>
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

    <!--<< Title >>-->
    <title>{{ $title }} | {{ env('APP_NAME') }}</title>
    <!--<< Favcion >>-->
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}" type="image/png">
    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="{{ asset('landing/assets/css/bootstrap.min.css') }}">
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="{{ asset('landing/assets/css/main.css') }}">
    <style>
        .whatsapp-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .whatsapp-icon a {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 15px;
            border-radius: 50%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .whatsapp-icon a:hover {
            background-color: #128C7E;
        }
    </style>
</head>

<body>

    <!--<< Prealoder >>-->
    <div class="preloaders">
        <span class="loader"></span>
    </div>
    <!--<< Prealoder >>-->

    <button type="button" class="click__sidebar remove__click">
        <svg width="30" height="14" viewBox="0 0 30 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line y1="1.25" x2="30" y2="1.25" stroke="black" stroke-width="1.5" />
            <line x1="5" y1="7.25" x2="30" y2="7.25" stroke="black" stroke-width="1.5" />
            <line x1="10" y1="13.25" x2="30" y2="13.25" stroke="black" stroke-width="1.5" />
        </svg>
    </button>

    <!----//-main header menu-//-->
    @include('landing.layout.header')
    <!----//-main header menu-//-->

    <!----//-main content-//-->
    @yield('main')
    <!----//-main content-//-->

    <!--<<  Footer v-1 >>-->
    @include('landing.layout.footer')
    <!--<<  Footer v-1 >>-->


    <!--<<  sub side bar custom >>-->
    @include('landing.layout.sidebar')
    <!--<<  sub side bar custom >>-->

    <!--<< Search Popup >>-->
    <div id="search">
        <button type="button" class="close">
            ×
        </button>
        <form>
            <input type="search" placeholder="Search Here">
            <button type="submit" class="btn">Go for Search</button>
        </form>
    </div>
    <!--<< Search Popup >>-->

    <div class="whatsapp-icon">
        <a href="https://wa.link/x5uy9m" target="_blank">
            <svg width="35" height="35" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path
                    d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" fill="white" />
            </svg>
        </a>
    </div>

    <!--<< Jquery Latest >>-->
    <script src="{{ asset('landing/assets/js/jquery-3.7.0.min.js') }}"></script>
    <!--<< Viewport Js >>-->
    <script src="{{ asset('landing/assets/js/viewport.jquery.js') }}"></script>
    <!--<< Bootstrap Js >>-->
    <script src="{{ asset('landing/assets/js/bootstrap.min.js') }}"></script>
    <!--<< Nice Select Js >>-->
    <script src="{{ asset('landing/assets/js/jquery.nice-select.min.js') }}"></script>
    <!--<< Swiper Slide Js >>-->
    <script src="{{ asset('landing/assets/js/swiper.min.js') }}"></script>
    <!--<< Swiper Bundle Js >>-->
    <script src="{{ asset('landing/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!--<< magnific popup Js >>-->
    <script src="{{ asset('landing/assets/js/odometer.min.js') }}"></script>
    <!--<< Odometer js Js >>-->
    <script src="{{ asset('landing/assets/js/wow.min.js') }}"></script>
    <!--<< Wow Animation js >>-->
    <script src="{{ asset('landing/assets/js/main.js') }}"></script>
    <!--<< Main.js >>-->

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
</body>

</html>
