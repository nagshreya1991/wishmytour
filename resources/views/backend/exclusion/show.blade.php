@extends('backend.layouts.master')
@section('title','Exclusion Edit | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Exclusions</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.exclusions.index') }}"><span>exclusions</span></a></li>
                <li class="active"><span>edit exclusion</span></li>
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
                                                exclusion</h6>
                                            <hr class="light-grey-hr"/>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"> {{ $exclusion->name }} </p>
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
                                                                <a href="{{ route('admin.exclusions.edit', $exclusion->id) }}"
                                                                   class="btn btn-success btn-icon left-icon mr-10"><i
                                                                            class="zmdi zmdi-edit"></i>
                                                                    <span>edit</span></a>
                                                                <a href="{{ route('admin.exclusions.index') }}" class="btn btn-default">Cancel
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