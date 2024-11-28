<div class="table-responsive" id="processing-commissions-table">
    <form action="{{ route('admin.agents.bulkPayout') }}" method="POST">
        @csrf
        <table id="datable_4" class="table table-hover display pb-30">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Booking ID</th>
                    <th>Package Name</th>
                    <th>Booking On</th>
                    <th>Total Days</th>
                    <th>Commission</th>
                    <th>Group Commission</th>
                    <th>Commission Amount</th>
                    <th>Payment Status</th>
                    <th>Tour Complete Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultProccessBookings as $booking)
                <tr>
                    <td><input type="checkbox" name="bookings[]" value="{{ $booking->booking_id }}"></td>
                    <td>#{{ $booking->booking_id }}</td>
                    <td>{{ $booking->package_name }}</td>
                    <td>{{ $booking->created_at }}</td>
                    <td>{{ $booking->total_days }}</td>
                    <td>{{ $booking->booking_commission ?? '--' }}</td>
                    <td>{{ $booking->group_commission ?? '--' }}</td>
                    <td>{{ $booking->commission_amount ?? '--' }}</td>
                    <td>
                        @if($booking->payment_status == 'Paid')
                        <span class="label label-success">Paid</span>
                        @elseif($booking->payment_status == 'Pending')
                        <span class="label label-warning">Pending</span>
                        @elseif($booking->payment_status == 'Processing')
                        <span class="label label-blue">Processing</span>
                        @else
                        {{ $booking->payment_status }}
                        @endif
                    </td>
                    <td>{{ $booking->tour_complete_date ?? '--' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th>Booking ID</th>
                    <th>Package Name</th>
                    <th>Booking On</th>
                    <th>Total Days</th>
                    <th>Commission</th>
                    <th>Group Commission</th>
                    <th>Commission Amount</th>
                    <th>Payment Status</th>
                    <th>Tour Complete Date</th>
                </tr>
            </tfoot>
        </table>
        <button type="submit" class="btn btn-success">All Payout</button>
    </form>
</div>
