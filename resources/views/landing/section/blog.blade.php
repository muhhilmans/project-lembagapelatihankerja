<section class="blog-section pb-120 pt-120">
    <div class="container">
        <div class="row justify-content-between align-items-center mb-60 g-3">
            <div class="col-lg-6 col-md-6">
                <div class="cmn-section-title apointment-content">
                    <a href="blog.html" class="cmn--btn cmn-alt2 wow fadeInDown" data-wow-delay="0.4s">
                        <span>
                            Blog dan Berita
                        </span>
                    </a>
                    <h2 class="title mt-xxl-4 mt-2 mb-0 wow fadeInUp" data-wow-delay="0.6s">
                        Informasi Terbaru dari Kami
                    </h2>
                </div>
            </div>
            <!--<div class="col-xl-5 col-lg-6 col-md-6 wow fadeInDown" data-wow-delay="0.7s">-->
            <!--    <p class="pra">-->
            <!--        We believe in the power of attention to detail. Our cleaners are meticulous in their work, leaving-->
            <!--        no nook or cranny untouched. We take pride in our ability to. We believe in the power of attention-->
            <!--        to detail.-->
            <!--    </p>-->
            <!--</div>-->
        </div>
        <div class="row g-4">
            @if (isset($blogs) && $blogs->isNotEmpty())
                @foreach ($blogs as $blog)
                    <div class="col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
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
                                    alt="img" class="img-fluid" style="max-height: 200px; object-fit: cover; width: 100%;">
                            </a>
                            <p class="pra text-muted">
                                {!! \Illuminate\Support\Str::limit($blog->content, 100) !!}
                            </p>
                            <a href="{{ route('blog-detail', ['slug' => $blog->slug]) }}" class="d-flex similer-btn align-items-center gap-3 title">
                                <span>
                                    Baca Selengkapnya
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
                <div class="servie-btn text-center mt-xxl-4 mt-4">
                    <a href="{{ route('all-blogs') }}" class="cmn--btn">
                        <span>
                            Lihat Semua Blog
                        </span>
                    </a>
                </div>
            @else
                <div class="col-12 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="text-center pra">
                        Belum ada Blog/Berita
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
