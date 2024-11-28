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
                    @if($booking->booking_status == 3)
                        <span class="label label-success">Completed</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.booking.view', $booking->bookings_id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete">
                        <i class="zmdi zmdi-delete text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
