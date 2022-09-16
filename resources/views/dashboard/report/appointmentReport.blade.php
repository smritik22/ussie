@extends('dashboard.layouts.master')
@section('title', __('backend.appointment'))
@section('content')
<?php
    use App\Models\MainUser;
?>
    {{-- @if(@Auth::user()->permissionsGroup->webmaster_status)
        @include('dashboard.permissions.list')
    @endif --}}
    <style type="text/css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
    </style>
    <div class="padding">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.appointment') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="">{{ __('backend.appointment') }}</a>
                </small>
            </div>

            <div class="box-tool">
                <ul class="nav">
                    @if($appointment > 0)
                            <li class="nav-item inline">
                                <button type="button" class="btn btn-outline b-success text-success" id="filter_btn">
                                    <i class="fa fa-search"></i>
                                </button>
                            </li>
                    @endif
                </ul>
            </div>

                <div class="dker b-b displayNone" id="filter_div">
                    <div class="p-a">
                        {{Form::open(['method'=>'GET','id'=>'filter_form','target'=>''])}}
                        <div class="filter_div">
                            <div class="row">
                                <div class="col-md-4"></div>
                                    <div class="col-md-3 col-xs-6 m-b-5p">
                                       <input placeholder="Search For" class="form-control" id="find_q" autocomplete="off" name="find_q" type="text">
                                    </div>
                                <div class="col-md-1 col-xs-6 m-b-5p">
                                    <button class="btn white w-full" id="search-btn" type="submit"><i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box-header dker">              
                    <label>Start Date</label>
                    <input type="date" class="form-control" name="startdate" id="startdate" placeholder="DD/MM/YYYY" style="width: 250px;">

                    <label>End Date</label>
                    <input type="date" class="form-control" name="enddate" id="enddate" placeholder="DD/MM/YYYY" style="width: 250px;">


                <a href="javascript:void(0)" id="filter"><button type="button" class="btn btn-primary mr-2">Filter</button></a>
                <button type="button" onclick="window.location.reload()" class="btn btn-default mr-2">Clear</button>
         
                </div>

             
            @if($appointment == 0)
                <div class="row p-a">
                    <div class="col-sm-12">
                        <div class=" p-a text-center ">
                            {{ __('backend.noData') }}
                            <br>
                        </div>
                    </div>
                </div>
            @endif

            @if($appointment > 0)
                {{Form::open(['method'=>'post'])}}
                <div class="table-responsive">
                    <table class="table table-bordered m-a-0"  id="appointmentTable">
                        <thead class="dker">
                        <tr>
                            <th  class="width20 dker">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th style="width:120px;">{{ __('backend.patientname') }}</th>
                            <th style="width:120px;">{{ __('backend.doctorName') }}</th>
                            <th style="width:140px;">{{ __('backend.hospitalName') }}</th>
                            <th style="width:120px;">{{ __('backend.topicDate') }}</th>
                            <th style="width:110px;">{{ __('backend.appointmentTime')}}</th>
                            <th>{{ __('backend.appointmentStatus') }}</th>

                            <th class="text-center" style="width:50px;">{{ __('backend.status') }}</th>
                            <th class="text-center" style="width:250px;">{{ __('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($appointments as $type)
                            
                        @php

                            $user_name = MainUser::select('full_name')->where('id',$type->user_id)->first();

                            $doctor_name = MainUser::select('full_name')->where('id',$type->doctor_id)->first();

                            $hospital_name = MainUser::select('full_name')->where('id',$type->hospital_id)->first();

                            $time = date('h:i A', strtotime($type->time));
                            
                        @endphp
                            <tr>
                                <td class="dker"><label class="ui-check m-a-0">
                                        <input type="checkbox" name="ids[]" value="{{ $type->id }}"><i
                                            class="dark-white"></i>
                                        {{-- {!! Form::hidden('row_ids[]',$type->id, array('class' => 'form-control row_no')) !!} --}}
                                    </label>
                                </td>
                                <td class="h6">
                                    {{ $user_name->full_name}}
                                </td>

                                <td class="h6">
                                    {{ $doctor_name->full_name}}
                                </td>

                                <td class="h6">
                                    {{ $hospital_name->full_name}}
                                </td>

                                <td class="h6">
                                    {{ $type->date}}
                                </td>

                                <td class="h6">
                                    {{ $time}}
                                </td>

                                @if($type->appointment_status == 0)
                                    <td class="h6">Pending</td>
                                @elseif($type->appointment_status == 1)
                                    <td class="h6">Confirmed</td>
                                @else
                                    <td class="h6">Canceled</td>
                                @endif
        
                                <td class="text-center">
                                    <i class="fa {{ ($type->status==1) ? "fa-check text-success":"fa-times text-danger" }} inline"></i>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm success"
                                       href="{{route('appointment.show',$type->id)}}">
                                        <small><i class="material-icons">&#xe3c9;</i> {{ __('backend.view') }}
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
                                                <strong>[ {{ $type->full_name }} ]</strong>
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn dark-white p-x-md"
                                                    data-dismiss="modal">{{ __('backend.no') }}</button>
                                            <a href="{{ route("appointment.delete",["id"=>$type->id]) }}"
                                               class="btn danger p-x-md" method="post">{{ __('backend.yes') }}</a>
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
                            @if(@Auth::user()->permissionsGroup->webmaster_status)
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
                            @endif
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

        $('#filter').click(function(){
            var from_date = $('#startdate').val();
            var to_date = $('#enddate').val();
            if(from_date != '' &&  to_date != '')
            {
                if( to_date < from_date ) {
                    alert('Start date must be less than end date');
                }
                else 
                {              
                    $('#appointmentTable').DataTable().destroy();
                    load_data(from_date, to_date);
                }
            }
            else
            {
                alert('Both Date is required');
            }
        });

        load_data();
        function load_data(from_date = '', to_date = '')
        {
            $('#datatable_table').DataTable({
                processing: true,
                serverSide: false,
                columnDefs: [{
                    'bSortable': false,
                    'aTargets': [ 4,5 ]
            }],
            ajax: {
                url: "{{ route("report.appointment.date") }}",
                data:{from_date:from_date,to_date:to_date}
            },
            columns: [
                    { data: 'full_name',
                      name: 'full_name',
                      searchable:true,
                      orderable:true
                    },
                    { 
                      data: 'full_name', 
                      name: 'full_name',
                      searchable:true,
                      orderable:true
                    },
                    { 
                      data: 'full_name',
                      name: 'full_name'
                    },
                    { 
                      data: 'date',
                      name: 'date'
                    },
                    { 
                      data: 'time',
                      name: 'time' 
                    }, 
                    {
                      data: 'appointment_status',
                      name: 'appointment_status'
                    },             
                    { 
                      data: 'action',
                    }
                ],
                order : ['3', 'desc'],
                dom: 'Blfrtip',
                buttons: [
                      {
                          extend: 'excel',
                          text: 'Export Excel',
                          exportOptions: {
                            columns: [ 0, 1, 2, 3]
                          },
                          title : 'InstruShare | User Report'
                      }
                ]
            });
        }

    </script>
@endpush
