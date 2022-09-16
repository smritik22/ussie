@extends('dashboard.layouts.master')
@section('title', __('backend.revenue_report'))
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <div class="padding school-report-manage list-school">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.revenue_report') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.revenue_report') }}</span>
                </small>
            </div>
            
            {{ Form::open(['route' => 'report.revenue.export', 'method' => 'post', 'id' => 'export']) }}
            <div class="row p-a pull-right" style="margin-top: -70px;">

                <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : '' }}"
                    id="export_start_date">
                <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : '' }}" id="export_end_date">
                <input type="hidden" name="subscription_type" value="" id="export_subscription_type">

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
                    {{--  <div class="" style="margin-left: 10px">
                        <label for="subscription_type_filter">Subscription Type</label>
                        <select name="subscription_type" id="subscription_type_filter" class="form-control">
                            <option value="">Select Subscription Type</option>
                            @if (@config('constants.SUBSCRIPTION_TYPE'))
                                @foreach (config('constants.SUBSCRIPTION_TYPE') as $item)
                                    <option value="{{$item['value']}}">{{ Helper::getLabelValueByKey($item['label_key']) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>  --}}

                    <div class="" style="margin-left: 10px">
                        <label for="plan_type_filter">Plan Type</label>
                        <select name="plan_type" id="plan_type_filter" class="form-control">
                            <option value="">Select Plan Type</option>
                            @if (@config('constants.AGENT_TYPE'))
                                @foreach (config('constants.AGENT_TYPE') as $item)
                                    <option value="{{$item['value']}}">{{ Helper::getLabelValueByKey($item['label_key']) }}</option>
                                @endforeach
                            @endif
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


            <div class="table-responsive school-report-form">
                <table class="table table-bordered m-a-0" id="property_report">
                    <thead class="dker">
                        <tr>
                            <th>{{ __('backend.transaction_number') }}</th>
                            <th>Plan Type</th>
                            <th>{{ __('backend.agent_name') }}</th>
                            <th>{{ __('backend.agent_contact') }}</th>
                            <th>{{ __('backend.transaction_amount') }}</th>
                            <th>{{ __('backend.date_transferred') }}</th>
                            <th>{{ __('backend.status') }}</th>
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

                var action_url = "{!! route('report.revenue.anyData') !!} ";

                var dataTable = $('#property_report').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': [0, 6]
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                                {{--  "subscription_type" : $("#subscription_type_filter").val().toLowerCase(),  --}}
                                "plan_type" : $("#plan_type_filter").val().toLowerCase(),
                            });
                        }
                    },
                    columns: [
                        {
                            data: 'transaction_number',
                            name: 'transaction_number',

                        },
                        {
                            data: 'subscription_type',
                            name: 'subscription_type',

                        },
                        {
                            data: 'agent_name',
                            name: 'agent_name',

                        },
                        {
                            data: 'agent_contact',
                            name: 'agent_contact',

                        },
                        {
                            data: 'amount',
                            name: 'amount',

                        },
                        {
                            data: 'date_listed',
                            name: 'date_listed',

                        },
                        {
                            data: 'status',
                            name: 'status',
                        },
                    ],
                    order: ['0', 'DESC']
                });


                $('#startdate, #enddate').change(function() {
                    $("#export_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_end_date").val($("#enddate").val().toLowerCase());
                    
                    dataTable.draw();
                });

                $("#subscription_type_filter").on("change",function(e){
                    $('#export_subscription_type').val($("#subscription_type_filter").val().toLowerCase());
                    dataTable.draw();
                });

                $("#plan_type_filter").on("change",function(e){
                    $('#export_plan_type').val($("#plan_type_filter").val().toLowerCase());
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
