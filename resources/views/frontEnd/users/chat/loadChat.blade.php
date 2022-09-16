@foreach ($chat_list as $value)
    @php
        if($value->from_id == $user_id) {
            $chat_user_id = $value->to_id;
            $chat_user_detail = $value->receiverDetails;
        }else {
            $chat_user_id = $value->from_id;
            $chat_user_detail = $value->senderDetails;
        }

        $profile_image = "";
        if (@$chat_user_detail->profile_image) {
            $profile_image = asset('uploads/general_users/' . $chat_user_detail->profile_image);
        }
    @endphp
    <div class="message-user"  onclick="location.href = '{{route('frontend.conversation.list',['id' => encrypt($chat_user_detail->id)])}}'" >
        <div class="message-user-img-box">
            
            <img src="{{$profile_image}}" alt="img" class="chat-profile-image" />
        </div>
        <div class="message-user-name ">
            <h5>{{urldecode($chat_user_detail->full_name) ?: ""}} <span>{{date("H:i", strtotime($value->created_at))}}</span></h5>
            <p>{{$value->message}}</p>
        </div>
    </div>
@endforeach