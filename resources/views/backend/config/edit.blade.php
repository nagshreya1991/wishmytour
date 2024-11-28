@extends('backend.layouts.master')
@section('title','Config Edit | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Configs</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.config.index') }}"><span>Configs</span></a></li>
                <li class="active"><span>edit config</span></li>
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
                            <form action="{{ route('admin.config.update', $config->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about
                                config</h6>
                                <hr class="light-grey-hr"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10"> Title:</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Title" id="title" name="title" value="{{ $config->title }}" required />
                                        </div>
                                    </div> 
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Name:</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Code" id="name" name="name" value="{{ $config->name }}" required />
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!-- Row -->
                                <div class="row">
                                <div class="col-md-6">
                                                   <div class="form-group">
                                                        <label class="control-label mb-10">Value:</label>
                                                        <input type="text" class="form-control"
                                                   placeholder="Value" id="value" name="value" value="{{ $config->value }}" required />
                                                    </div>
                                                </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Regex:</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Regex" id="regex" name="regex" value="{{ $config->regex }}" required />
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!-- Row -->
                               
                               


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