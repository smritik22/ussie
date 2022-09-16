@extends('dashboard.layouts.master')
@section('title','School Report')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="padding school-report-manage list-school">
        <div class="box">

            <div class="box-header dker">
                <h3>Report</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <span>School Report</span>
                </small>
            </div>
            {{Form::open(['route'=>'report.schoolAdmin.export','method'=>'post','id'=>'export'])}}
             <div class="row p-a pull-right" style="margin-top: -70px;">
             			<input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : ''}}" id="export_start_date">
             			<input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : ''}}" id="export_end_date">
                        <div class="col-sm-12">
                            <a class="btn btn-fw primary export-form" href="javascript:void(0)">
                             Export
                            </a>
                        </div>
                    </div>
              {{Form::close()}} 
            {{Form::open(['route'=>'report.schoolAdmin.filter','method'=>'post','id'=>'filter_form'])}}
                <div class="box-header dker">              
                    <label>Start Date</label>
                    <input type="date" class="form-control" name="startdate" id="startdate" placeholder="DD/MM/YYYY" style="width: 250px;" value="{{ isset($startdate) ? $startdate : ''}}">

                    <label>End Date</label>
                    <input type="date" class="form-control" name="enddate" id="enddate" placeholder="DD/MM/YYYY" style="width: 250px;" value="{{ isset($enddate) ? $enddate : ''}}">


                <a href="javascript:void(0)" id="filter"><button type="button" class="btn btn-primary mr-2">Filter</button></a>
                <a href="{{ route('report.schoolAdmin') }}" id="filter">
                <button type="button"  class="btn btn-default mr-2">Clear</button>
         		</a>
                </div>
               
            {{Form::close()}}
            
                
                <div class="table-responsive school-report-form">
                    <table class="table table-bordered m-a-0" id="school_admin">
                        <thead class="dker">
                        <tr>
                            <th>School Name</th>
                            <th>School Admin Name</th>
                            <th>{{ __('backend.loginEmail') }}</th>
                            <th class="text-center" style="width:50px;">{{ __('backend.status') }}</th>
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
@push("after-scripts")

  <!--  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> -->

   <script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>

    <script type="text/javascript">

        $(function() {
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           load_data();
           function load_data(startdate,enddate) 
           {
        
              var action_url = "{!!  route('report.schoolAdmin.anyData') !!} ";
            
               $('#school_admin').DataTable({
                   processing: true,
                   serverSide: true,
                   responsive: true,
                   ordering: true,
                   columnDefs: [{
                       'bSortable': false,
                       'aTargets': [0,3]
                   }],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data:{
                        startdate : startdate,
                        enddate : enddate
                       }
                   },
                   columns: [
                   
                   {
                       data: 'school_name',
                       name: 'school_name',
                   },
                   {
                      data: 'school_admin_Name',
                      name: 'school_admin_Name',
                   },
                   {
                       data: 'email',
                       name: 'email',
                   },
                   
                   {
                       data: 'status',
                       name: 'status',
                   },
                   
                   ],
                   order: ['0','DESC']
               });
           }
            
            var flag = true;
            $('#filter').click(function(){
                var from_date = $('#startdate').val();
                var to_date = $('#enddate').val();
                if(from_date != '' &&  to_date != '')
                {
                    if( to_date < from_date ) {
                        alert('Start date must be less than end date');
                        flag = false;
                    }
                    else
                    {
                        flag = true;

                    }
                }
                else
                {
                    alert('Both Date is required');
                        flag = false;
                }
                if(flag)
                {   
                    $('#school_admin').DataTable().destroy();
                    $(document).find('#export_start_date').val(from_date);
                    $(document).find('#export_end_date').val(to_date);
                    load_data(from_date,to_date);

                }

            });
        });
        	
        $(".export-form").click(function () {
			$('#export').submit();        	
        });
       
       
    </script>
@endpush
