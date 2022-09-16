<?php
use App\Helpers\Helper;
$language_id = Helper::currentLanguage()->id;
$labels = Helper::LabelList($language_id);

$PageTitle = $labels['404_not_found'];
$PageDescription = '';
$PageKeywords = '';
$WebmasterSettings = '';

?>
@extends('frontEnd.layout')
@section('content')
    <section class="inner-pading thank-you-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="thank-you">
                        <h2>{{$labels['404']}}</h2>
                        <h1>{{$labels['oops_that_page_cant_be_found']}}</h1>

                        <p>{{$labels['the_page_you_were_looking_for_not_exists']}}</p>
                        <a href="{{route('frontend.homePage')}}" class="comman-btn">{{$labels['back_to_home']}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
