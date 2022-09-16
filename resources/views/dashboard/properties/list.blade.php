@extends('dashboard.layouts.master')
@section('title', __('backend.properties'))
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <div class="padding website-label">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.properties') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.properties') }}</span>
                </small>
            </div>

            {{-- <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="btn btn-fw primary" href="{{ route('property.create') }}">
                            <i class="material-icons">&#xe7fe;</i>
                            &nbsp; {{ __('backend.add_properties') }}
                        </a>
                    </li>

                </ul>
            </div> --}}

            {{-- {{ Form::open([ 'method' => 'post', 'id' => 'export']) }}
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
            {{ Form::close() }} --}}

            {{--  {{ Form::open(['method' => 'post', 'id' => 'filter_form']) }}
            <div class="box-header" style="margin-bottom: 10px">
                <h3>Filters</h3>
                <hr>
                <div class="dflex" style="margin-top:10px;display: flex;">
                    <div class="">
                        <label class="">From Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY" readonly 
                            style="width: 200px;height: 30px" value="{{ isset($startdate) ? $startdate : '' }}">
                    </div>
                    <div class="" style="margin-left: 10px">
                        <label>To Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY" readonly 
                            style="width: 200px;height: 30px" value="{{ isset($enddate) ? $enddate : '' }}">
                    </div>
                    <div style="margin-left: 10px;">
                        <label>By Property Type</label> <br>
                        <select name="property_type" id="property_type_select" class="form-control">
                            <option value="">Select Property Type</option>
                            @if ($propertyTypes)
                                @foreach ($propertyTypes as $type)
                                    <option value="{{$type->id}}">{{$type->type}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="" style="padding-top:30px">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2">Clear</button>
                        </a>
                    </div>

                </div>
            </div>
            {{ Form::close() }}  --}}

            {{-- <div class=" " style="margin-top: 40px">
                <div id="sandbox-container">
                    <div class="col-md-3 form-group">
                        <label>Start Date</label>
                        <div class='input-group date'>
                            <input type='text' class="form-control" placeholder="dd-mm-yyyy" name="min" id='min' />
                            <span class="input-group-addon glyphiconstart" style="border-radius:0px 10px 10px 0px">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div id="sandbox-container">
                    <div class="col-md-3 form-group">
                        <label>End Date</label>
                        <div class='input-group date'>
                            <input type='text' class="form-control" placeholder="dd-mm-yyyy" name="max" id='max' />
                            <span class="input-group-addon glyphiconend" style="border-radius:0px 10px 10px 0px">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 form-group" style="margin-top: 24px;width:8%">
                    <div class='input-group date'>
                        <input type='button' class="form-control" name="clear" id='clear' value="Clear"
                            style="background: #f1f1f1;border-radius: 10px;border: 1px solid #999999;" />
                    </div>
                </div>
            </div> --}}

            {{ Form::open(['route' => 'property.updateAll', 'method' => 'post', 'id' => 'updateAll']) }}

            <div class="bulk-action">
                <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                    <option value="no">{{ __('backend.bulkAction') }}</option>
                    <option value="activate">{{ __('backend.activeSelected') }}</option>
                    <option value="block">{{ __('backend.blockSelected') }}</option>
                    <option value="delete">{{ __('backend.deleteSelected') }}</option>
                </select>
                <button type="submit" class="btn white">{{ __('backend.apply') }}</button>
            </div>


            <div class="table-responsive">
                <table class="table table-bordered m-a-0" id="properties">
                    <thead class="dker">
                        <tr>
                            <th class="width20 dker">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>{{ __('backend.property_name') }}</th>
                            <th>{{ __('backend.agent_name') }}</th>
                            <th>{{ __('backend.agent_contact') }}</th>
                            <th>{{ __('backend.property_type') }}</th>
                            <th>{{ __('backend.property_for') }}</th>
                            <th>{{ __('backend.property_address') }}</th>
                            <th>{{ __('backend.property_sqft_area') }}</th>
                            <th>{{ __('backend.property_price') }}</th>
                            <th>{{ __('backend.date_listed') }}</th>
                            <th>{{ __('backend.property_subscription_expire_date') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}


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

            <!-- .modal -->
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
            <!-- / .modal -->


        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
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

            function load_data(start_date, end_date) {

                if ($.fn.DataTable.isDataTable('#properties')) {
                    $('#properties').DataTable().destroy();
                }

                {{-- $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var min = $('#startdate').datepicker("getDate");
                        var max = $('#enddate').datepicker("getDate");

                        var d = data[4].split("-");
                        var startDate = new Date(d[1] + "-" + d[0] + "-" + d[2]);
                        if (min == null && max == null) {
                            return true;
                        }
                        if (min == null && startDate <= max) {
                            return true;
                        }
                        if (max == null && startDate >= min) {
                            return true;
                        }
                        if (startDate <= max && startDate >= min) {
                            return true;
                        }
                        return false;
                    }
                ); --}}


                var action_url = "{!! route('property.anyData') !!} ";
                var dataTable = $('#properties').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': [0, 11, 12]
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function (d){
                            return $.extend( {}, d, {
                                {{--  "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                                "property_type": $("#property_type_select").val().toLowerCase(),  --}}
                                "property_for" : "{{$property_for}}",
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
                            data: 'property_name',
                            name: 'property_name',

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
                            data: 'property_type',
                            name: 'property_type',

                        },
                        {
                            data: 'property_for',
                            name: 'property_for',

                        },
                        {
                            data: 'property_address',
                            name: 'property_address',

                        },
                        {
                            data: 'property_sqft_area',
                            name: 'property_sqft_area',

                        },
                        {
                            data: 'property_price',
                            name: 'property_price',

                        },
                        {
                            data: 'date_listed',
                            name: 'date_listed',

                        }, {
                            data: 'subscription_expire_date',
                            name: 'subscription_expire_date',

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

                $("#property_type_select").on('change', function (e){
                    e.preventDefault();
                    dataTable.draw();
                });

                $('#startdate, #enddate').change(function() {
                    {{--  let start_date = $('#startdate').val();
                    let end_date = $('#enddate').val();
                    load_data(start_date, end_date);  --}}
                    dataTable.draw();
                });
            } // load data end

        });

        $(document).ready(function(){
            
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
    </script>
@endpush
