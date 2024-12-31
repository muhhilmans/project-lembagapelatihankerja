@extends('landing.layout.main', ['title' => 'Beranda'])

@section('main')
    <!----//-banner V-1-//-->
    @include('landing.section.banner')
    <!----//-banner V-1-//-->


    <!----//-banner Category-//-->
    @include('landing.section.category')
    <!----//-banner Category-//-->

    <!----//-Provide Section-//-->
    @include('landing.section.provide')
    <!----//-Provide Section-//-->

    <!----//-why choose Section-//-->
    @include('landing.section.why')
    <!----//-why choose Section-//-->

    <!--<<  Howit work >>-->
    @include('landing.section.howit')
    <!--<<  Howit work >>-->

    <!--<<  Team Member >>-->
    @include('landing.section.team')
    <!--<<  Team Member >>-->


    <!--<<  Testimonial sectioN >>-->
    @include('landing.section.testimonial')
    <!--<<  Testimonial sectioN >>-->

    <!--<<  Pricing sectioN >>-->
    @include('landing.section.pricing')
    <!--<<  Pricing sectioN >>-->

    <!--<<  Sponsor section >>-->
    @include('landing.section.sponsor')
    <!--<<  Sponsor section >>-->

    <!--<<  Cleaning Quote section >>-->
    @include('landing.section.quote')
    <!--<<  Cleaning Quote section >>-->

    <!--<<  Counter section >>-->
    @include('landing.section.counter')
    <!--<<  Counter section >>-->

    <!--<<  Faq section >>-->
    @include('landing.section.faq')
    <!--<<  Faq section >>-->

    <!--<<  Blog section >>-->
    @include('landing.section.blog')
    <!--<<  Blog section >>-->

    <!--<<  Apointment section >>-->
    @include('landing.section.apointment')
    <!--<<  Apointment section >>-->
@endsection
