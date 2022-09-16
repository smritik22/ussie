
@foreach ($spotlight as $area)

    <div class="col-md-2">
        <div class="property-areas-box">
            <a href="{{route('frontend.propertylist', ['id' => 'area']) . '?' . http_build_query(['area' => $area->slug])}}">
                @php
                    $area_name = $area->name;
                    if ($language_id > 1) {
                        $area_name = @$area->childdata[0]->name ?: $area->name;
                    }
                @endphp
                <img src="{{$area->image ? asset($image_url . $area->image) : asset('assets/dashboard/images/no_image.png')}}" alt="{{urldecode($area_name)}}" />
                <h4>{{urldecode($area_name)}}</h4>
            </a>
        </div>
    </div>
@endforeach