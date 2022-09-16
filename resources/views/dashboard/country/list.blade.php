@extends('dashboard.layouts.master')
@section('title', __('backend.countries'))
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="padding website-label">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.countries') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.countries') }}</span>
                </small>
            </div>

            {{-- <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="btn btn-fw primary" href="{{ route('country.create') }}">
                            <i class="material-icons">&#xe7fe;</i>
                            &nbsp; {{ __('backend.add_country') }}
                        </a>
                    </li>

                </ul>
            </div> --}}


            {{ Form::open(['route' => 'country.updateAll', 'method' => 'post', 'id' => 'updateAll']) }}

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
                <table class="table table-bordered m-a-0" id="country">
                    <thead class="dker">
                        <tr>
                            <th class="width20 dker">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            {{-- <th>{{ __('backend.country_flag') }}</th> --}}
                            <th>{{ __('backend.country_name') }}</th>
                            <th>{{ __('backend.country_code') }}</th>
                            <th>{{ __('backend.currency_code') }}</th>
                            <th>{{ __('backend.currency_decimal_point') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bannerTable">

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
            <div id="country_delete_modal" class="modal fade" data-backdrop="true">
                <div class="modal-dialog" id="animate">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                        </div>
                        <div class="modal-body text-center p-lg">
                            <p>
                                {{ __('backend.confirmationDeleteMsg') }}
                                <br>
                                <strong id="show_country_name"> </strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark-white p-x-md"
                                data-dismiss="modal">{{ __('backend.no') }}</button>
                            <a href="javascript:void(0);"
                                class="btn danger confrimDeletCountry p-x-md">{{ __('backend.yes') }}</a>
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
        function deleteCountry(element) {
            let user_name = $(element).data('name');
            let href = $(element).data('href');

            $('#show_country_name').text(user_name);
            $('.confrimDeletCountry').attr('href', href);
            $("#country_delete_modal").modal('show')
        }

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();

            function load_data() {
                var action_url = "{!! route('country.anyData') !!} ";

                $('#country').DataTable({
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
                        data: {

                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        {{-- {
                            data: 'country_flag',
                            name: 'country_flag',
                            orderable: false,
                            searchable: false
                        }, --}} {
                            data: 'country_name',
                            name: 'country_name',

                        },
                        {
                            data: 'country_code',
                            name: 'country_code',

                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code',

                        },
                        {
                            data: 'currency_decimal_point',
                            name: 'currency_decimal_point',

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
