@extends('backend.layouts.master')
@section('title', 'Coupon Bookings | Wishmytour Admin')
@section('main-content')

<!-- Title -->
<div class="row heading-bg">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
        <h5 class="txt-dark">Bookings for Coupon: {{ $coupon->code }}</h5>
    </div>
    <!-- Breadcrumb -->
    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.coupons.index') }}"><span>Coupons</span></a></li>
            <li class="active"><span>Coupon Bookings</span></li>
        </ol>
    </div>
    <!-- /Breadcrumb -->
</div>
<!-- /Title -->

<!-- Row -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover display pb-30">
                                <thead>
                                    <tr>
                                        <th>Booking Number</th>
                                        <th>Customer Name</th>
                                        <th>Customer Email</th>
                                        <th>Customer Phone Number</th>
                                        <th>Booking Created At</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Booking Number</th>
                                        <th>Customer Name</th>
                                        <th>Customer Email</th>
                                        <th>Customer Phone Number</th>
                                        <th>Booking Created At</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->booking_number }}</td>
                                            <td>
                                                {{ $booking->name }} 
                                            </td>
                                            <td>
                                                {{ $booking->email }} 
                                            </td>
                                            <td>
                                                {{ $booking->phone_number }} 
                                            </td>

                                            <td>{{ $booking->created_at }}</td>
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
    <!-- Datatables CSS -->
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Datatables JavaScript -->
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@endpush
