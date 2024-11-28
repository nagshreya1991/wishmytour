@extends('backend.layouts.master')
@section('title','Page | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Pages</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.pages.index') }}"><span>Pages</span></a></li>
                <li class="active"><span>view page</span></li>
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
                                            <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about page</h6>
                                            <hr class="light-grey-hr"/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Page Title:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $page->title }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                </div>
                                            <!-- /Row -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Page Slug:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $page->slug }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <!-- /Row -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Page Content:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ strip_tags($page->content) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Row -->

                                            <!-- Meta Information -->
                                            <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>Meta Information</h6>
                                            <hr class="light-grey-hr"/>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Meta Title:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $page->meta_title }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Meta Description:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ strip_tags($page->meta_description) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Meta Keywords:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $page->meta_keywords }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Row -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Is Published:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $page->is_published ? 'Yes' : 'No' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Row -->

                                            <div class="form-actions mt-10">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-offset-3 col-md-9">
                                                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-success btn-icon left-icon mr-10">
                                                                    <i class="zmdi zmdi-edit"></i> <span>edit</span>
                                                                </a>
                                                                <a href="{{ route('admin.pages.index') }}" class="btn btn-default">Cancel</a>
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
