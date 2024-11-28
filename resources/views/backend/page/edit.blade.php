@extends('backend.layouts.master')
@section('title','Page Edit | Wishmytour Admin')
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
                <li class="active"><span>edit page</span></li>
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
                            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about
                                page</h6>
                                <hr class="light-grey-hr"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Page Title:</label>
                                            <input type="text" class="form-control" placeholder="Title" id="title" name="title" value="{{ $page->title }}" required />
                                        </div>
                                    </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Page Slug:</label>
                                            <input type="text" class="form-control" placeholder="Slug" id="slug" name="slug" value="{{ $page->slug }}" required />
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!-- /Row -->

                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Page Content:</label>
                                            <textarea class="form-control" id="content" name="content" rows="10">{{ $page->content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Row -->
                                
                                <!-- Meta Information -->
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>Meta Information</h6>
                                <hr class="light-grey-hr"/>
                                
                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Meta Title:</label>
                                            <input type="text" class="form-control" placeholder="Meta Title" id="meta_title" name="meta_title" value="{{ $page->meta_title }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Meta Description:</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ $page->meta_description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Meta Keywords:</label>
                                            <input type="text" class="form-control" placeholder="Meta Keywords" id="meta_keywords" name="meta_keywords" value="{{ $page->meta_keywords }}" />
                                        </div>
                                    </div>
                                </div>
                                <!-- /Row -->

                                <!-- Row -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label mb-10">Is Published:</label>
                                            <select class="form-control" id="is_published" name="is_published">
                                                <option value="0" {{ $page->is_published == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ $page->is_published == 1 ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Row -->

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success btn-icon left-icon mr-10 pull-left"><i class="fa fa-check"></i> <span>update</span></button>
                                    <button type="button" class="btn btn-warning pull-left" onclick="window.location='{{ route('admin.pages.index') }}'">Cancel</button>
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
    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content', {
            extraPlugins: 'pastefromword',
        });
    </script>
@endpush