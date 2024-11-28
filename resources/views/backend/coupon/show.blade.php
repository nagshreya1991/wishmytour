@extends('backend.layouts.master')
@section('title', 'Coupon Edit | Wishmytour Admin')
@section('main-content')
<style>
.checkbox label:before { content: none; }
</style>
<!-- Title -->
<div class="row heading-bg">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
        <h5 class="txt-dark">Coupons</h5>
    </div>
    <!-- Breadcrumb -->
    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.coupons.index') }}"><span>Coupons</span></a></li>
            <li class="active"><span>Edit Coupons</span></li>
        </ol>
    </div>
    <!-- /Breadcrumb -->
</div>
<!-- /Title -->
<!-- Row -->
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="panel panel-default card-view pa-0">
            <div class="panel-wrapper collapse in">
                <div class="panel-body pa-0">
                    <div class="sm-data-box">
                        <div class="container-fluid">
                        
                            <div class="row">
                            <a href="{{ route('admin.coupons.bookings', $coupon->id) }}" >
                                <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                    <span class="txt-dark block counter"><span class="counter-anim">{{ $totalUsed }}</span></span>
                                    <span class="weight-500 uppercase-font block">Total Used</span>
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
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-body">
                                        <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>About Coupons</h6>
                                        <hr class="light-grey-hr"/>
                                        <!-- Coupon Details -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Coupon Code:</label>
                                                    <div class="col-md-9">
                                                        <p class="form-control-static"> {{ $coupon->code }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Coupon Description:</label>
                                                    <div class="col-md-9">
                                                        <p class="form-control-static"> {{ $coupon->description }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Discount Type:</label>
                                                    <div class="col-md-9">
                                                        <p class="form-control-static"> {{ $coupon->discount_type }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Discount Amount:</label>
                                                    <div class="col-md-9">
                                                        <p class="form-control-static"> {{ $coupon->discount_amount }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Start Date:</label>
                                                    <div class="col-md-9">
                                                        <p class="form-control-static"> {{ $coupon->start_date }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">End Date:</label>
                                                    <div class="col-md-9">
                                                        <p class="form-control-static"> {{ $coupon->end_date }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Status:</label>
                                                    <div class="col-md-9">
                                                        @if($coupon->status == 1)
                                                            <span class="label label-success">Active</span>
                                                        @else
                                                            <span class="label label-warning">Inactive</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Usage Statistics -->
                                     
                                        <!-- Customer List -->
                                        <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-account mr-10"></i>Eligible Customers</h6>
                                        <hr class="light-grey-hr"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Select Customers:</label>
                                                    <div class="col-md-12">
                                                        <table id="datable_1" class="table table-hover display pb-30">
                                                            <thead>
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Mobile</th>
                                                                <th>Joined At</th>
                                                            </tr>
                                                            </thead>
                                                            <tfoot>
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Mobile</th>
                                                                <th>Joined At</th>
                                                            </tr>
                                                            </tfoot>
                                                            <tbody>
                                                            @foreach($customers as $customer)
                                                                <tr>
                                                                    <td>
                                                                        <div class="checkbox checkbox-primary">
                                                                            <label>
                                                                                <input type="checkbox" name="customers[]" value="{{ $customer->id }}" {{ in_array($customer->id, explode(',', $coupon->user_id)) ? 'checked' : '' }}  style="opacity: unset;">
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @isset($customer->customerDetail)
                                                                            {{ $customer->customerDetail->first_name }} {{ $customer->customerDetail->last_name }}
                                                                        @else
                                                                            -
                                                                        @endisset
                                                                    </td>
                                                                    <td>{{ $customer->email }}</td>
                                                                    <td>{{ $customer->mobile }}</td>
                                                                    <td>{{ $customer->created_at ? $customer->created_at->diffForHumans() : '' }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Actions -->
                                        <div class="form-actions mt-10">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-offset-3 col-md-9">
                                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                                                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-default">Cancel</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6"></div>
                                            </div>
                                        </div>
                                    </div>
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
    <!-- Datatables CSS -->
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Datatables JavaScript -->
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#datable_1').DataTable();
        });
    </script>
@endpush
