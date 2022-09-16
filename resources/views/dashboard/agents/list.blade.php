@extends('dashboard.layouts.master')
@section('title', __('backend.agents_mngmnt'))
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="padding website-label">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.agents_mngmnt') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.agents_mngmnt') }}</span>
                </small>
            </div>

            {{ Form::open(['method' => 'post', 'id' => 'filter_form']) }}
            <div class="box-header" style="margin-bottom: 10px; @if (@$agent_type != '') display:none @endif">
                <h3>Filter</h3>
                <hr>
                <div class="dflex" style="margin-top:10px;display: flex;">
                    <div class="">
                        <label for="searchByAgentType">Agent Type</label><br>
                        <select name="searchByAgentType" id="searchByAgentType"
                            class="form-control c-select w-md inline v-middle">
                            <option value="" @selected(true)>Select Agent Type</option>
                            @foreach (config('constants.AGENT_TYPE') as $type)
                                <option value="{{ $type['urlText'] }}" @if (@$agent_type == $type['urlText']) @selected(true) @endif>
                                    {{ Helper::getLabelValueByKey($type['label_key']) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>

                    </div>
                </div>
            </div>
            {{ Form::close() }}

            {{ Form::open(['route' => 'agent.updateAll', 'method' => 'post', 'id' => 'updateAll']) }}
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
                <table class="table table-bordered m-a-0" id="agents">
                    <thead class="dker">
                        <tr>
                            <th class="width20 dker">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>{{ __('backend.full_name') }}</th>
                            <th>{{ __('backend.property_agent_type') }}</th>
                            <th>{{ __('backend.email') }}</th>
                            <th>{{ __('backend.mobile_number') }}</th>
                            <th>{{ __('backend.agent_total_properties') }}</th>
                            <th>{{ __('backend.join_date') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    {{-- <tfoot>
                        <th class="width20 dker">
                            <!-- <label class="ui-check m-a-0">
                                <input id="checkAll" type="checkbox"><i></i>
                            </label> -->
                        </th>
                        <th>{{ __('backend.full_name') }}</th>
                        <th>{{ __('backend.property_agent_type') }}</th>
                        <th>{{ __('backend.email') }}</th>
                        <th>{{ __('backend.mobile_number') }}</th>
                        <th>{{ __('backend.join_date') }}</th>
                        <th>{{ __('backend.agent_total_properties') }}</th>
                        <th>{{ __('backend.status') }}</th>
                        <th>{{ __('backend.options') }}</th>
                    </tfoot> --}}
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

            function load_data() {
                var action_url = "{!! route('agent.anyData') !!} ";
                var agent_type = "{!! $agent_type !!}";
                var dataTable = $('#agents').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': [0, 2, 8]
                    }],
                    ajax: {
                        'url': action_url,
                        'data': function(data, agent_type) {
                            var agent_type = $('#searchByAgentType').val();
                            data.searchByAgentType = agent_type;
                            data.agent_type = agent_type;
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'full_name',
                            name: 'full_name',

                        },
                        {
                            data: 'agent_type',
                            name: 'agent_type',
                            orderable: false,
                            searchable: false

                        },
                        {
                            data: 'email',
                            name: 'email',

                        },
                        {
                            data: 'mobile_number',
                            name: 'mobile_number',

                        },
                        {
                            data: 'totla_properties',
                            name: 'totla_properties',
                            orderable: true,
                            searchable: false

                        },
                        {
                            data: 'join_date',
                            name: 'join_date',

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
                    order: ['0', 'DESC'],
                    initComplete: function() {
                        this.api().columns([2]).every(function() {
                            var column = this;
                            var select = $(
                                    '<select class=""><option value=""> Select </option></select>'
                                    )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $(this).val();
                                    column.search(this.value).draw();
                                });

                            // Only contains the *visible* options from the first page
                            // console.log(column.data().unique());

                            // If I add extra data in my JSON, how do I access it here besides column.data?
                            {{-- /* <?php 
                                foreach (config('constants.AGENT_TYPE') as $type ) {
                            ?>
                                select.append(`<option value="<?php echo $type['urlText']; ?>" <?php if (@$agent_type == $type['urlText']) {
    echo 'selected';
} ?>><?php echo Helper::getLabelValueByKey($type['label_key']); ?></option>`);
                            <?php 
                                }
                            ?>*/ --}}

                        });
                    }
                });

                $("#searchByAgentType").change(function() {
                    dataTable.draw();
                });

            }

        });
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


        $("#filter_btn").click(function() {
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
