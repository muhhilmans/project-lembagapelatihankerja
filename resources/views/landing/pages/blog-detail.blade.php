@extends('landing.layout.main', ['title' => 'Detail Blog'])

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
                                            Blog Details
                                        </li>
                                    </ul>
                                </div>
                                <h2 class="title mb-0 wow fadeInDown" data-wow-delay="1.5">
                                    Blog Details
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
                    <div class="blog-details-left">
                        <div class="blog-details-item">
                            <div class="thumb-big">
                                <img src="{{ route('getImage', ['path' => 'blogs', 'imageName' => $blog->image]) }}"
                                    alt="img">
                            </div>
                            <ul class="admin d-flex flex-wrap align-items-center gap-3">
                                <li class="d-flex align-items-center gap-1">
                                    <i class="bi bi-person base fz-18"></i>
                                    <span class="pra prafont">
                                        {{ $blog->user->name }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-1">
                                    <i class="bi bi-calendar4-week base fz-18"></i>
                                    <span class="pra prafont">
                                        {{ \Carbon\Carbon::parse($blog->created_at)->format('d F Y') }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-1">
                                    <i class="bi bi-bookmark base fz-18"></i>
                                    <span class="pra prafont">
                                        {{ $blog->category }}
                                    </span>
                                </li>
                            </ul>
                            <h2 class="title">
                                {{ $blog->title }}
                            </h2>
                            <p class="pra m-space5">
                                {!! $blog->content !!}
                            </p>
                            {{-- <div class="parti-wrap">
                                <div class="part-thumb">
                                    <img src="assets/images/blog/parti1.png" alt="img">
                                </div>
                                <div class="part-thumb">
                                    <img src="assets/images/blog/parti2.png" alt="img">
                                </div>
                            </div>
                            <div class="parti-content">
                                <h3 class="title">
                                    Practical Tips for Incorporating Outdoor Education:
                                </h3>
                                <p class="pra prafont m-space5">
                                    The field of web development is constantly evolving, and being adaptable and open to
                                    learning new technologies is essential for long-term success. Consider these guidelines
                                    as a starting point and regularly update your skills based on industry trends and
                                    emerging technologies.
                                </p>
                                <p class="pra prafont mb-0">
                                    The field of web development is constantly evolving, and being adaptable and open to
                                    learning new technologies is essential for long-term success. Consider these guidelines
                                    as a starting point and regularly update your skills based on industry trends and
                                    emerging technologies.
                                </p>
                                <p class="pra-badge">
                                    When I was a child, I used to fear mathematics. But now, I am in love with mathematics
                                    because of Steed School.”It is our goal to bring gifted style education to everyone
                                    using tools such as problem based learning.
                                </p>
                                <h3 class="title">
                                    Additional Resources:
                                </h3>
                                <p class="pra prafont mb-0">
                                    The field of web development is constantly evolving, and being adaptable and open to
                                    learning new technologies is essential for long-term success. Consider these guidelines
                                    as a starting point and regularly update your skills based on industry trends and
                                    emerging technologies.
                                </p>
                            </div> --}}
                        </div>
                        {{-- <div class="leav-commentwrap">
                            <h3 class="title">
                                Leave A Reply
                            </h3>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="leave-grp">
                                        <input type="text" placeholder="Your email">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="leave-grp">
                                        <input type="text" placeholder="Your name">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="leave-grp">
                                        <textarea name="comments" rows="7" placeholder="Your Comments..."></textarea>
                                    </div>
                                </div>
                                <div class="check-consition">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value=""
                                            id="flexCheckDefault">
                                        <label class="form-check-label pra prafont" for="flexCheckDefault">
                                            Save my name, email, and website in this browser for the next time I comment.
                                        </label>
                                    </div>
                                </div>
                                <div class="submit-process">
                                    <button type="submit" class="cmn--btn d-print-inline-flex">
                                        <span>
                                            Post A Comments
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div> --}}
                    </div>
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
                                    Kategori
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
                                    @foreach ($blogs as $blog)
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
                                    @foreach (json_decode($blog->tags, true) as $tag)
                                        <a href="javascript:void(0)">{{ $tag['value'] }}</a>
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
