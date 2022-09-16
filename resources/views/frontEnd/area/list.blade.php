@extends('frontEnd.layout')
@section('content')
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('frontend.homePage') }}">{{ $labels['home'] }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><img
                                    src="{{ asset('assets/img/bread.svg') }}" alt="icon" />{{ $labels['property_areas'] }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pading property property-page">
        <div class="container">
            <div class="row" id="area_listing">
                <div class="col-12" id="title_header">
                    <h2>{{ $labels['property_areas'] }}</h2>
                </div>

            </div>
        </div>
        <div class="text-center">
            <span id="show_data_text"></span>
            <a onclick="loadMore(event)" id="loadmore">
                {{--  <i class="fa fa-spinner" aria-hidden="true"></i>  --}}
            </a>
        </div>
        <input type="hidden" id="limit_page_exceeded" value="0">
        <input type="hidden" id="page_number" value="1">
    </section>
@endsection
@section('footerInclude')
    <script>

        function show_area_fetching(){
            console.log('showing');
            $("#show_data_text").text("{{$labels['fetching_data']}}")
        }

        function hide_area_fetching() {
            console.log('Hiding');
            $("#show_data_text").empty();
        }

        var ready = 1;

        function loadAreaList(page_no) {
            if(ready && $('#limit_page_exceeded').val() != 1){
                ready = 0;
                show_area_fetching();
                let url = "{{ route('frontend.getAreas') }}";
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "page_no": page_no
                    },
                    success: function(result) {
    
                        if (result == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: "{{ $labels['something_went_wrong'] }}",
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                        } else if (result == 2) {
                            limit_page_exceeded = 1;
                            $("#limit_page_exceeded").val(limit_page_exceeded);
                        } else {
                            $(result).appendTo("#area_listing");
                        }
    
                        page_no = parseInt(page_no);
                        page_no++;
    
                        $("#page_number").val(page_no);
                        hide_area_fetching();
                        ready = 1;
                    },
                    error: function(error) {
                        hide_area_fetching();
                        ready = 1;
                    }
                });
            }
        }

    $(document).ready(function() {
        loadAreaList(1);

        $(window).on('scroll', function() {
            {{--  console.log('window.innerHeight', window.innerHeight);
            console.log('$(window).scrollTop()', $(window).scrollTop());
            console.log("$('.section-pading').offset().top", $('.section-pading').offset().top);
            console.log("$('#title_header').outerHeight()", $('#title_header').outerHeight());
            console.log('======================================================================');  --}}

            if ($(window).scrollTop() >= $('.section-pading').offset().top + $('#title_header').outerHeight() - window.innerHeight) {
                if($('#limit_page_exceeded').val() != 1) {
                    let page = $('#page_number').val();
                    loadAreaList(page);
                }
            }
        });

    });

    function loadMore(e){
        e.preventDefault();
        loadAreaList($('#page_number').val());
    }
    </script>
@endsection
