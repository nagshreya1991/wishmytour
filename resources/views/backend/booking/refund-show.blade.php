@extends('backend.layouts.master')
@section('title', 'Refund Details || Wishmytour Admin')
@section('main-content')

<style>
    #details_table th, #details_table td {
        padding: 10px;
    }
</style>

<!-- Row -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">
            <div class="panel-heading">
                <div class="pull-left">
                    <h6 class="panel-title txt-dark">Refund Details</h6>
                </div>
                <div class="pull-right">
                    <a href="{{ route('admin.booking.refund-booking') }}" class="btn btn-primary">Back to List</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table id="details_table" class="table table-bordered">
                                <tr>
                                    <th>Booking ID</th>
                                    <td>#{{ $bookingCancellation->booking_number }}</td>
                                </tr>
                                <tr>
                                    <th>Cancellation Type</th>
                                    <td>{{ $bookingCancellation->cancellation_type }}</td>
                                </tr>
                                <tr>
                                    <th>Transaction Number</th>
                                    <td>{{ $bookingCancellation->transaction_number }}</td>
                                </tr>
                                <tr>
                                    <th>Booking Amount</th>
                                    <td>{{ number_format($bookingCancellation->booking_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Cancellation Charge</th>
                                    <td>{{ number_format($bookingCancellation->cancellation_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>GST Charge</th>
                                    <td>{{ number_format($bookingCancellation->gst_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Cancellation + GST Amount</th>
                                    <td>{{ number_format($bookingCancellation->cancellation_charge + $bookingCancellation->gst_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Refund Amount</th>
                                    <td>{{ number_format($bookingCancellation->refund_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Cancellation Reason</th>
                                    <td>{{ $bookingCancellation->cancellation_reason }}</td>
                                </tr>
                                <tr>
                                    <th>Cancellation Date</th>
                                    <td>{{ $bookingCancellation->created_at }}</td>
                                </tr>
                            </table>
                            <!-- Pay Now Form -->
                            <form action="{{ route('admin.refund.process') }}" method="POST">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $bookingCancellation->id }}">
                                <input type="hidden" name="refund_amount" value="{{ $bookingCancellation->refund_amount }}">
                                <input type="hidden" name="payment_transaction" value="{{ $bookingCancellation->payment_transaction }}">
                                <button type="submit" class="btn btn-primary">Pay Now</button>
                            </form>
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
<!-- Include any additional CSS if required -->
@endpush

@push('scripts')
<!-- Include any additional JS if required -->
@endpush
