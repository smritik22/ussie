@php
    $arrayData = $chatData->toArray();
    $data = $arrayData['data'];
    $resArr = array_reverse($data);
@endphp
@foreach ($resArr as $value)
    <div class="{{ ($value['to_id'] == $user_id)?'messege-box-recived':'messege-box-send'}} ">
        <p>{{$value['message']}}</p>
        <span>{{\Helper::get_day_name($value['created_at'])}}</span>
    </div>
@endforeach