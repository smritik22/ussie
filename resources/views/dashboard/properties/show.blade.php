@extends('dashboard.layouts.master')
@section('title', __('backend.view_property'))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

@endpush
@section('content')
@php
    use App\Models\Amenity;
    use App\Models\Label;
    use Illuminate\Support\Carbon;
@endphp
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view_property') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('properties') }}">{{ __('backend.properties') }}</a> / 
                   <span>{{__('backend.view_property')}}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['properties'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <div class="col-sm-10">
                            <h3>Property Details</h3>
                        </div>
                        <div class="col-sm-2 text-right">
                            <a href="javascript:void(0);" class="text-info view-property-images" data-propert_id="{{$property->id}}" id="view_property_images">View Images</a>
                        </div>
                    </div>

                    <br>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_name') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{$property->property_name}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_description') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->property_description ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_id') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{$property->property_id}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_listed_on') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->created_at ? Carbon::parse($property->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') : '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_address') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->property_address ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_area') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @urldecode($property->areaDetails->name) ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_governorate') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @urldecode($property->areaDetails->governorate->name) ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_country') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @urldecode($property->areaDetails->country->name) ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_type') !!} :</label>
                        <div class="col-sm-9">
                           <label>{!! @$property->propertyTypeDetails->type?:"-" !!}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_for') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->property_for ? (config('constants.PROPERTY_FOR.'.$property->property_for.'.name')) : '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_price') !!} :</label>
                        <div class="col-sm-9">
                           <label>{!! @$property->base_price ? Helper::getPropertyPriceByPrice($property->base_price) . ' ' . $currency_code :"-" !!}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_sqft_area') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->property_sqft_area ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_condition') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->propertyCondition->condition_text ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_completion_status') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->propertyCompletionStatus->completion_type ?: '-' }}</label>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_amenities') !!} :</label>
                        <div class="col-sm-9">
                            <div class="form-group row">
                                @if(@$property->property_amenities_ids)
                                    <ul class="amenities" style="list-style-type:square">
                                        @foreach (Amenity::where('status','!=',2)->where('parent_id','=',0)->whereIn('id', explode(',', $property->property_amenities_ids))->get() as $key => $amenity )
                                            {{--  @if($key > 0 && $key % 4 == 0)
                                                </div>
                                                <div class="form-group row">
                                            @endif
                                                <div class="col-sm-3">
                                                    <label>{{urldecode($amenity->amenity_name)}}</label>
                                                </div>  --}}
                                            <li>{{urldecode($amenity->amenity_name)}}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        {{--  @if(@$property->property_amenities_ids)
                            @foreach (Amenity::whereIn('id', explode(',', $property->property_amenities_ids))->get() as $key => $amenity )
                                @if($key > 0 && $key % 4 == 0)
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3"></label>
                                @endif
                                        <div class="col-sm-3">
                                            <label>{{urldecode($amenity->amenity_name)}}</label>
                                        </div>
                            @endforeach
                        @endif  --}}

                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_bedroom_type') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->bedroomTypeDetails->type ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_total_bedrooms') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->total_bedrooms ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_bathrooms_type') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->bathroomTypeDetails->type ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_total_bathrooms') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->total_bathrooms ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_total_toilets') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->total_toilets ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.latitude') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->property_address_latitude ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.longitude') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $property->property_address_longitude ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_user_favourite') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ $favourite_count }}</label>
                        </div>
                    </div>

                    <br>
                    <h3>Agent Details</h3>
                    <br>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_agent_name') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @urldecode($property->agentDetails->full_name) ?: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_agent_contact_number') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->agentDetails ? urldecode($property->agentDetails->country_code).' '. $property->agentDetails->mobile_number: '-' }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.property_agent_type') !!} :</label>
                        <div class="col-sm-9">
                           <label>{{ @$property->agentDetails->agent_type?config('constants.AGENT_TYPE.'.$property->agentDetails->agent_type.'.agent'): '-' }}</label>
                        </div>
                    </div>

                    {{--  config('constants.AGENT_TYPE.'.$agne.'.name')  --}}

                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3">
                        <a href="{{ route('properties') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                            <i class="material-icons">
                            &#xe5cd;</i> {!! __('backend.back') !!}
                        </a>
                    </div>
                </div>
                {{Form::close()}}
            </div>

            <!-- .modal -->
            <div id="image_show" class="modal fade" data-backdrop="true" data-keyboard="true">
                <div class="modal-dialog modal-lg" id="animate">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="row">
                                <div class="col-sm-10">
                                    <h5 class="modal-title">Property Images</h5>
                                </div>
                                <div class="col-sm-2 text-right">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-body text-center p-lg" id="images_append_div">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark-white p-x-md"
                                data-dismiss="modal">{{ __('backend.close') }}</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
            <!-- / .modal -->
            

        </div>
    </div>
@endsection
@push("after-scripts")
    <script src="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.js") }}"></script>
    <script src="{{ asset("assets/dashboard/js/summernote/dist/summernote.js") }}"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

 

    <script>

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({value: e.loaded, max: e.total});
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }

        $(document).ready(function(){
            $("#view_property_images").click(function(e){
                let self = $(this);
                let property_id = self.data('propert_id');
                let ajax_url = "{{route('property.getPropertyImages')}}";
                if(property_id){
                    $.ajax({
                        url  : ajax_url,
                        type : 'post',
                        dataType : 'html',
                        data : {
                            "_token" : "{{ csrf_token() }}",
                            "property_id" : property_id,
                        },
                        success: function(images){
                            if(images){
                                $("#images_append_div").html(images);
                                $("#image_show").modal('show');
                            }else{
                                $("#images_append_div").html('No Images Found');
                                $("#image_show").modal('show');
                            }
                        }
                    });
                }else{
                    location.reload();
                }
            })
        })
    </script>
@endpush
