@extends('backend.layouts.master')
@section('title','Customer Bookings || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Customer Bookings</h6>
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
                                            <th>S.N.</th>
                                            <th>Booking ID</th>
                                            <th>Package Name</th>
                                            <th>Vendor Name</th>
                                            <th>First Booking Date</th>
                                            <th>Last Booking Date</th>
                                            <th>Total Pax</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Booking Number</th>
                                            <th>Package Name</th>
                                            <th>Vendor Name</th>
                                            <th>First Booking Date</th>
                                            <th>Last Booking Date</th>
                                            <th>Total Pax</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($bookings as $key => $booking)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $booking->booking_number }}</td>
                                                <td>{{ $booking->name }}</td>
                                                <td>{{ $booking->vendor_fullname }}</td>
                                                <td>{{ $booking->first_booking_date }}</td>
                                                <td>{{ $booking->last_booking_date }}</td>
                                                <td>{{ $booking->total_pax }}</td>
                                                <td>{{ $booking->booking_status }}</td>
                                                <td>{{ $booking->created_at }}</td>
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
    <!-- /Row -->
@endsection

@push('styles')

@endpush

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#datable_1').DataTable();
    });
</script>
@endpush
