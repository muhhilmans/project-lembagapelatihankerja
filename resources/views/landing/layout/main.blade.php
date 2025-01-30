<!DOCTYPE html>
<html lang="en">

<head>
    <!--<< Required meta tags >>-->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="pembantu rumah tangga, babysitter, penyalur pembantu, yayasan pembantu, perawat lansia, sopir">
    <meta name="description" content="Sipembantu.com menyediakan jasa pembantu rumah tangga profesional dan terpercaya. Cari pembantu harian, bulanan, atau perawat lansia dengan mudah dan aman. Hubungi kami sekarang!">
    <meta name="author" content="Sipembantu">
    <meta property="og:title" content="Jasa Pembantu Rumah Tangga Profesional | Sipembantu.com">
    <meta property="og:description" content="Sipembantu.com menyediakan jasa pembantu rumah tangga profesional dan terpercaya. Cari pembantu harian, bulanan, atau perawat lansia dengan mudah dan aman. Hubungi kami sekarang!">
    <meta property="og:image" content="https://sipembantu.com/images/logo.png">
    <meta property="og:url" content="https://sipembantu.com/">
    <meta property="og:site_name" content="sipembantu.com/">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Jasa Pembantu Rumah Tangga Profesional | Sipembantu.com">
    <meta name="twitter:description" content="Sipembantu.com menyediakan jasa pembantu rumah tangga profesional dan terpercaya. Cari pembantu harian, bulanan, atau perawat lansia dengan mudah dan aman. Hubungi kami sekarang!">
    <meta name="twitter:image" content="https://sipembantu.com/images/logo.png">

    <!--<< Title >>-->
    <title>{{ $title }} | {{ env('APP_NAME') }}</title>

    <link rel="canonical" href="https://sipembantu.com/">
    <!--<< Favcion >>-->
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}">
    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="{{ asset('landing/assets/css/bootstrap.min.css') }}">
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="{{ asset('landing/assets/css/main.css') }}">
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
</body>

</html>
