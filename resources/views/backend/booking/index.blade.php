@extends('backend.layouts.master')
@section('title','Bookings || Wishmytour Admin')
@section('main-content')

<!-- Include CSS for Switchery -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">

<!-- Inline style -->
<style>
    th:after {
        display: none !important;
    }
    .odd { background:none !important;}
    .sorting_1 { background:none !important; }
    th { font-weight:unset !important; }
</style>

<!-- Row -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">
            <div class="panel-heading">
                <div class="pull-left">
                    <h6 class="panel-title txt-dark">All Bookings</h6>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="tab-struct custom-tab-1 mt-40">
                        <ul role="tablist" class="nav nav-tabs" id="myTabs_7">
                            <li class="active" role="presentation"><a aria-expanded="true" data-toggle="tab" role="tab" id="open_tab_7" href="#open">Completed</a></li>
                            <li role="presentation" class=""><a data-toggle="tab" id="invoiced_tab_7" role="tab" href="#invoiced" aria-expanded="false">Upcoming</a></li>
                            <li role="presentation" class=""><a data-toggle="tab" id="paid_tab_7" role="tab" href="#paid" aria-expanded="false">Cancelled</a></li>
                            <li role="presentation" class=""><a data-toggle="tab" id="dis_tab_7" role="tab" href="#dis" aria-expanded="false">Disputed</a></li>
                        </ul>
                        <div class="tab-content" id="myTabContent_7">
                            <div id="open" class="tab-pane fade active in" role="tabpanel">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table id="datable_1" class="table table-hover display pb-30">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @foreach($resultBookings as $booking)
                                                    <tr>
                                                        <td>#{{ $booking->bookings_id }}</td>
                                                        <td>{{ $booking->name }}</td>
                                                        <td>{{ $booking->vendor_fullname }}</td>
                                                        <td>{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</td>
                                                        <td>{{ $booking->total_pax }}</td>
                                                        <td>{{ $booking->created_at }}</td>
                                                        <td>{{ $booking->first_booking_date }}</td>
                                                        <td>{{ $booking->last_booking_date }}</td>
                                                        <td>
                                                            @if($booking->booking_status == 1)
                                                                <span class="label label-success">In process</span>
                                                            @elseif($booking->booking_status == 2)
                                                                <span class="label label-success">Confirmed</span>
                                                            @elseif($booking->booking_status == 3)
                                                                <span class="label label-success">Completed</span>
                                                            @elseif($booking->booking_status == 4)
                                                                <span class="label label-danger">Cancelled</span>
                                                            @elseif($booking->booking_status == 5)
                                                                <span class="label label-warning">Modified</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.booking.view', $booking->bookings_id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details">
                                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="invoiced" class="tab-pane fade" role="tabpanel">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table id="datable_2" class="table table-hover display pb-30">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @foreach($confirmbookings as $booking)
                                                    <tr>
                                                        <td>#{{ $booking->bookings_id }}</td>
                                                        <td>{{ $booking->name }}</td>
                                                        <td>{{ $booking->vendor_fullname }}</td>
                                                        <td>{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</td>
                                                        <td>{{ $booking->total_pax }}</td>
                                                        <td>{{ $booking->created_at }}</td>
                                                        <td>{{ $booking->first_booking_date }}</td>
                                                        <td>{{ $booking->last_booking_date }}</td>
                                                        <td>
                                                            @if($booking->booking_status == 1)
                                                                <span class="label label-success">In process</span>
                                                            @elseif($booking->booking_status == 2)
                                                                <span class="label label-success">Confirmed</span>
                                                            @elseif($booking->booking_status == 3)
                                                                <span class="label label-success">Completed</span>
                                                            @elseif($booking->booking_status == 4)
                                                                <span class="label label-danger">Cancelled</span>
                                                            @elseif($booking->booking_status == 5)
                                                                <span class="label label-warning">Modified</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.booking.view', $booking->bookings_id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details">
                                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="paid" class="tab-pane fade" role="tabpanel">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table id="datable_3" class="table table-hover display pb-30">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @foreach($cancelbookings as $booking)
                                                    <tr>
                                                        <td>#{{ $booking->bookings_id }}</td>
                                                        <td>{{ $booking->name }}</td>
                                                        <td>{{ $booking->vendor_fullname }}</td>
                                                        <td>{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</td>
                                                        <td>{{ $booking->total_pax }}</td>
                                                        <td>{{ $booking->created_at }}</td>
                                                        <td>{{ $booking->first_booking_date }}</td>
                                                        <td>{{ $booking->last_booking_date }}</td>
                                                        <td>
                                                            @if($booking->booking_status == 1)
                                                                <span class="label label-success">In process</span>
                                                            @elseif($booking->booking_status == 2)
                                                                <span class="label label-success">Confirmed</span>
                                                            @elseif($booking->booking_status == 3)
                                                                <span class="label label-success">Completed</span>
                                                            @elseif($booking->booking_status == 4)
                                                                <span class="label label-danger">Cancelled</span>
                                                            @elseif($booking->booking_status == 5)
                                                                <span class="label label-warning">Modified</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.booking.view', $booking->bookings_id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details">
                                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="dis" class="tab-pane fade" role="tabpanel">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table id="datable_4" class="table table-hover display pb-30">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Package Name</th>
                                                    <th>Vendor Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Pax</th>
                                                    <th>Booking On</th>
                                                    <th>Tour Start Date</th>
                                                    <th>Tour End Date</th>
                                                    <th>Booking Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @foreach($disputedbookings as $booking)
                                                    <tr>
                                                        <td>#{{ $booking->bookings_id }}</td>
                                                        <td>{{ $booking->name }}</td>
                                                        <td>{{ $booking->vendor_fullname }}</td>
                                                        <td>{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</td>
                                                        <td>{{ $booking->total_pax }}</td>
                                                        <td>{{ $booking->created_at }}</td>
                                                        <td>{{ $booking->first_booking_date }}</td>
                                                        <td>{{ $booking->last_booking_date }}</td>
                                                        <td>
                                                                <span class="label label-warning">Disputed</span>
                                                         
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.booking.view', $booking->bookings_id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details">
                                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
<!-- Include DataTables CSS -->
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
@endpush

@push('scripts')
<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTables for each table
        $('#datable_1').DataTable();
        $('#datable_2').DataTable();
        $('#datable_3').DataTable();
        $('#datable_4').DataTable();

        // Initialize Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            new Switchery(html, { color: '#2ecd99', secondaryColor: '#dedede', size: 'small' });
        });
    });
</script>
@endpush
