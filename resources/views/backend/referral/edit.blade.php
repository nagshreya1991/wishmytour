@extends('backend.layouts.master')
@section('title','referral Edit | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">referrals</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.referrals.index') }}"><span>referrals</span></a></li>
                <li class="active"><span>edit referral</span></li>
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
                            <form action="{{ route('admin.referrals.update', $referral->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about
                                referral</h6>
                                <hr class="light-grey-hr"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">referral Code:</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Code" id="code" name="code" value="{{ $referral->code }}" required />
                                        </div>
                                    </div> 
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">referral Description:</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Code" id="description" name="description" value="{{ $referral->description }}" required />
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Discount Type:</label>
                                            <select name="discount_type" class="form-control">
                                             <option value="percentage" {{(($referral->discount_type=='percentage') ? 'selected' : '')}}>
                                            Percentage</option>
                                             <option value="amount" {{(($referral->discount_type=='amount') ? 'selected' : '')}}>
                                            Amount</option>
                                            </select>
                                                                       
                                        </div>
                                    </div> 
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Discount Amount:</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Amount" id="discount_amount" name="discount_amount" value="{{ $referral->discount_amount }}" required />
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!-- Row -->
                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Start Date:</label>
                                           
                                           <input type="datetime-local" class="form-control"
                                                   placeholder="Start date" id="start_date" name="start_date" value="{{ $referral->start_date }}" required  />
                                        </div>
                                    </div> 
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">End Date:</label>
                                           
                                            <input type="datetime-local" class="form-control"
                                                   placeholder="End Date" id="end_date" name="end_date" value="{{ $referral->end_date }}"/>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Status:</label>
                                           <select name="status" class="form-control">
                                             <option value="0" {{(($referral->status==0) ? 'selected' : '')}}>
                                            Inactive</option>
                                             <option value="1" {{(($referral->status==1) ? 'selected' : '')}}>
                                            Active</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <!--/span-->
                                  
                                </div>


                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success btn-icon left-icon mr-10 pull-left"><i
                                                class="fa fa-check"></i> <span>update</span></button>
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