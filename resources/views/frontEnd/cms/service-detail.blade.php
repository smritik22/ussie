@extends('frontEnd.layout')
@section('content')

    <div class="site_content progrm_details_page">
        <section class="banner">
            <div class="container">
                <div class="banner-content">
                    <h1 class="main-title color_dark">{!! $service_details['name_show'] !!}</h1>
                </div>
            </div>
        </section>
        <section class="program-details">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="details-img">
                            {!! Html::image($service_details['document_two'], $service_details['name_show'], ['title' => $service_details['name_show']]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="details-content">
                            <div class="details-content-area">
                                {!! $service_details['description'] !!}
                                {{--  <p class="paragraph-16r color_light_gray">I'm a paragraph. I’m a great place for you to tell
                                    a story and let your users know a little more about you. I'm a paragraph. I’m a great
                                    place for you to tell a story and let your users know a little more about you.</p>
                                <h2 class="section-sub-title color_dark">Lorem, ipsum dolor.</h2>
                                <ul class="paragraph-16r color_light_gray mb-0">
                                    <li>Lorem, ipsum dolor.</li>
                                    <li>Lorem ipsum dolor sit.</li>
                                    <li>Lorem, ipsum dolor.</li>
                                    <li>Lorem ipsum dolor sit amet.</li>
                                    <li>Lorem ipsum dolor sit amet consectetur adipisicing. Lorem ipsum dolor sit amet.
                                        Lorem ipsum, dolor sit amet consectetur adipisicing elit.</li>
                                </ul>  --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{--  <section class="program-details-other-content">
            <div class="container">
                <h2 class="section-sub-title color_dark">Lorem, ipsum dolor.</h2>
                <ul class="paragraph-16r color_light_gray mb-0">
                    <li>Lorem ipsum dolor sit amet.</li>
                    <li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
                    <li>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, a.</li>
                </ul>
            </div>
        </section>  --}}
    </div>
@endsection
