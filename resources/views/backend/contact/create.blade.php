@extends('backend.layouts.master')
@section('title','Coupon Create | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Coupons</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.coupons.index') }}"><span>Coupons</span></a></li>
                <li class="active"><span>add coupon</span></li>
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
                            <form action="{{ route('admin.coupons.store') }}" method="POST">
                                @csrf
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about
                                coupon</h6>
                                <hr class="light-grey-hr"/>
                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Coupon Code</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Coupon Code" id="code" name="code" required />
                                        </div>
                                      </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Coupon Description</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Coupon Description" id="description" name="description" required />
                                    </div>
                                        
                                </div>
                               </div>
                                <!-- /Row -->

                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Discount Type</label>
                                            <select name="discount_type" id="discount_type" class="form-control">
                                          <option value="Percentage">Percentage</option>
                                          <option value="amount">Amount</option>
                                           </select>
                                        </div>
                                      </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Discount Amount</label>
                                            <input type="number" class="form-control"
                                                   placeholder="Amount" id="discount_amount" name="discount_amount" value="0"/>
                                         </div>
                                        
                                     </div>
                                 </div>
                                <!-- Row -->


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Start Date</label>
                                            <input type="datetime-local" class="form-control"
                                                   placeholder="Start Date" id="start_date" name="start_date"  />
                                        </div>
                                      </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">End Date</label>
                                            <input type="datetime-local" class="form-control"
                                                   placeholder="End Date" id="end_date" name="end_date"/>
                                    </div>
                                        
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success btn-icon left-icon mr-10 pull-left"><i
                                                class="fa fa-check"></i> <span>save</span></button>
                                    <button type="button" class="btn btn-warning pull-left">Cancel</button>
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
@endpush
@push('scripts')
@endpush