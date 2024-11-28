@extends('backend.layouts.master')
@section('title','Coupon Create | Wishmytour Admin')
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
                <li class="active"><span>Add Coupon</span></li>
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
                        <div class="form-wrap">
                            <form id="couponForm" action="{{ route('admin.coupons.store') }}" method="POST">
                                @csrf
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>About Coupon</h6>
                                <hr class="light-grey-hr"/>
                                <!-- Coupon Details -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Coupon Code</label>
                                            <input type="text" class="form-control" placeholder="Coupon Code" id="code" name="code" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Coupon Description</label>
                                            <input type="text" class="form-control" placeholder="Coupon Description" id="description" name="description" required />
                                        </div>
                                    </div>
                                </div>
                                <!-- Discount Details -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Discount Type</label>
                                            <select name="discount_type" id="discount_type" class="form-control">
                                                <option value="Percentage">Percentage</option>
                                                <option value="Amount">Amount</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Discount Amount</label>
                                            <input type="number" class="form-control" placeholder="Amount" id="discount_amount" name="discount_amount" value="0"/>
                                        </div>
                                    </div>
                                </div>
                                <!-- Date Range -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Start Date</label>
                                            <input type="datetime-local" class="form-control" placeholder="Start Date" id="start_date" name="start_date"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">End Date</label>
                                            <input type="datetime-local" class="form-control" placeholder="End Date" id="end_date" name="end_date"/>
                                        </div>
                                    </div>
                                </div>
                                <!-- Customer List -->
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-account mr-10"></i>Eligible Customers</h6>
                                <hr class="light-grey-hr"/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Select Customers:</label>
                                            <div class="col-md-12">
                                                <table id="customerTable" class="table table-hover display pb-30">
                                                    <thead>
                                                    <tr>
                                                        <th>Select</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Joined At</th>
                                                    </tr>
                                                    </thead>
                                                    <tfoot>
                                                    <tr>
                                                        <th>Select</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Joined At</th>
                                                    </tr>
                                                    </tfoot>
                                                    <tbody>
                                                    @foreach($customers as $customer)
                                                        <tr>
                                                            <td>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="customer-checkbox" value="{{ $customer->id }}" style="opacity: unset;">
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
                                                            <td>{{ $customer->created_at ? $customer->created_at->diffForHumans() : '' }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden Field for Customer IDs -->
                                <input type="hidden" id="user_ids" name="user_ids">
                                <!-- Actions -->
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success btn-icon left-icon mr-10 pull-left"><i class="fa fa-check"></i> <span>Save</span></button>
                                    <button type="button" class="btn btn-warning pull-left" onclick="window.location.href='{{ route('admin.coupons.index') }}'">Cancel</button>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
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
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Include DataTables JavaScript -->
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#customerTable').DataTable();

            // Function to update hidden input with selected customer IDs
            function updateSelectedCustomers() {
                var selectedCustomers = [];
                $('.customer-checkbox:checked').each(function() {
                    selectedCustomers.push($(this).val());
                });
                $('#user_ids').val(selectedCustomers.join(','));
            }

            // Bind the update function to checkbox changes
            $('.customer-checkbox').on('change', updateSelectedCustomers);
        });
    </script>
@endpush
