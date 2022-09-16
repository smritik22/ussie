@extends('frontEnd.layout')
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/lightboxed.css') }}">
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('frontend.homePage')}}">{{$labels['home']}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><img src="{{asset('assets/img/bread.svg')}}" alt="icon" />{{$labels['my_ads']}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="inner-pading my-ads">
        <div class="container">
            <div class="row appendMyAds" data-page_no="1" data-limit_exceeded="0">
                <div class="col-12">
                    <h2>{{$labels['my_ads']}}</h2>
                </div>

                <div class="text-center d-none" id="something_went_wrong" style="padding: 20% 0 10% 0">
                    <span id='message'>{{ $labels['something_went_wrong'] }}</span>
                    <br><br>
                    <a href="#" id="refresh_page" onclick="event.preventDefault();location.reload();" class="forget-password d-none">
                        {{ $labels['refresh_page'] }}
                    </a>
                </div>
                
            </div>
        </div>
    </section>

@endsection
@push('after-scripts')
    <script>
        var is_ready = 1;
        function fetchData() {
            if(is_ready == 1) {
                is_ready = 0;

                var __url = "{{route('frontend.property.my_ads.fetch')}}";
                let page_no = $(".appendMyAds").data('page_no');
                console.log({page_no});
                $.ajax({
                    url : __url,
                    type : 'post',
                    dataType : 'json',
                    data : {"_token" : "{{csrf_token()}}", "page_no":page_no},
                    success : function (response) {
                        if(response.statusCode == 200) {
                            page_no = parseInt(page_no);
                            page_no++;
                            console.log( 'after plus , ',page_no);
                            $(".appendMyAds").data('page_no',page_no);
                            if(page_no > response.total_page) {
                                console.log('Done');
                                $('.appendMyAds').data('limit_exceeded',1);
                            }

                            if(response.html && response.total_records > 0) {
                                $('#something_went_wrong').addClass('d-none');
                                $(".appendMyAds").append(response.html);
                            } else {
                                $('#something_went_wrong').removeClass('d-none');
                                $('#something_went_wrong').find('#message').text("{{$labels['no_data_is_available']}}");
                            }
                        }
                        else {
                            $('#something_went_wrong').removeClass('d-none');
                            $('#something_went_wrong').find('#message').text("{{$labels['something_went_wrong']}}");
                        }

                        is_ready = 1;
                        console.log({is_ready});
                    }, 
                    error : function (err) {
                        $('#something_went_wrong').removeClass('d-none');
                        $('#something_went_wrong').find('#message').text("{{$labels['something_went_wrong']}}");
                    }
                });
            }
            
        }

        $().ready(function(){
            fetchData();

            $(window).on('scroll', function() {
                if ($(window).scrollTop() >= $('.appendMyAds').offset().top + $('.inner-pading').outerHeight() - window.innerHeight) {
                    console.log('working...');
                    if($('.appendMyAds').data('limit_exceeded') != 1) {
                        fetchData();
                    }
                }
            });
        });
    </script>
@endpush