@extends('backend.layouts.master')
@section('title','Refund Bookings | Wishmytour Admin')
@section('main-content')

<!-- Add this to the head section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">

<!-- Add this before the closing body tag -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>

<!-- Row -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">
            <div class="panel-heading">
                <div class="pull-left">
                    <h6 class="panel-title txt-dark">Refund Bookings</h6>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table id="datable_1" class="table table-hover display pb-30">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Cancellation Type</th>
                                        <th>Transaction Number</th>
                                        <th>Booking Amount</th>
                                        <th>Cancellation + GST Amount</th>
                                        <th>Refund Amount</th>
                                        <th>Cancellation Date</th>
                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Cancellation Type</th>
                                        <th>Transaction Number</th>
                                        <th>Booking Amount</th>
                                        <th>Cancellation + GST Amount</th>
                                        <th>Refund Amount</th>
                                        <th>Cancellation Date</th>
                                        
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach($cancelledBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->booking_number }}</td>
                                            <td>{{ $booking->cancellation_type }}</td>
                                            <td>{{ $booking->transaction_number }}</td>
                                            <td>{{ number_format($booking->booking_price, 2) }}</td>
                                            <td>{{ number_format($booking->cancellation_charge + $booking->gst_charge, 2) }}</td>
                                            <td>{{ number_format($booking->refund_amount, 2) }}</td>
                                            <td>{{ $booking->created_at }}</td>
                                            
                                            <td>
                                            <a href="{{ route('admin.booking.refund-show', $booking->id) }}" class="pr-10" data-toggle="tooltip" data-original-title="View Details">
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
<!-- /Row -->
@endsection

@push('styles')
@endpush

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable for datable_1
        $('#datable_1').DataTable();

        // Initialize Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            new Switchery(html, { color: '#2ecd99', secondaryColor: '#dedede', size: 'small' });
        });
    });
</script>
@endpush
