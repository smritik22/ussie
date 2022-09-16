@extends('dashboard.layouts.master')
@section('title', __('backend.cms'))
@section('content')
    {{-- @if(@Auth::user()->permissionsGroup->webmaster_status)
        @include('dashboard.permissions.list')
    @endif --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />  
    <div class="padding website-label">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.cms') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>{{ __('backend.cms') }}</span>
                </small>
            </div>

            {{-- @if( \Helper::check_permission(3,2) ) --}}
                <div class="box-tool">
                    <ul class="nav">
                        {{-- @if(@Auth::user()->permissionsGroup->webmaster_status) --}}
                            <li class="nav-item inline">
                            <a class="btn btn-fw primary" href="{{route('cms.create')}}">
                                        <i class="material-icons">&#xe7fe;</i>
                                        &nbsp; New CMS
                                    </a>
                            </li>
                        {{-- @endif --}}
                    </ul>
                </div>
            {{-- @endif --}}
            
            @if($cmsData == 0)
                <div class="row p-a">
                    <div class="col-sm-12">
                        <div class=" p-a text-center ">
                            {{ __('backend.noData') }}
                            <br>
                            {{-- @if(@Auth::user()->permissionsGroup->webmaster_status) --}}
                                <br>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
            @endif

            @if($cmsData > 0)
                {{Form::open(['method'=>'post'])}}
                <div class="table-responsive">
                    <table class="table table-bordered m-a-0" id="cms">
                        <thead class="dker">
                        <tr>
                            
                            <th>{{ __('backend.topicCommentName') }}</th>
                            <!-- <th >CMS Description</th>   -->
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody id="bannerTable">

                     

                        </tbody>
                    </table>

                </div>
              
                {{Form::close()}}
            @endif
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
        </div>
    </div>
@endsection
@push("after-scripts")
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
           function load_data() 
           {
        
              var action_url = "{!!  route('cms.anyData') !!} ";
            
               $('#cms').DataTable({
                   processing: true,
                   serverSide: true,
                   responsive: true,
                   ordering: true,
                   columnDefs: [{
                       'bSortable': false,
                       'aTargets': [0,1]
                   }],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data:{
                       
                       }
                   },
                   columns: [
                   {
                       data: 'name',
                       name: 'Name',
                      
                   },
                   //  {
                   //    data: 'description',
                   //    name: 'Description',
                      
                   // },  
                  
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
