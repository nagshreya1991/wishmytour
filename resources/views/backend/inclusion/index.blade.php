@extends('backend.layouts.master')
@section('title','Inclusions | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Inclusions</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li class="active"><span>inclusions</span></li>
            </ol>
        </div>
        <!-- /Breadcrumb -->
    </div>
    <!-- /Title -->
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Inclusions</h6>
                    </div>
                    <div class="pull-right">
                        <a href="{{ route('admin.inclusions.create') }}" class="btn btn-primary">Create</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table id="datable_1" class="table table-hover display  pb-30">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach($inclusions as $inclusion)
                                        <tr>
                                            <td>{{ $inclusion->id }}</td>
                                            <td>{{ $inclusion->name }}</td>
                                            <td>
                                                <a href="{{ route('admin.inclusions.show', $inclusion->id) }}"
                                                   class="pr-10"
                                                   data-toggle="tooltip" data-original-title="Details"><i
                                                            class="zmdi zmdi-file text-primary" aria-hidden="true"></i></a>
                                                <a href="{{ route('admin.inclusions.edit', $inclusion->id) }}"
                                                   class="pr-10" data-toggle="tooltip" data-original-title="Edit" aria-hidden="true">
                                                    <i class="zmdi zmdi-edit text-success"></i></a>
                                                <a href="#"
                                                   class="text-inverse delete-warning" title=""
                                                   data-toggle="tooltip" data-original-title="Delete" aria-hidden="true">
                                                    <i class="zmdi zmdi-delete text-danger"></i></a>
                                                <form id="delete-form-{{ $inclusion->id }}"
                                                      action="{{ route('admin.inclusions.destroy', $inclusion->id) }}"
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
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

@endpush

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize DataTable for datable_1
            $('#datable_1').DataTable();
        });

        $('.delete-warning').on('click', function (e) {
            e.preventDefault();

            var inclusionId = $(this).data('inclusion-id');

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this inclusion!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f0c541",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $('#delete-form-' + inclusionId).submit();
                    swal("Deleted!", "Your item has been deleted.", "success");
                }
            });
            return false;
        });
    </script>
@endpush