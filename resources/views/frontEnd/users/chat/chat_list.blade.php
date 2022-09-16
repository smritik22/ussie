@extends('frontEnd.layout')
@section('content')
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{route('frontend.homePage')}}">{{$labels['home']}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><img src="{{asset('assets/img/bread.svg')}}" alt="icon" />{{$labels['chat_message']}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="inner-pading message">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="message-left" id="chatsAppend" data-limit_exceeded="0" data-page="1">
                        <div class="text-center d-none" id="something_went_wrong" style="padding: 20% 0 10% 0">
                            <span id='message'>{{ $labels['something_went_wrong'] }}</span>
                            <br><br>
                            <a href="#" id="refresh_page" onclick="event.preventDefault();location.reload();" class="forget-password d-none">
                                {{ $labels['refresh_page'] }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('after-scripts')
    <script>
        
        $().ready( function (e) {
            fetchChatLists();
            $(".message-left").on('scroll', function() {
                if ($(".message-left").scrollTop() >= $('.message-left').offset().top + $('.inner-pading').outerHeight() - window.innerHeight) {
                    if($('#chatsAppend').data('limit_exceeded') != 1) {
                        fetchChatLists();
                    }
                }
            });
        });

    </script>
@endpush
