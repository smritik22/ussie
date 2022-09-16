@extends('dashboard.layouts.master')
@section('title', __('backend.ride_management'))
@section('content')
<style type="text/css">
    .w-sm{
            width: 126px !important;
    }
    #export{
        margin-top: 55px;
    }
    .ride_status_font{
        font-style: normal !important;
    }
</style>
<style type="text/css">
    .penging{
        color: #F92E3D;
    }
    .request_accept{
        color: #F92E3D;
    }
    .reject{
        color: #F92E3D;
    }
    .arrived_at_pickup{
        color: #F92E3D;
    }
    .picked_up_customer{
        color: #F92E3D;
    }   
    .arrived_at_destination{
        color: #F92E3D;
    }
    .complated{
        color: #F92E3D;
    }
    .cancelled_by_customer{
        color: #F92E3D;
    }
    .cancelled_by_admin{
        color: #F92E3D;
    }
    .no_driver_avilable{
        color: #F92E3D;
    }
</style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <div class="padding school-report-manage list-school">
        <div class="box">
            <div id="success_file_popup"></div>
            <div class="box-header dker">
                <h3>{{ __('backend.ride_management') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.ride_management') }}</span>
                </small>
            </div>
            
            {{ Form::open(['route' => 'ride.export', 'method' => 'post', 'id' => 'export']) }}
            <div class="row p-a pull-right" style="margin-top: -70px;">

                <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : '' }}"
                    id="export_start_date">
                <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : '' }}" id="export_end_date">

                <input type="hidden" name="ride_status" value="" id="export_ride_status_check">
                <input type="hidden" name="property_for" value="" id="export_property_for">

                <div class="col-sm-12">
                    <a class="btn btn-fw primary export-form" href="javascript:void(0)">
                        Export
                    </a>
                </div>
            </div>
            {{ Form::close() }}

            {{-- {{ Form::open(['method' => 'post', 'id' => 'filter_form']) }}
                <div class="box-header dker">
                    <label>Start Date</label>
                    <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY"
                        style="width: 250px;" value="{{ isset($startdate) ? $startdate : '' }}">

                    <label>End Date</label>
                    <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY"
                        style="width: 250px;" value="{{ isset($enddate) ? $enddate : '' }}">


                    <a href="javascript:void(0)" id="filter"><button type="button"
                            class="btn btn-primary mr-2">Filter</button></a>
                    <a onclick="location.reload();" id="filter">
                        <button type="button" class="btn btn-danger mr-2">Clear</button>
                    </a>
                </div>

            {{ Form::close() }} --}}

            {{ Form::open(['method' => 'post', 'id' => 'filter_form']) }}
            <div class="box-header dker" style="margin-bottom: 10px">
                {{-- <h3>Filters</h3>
                <hr>
                <br> --}}
                <div class="dflex" style="margin-top:10px;display: flex;">
                    {{-- <label>Start Date</label>
                    <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY" readonly 
                    style="width: 200px;height: 30px" value="{{ isset($startdate) ? $startdate : '' }}">

                    <label>End Date</label>
                    <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY" readonly 
                    style="width: 200px;height: 30px" value="{{ isset($enddate) ? $enddate : '' }}"> --}}

                    <div class="">
                        <label class="" style="margin-left: 15px">From Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY"
                            readonly style="width: 200px;height: 30px" value="{{ isset($startdate) ? $startdate : '' }}">
                    </div>
                    <div class="" style="margin-left: 10px">
                        <label style="margin-left: 15px">To Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY"
                            readonly style="width: 200px;height: 30px" value="{{ isset($enddate) ? $enddate : '' }}">
                    </div>
                   
                     <div style="margin-left: 10px;">
                        <label>Select Ride Status</label> <br>
                        <select name="ride_status_check" id="ride_status_check" class="form-control">
                            <option value="">Select Status</option>
                            <option value="1">Pending</option>
                            <option value="2">Request Accept</option>
                            <option value="3">Reject</option>
                            <option value="4">Arrived At Pickup</option>
                            <option value="5">Picked Up Customer</option>
                            <option value="6">Arrived At Destination</option>
                            <option value="7">Completed</option>
                            <option value="8">Cancelled by customer</option>
                            <option value="9">Cancelled by admin</option>
                            <!-- <option value="">Select</option> -->
                            
                        </select>
                    </div>
                    <div class="" style="padding-top:20px">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2">Clear</button>
                        </a>
                    </div>

                </div>
            </div>
            {{ Form::close() }}

            {{ Form::open(['route' => 'ride.updateAll', 'method' => 'post', 'id' => 'updateAll']) }}

                <div class="bulk-action">
                    <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                        <option value="no">{{ __('backend.bulkAction') }}</option>
                      <!--   <option value="approve">{{ __('backend.approveSelected') }}</option>
                        <option value="reject">{{ __('backend.rejectSelected') }}</option>
                        <option value="activate">{{ __('backend.activeSelected') }}</option>
                        <option value="block">{{ __('backend.inactiveSelected') }}</option> -->
                        <option value="9">{{ __('backend.cancleSelected') }}</option>
                    </select>
                    <button type="submit" class="btn white">{{ __('backend.apply') }}</button>
                </div>

            <div class="table-responsive school-report-form">
                <table class="table table-bordered m-a-0" id="property_report">
                    <thead class="dker">
                        <tr>
                            <th class="width20 dker">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>{{ __('backend.passenger_name') }}</th>
                            <th>{{ __('backend.driver_name') }}</th>
                           <!-- <th>{{ __('backend.Vehicle_type') }}</th> -->
                            <th>{{ __('backend.pickup_licaiotn') }}</th>
                            <th>{{ __('backend.drop_licaiotn') }}</th>
                            <th>{{ __('backend.estimated_fare') }}</th>
                            <th>{{ __('backend.actual_fare') }}</th>
                            <th>{{ __('backend.ride_km') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
             {{ Form::close() }}
            <div class="white-space"></div>

        </div>
    </div>
@endsection
@push('after-scripts')
    <!--  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> -->

    <script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();

            function load_data(startdate, enddate) {

                var action_url = "{!! route('ride.anyData') !!} ";

                var dataTable = $('#property_report').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': [0, 8]
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                                "ride_status": $("#ride_status_check").val().toLowerCase(),
                               
                            });
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        
                        
                        {
                            data: 'username',
                            name: 'username',

                        },
                        {
                            data: 'drivername',
                            name: 'drivername',

                        },
                        {
                            data: 'pickup_address',
                            name: 'pickup_address',

                        },
                        {
                            data: 'dest_address',
                            name: 'dest_address',

                        },
                        {
                            data: 'estimate_fare',
                            name: 'estimate_fare',

                        },
                        {
                            data: 'actual_fare',
                            name: 'actual_fare',

                        },
                        {
                            data: 'ride_km',
                            name: 'ride_km',

                        },
                        {
                            data: 'status',
                            name: 'status',

                        },
                        {
                            data: 'options',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: ['0', 'DESC']
                });

                // $("#property_type_select").on('change', function(e) {
                //     e.preventDefault();
                //     $("#export_property_type").val($("#property_type_select").val().toLowerCase());
                //     dataTable.draw();
                // });

               $("#ride_status_check").on('change', function(e) {
                    e.preventDefault();
                    $("#export_ride_status_check").val($("#ride_status_check").val().toLowerCase());
                    dataTable.draw();
                });

                $('#startdate, #enddate').change(function() {
                    {{-- let start_date = $('#startdate').val();
                    let end_date = $('#enddate').val();
                    load_data(start_date, end_date); --}}

                    $("#export_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_end_date").val($("#enddate").val().toLowerCase());
                    
                    dataTable.draw();
                });
            }
        });

    $(document).on('submit', '#updateAll', function(e) {

            e.preventDefault();
            var allVals = [];
            var check = false;

            var select_row = "{{ __('backend.select_row') }}";

            var select_status = "{{ __('backend.select_status') }}";

            var type = $(document).find('#action').val();

            if (type == 'no') {
                // alert('hello2')
                $(document).find('#alert_confirm').modal('show');
                $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_status);

            } else {
                // alert('hello')
                $(".has-value:checked").each(function() {

                    var idvalue = $(this).attr('data-id');
                    if (typeof idvalue === "undefined") {

                    } else {
                        allVals.push(idvalue);
                    }
                });

                if (allVals.length <= 0) {

                    $(document).find('#alert_confirm').modal('show');
                    $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_row);
                } else {
                    var msg = "";

                    if (type == 9) {
                        msg = "Are you sure you want to cancle this ride?";

                    }
                     else {
                        msg = "Are you sure you want to delete this ride?";
                    }

                    $(document).find('#default_confirm').modal('show');
                    $(document).find('#default_confirm').find('.dynamic_message').text(msg);
                    var join_selected_values = allVals.join(",");
                    $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
                    $(document).find('#default_confirm').find('.checkbox_type').val(type);

                }

            }
        });

         $(document).on('click', '.yes_click', function(e) {
            var join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
            var type = $(document).find('#default_confirm').find('.checkbox_type').val();
            var csrf = "{{ csrf_token() }}";
            ajaxUpdateAll(csrf, join_selected_values, type);
        });
        $(document).on('click', '.delete-package', function(e) {
            e.preventDefault();
            var package_id = $(this).attr('data-id');
            var allVals = [];
            allVals.push(package_id);
            var type = 3;
            var msg = "Are you sure you want to delete?";

            $(document).find('#default_confirm').modal('show');
            $(document).find('#default_confirm').find('.dynamic_message').text(msg);
            var join_selected_values = allVals.join(",");
            $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
            $(document).find('#default_confirm').find('.checkbox_type').val(type);
        });

        function ajaxUpdateAll(csrf, join_selected_values, type) {
            // alert(join_selected_values);
            $.ajax({
                url: "{{ route('ride.updateAll') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: 'ids=' + join_selected_values + '&status=' + type,
                success: function(data) {

                    if (data.success == true) {
                        $('#success_file_popup').append(messages('alert-success', data.msg));
                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);


                        $(document).find('#default_confirm').modal('hide');
                        var tabe = $('#property_report').DataTable();
                        $(document).find('#action').prop('selectedIndex', 0);
                        tabe.ajax.reload(null, false);
                        $("#checkAll").prop('checked', false);

                    } else {

                        $('#success_file_popup').append(messages('alert-danger', data.error));

                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);
                    }
                },
                error: function(data) {

                    alert(data.responseText);
                }
            });
        }

        function messages(classname, msg) {
            return '<div class="alert ' + classname +
                ' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' +
                msg + '</div>';
        }

        $(document).ready(function() {

            $("#startdate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                autoclose: true
            }).on('changeDate', function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#enddate').datepicker('setStartDate', minDate);
            });

            $("#enddate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                autoclose: true
            }).on('changeDate', function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#startdate').datepicker('setEndDate', minDate);
            });

            // Table updating forms changes
            $("#checkAll").click(function() {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });

            $("#action").change(function() {
                {{-- if (this.value == "delete") {
                    $("#submit_all").css("display", "none");
                    $("#submit_show_msg").css("display", "inline-block");
                } else {
                    $("#submit_all").css("display", "inline-block");
                    $("#submit_show_msg").css("display", "none");
                } --}}
            });
        })

        $(".export-form").click(function() {
            $('#export').submit();
        });
    </script>
@endpush
