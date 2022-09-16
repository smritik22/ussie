@extends('frontEnd.layouts.app')
@section('title','Dashboard')
@section('content')
    @section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/custom/css/dataTables.bootstrap5.min.css') }}">
    <link href="{{ asset('assets/frontend/css/datepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap-select.min.css') }}">
    @endsection
    @section('data')
    <div class="content-body profile-main-body">
        <div class="body-head">
            <h2>User Profile</h2>
        </div>
        <div class="body-content">
              @include('frontEnd.shared.messages')
            <div class="profile-body">
                <div class="body-head">
                    <h3>User Information</h3>
                </div>
                {{Form::open(['route'=>['frontend.user.update'],'method'=>'POST','id'=>'profileSubmit', 'files' => true])}}
                    <div class="body-content">
                        
                            <div class="col-md-12 form-group upload-mailn">
                                <div class="form-group upload-file-image">
                                    <div id="imagefile_old">
                                     @if(isset($user) && ($user->profile!=""))
                                        @if($user->user_type == 1)
                                            <img id="image_old" src="{{ asset('uploads/users/'.$user->profile) }}" class="img-responsive" width="100">
                                        @else
                                            <img id="image_old" src="{{ asset('uploads/'.$schoolData->location_id.'/users/'.$user->profile) }}" class="img-responsive" width="100">
                                        @endif
                                    @else
                                        <img id="image_old" src="{{ asset('assets/frontend/preview/student-no-image.jpg') }}" class="img-responsive" width="100">
                                    @endif
                                    </div>
                                    <div id="imagefile" style="display: none;">
                                        <img id="image" src="{{ asset('assets/frontend/images/no_image.jpg') }}" class="thumbnail" width="100"/> 
                                    </div>
                                    <div class="upload-file">
                                        <div class="choose-file">
                                            <button class="upload">Upload</button>
                                            <input type="file" name="profile" accept="image/*" onchange="readURL(this);" id="profile">
                                            
                                        </div>
                                        <span class="text-danger">
                                            @if ($errors->has('profile'))
                                            <strong>{{ $errors->first('profile') }}</strong>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="body-head">
                    </div>
                    <div class="body-content user-profile-content">
                       
                            <div class="col-md-12 form-group contact">
                                <div class="col-sm-6 email">
                                    <label for="">Frist Name</label>
                                    {!! Form::text('first_name',old('first_name', isset($user->first_name) ? $user->first_name : ''), ['id' => 'first_name','autocomplete'=>"off"]) !!}
                                     <span class="text-danger">
                                        @if ($errors->has('first_name'))
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                        @endif
                                    </span>
                                </div>
                                <div class="col-sm-6 number">
                                    <label for="">Last Name</label>
                                    {!! Form::text('last_name',old('last_name', isset($user->last_name) ? $user->last_name : ''), ['id' => 'last_name','autocomplete'=>"off"]) !!}
                                    <span class="text-danger">
                                        @if ($errors->has('last_name'))
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12 form-group contact">
                                <div class="col-sm-6 email">
                                    <label for="">Email</label>
                                    {!! Form::email('email',old('email', isset($user->email) ? $user->email : ''), ['id' => 'email','autocomplete'=>"off"]) !!}
                                    <span class="text-danger">
                                        @if ($errors->has('email'))
                                        <strong>{{ $errors->first('email') }}</strong>
                                        @endif
                                    </span>
                                </div>
                                <div class="col-sm-6 number">
                                    <label for="">Mobile Number</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="location-desc">
                                                  {!! Form::select('country_code', $country,old('country_code',isset($user->country_code)? $user->country_code : ''), ['id' => 'country_code','class' => '']) !!}
                                                <img src="{{ asset('assets/frontend/images/arrow.png') }}" alt="" class="arrow-image-location">
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                        {!! Form::text('phone',old('phone', isset($user->phone) ? $user->phone : ''), ['id' => 'phone','autocomplete'=>"off"]) !!}
                                            <span class="text-danger">
                                                @if ($errors->has('phone'))
                                                <strong>{{ $errors->first('phone') }}</strong>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="footer-btn user-btn">
                                <button type="submit">Update</button>
                            </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <script type="text/javascript">
        function readURL(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#image').attr('src', e.target.result);
              $("#remove").remove();
              $('<button class="remove" id="remove">Remove</button>').appendTo('.choose-file');
              $("#imagefile").css({'display':'block'});
              $("#imagefile_old").css({'display':'none'});
            };
            reader.readAsDataURL(input.files[0]);
          }
        }
        $('body').on('click','#remove',function(){
            $('#image').attr('src','');
            $("#remove").remove();
            $('#imagefile').css({'display':'none'});
            $("#profile").val("");
            $('#imagefile_old').css({'display':'block'});
        });
    </script>
    @endsection
@endsection