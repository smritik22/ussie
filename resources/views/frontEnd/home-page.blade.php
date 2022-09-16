@extends('frontEnd.layout')
@section('content')
    <link rel="stylesheet" href="{{ URL::asset('/assets/frontend/') }}/css/ckeditor.css" />
    <div class="site_content home_page">

        <section class="banner">
            <div class="container">
                <div class="row">
                    <div class="col-md-5">
                        <div class="main-banner-content">
                            <h1 class="main-title color_dark">{!! $labels['diamond_education_for_essential_jewellery_skills'] !!}</h1>
                            <a class="site-btn bg_blue" href="#programms_owl">
                                <span>{!! $labels['view_programs'] !!}</span>
                                <svg width="10" height="8" viewBox="0 0 10 8" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1 3.5C0.723858 3.5 0.5 3.72386 0.5 4C0.5 4.27614 0.723858 4.5 1 4.5L1 3.5ZM9.35355 4.35355C9.54882 4.15829 9.54882 3.84171 9.35355 3.64645L6.17157 0.464466C5.97631 0.269204 5.65973 0.269204 5.46447 0.464466C5.2692 0.659728 5.2692 0.976311 5.46447 1.17157L8.29289 4L5.46447 6.82843C5.2692 7.02369 5.2692 7.34027 5.46447 7.53553C5.65973 7.7308 5.97631 7.7308 6.17157 7.53553L9.35355 4.35355ZM1 4.5L9 4.5V3.5L1 3.5L1 4.5Z"
                                        fill="white" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="main-banner-img">
                            {!! Html::image('assets/frontend/images/Diamonds.png', $labels['banner_image'], ['class' => '']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($about_us)
            <section class="about">
                <div class="about-left">
                    <div class="about-left-content">
                        {!! $about_us['description'] !!}
                        @if ($about_us['profile_download'])
                            <a class="site-btn bg_blue download-btn"
                                href="{{ URL::asset('uploads/settings/' . $about_us['profile_download']) }}" download>
                                <span>{{ $labels['download_our_profile'] }}</span>
                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.7695 6.46144V10.6153C10.7695 11.3791 10.1488 12 9.38475 12H1.3847C0.620895 12 0 11.3793 0 10.6153L0.000160933 6.46144C0.000160933 6.20758 0.205562 5.99994 0.46166 5.99994C0.717757 5.99994 0.923159 6.20759 0.923159 6.46144V10.6153C0.923159 10.8692 1.13081 11.0768 1.38466 11.0768H9.3847C9.63856 11.0768 9.8462 10.8691 9.8462 10.6153L9.84636 6.46144C9.84636 6.20758 10.0518 5.99994 10.3079 5.99994C10.564 5.99994 10.7695 6.20759 10.7695 6.46144ZM5.05967 9.0946C5.14965 9.18459 5.26732 9.23063 5.38499 9.23063C5.50266 9.23063 5.62033 9.18443 5.71032 9.09444L8.01802 6.78674C8.19799 6.60677 8.19799 6.31366 8.01802 6.13366C7.83805 5.9537 7.54494 5.9537 7.36494 6.13366L5.84647 7.65452V0.461499C5.84647 0.207643 5.64107 0 5.38497 0C5.12888 0 4.92347 0.207656 4.92347 0.461499V7.65452L3.40278 6.13383C3.22281 5.95386 2.9297 5.95386 2.7497 6.13383C2.56973 6.3138 2.56973 6.60691 2.7497 6.78691L5.05967 9.0946Z"
                                        fill="white" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="about-right">
                    <div class="about-right-content">
                        {{-- {!! Html::image($about_us['document_two'], $labels['about_video'], ['data-bs-toggle' => 'modal', 'data-bs-target' => '#aboutModal', 'title' => $labels['about_video']]) !!} --}}

                        {{--  <iframe src="{{ $about_us['video_url'] }}" title="{!! $labels['youtube_video_player'] !!}?autoplay=0&showinfo=0&controls=0" width="100%" height="100%"></iframe>  --}}

                        <video width="100%" height="100%" autoplay muted>
                            <source src="{{$about_us['video_url']}}" type="video/mp4">
                            <source src="{{$about_us['video_url']}}" type="video/ogg">
                            {!!$labels['your_browser_does_not_support_the_video_tag']!!}
                        </video>

                    </div>
                </div>
                <!-- Modal for about video -->
                <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="close-about-video">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="">
                                <iframe width="100%" height="100%" src="{{ $about_us['video_url'] }}"
                                    title="{!! $labels['youtube_video_player'] !!}" frameborder="10"
                                    allow="clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if ($enroll)
            <section class="why-enroll">
                <div class="container">
                    {!! $enroll['description'] !!}
                </div>
            </section>
        @endif

        @if ($courseList)
            <section class="program" id="programms_owl">
                <div class="container">
                    <h2 class="paragraph-16r color_white">{{ $labels['programs'] }}</h2>
                    <h2 class="title color_white">{{ $labels['explore_our_programs'] }} </h2>
                </div>
                <div class="program-slider {!! \Helper::currentLanguage()->direction !!} owl-carousel owl-theme">

                    @foreach ($courseList as $courseDetail)
                        <div class="item" data-match-height="groupName">
                            <div class="program-img">
                                {!! Html::image($courseDetail['course_image'], $courseDetail['course_name_show'], ['title' => $courseDetail['course_name_show']]) !!}
                            </div>
                            <div class="program-content">
                                <h4 class="section-sub-title color_black" title="{!! $courseDetail['course_name_show'] !!}">{!! $courseDetail['course_name_show'] !!}</h4>
                                <p class="paragraph-16r color_dark">{!! $courseDetail['course_price'] !!} {!! \Helper::GeneralSiteSettings('currency') !!} (
                                    {!! $labels['vat_included'] !!} )</p>
                                <a class="site-btn bg_blue" href="{{route('frontend.course.details',[$courseDetail['course_name']])}}">
                                    <span>{!! $labels['book_now'] !!}</span>
                                    <svg width="10" height="8" viewBox="0 0 10 8" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1 3.5C0.723858 3.5 0.5 3.72386 0.5 4C0.5 4.27614 0.723858 4.5 1 4.5L1 3.5ZM9.35355 4.35355C9.54882 4.15829 9.54882 3.84171 9.35355 3.64645L6.17157 0.464466C5.97631 0.269204 5.65973 0.269204 5.46447 0.464466C5.2692 0.659728 5.2692 0.976311 5.46447 1.17157L8.29289 4L5.46447 6.82843C5.2692 7.02369 5.2692 7.34027 5.46447 7.53553C5.65973 7.7308 5.97631 7.7308 6.17157 7.53553L9.35355 4.35355ZM1 4.5L9 4.5V3.5L1 3.5L1 4.5Z"
                                            fill="white"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </section>
        @endif

        @if ($our_services['laboratory'])
            <section class="about our-service">
                <div class="about-left">
                    <div class="about-left-content">
                        <h2 class="paragraph-16r color_light_gray ser-sub-title">{{$labels['our_services']}}</h2>
                        <h2 class="section-sub-title color_dark">{{$labels['laboratory']}}</h2>
                        {!! \Helper::truncate($our_services['laboratory']['description'], \Helper::GeneralWebmasterSettings('word_limit_home_content')) !!}
                        <a class="site-link colol_blue"
                            href="{{ route('frontend.ourservices.type', ['servicetype' => urlencode($our_services['laboratory']['page_url'])]) }}">
                            {{ $labels['read_more'] }}
                        </a>
                    </div>
                </div>
                <div class="about-right">
                    <div class="about-right-content services-img">
                        {!! Html::image($our_services['laboratory']['document_two'], $labels['laboratory'], ['title' => $labels['laboratory']]) !!}
                    </div>
                </div>
            </section>
        @endif

        @if ($our_services['selling_tools'])
            <section class="about our-service">
                <div class="about-right">
                    <div class="about-right-content services-img">
                        {!! Html::image($our_services['selling_tools']['document_two'], $labels['selling_tools'], ['title' => $labels['selling_tools']]) !!}
                    </div>
                </div>
                <div class="about-left">
                    <div class="about-left-content service-right-content">
                        <h2 class="section-sub-title color_dark">{{$labels['selling_tools']}}</h2>
                        {!! \Helper::truncate($our_services['selling_tools']['description'], \Helper::GeneralWebmasterSettings('word_limit_home_content')) !!}
                        <a class="site-link colol_blue"
                            href="{{ route('frontend.ourservices.type', ['servicetype' => urlencode($our_services['selling_tools']['page_url'])]) }}">
                            {{ $labels['read_more'] }}
                        </a>
                    </div>
                </div>
            </section>
        @endif

        @if ($our_services['professional_consultation'])
            <section class="about our-service">
                <div class="about-left">
                    <div class="about-left-content">
                        <h2 class="section-sub-title color_dark">{{$labels['professional_consultation']}}</h2>
                        {!! \Helper::truncate($our_services['professional_consultation']['description'], \Helper::GeneralWebmasterSettings('word_limit_home_content')) !!}
                        <a class="site-link colol_blue"
                            href="{{ route('frontend.ourservices.type', ['servicetype' => urlencode($our_services['professional_consultation']['page_url'])]) }}">
                            {{ $labels['read_more'] }}
                        </a>
                    </div>
                </div>
                <div class="about-right">
                    <div class="about-right-content services-img">
                        {!! Html::image($our_services['professional_consultation']['document_two'], $labels['professional_consultation'], ['title' => $labels['professional_consultation']]) !!}
                    </div>
                </div>
            </section>
        @endif



        <section class="join color_white text-center">
            <div class="container">
                <div class="join-content">
                    <h4 class="h4-48B color_white">{!! str_replace('{$numbers}', \Helper::getJoinNumber(), $labels['join_numbers_creators']) !!}</h4>
                    <h5 class="h5-24R">{!! $labels['join_numbers_creators_description'] !!}</h5>
                    <a class="site-btn bg_white" href="{{ route('frontend.jointeam') }}">
                        <span>{{ $labels['get_started'] }}</span>
                        <svg width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 3.5C0.723858 3.5 0.5 3.72386 0.5 4C0.5 4.27614 0.723858 4.5 1 4.5L1 3.5ZM9.35355 4.35355C9.54882 4.15829 9.54882 3.84171 9.35355 3.64645L6.17157 0.464466C5.97631 0.269204 5.65973 0.269204 5.46447 0.464466C5.2692 0.659728 5.2692 0.976311 5.46447 1.17157L8.29289 4L5.46447 6.82843C5.2692 7.02369 5.2692 7.34027 5.46447 7.53553C5.65973 7.7308 5.97631 7.7308 6.17157 7.53553L9.35355 4.35355ZM1 4.5L9 4.5V3.5L1 3.5L1 4.5Z"
                                fill="white"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

    </div>
@endsection