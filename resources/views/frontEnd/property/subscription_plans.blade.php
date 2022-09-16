@extends('frontEnd.layout')
@section('content')
    <style>
        .swal2-confirm.swal2-styled {
            padding: 12px 22px 12px 22px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #2A4B9B;
            line-height: 24px;
            background-color: #2A4B9B;
            color: #fff;
            display: inline-block;
            transition: all .4s;
        }

        .swal2-styled.swal2-confirm:focus {
            box-shadow: none;
        }

        .swal2-styled.swal2-deny:focus {
            box-shadow: none;
        }

        .swal2-styled.swal2-cancel:focus {
            box-shadow: none;
        }

        .swal2-deny.swal2-styled {
            padding: 12px 22px 12px 22px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #bb4f4f;
            line-height: 24px;
            background-color: #bb4f4f;
            color: #fff;
            display: inline-block;
            transition: all .4s;
        }

        .swal2-cancel.swal2-styled {
            padding: 12px 22px 12px 22px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #555555;
            line-height: 24px;
            background-color: #555555;
            color: #fff;
            display: inline-block;
            transition: all .4s;
        }

    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/lightboxed.css') }}">

    <section class="add-new-property">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{$labels['my_subscription_plan']}}</h2>
                    <div class="add-new-property-box">
                        <div class="tab-content" id="myTabContent">
                            <div class="add-new-property-field">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="select-plan">
                                            @if ($subscription_plans->count() > 0)
                                                @foreach ($subscription_plans as $key => $plan)    
                                                    <div class="active-plan-box {{$key > 0 ? 'mt-md-5 mt-4' : ''}}">
                                                        <div class="active-plan-detail">
                                                            <img src="{{asset('assets/img/icon_premium.svg')}}" alt="icon" />
                                                            <div class="active-plan-detail-content">
                                                                @php
                                                                    $plan_name = ($language_id != 1 && @$plan->plan_name_ar) ? $plan->plan_name_ar : $plan->plan_name;

                                                                    $duration = \Helper::getValidTillDate(date('Y-m-d H:i:s'),$plan->plan_duration_value, $plan->plan_duration_type);
                                                                @endphp
                                                                <h4>{{$plan_name}}</h4>
                                                                <h5>@if(!$plan->is_free_plan)<span> {{$plan->plan_price . ' ' . \Helper::getDefaultCurrency()}} / {{$duration['value'] . ' ' . $duration['label_value'] }}</span>@endif</h5>
                                                                @if ($plan->end_date > date('Y-m-d H:i:s'))
                                                                    <p>{{str_replace( '{$DATE}', date('jS F, Y',strtotime($plan->end_date)) ,$labels['expires_on'])}}</p> 
                                                                @else
                                                                    <p>{{$labels['expired']}}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if ($plan->end_date > date('Y-m-d H:i:s'))
                                                        <div class="progress">
                                                            <div class="progress-bar w-75" role="progressbar" aria-valuenow="75"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <h3>{{$labels['ads']}} {{ (@$plan->propertiesSubscribed->count() ?: 0) .'/'. ((@$plan->no_of_plan_post?:0) + (@$plan->no_of_default_featured_post?:0))}}</h3>
                                                        <button type="button" data-id="{{$plan->id}}" onclick="cancelPlan(this,{{$plan->id}})">{{$labels['cancel_plan']}}</button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                
                                                <div class="text-center" id="something_went_wrong" style="padding: 20% 0 10% 0">
                                                    <span id='message'>{{ $labels['no_data_is_available'] }}</span>
                                                    {{--  <br><br>  --}}
                                                    {{--  <a href="#" id="refresh_page" onclick="event.preventDefault();location.reload();"
                                                        class="forget-password d-none">
                                                        {{ $labels['refresh_page'] }}
                                                    </a>  --}}
                                                </div>

                                            @endif

                                            {{--  <div class="active-plan-box mt-md-5 mt-4">
                                                <div class="active-plan-detail">
                                                    <img src="assets/img/icon_premium.svg" alt="icon" />
                                                    <div class="active-plan-detail-content">
                                                        <h4>Basic Plan</h4>
                                                        <h5>150 KD/ year</h5>
                                                        <p>Expired</p>
                                                    </div>
                                                </div>
                                            </div>  --}}


                                            <div class="select-plan-img">
                                                <img src="assets/img/login-icon.svg" alt="icon" />
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/frontend/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/additional.min.js') }}"></script>
    <script>
        function cancelPlan(element, usersubId) {
            Swal.fire({
                title: "{{$labels['are_you_sure_you_want_to_cancel_this_plan']}}",
                iconColor : '#2A4B9B',
                showConfirmButton: true,
                confirmButtonText: "{{$labels['yes']}}",
                showDenyButton: true,
                denyButtonText: "{{$labels['no']}}",
            }).then((result) => {
                if (result.isConfirmed) {
                    loader_show();
                    event.preventDefault();
                    let __url  = "{{route('frontend.usersubscription.cancelplan')}}";
                    $.ajax({
                        url : __url,
                        type : 'post',
                        data : {'_token' : "{{csrf_token()}}", "plan_id": usersubId},
                        success : function (response) {
                            loader_hide();
                            if(response.statusCode == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    text: response.message,
                                    iconColor : '#2A4B9B',
                                    showConfirmButton: true,
                                    confirmButtonText: "{{$labels['ok']}}",
                                }).then(function(e){
                                    location.reload();
                                });
                            }
                            else {
                                Swal.fire({
                                    icon: 'error',
                                    text: response.message,
                                    iconColor : '#bb4f4f',
                                    showConfirmButton : true,
                                    confirmButtonText: "{{$labels['ok']}}",
                                })
                            }
                        },
                        error : function (error) {
                            loader_hide();
                            Swal.fire({
                                icon: 'error',
                                text: response.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : true,
                                confirmButtonText: "{{$labels['ok']}}",
                            })
                        }
                    });
                }
              })

            
        }
    </script>
@endpush