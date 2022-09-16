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
                    <h2>{{ $labels['subscription_plan'] }}</h2>
                    <div class="add-new-property-box">
                        <div class="tab-content" id="myTabContent">
                            <div class="add-new-property-field">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="add-property-field">
                                            <label for="" class="form-label">{{ $labels['agent_type'] }}</label>
                                            <div class="row">
                                                @foreach (config('constants.AGENT_TYPE') as $key => $item)
                                                    <div class="col-6">
                                                        <div class="form-check form-check-inline me-0 ps-0">
                                                            <label class="form-check-label {{Auth::guard('web')->user()->user_type == $item['value'] ? 'checked1' : ''}}"
                                                                for="agentTypeRadio{{ $key }}">
                                                                <input class="form-check-input" type="radio"
                                                                    name="agent_type" id="agentTypeRadio{{ $key }}"
                                                                    value="{{ $item['value'] }}" {{Auth::guard('web')->user()->user_type == $item['value'] ? 'checked' : ''}}>
                                                                <span
                                                                    class="agent-type-name">{{ $labels[$item['label_key']] }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="select-plan">
                                            <p class="select-plan-heading">{{ $labels['select_plan'] }}</p>
                                            @foreach ($subscription_plans as $key => $value)
                                                <div class="subscription-plan-box-price" style="background-color : {{$value->bg_color}}">
                                                    <div class="form-check form-check-inline p-0 m-0">
                                                        <label class="form-check-label prime-plan"
                                                            for="subscriptionPlanRadio{{ $key }}">
                                                            <div class="subscription-plan-name">
                                                                <input class="form-check-input" type="radio"
                                                                    name="subscription_plan"
                                                                    id="subscriptionPlanRadio{{ $key }}"
                                                                    value="{{ $value->id }}" data-price="{{$value->plan_price}}">
                                                            </div>
                                                            <div class="subscription-plan-detail">
                                                                @php
                                                                    $duration = \Helper::getValidTillDate(date('Y-m-d H:i:s'),$value->plan_duration_value, $value->plan_duration_type);
                                                                @endphp
                                                                <h3>{!! $language_id != 1 && @$value->childdata[0]->plan_name ? $value->childdata[0]->plan_name : $value->plan_name !!} @if(!$value->is_free_plan)<span> {{$value->plan_price . ' ' . \Helper::getDefaultCurrency()}} / {{$duration['value'] . ' ' . $duration['label_value'] }}</span>@endif</h3>
                                                                <p>{!! $language_id != 1 && @$value->childdata[0]->plan_description ? $value->childdata[0]->plan_description : $value->plan_description !!}</p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <span class="error-login" id="subscription_plan_error"></span>

                                            <div class="select-plan-img">
                                                <img src="{{ asset('assets/img/login-icon.svg') }}" alt="icon" />
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="subscription-btn">
                                            <button class="comman-btn" id="pay_now">{{ $labels['pay_now'] }}</button>
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
    <script>

        function popup(mylink, windowname) {
            winopened = window.open(mylink, windowname); 
            winopened.onblur = () => winopened.focus();
            {{--  return false;   --}}
        } 

        function resultFetched(response) {
            winopened.close();
            console.info(response);

            if(response.Result == "{{config('constants.UPAY_RESULT.success')}}") {
                $('#PaymentID').val(response.PaymentID);
                $('#TrackID').val(response.TranID);
                {{--  $('#TranID').val(response.TranID);  --}}
                $('#trnUdf').val(response.trnUdf);
                $('#Auth').val(response.Auth);
            } else {
                Swal.fire({
                    icon: 'error',
                    iconColor : '#bb4f4f',
                    text: "{{$labels['payment_failed']}}",
                    showConfirmButton: true,
                    confirmButtonText: "{{$labels['ok']}}",
                });
            }
            // alert('Yes this is called now');
        }

        $().ready(function () {
            $("#pay_now").click( function(e) {
                let subscription_plan = $('input[name="subscription_plan"]:checked').data('price');
                var is_renew = $("#is_renew").val();
                
                // if(is_renew == 1) {
                //     total_amount = ("#renew_plan_submit").data('price');
                // } 
                // else if( is_renew == 2) {
                    if( typeof(subscription_plan) != "undefined" && subscription_plan !== null ) {
                        $("#subscription_plan_error").text("");
                        total_amount = subscription_plan;
                    } else {
                        $("#subscription_plan_error").text("{{$labels['please_select_plan']}}");
                        return false;
                    }
                // }

                $.ajax({
                    url : "{{route('frontend.payment')}}",
                    data : {"user_id" : "{{Auth::guard('web')->id()}}", "payable_amount" : total_amount, "language_id" : "{{$language_id}}", "is_web":1},
                    type : 'post',
                    dataType : 'json',
                    success : function (response) {
                        if(response[0].code == 1) {
                            popup(response[0].redirect_url, '_blank');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                iconColor : '#bb4f4f',
                                text: "{{$labels['something_went_wrong']}}",
                                showConfirmButton: true,
                                confirmButtonText: "{{$labels['ok']}}",
                            }).then(function (res) {
                                location.reload();
                            });
                            return false;
                        }

                        loader_hide();
                    },
                    error : function (err) {
                        loader_hide();
                        Swal.fire({
                            icon: 'error',
                            iconColor : '#bb4f4f',
                            text: "{{$labels['something_went_wrong']}}",
                            showConfirmButton: true,
                            confirmButtonText: "{{$labels['ok']}}",
                        }).then(function (res) {
                            location.reload();
                        });
                        return false;
                    }
                });
            });
        });
    </script>
@endpush