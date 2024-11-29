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
                <li class="active"><span>edit referrals</span></li>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-wrap">
                                    <form class="form-horizontal" role="form">
                                        <div class="form-body">
                                            <h6 class="txt-dark capitalize-font"><i
                                                        class="zmdi zmdi-info-outline mr-10"></i>about
                                                        referrals</h6>
                                            <hr class="light-grey-hr"/>
                                            <div class="row">
                                                 <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">referral Code:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $referral->code }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">referral Description:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $referral->description }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Row -->
                                            <div class="row">
                                                 <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">Discount Type:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $referral->discount_type }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">Discount Amount:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $referral->discount_amount }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                 <!-- Row -->
                                                 <div class="row">
                                                 <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">Start Date:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $referral->start_date }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">End Date:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $referral->end_date }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <!-- Row -->
                                             <div class="row">
                                                 <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label col-md-3">Status:</label>
                                                        <div class="col-md-9">
                                                           
                                                            @if($referral->status == 1)
                                                            <span class="label label-success"> Active</span>
                                                            @else
                                                            <span class="label label-warning">Inactive</span>
                                                            @endif
                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                             
                                            </div>
                                            <!-- Row -->
                                            <div class="form-actions mt-10">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-offset-3 col-md-9">
                                                                <a href="{{ route('admin.referrals.edit', $referral->id) }}"
                                                                   class="btn btn-success btn-icon left-icon mr-10"><i
                                                                            class="zmdi zmdi-edit"></i>
                                                                    <span>edit</span></a>
                                                                <a href="{{ route('admin.referrals.index') }}" class="btn btn-default">Cancel
                                                                </a>
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
    </div>
    <!-- /Row -->
@endsection

@push('styles')
@endpush
@push('scripts')
@endpush