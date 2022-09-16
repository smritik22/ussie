@extends('dashboard.layouts.master')
@section('title','Certificate')
@section('content')
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="padding">
        <div class="box">

            <div class="box-header dker">
                <h3>Certificate</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>Certificate</span>
                </small>
            </div>

            {{--<div class="box-tool">
                <ul class="nav">
                @if(@Auth::user()->permissionsGroup->webmaster_status)
                    <li class="nav-item inline">
                        <a class="btn btn-fw primary" href="{{route('certificate.create')}}">
                            <i class="material-icons">&#xe02e;</i>
                            &nbsp; New Certificate Templete
                        </a>
                    </li>
                @endif
                </ul>
            </div>--}}

            @if($countcertificates == 0)
                <div class="row p-a">
                    <div class="col-sm-12">
                        <div class=" p-a text-center ">
                            {{ __('backend.noData') }}
                            <br>
                            @if(@Auth::user()->permissionsGroup->webmaster_status)
                                <br>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($countcertificates > 0)
                {{Form::open(['method'=>'post'])}}
                <div class="table-responsive">
                    <table class="table table-bordered m-a-0" id="certificate">
                        <thead class="dker">
                        <tr>
                            <th>{{ __('backend.topicName') }}</th>
                            <th>{{ __('backend.subject') }}</th>
                            
                            <th class="text-center" style="width:200px;">{{ __('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody id="emailTemplateTable">

                        @foreach($certificates as $type)
                      
                            <tr>
                                <td class="h6">
                                    {{ $type->title}}
                                </td>

                                <td class="h6">
                                    {{ $type->subject}}
                                </td>
        

                                <td class="text-center">
                                 <a class="btn btn-sm danger"
                                       href="{{route('certificateGenerator',$type->id)}}">
                                        <small>Generate
                                        </small>
                                    </a><br>
                                    <a class="btn btn-sm success"
                                       href="{{route('certificate.edit',$type->id)}}">
                                        <small><i class="material-icons">&#xe3c9;</i>
                                        </small>
                                    </a>

                                </td>
                            </tr>
                            <!-- .modal -->
                            <div id="m-{{ $type->id }}" class="modal fade" data-backdrop="true">
                                <div class="modal-dialog" id="animate">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                                        </div>
                                        <div class="modal-body text-center p-lg">
                                            <p>
                                                {{ __('backend.confirmationDeleteMsg') }}
                                                <br>
                                                <strong>[ {{ $type->title }} ]</strong>
                                            </p>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <!-- / .modal -->
                        @endforeach

                        </tbody>
                    </table>

                </div>
                <footer class="dker p-a">
                    <div class="row">
                        <div class="col-sm-3 hidden-xs">
                            <!-- .modal -->
                            <div id="m-all" class="modal fade" data-backdrop="true">
                                <div class="modal-dialog" id="animate">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                                        </div>
                                        <div class="modal-body text-center p-lg">
                                            <p>
                                                {{ __('backend.confirmationDeleteMsg') }}
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn dark-white p-x-md"
                                                    data-dismiss="modal">{{ __('backend.no') }}</button>
                                            <button type="submit"
                                                    class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <!-- / .modal -->
                           <!--  @if(@Auth::user()->permissionsGroup->webmaster_status)
                                <select name="action" id="action" class="form-control c-select w-sm inline v-middle"
                                        required>
                                    <option value="">{{ __('backend.bulkAction') }}</option>
                                    <option value="activate">{{ __('backend.activeSelected') }}</option>
                                    <option value="block">{{ __('backend.blockSelected') }}</option>
                                    <option value="delete">{{ __('backend.deleteSelected') }}</option>
                                </select>
                                <button type="submit" id="submit_all"
                                        class="btn white">{{ __('backend.apply') }}</button>
                                <button id="submit_show_msg" class="btn white" data-toggle="modal"
                                        style="display: none"
                                        data-target="#m-all" ui-toggle-class="bounce"
                                        ui-target="#animate">{{ __('backend.apply') }}
                                </button>
                            @endif -->
                        </div>

                      
                        <div class="col-sm-6 text-right text-center-xs">
                           
                        </div>
                    </div>
                </footer>
                {{Form::close()}}
            @endif
        </div>
    </div>
@endsection
@push("after-scripts")
 <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $('#certificate').DataTable({});
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $("#action").change(function () {
            if (this.value == "delete") {
                $("#submit_all").css("display", "none");
                $("#submit_show_msg").css("display", "inline-block");
            } else {
                $("#submit_all").css("display", "inline-block");
                $("#submit_show_msg").css("display", "none");
            }
        });


        $("#filter_btn").click(function () {
            $("#filter_div").slideToggle();
        });

        $("#find_q").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#doctorTypeTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>
@endpush
