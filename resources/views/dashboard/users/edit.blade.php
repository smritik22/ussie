@extends('dashboard.layouts.master')
@section('title','Edit Profile')
@section('content')
    <div class="padding edit-package edit-user">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe3c9;</i> Edit Profile</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>Edit Profile</span>
                    <!-- <a href="javascript:void(0)">Edit Profile</a> -->
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{route("users")}}">
                            <!-- <i class="material-icons md-18">×</i> -->
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['usersUpdate',$Users->id],'method'=>'POST', 'files' => true])}}

                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">{!!  __('backend.fullName') !!}
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('name',$Users->name, array('placeholder' => '','class' => 'form-control','id'=>'name','required'=>'')) !!}
                    </div>
                    <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                @endif
                            </span>
                </div>

                <div class="form-group row">
                    <label for="email"
                           class="col-sm-2 form-control-label">{!!  __('backend.loginEmail') !!}
                    </label>
                    <div class="col-sm-10">
                        {!! Form::email('email',$Users->email, array('placeholder' => '','class' => 'form-control','id'=>'email','required'=>'')) !!}
                    </div>
                    <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('email'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                                @endif
                            </span>
                </div>

                {{--<div class="form-group row">
                    <label for="password"
                           class="col-sm-2 form-control-label">{!!  __('backend.loginPassword') !!}
                    </label>
                    <div class="col-sm-10">
                    <input type="password" name="password" class="form-control" autocomplete="new-password">

                    </div>
                </div>--}}

                <div class="form-group row">
                    <label for="photo_file"
                           class="col-sm-2 form-control-label">{!!  __('backend.topicPhoto') !!}</label>
                    <div class="col-sm-10">
                        @if($Users->photo!="")
                            <div class="row">
                                <div class="col-sm-12 images">
                                    <div id="user_photo" class="col-sm-4 box p-a-xs">
                                        <a target="_blank"
                                           href="{{ asset('uploads/users/'.$Users->photo) }}"><img
                                                src="{{ asset('uploads/users/'.$Users->photo) }}"
                                                class="img-responsive">
                                        </a>
                                        <br>
                                        <div class="delete">
                                            <a onclick="document.getElementById('user_photo').style.display='none';document.getElementById('photo_delete').value='1';document.getElementById('undo').style.display='block';"
                                            class="btn btn-sm btn-default">{!!  __('backend.delete') !!}</a>
                                            {{ $Users->photo }}
                                        </div>
                                    </div>
                                    <div id="undo" class="col-sm-4 p-a-xs" style="display: none">
                                        <a onclick="document.getElementById('user_photo').style.display='block';document.getElementById('photo_delete').value='0';document.getElementById('undo').style.display='none';">
                                            <i class="material-icons">&#xe166;</i> {!!  __('backend.undoDelete') !!}
                                        </a>
                                    </div>

                                    {!! Form::hidden('photo_delete','0', array('id'=>'photo_delete')) !!}
                                </div>
                            </div>
                        @endif

                        {!! Form::file('photo', array('class' => 'form-control','id'=>'photo','accept'=>'image/*')) !!}
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            {!!  __('backend.imagesTypes') !!}
                        </small>
                        <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('photo'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('photo') }}</span>
                                @endif
                            </span>
                    </div>
                </div>

                


                <div class="form-group row m-t-md">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                &#xe31b;</i> {!! __('backend.update') !!}</button>
                        <a href="{{route('adminHome')}}"
                           class="btn btn-default m-t"><i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}</a>
                    </div>
                </div>

                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
