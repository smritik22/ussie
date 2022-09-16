@extends('frontEnd.layout')
@section('content')
    <section class="inner-pading message">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4 d-lg-block d-none">
                    <div class="message-left" id="chatsAppend" data-limit_exceeded="0" data-page="1">
                        {{--  // chats   --}}
                        <div class="message-user active" {{--  onclick="location.href = '{{route('frontend.conversation.list',['id' => encrypt($chatUser->id)])}}'"  --}}>
                            <div class="message-user-img-box">
                                @php
                                    $profile_image = asset('assets/dashboard/images/no_image.png');
                                    if($chatUser->profile_image) {
                                        $profile_image = asset('uploads/general_users/' . $chatUser->profile_image);
                                    }
                                @endphp
                                <img src="{{$profile_image}}" alt="img" class="chat-profile-image" />
                            </div>
                            <div class="message-user-name ">
                                <h5>{{urldecode($chatUser->full_name) ?: ""}} <span>{{@$chatUserConvo->created_at ? date("H:i", strtotime($chatUserConvo->created_at)) : date('H:i')}}</span></h5>
                                <p>{{@$chatUserConvo->message ?: ""}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8" >
                    <div class="message-right-outer">
                    {{--  conversations  --}}
                        <div class="message-user-detail">
                            <div class="message-user">
                                <div class="message-user-img-box">
                                    <img src="{{$profile_image}}" alt="img" class="chat-profile-image">
                                </div>
                                <div class="message-user-name">
                                    <h5>{{@urldecode($chatUser->full_name) ?: ""}} </h5>
                                </div>
                            </div>
                        </div>
                        <div class="message-right">
                            <div class="message-right-box" id="conversationAppend" data-page_no="1" data-limit_exceeded="0">
                                
                            </div>
                        </div>
                        <div class="message-send">
                            <textarea type="text" id="txt_message" rows="1" placeholder="{{$labels['type_something']}}"></textarea>
                            <input type="hidden" name="to_id" id="to_id" value="{{encrypt($chatUser->id)}}">
                            <button type="button" id="txt_send" onclick="sendmessage(this)" style="background-color: #aab0bd;cursor : default"><img src="{{asset('assets/img/send.svg')}}" alt="icon" /></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <input type="hidden" name="chat_user_id" value="{{encrypt($chatUser->id)}}" id="chat_user_id">
@endsection
@push('after-scripts')
    <script>
        var uniquemsgid = 1;
        function sendmessage(element) {
            let to_id = $("#to_id").val();
            let txt_message = $("#txt_message").val();
            let __sendMessageUrl = "{{route('frontend.conversation.message.submit')}}";
            let msgid = uniquemsgid;

            if(txt_message) {
                let now = new Date();
                let datetime = now.getHours() + ":" + now.getMinutes();
                $('#conversationAppend').append(`<div class="messege-box-send new_message_sent sentmessage${msgid}"><p>${txt_message}</p><span class="datetime">${datetime}</span></div>`);
                $.ajax({
                    url : __sendMessageUrl,
                    data : {"_token" : "{{csrf_token()}}", "to_id" : to_id, "message" : txt_message},
                    type : 'post',
                    success : function (response) {
                        if(response.statusCode == 201) {
                        } else if(response.statusCode == 203) {

                        } else {
                            $("#txt_message").val('');
                            $(".sentmessage"+msgid+"").find('span.datetime').text(response.message_time);
                        }
                        uniquemsgid++;
                    },
                    error : function (err) {
                        
                    }
                });
            }

        }

        var is_convo_ready = 1;
        function fetchCoversationList() {
            if(is_convo_ready == 1) {
                is_convo_ready = 0;
                
                var __url = "{{route('frontend.conversation.list.fetch')}}";
                let page_no = $("#conversationAppend").data('page_no');
                let chat_user_id = $("#chat_user_id").val();

                $.ajax({
                    url : __url,
                    type : 'post',
                    dataType : 'json',
                    data : {"_token" : "{{csrf_token()}}", "page_no":page_no, "chat_user_id" : chat_user_id},
                    success : function (response) {
                        if(response.statusCode == 200) {
                            page_no = parseInt(page_no);
                            page_no++;
                            $("#conversationAppend").data('page_no',page_no);
                            if(page_no > response.total_page) {
                                $('#conversationAppend').data('limit_exceeded',1);
                            }

                            if(response.html && response.total_records > 0) {
                                $('#something_went_wrong').addClass('d-none');
                                $("#conversationAppend").prepend(response.html);
                            } else {
                                $('#something_went_wrong').removeClass('d-none');
                                $('#something_went_wrong').find('#message').text("{{$labels['no_data_is_available']}}");
                            }
                        }
                        else {
                            $('#something_went_wrong').removeClass('d-none');
                            $('#something_went_wrong').find('#message').text("{{$labels['something_went_wrong']}}");
                        }

                        is_convo_ready = 1;
                    },
                    error : function(err) {
                        $('#something_went_wrong').removeClass('d-none');
                        $('#something_went_wrong').find('#message').text("{{$labels['something_went_wrong']}}");
                    }
                });
            }
        }

        
        $().ready( function (e) {
            fetchChatLists();
            fetchCoversationList();
            $("#chatsAppend").on('scroll', function() {
                if ($("#chatsAppend").scrollTop() >= $('#chatsAppend').offset().top + $('.inner-pading').outerHeight() - window.innerHeight) {
                    if($('#chatsAppend').data('limit_exceeded') != 1) {
                        fetchChatLists();
                    }
                }
            });

            $(".message-right").stop().animate({ scrollTop: $(".message-right")[0].scrollHeight}, 1000);

            var changeSendBtnColorActive = "#2A4B9B";
            var changeSendBtnColorInactive = "#aab0bd";

            $('#txt_message').on('keyup', function (e) {
                if($(this).val()) {
                    $('#txt_send').css({'background-color' : changeSendBtnColorActive, 'cursor' : 'pointer', 'pointer-events' : 'all'});
                } else{
                    $('#txt_send').css({'background-color' : changeSendBtnColorInactive, 'cursor' : 'default', 'pointer-events' : 'none'});
                }
            });


            var lastScrollTop = 0;
            $('.message-right').on('scroll', function(e) {
                st = $(this).scrollTop();
                if (st < lastScrollTop) {
                    if($('#conversationAppend').data('limit_exceeded') != 1) {
                        fetchCoversationList();
                    }
                }
                lastScrollTop = st;
            });

        });



    </script>
@endpush