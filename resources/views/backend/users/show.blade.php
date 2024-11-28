@extends('backend.layouts.master')
@section('title','Customer | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Customer Details</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.agents') }}"><span>Customers</span></a></li>
                <li class="active"><span>Customer details</span></li>
            </ol>
        </div>
        <!-- /Breadcrumb -->
    </div>
    <!-- /Title -->
     <style>
        .sm-data-box .data-wrap-left, .sm-data-box .data-wrap-right { min-height:118px;}
     </style>
     <!-- Row -->
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="panel panel-default card-view pa-0">
            <div class="panel-wrapper collapse in">
                <div class="panel-body pa-0">
                    <div class="sm-data-box">
                        <div class="container-fluid">
                        
                            <div class="row">
                            <a href="{{ route('admin.customers.bookings', $customer->user_id) }}" >
                                <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                    <span class="txt-dark block counter"><span class="counter-anim">{{ $bookingCount }}</span></span>
                                    <span class="weight-500 uppercase-font block">Total Bookings</span>
                                </div>
                                </a>
                                <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                    <i class="icon-calender data-right-rep-icon txt-light-grey"></i>
                                </div>
                            </div>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="panel panel-default card-view pa-0">
            <div class="panel-wrapper collapse in">
                <div class="panel-body pa-0">
                    <div class="sm-data-box">
                        <div class="container-fluid">
                        
                            <div class="row">
                            <a href="{{ route('admin.customers.complete-bookings', $customer->user_id) }}" >
                                <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                    <span class="txt-dark block counter"><span class="counter-anim">{{ $completeBookingCount }}</span></span>
                                    <span class="weight-500 uppercase-font block">Total Complete Bookings</span>
                                </div>
                                </a>
                                <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                    <i class="icon-calender data-right-rep-icon txt-light-grey"></i>
                                </div>
                            </div>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="panel panel-default card-view pa-0">
            <div class="panel-wrapper collapse in">
                <div class="panel-body pa-0">
                    <div class="sm-data-box">
                        <div class="container-fluid">
                        
                            <div class="row">
                            <a href="{{ route('admin.customers.cancelled-bookings', $customer->user_id) }}" >
                                <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                    <span class="txt-dark block counter"><span class="counter-anim">{{ $cancelBookingCount }}</span></span>
                                    <span class="weight-500 uppercase-font block">Total Canceled Bookings</span>
                                </div>
                                </a>
                                <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                    <i class="icon-calender data-right-rep-icon txt-light-grey"></i>
                                </div>
                            </div>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="panel panel-default card-view pa-0">
            <div class="panel-wrapper collapse in">
                <div class="panel-body pa-0">
                    <div class="sm-data-box">
                        <div class="container-fluid">
                        
                            <div class="row">
                            <a href="{{ route('admin.customers.upcoming-bookings', $customer->user_id) }}" >
                                <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                    <span class="txt-dark block counter"><span class="counter-anim">{{ $confirmBooking }}</span></span>
                                    <span class="weight-500 uppercase-font block">Total Upcoming Bookings</span>
                                </div>
                                </a>
                                <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                    <i class="icon-calender data-right-rep-icon txt-light-grey"></i>
                                </div>
                            </div>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
    <!-- Row -->
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Customer</h6>
                    </div>
                   
                   
                   
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-wrap">
                                    <form class="form-horizontal">
                                        <div class="form-body">
                                            <h6 class="txt-dark capitalize-font"><i

                                                        class="zmdi zmdi-account mr-10"></i>Personal Info</h6>
                                            <hr class="light-grey-hr"/>
                                             
                                            <div class="row">
                                           
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">First Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->first_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Last Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->last_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Email Address:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->email }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Mobile:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->mobile }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Address:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->address }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">City:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->cities_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">State:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->state_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Zipcode:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->zipcode }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Joined at:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $customer->joined_at }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                            </div> <!----end row-->

                                      
                                           

                                          
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Row -->
@endsection

@push('styles')
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#copyButton').click(function (event) {
                event.preventDefault(); // Prevent the default anchor behavior

                // Get the text from the span
                var agentCode = $('#agentCode').text();

                // Create a temporary input element
                var tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(agentCode).select();

                // Copy the text to the clipboard
                document.execCommand("copy");

                // Remove the temporary input element
                tempInput.remove();

                // Show a toast notification
                $.toast({
                    heading: 'Copied to clipboard',
                    text: 'Agent code ' + agentCode + ' has been copied.',
                    position: 'top-right',
                    loaderBg: '#f0c541',
                    icon: 'success',
                    hideAfter: 3500,
                    stack: 6
                });
            });
        });
    </script>
@endpush
@push('scripts')
  <!-- Progressbar Animation JavaScript -->
  <script src="{{asset('public/backend/vendors/bower_components/waypoints/lib/jquery.waypoints.min.js')}}"></script>
  <script src="{{asset('public/backend/vendors/bower_components/jquery.counterup/jquery.counterup.min.js')}}"></script>

  <script type="text/javascript">

  </script>
@endpush