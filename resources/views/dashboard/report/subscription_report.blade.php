@extends('dashboard.layouts.master')
@section('title', __('backend.subscription_report'))
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <div class="padding school-report-manage list-school">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.subscription_report') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.subscription_report') }}</span>
                </small>
            </div>
            
            {{ Form::open(['route' => 'report.subscription.export', 'method' => 'post', 'id' => 'export']) }}
            <div class="row p-a pull-right" style="margin-top: -70px;">

                <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : '' }}"
                    id="export_start_date">
                <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : '' }}" id="export_end_date">

                <div class="col-sm-12">
                    <a class="btn btn-fw primary export-form" href="javascript:void(0)">
                        Export
                    </a>
                </div>
            </div>
            {{ Form::close() }}

            {{ Form::open(['method' => 'post', 'id' => 'filter_form']) }}
            <div class="box-header dker" style="margin-bottom: 10px">

                <div class="dflex" style="margin-top:10px;display: flex;">

                    <div class="">
                        <label class="">From Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY"
                            readonly style="width: 200px;height: 30px" value="{{ isset($startdate) ? $startdate : '' }}">
                    </div>
                    <div class="" style="margin-left: 10px">
                        <label>To Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY"
                            readonly style="width: 200px;height: 30px" value="{{ isset($enddate) ? $enddate : '' }}">
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


            <div class="table-responsive school-report-form">
                <table class="table table-bordered m-a-0" id="subscription_report">
                    <thead class="dker">
                        <tr>
                            {{--  <th class="width20 dker">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>  --}}
                            <th>Id</th>
                            <th>{{ __('backend.subscription_plan_name') }}</th>
                            <th>{{ __('backend.subscription_plan_duration') }}</th>
                            <th>{{ __('backend.number_of_ads') }}</th>
                            <th>{{ __('backend.total_agents_subscribed')}}</th>
                            <th>{{ __('backend.subscription_plan_type') }}</th>
                            <th>{{ __('backend.subscription_plan_price') }}</th>
                            <th>{{ __('backend.total_plan_revenue') }}</th>
                            <th>{{ __('backend.created_on') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
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

                var action_url = "{!! route('report.subscription.anyData') !!} ";

                var dataTable = $('#subscription_report').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': []
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                            });
                        }
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            visible:false
                            
                        },
                        {
                            data: 'plan_name',
                            name: 'plan_name',

                        },
                        {
                            data: 'plan_duration',
                            name: 'plan_duration',

                        },
                        {
                            data: 'number_of_ads',
                            name: 'number_of_ads',

                        },
                        {
                            data: 'total_agents_subscribed',
                            name: 'total_agents_subscribed',
                        },
                        {
                            data: 'plan_type',
                            name: 'plan_type',

                        },
                        {
                            data: 'plan_price',
                            name: 'plan_price',

                        },
                        {
                            data: 'total_plan_revenue',
                            name: 'total_plan_revenue',

                        },
                        {
                            data: 'created_on',
                            name: 'created_on',
                        }
                    ],
                    order: ['0', 'DESC']
                });

                {{--  $("#property_type_select").on('change', function(e) {
                    e.preventDefault();
                    $("#export_property_type").val($("#property_type_select").val().toLowerCase());
                    dataTable.draw();
                });

                $("#property_for_select").on('change', function(e) {
                    e.preventDefault();
                    $("#export_property_for").val($("#property_for_select").val().toLowerCase());
                    dataTable.draw();
                });  --}}

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
                todayHighlight: true
            }).on('changeDate', function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#enddate').datepicker('setStartDate', minDate);
            });

            $("#enddate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true
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
