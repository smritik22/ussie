@extends('frontEnd.layout')
@section('content')
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('frontend.homePage') }}">{{ $labels['home'] }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <img src="{{ asset('assets/img/bread.svg') }}" alt="icon" />
                                {{ $language_id != 1 && @$cms_data->childdata[0]->page_name? $cms_data->childdata[0]->page_name: $cms_data->page_name }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section class="inner-pading about">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>
                        {{ $language_id != 1 && @$cms_data->childdata[0]->page_name? $cms_data->childdata[0]->page_name: $cms_data->page_name }}
                    </h2>
                    <div class="about-us-content">
                        {!! $language_id != 1 && @$cms_data->childdata[0]->description ? $cms_data->childdata[0]->description : $cms_data->description !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
