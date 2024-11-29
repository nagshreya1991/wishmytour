@extends('backend.layouts.master')

@section('title','Booking Details | Wishmytour Admin')

@section('main-content')



    <div class="card">

        <h5 class="card-header">Booking Details</h5>

        <div class="card-body">



            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-default card-view">

                        <div class="panel-heading">



                            <div class="clearfix"></div>

                        </div>

                        <div class="panel-wrapper collapse in">

                            <div class="panel-body">

                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="form-wrap">

                                            <div class="form-body">

                                                <h6 class="txt-dark capitalize-font"><i

                                                            class="zmdi zmdi-account mr-10"></i>Booking's Info</h6>

                                                <hr class="light-grey-hr"/>





                                                <div class="row">

                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Booking Number:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">#{{ $bookingdetails->booking_number }}  </p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <!--/span-->

                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Package ID:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">#{{ $bookingdetails->package_id }}  </p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <!--/span-->

                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Package Name:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{ $bookingdetails->name }}  </p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <!--/span-->

                                                </div>

                                                <hr class="light-grey-hr"/>

                                                <h6 class=""style="text-align: center;">Customer Details</h6>

                                                <div class="row">

                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Customer Name:</label>

                                                            <div class="col-md-9">

                                                                @if($bookingdetails->booking_for == 0)

                                                                    <p class="form-control-static">{{ $bookingdetails->customer_first_name }} {{ $bookingdetails->customer_last_name }}</p>

                                                                @elseif($bookingdetails->booking_for == 1)

                                                                    <p class="form-control-static">{{ $bookingdetails->booking_customer_name }}</p>

                                                                    <p class="form-control-static"><strong>Booked By</strong> : {{ $bookingdetails->customer_first_name }} {{ $bookingdetails->customer_last_name }}(Customer ID : {{ $bookingdetails->customer_id }})



                                                                        <a class="btn btn-info" role="button"  href="{{ route('admin.customers.show', ['id' => $bookingdetails->customer_id]) }}"  target="_blank" >View Customer</a></p>



                                                                @endif

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Email Address:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->booking_customer_email}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Phone Number:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->booking_customer_phone_number}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Pan Number:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->booking_customer_pan_number}}@if($bookingdetails->id_verified == 0)

                                                                        <span class="label label-danger">Not Verified</span>

                                                                    @elseif($bookingdetails->id_verified == 1)

                                                                        <span class="label label-success ">Verified</span>

                                                                    @endif</p>

                                                            </div>

                                                        </div>

                                                    </div>





                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Address:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->booking_customer_address}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">State:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->customer_state}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">City:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->customer_city}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Zipcode:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->zipcode}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Tour Start Date:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->first_booking_date}}</p>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Tour End Date:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static">{{$bookingdetails->last_booking_date}}</p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <!--/span-->

                                                    <div class="col-md-12">

                                                        <div class="form-group">

                                                            <label class="control-label col-md-3">Vendor Name:</label>

                                                            <div class="col-md-9">

                                                                <p class="form-control-static"> {{ $bookingdetails->vendor_fullname }} </p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <!--/span-->

                                                </div><!--end customer details--->
                                            <!---Payment Details----->
                                            <div class="seprator-block"></div>
                                            <hr class="light-grey-hr"/>
                                            <h6 class=""style="text-align: center;">Payment Details</h6>

                                            <div class="row">
                                            @foreach($booking_payments as $payments)
                                            <div class="col-md-12">
                                            <div class="form-group">
                                            <label class="control-label col-md-3">Payment Transaction:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> #{{ $payments->transaction_number }}
                                            </p>
                                            </div>
                                            <label class="control-label col-md-3">Total Amount:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $payments->total_amount }} </p>
                                            </div>
                                            <label class="control-label col-md-3">Paid Amount:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $payments->paid_amount }} </p>
                                            </div>
                                            <label class="control-label col-md-3">Payment Type:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $payments->payment_type }} </p>
                                            </div>
                                            </div>
                                            </div>
                                            <div class="seprator-block" style="padding-bottom: 10px;padding-top: 10px;border-radius: 0;box-shadow: none !important;color: #878787;height: 42px;"></div>
                                            <hr class="light-grey-hr"/>
                                            @endforeach
                                            </div>
                                            <!----end Payment Section --->
                                            <!---Cancellation Details----->
                                            @if(!empty($booking_cancellation))
                                            <div class="seprator-block"></div>
                                            <hr class="light-grey-hr"/>
                                            <h6 class=""style="text-align: center;">Cancellation Details</h6>

                                            <div class="row">
                                            @foreach($booking_cancellation as $cancellation)
                                            <div class="col-md-12">
                                            <div class="form-group">
                                            <label class="control-label col-md-3">Cancellation Transaction:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> #{{ $cancellation->transaction_number }}
                                            </p>
                                            </div>
                                            <label class="control-label col-md-3">Cancellation Reason:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $cancellation->cancellation_reason }} </p>
                                            </div>
                                            <label class="control-label col-md-3">Cancellation Type:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $cancellation->cancellation_type }} </p>
                                            </div>
                                            <label class="control-label col-md-3">Cancellation Charge:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $cancellation->cancellation_charge }} </p>
                                            </div>
                                            </div>
                                            </div>
                                            <div class="seprator-block" style="padding-bottom: 10px;padding-top: 10px;border-radius: 0;box-shadow: none !important;color: #878787;height: 42px;"></div>
                                            <hr class="light-grey-hr"/>
                                            @endforeach
                                            </div>
                                            <!----end Cancellation Section --->
                                            @endif
                                                <div class="seprator-block"></div>

                                                <hr class="light-grey-hr"/>

                                                <h6 class=""style="text-align: center;">Passenger Details</h6>

                                               

                                            <div class="row">
                                            @foreach($booking_passengers as $passenger)
                                            <div class="col-md-12">
                                            <div class="form-group">
                                            <label class="control-label col-md-3">Passenger Name:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $passenger->first_name }} {{ $passenger->last_name }}
                                            @if($passenger->status == 0)
                                            <span class="label label-danger">Cancelled</span>
                                            @endif
                                            </p>
                                            </div>
                                            <label class="control-label col-md-3">Date of Birth:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $passenger->dob }} </p>
                                            </div>
                                            <label class="control-label col-md-3">Gender:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $passenger->gender }} </p>
                                            </div>
                                            </div>
                                            </div>
                                            <div class="seprator-block" style="padding-bottom: 10px;padding-top: 10px;border-radius: 0;box-shadow: none !important;color: #878787;height: 42px;"></div>
                                            <hr class="light-grey-hr"/>
                                            @endforeach
                                            </div>

                                               <!----Report---->
                                               
                                            @if($bookingdetails->report != null)
                                            <hr class="light-grey-hr"/>
                                            <h6 class=""style="text-align: center;">Report</h6>
                                            <!--------------------------------------->
                                            <div class="row">
                                            <div class="col-md-12">
                                            <div class="form-group">
                                            <label class="control-label col-md-3">Report:</label>
                                            <div class="col-md-9">
                                            <p class="form-control-static"> {{ $bookingdetails->report }}
                                            </p>
                                            </div>
                                            </div>
                                            </div>
                                            <div class="seprator-block" style="padding-bottom: 10px;padding-top: 10px;border-radius: 0;box-shadow: none !important;color: #878787;height: 42px;"></div>
                                            <hr class="light-grey-hr"/>
                                            <!--/span-->
                                            </div>
                                            @endif


                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- /Row -->

            </div>

        </div>



        @endsection



        @push('scripts')

            <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>

            <script>

                $('#lfm').filemanager('image');

            </script>

            <style>

                .scrollable {

                    max-height: 200px; /* Adjust the height as needed */

                    overflow-y: auto;

                }

            </style>

    @endpush