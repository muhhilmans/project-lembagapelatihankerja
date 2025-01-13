@extends('landing.layout.main', ['title' => 'Blog'])

@section('main')
    <!----//-banner V-1-//-->
    <section class="banner-breadcrumnd overflow-hidden">
        <div class="container">
            <div class="breadcrumnd-wrapper position-relative">
                <div class="bread-man">
                    <img src="assets/images/about/bread-man.png" alt="img">
                </div>
                <div class="row align-items-center justify-content-between">
                    <div class="breadcrumnd-content text-center">
                        <div class="about-content-head2">
                            <div class="cmn-section-title">
                                <div class="breadcrumb-bg wow fadeInUp" data-wow-delay="1.5">
                                    <ul class="breads">
                                        <li>
                                            <a href="{{ route('home') }}">
                                                Home
                                            </a>
                                        </li>
                                        <li>
                                            /
                                        </li>
                                        <li>
                                            Blog & News
                                        </li>
                                    </ul>
                                </div>
                                <h2 class="title mb-0 wow fadeInDown" data-wow-delay="1.5">
                                    Blog & News
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <img src="{{ asset('landing/assets/images/icon/working-ball.png') }}" alt="img" class="working-ball">
        <img src="{{ asset('landing/assets/images/icon/fl-right.png') }}" alt="img" class="working-fl">
        <img src="{{ asset('landing/assets/images/about/arrow-rotate.png') }}" alt="img" class="working-arrow">
    </section>
    <!----//-banner V-1-//-->

    <!----//-blog Section-//-->
    <section class="blog-main-section pt-120 pb-120 overflow-hidden">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    @if ($blogs->count() > 0)
                        <div class="row g-4">
                            @foreach ($blogs as $blog)
                                <div class="col-lg-lg-6 col-md-6 col-sm-6">
                                    <div class="blog-v1-item">
                                        <ul class="admin">
                                            <li class="d-flex align-items-center gap-1">
                                                <i class="bi bi-person base fz-18"></i>
                                                <span class="pra">
                                                    {{ $blog->user->name }}
                                                </span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1">
                                                <i class="bi bi-calendar4-week base fz-18"></i>
                                                <span class="pra">
                                                    {{ \Carbon\Carbon::parse($blog->created_at)->format('d F Y') }}
                                                </span>
                                            </li>
                                        </ul>
                                        <h3>
                                            <a href="{{ route('blog-detail', ['slug' => $blog->slug]) }}" class="title">
                                                {{ $blog->title }}
                                            </a>
                                        </h3>
                                        <a href="{{ route('blog-detail', ['slug' => $blog->slug]) }}" class="thumb">
                                            <img src="{{ route('getImage', ['path' => 'blogs', 'imageName' => $blog->image]) }}"
                                                alt="img" class="img-fluid"
                                                style="max-height: 200px; object-fit: cover; width: 100%;">
                                        </a>
                                        <p class="pra text-muted">
                                            {!! \Illuminate\Support\Str::limit($blog->content, 100) !!}
                                        </p>
                                        <a href="{{ route('blog-detail', ['slug' => $blog->slug]) }}"
                                            class="d-flex similer-btn align-items-center gap-3 title">
                                            <span>
                                                Continue Reading
                                            </span>
                                            <span class="icon">
                                                <svg width="32" height="8" viewBox="0 0 32 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M31.3536 4.35355C31.5488 4.15829 31.5488 3.84171 31.3536 3.64645L28.1716 0.464466C27.9763 0.269204 27.6597 0.269204 27.4645 0.464466C27.2692 0.659728 27.2692 0.976311 27.4645 1.17157L30.2929 4L27.4645 6.82843C27.2692 7.02369 27.2692 7.34027 27.4645 7.53553C27.6597 7.7308 27.9763 7.7308 28.1716 7.53553L31.3536 4.35355ZM0 4.5H31V3.5H0V4.5Z"
                                                        fill="#032B52" />
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{ $blogs->links('landing.layout.pagination') }}
                    @else
                        <div class="col-12">
                            <div class="text-center pra">
                                No blogs found
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="service-details-rightbar mt-3 mt-lg-0">
                        <div class="service-box1 ser-inform cmn-padding">
                            {{-- <div class="mb-space50">
                                <h3 class="title mb-4">
                                    Send Message
                                </h3>
                                <form action="#">
                                    <input type="text" placeholder="Search">
                                </form>
                            </div>
                            <div class="mb-space50">
                                <h3 class="title">
                                    Categories
                                </h3>
                                <ul class="se-boxlist">
                                    <li>
                                        <a href="javascript:void(0)" class="justify-content-between">
                                            <span class="pra prafont fw-400">
                                                Branding Home
                                            </span>
                                            <span class="pra prafont fw-400">
                                                (04)
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="justify-content-between">
                                            <span class="pra prafont fw-400">
                                                Digital Art
                                            </span>
                                            <span class="pra prafont fw-400">
                                                (06)
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="justify-content-between">
                                            <span class="pra prafont fw-400">
                                                Business Indrusties
                                            </span>
                                            <span class="pra prafont fw-400">
                                                (09)
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="justify-content-between">
                                            <span class="pra prafont fw-400">
                                                Indoor Cleaning
                                            </span>
                                            <span class="pra prafont fw-400">
                                                (03)
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div> --}}
                            <div class="mb-space50">
                                <h3 class="title">
                                    Latest Posts
                                </h3>
                                <ul class="latest-recent-post">
                                    @foreach ($blogLatest as $blog)
                                        <li>
                                            <a href="{{ route('blog-detail', ['slug' => $blog->slug]) }}">
                                                <span class="thumb">
                                                    <img src="{{ route('getImage', ['path' => 'blogs', 'imageName' => $blog->image]) }}"
                                                        alt="img" style="max-width: 70px;">
                                                </span>
                                                <span class="cont">
                                                    <span class="fw-700 title">
                                                        {{ $blog->title }}
                                                    </span>
                                                    <span>
                                                        <span class="d-flex align-items-center gap-1">
                                                            <i class="bi bi-calendar4-week base fz-18"></i>
                                                            <span class="pra prafont">
                                                                {{ \Carbon\Carbon::parse($blog->created_at)->format('d F Y') }}
                                                            </span>
                                                        </span>
                                                    </span>
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mb-space50-not">
                                <h3 class="title">
                                    Popular tags:
                                </h3>
                                <div class="populat-tag">
                                    @foreach ($popularTags as $tag => $count)
                                        <a href="javascript:void(0)">
                                            {{ $tag }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!----//-blog Section-//-->
@endsection
