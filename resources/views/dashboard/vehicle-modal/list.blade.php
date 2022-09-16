@extends('dashboard.layouts.master')
@section('title', __('backend.Vehicle_modal'))
@section('content')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <div class="padding school-report-manage list-school">
        <div class="box">
            <div id="success_file_popup"></div>
            <div class="box-header dker">
                <h3>{{ __('backend.Vehicle_modal') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.Vehicle_modal') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('vehicle-modal.create')}}">
                                    <i class="material-icons">&#xe02e;</i>
                                    &nbsp; {{ __('backend.newvehiclemodal') }}
                                </a>
                            </li>
                </ul>
            </div>

             
            
           <!--  {{ Form::open(['route' => 'report.ride.export', 'method' => 'post', 'id' => 'export']) }}
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
            {{ Form::close() }} -->

            {{-- {{ Form::open(['method' => 'post', 'id' => 'filter_form']) }}
                <div class="box-header dker">
                    <label style="margin-left: 15px;">Start Date</label>
                    <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY"
                        style="width: 250px;" value="{{ isset($startdate) ? $startdate : '' }}">

                    <label style="margin-left: 15px;">End Date</label>
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

                    <label style="margin-left: 20px;">End Date</label>
                    <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY" readonly 
                    style="width: 200px;height: 30px" value="{{ isset($enddate) ? $enddate : '' }}"> --}}

                    <div class="" style="margin-top: 50px;">
                        <label class="" style="margin-left: 15px;">From Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY"
                            readonly style="width: 200px;height: 30px" value="{{ isset($startdate) ? $startdate : '' }}">
                    </div>
                    <div class="" style="margin-left: 10px;margin-top: 50px;">
                        <label style="margin-left: 15px;">To Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY"
                            readonly style="width: 200px;height: 30px" value="{{ isset($enddate) ? $enddate : '' }}">
                    </div>
                    <div style="margin-left: 10px;margin-top: 50px;">
                        <label>Select Vehicle Make</label> <br>
                        <select name="ride_status_check" id="ride_status_check" class="form-control">
                            <option value="">Select Vehicle Make</option>
                            @if ($vehicle_type)
                                @foreach ($vehicle_type as $item)
                                    <option value="{{$item['id']}}">{{ $item->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="" style="padding-top:20px;margin-top: 50px;">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2">Clear</button>
                        </a>
                    </div>

                </div>
            </div>
            {{ Form::close() }}

            {{ Form::open(['route' => 'vehicle-modal.updateAll', 'method' => 'post', 'id' => 'updateAll']) }}

            <div class="bulk-action">
                    <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                        <option value="no">{{ __('backend.bulkAction') }}</option>
                        <option value="1">{{ __('backend.activeSelected') }}</option>
                        <option value="2">{{ __('backend.inactiveSelected') }}</option>
                        <option value="3">{{ __('backend.deleteSelected') }}</option>
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
                           <th>{{ __('backend.Vehicle_type') }}</th>
                             <th>{{ __('backend.vehicle_modal') }}</th>

                            <!--<th>{{ __('backend.mobile_number') }}</th>
                            <th>{{ __('backend.create_date') }}</th>
                            <th>{{ __('backend.driver_status') }}</th> -->
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
                                        <button type="submit" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <div id="delete_modal" class="modal fade" data-backdrop="true">
                <div class="modal-dialog" id="animate">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                        </div>
                        <div class="modal-body text-center p-lg">
                            <p>
                                {{ __('backend.confirmationDeleteMsg') }}
                                <br>
                                <strong id="show_name"> </strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark-white p-x-md"
                                data-dismiss="modal">{{ __('backend.no') }}</button>
                            <a href="javascript:void(0);"
                                class="btn danger confirmDelete p-x-md">{{ __('backend.yes') }}</a>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>

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
            function deleteData(element) {
            let user_name = $(element).data('name');
            let href = $(element).data('href');

            $('#show_name').text(user_name);
            $('.confirmDelete').attr('href', href);
            $("#delete_modal").modal('show')
        }
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();

            function load_data(startdate, enddate) {

                var action_url = "{!! route('vehicle-modal.anyData') !!} ";

                var dataTable = $('#property_report').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': [0,3,4]
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
                            data: 'vehiclename',
                            name: 'vehiclename',

                        },
                        {
                            data: 'name',
                            name: 'name',

                        },
                        // {
                        //     data: 'mobile_number',
                        //     name: 'mobile_number',

                        // },
                        // {
                        //     data: 'join_date',
                        //     name: 'join_date',

                        // },
                        // {
                        //     data: 'is_driver',
                        //     name: 'is_driver',

                        // },
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

                    if (type == 2) {
                        msg = "Are you sure you want to inactive this vehicle-modal?";

                    } else if (type == 1) {
                        msg = "Are you sure you want to active this vehicle-modal?";
                    }else if (type == 3) {
                        msg = "Are you sure you want to delete this vehicle-modal?";
                    }
                     else {
                        msg = "Are you sure you want to delete this vehicle-modal?";
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
                url: "{{ route('vehicle-modal.updateAll') }}",
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
