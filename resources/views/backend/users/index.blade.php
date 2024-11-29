@extends('backend.layouts.master')
@section('title','Customers || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Customers</h6>
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
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Joined at</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Joined at</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                            {{$user->first_name}} {{$user->last_name}}
                                            </td>
                                            <td>{{$user->email}}</td>
                                            <td>{{$user->mobile}}</td>
                                            <td>{{(($user->created_at)? $user->created_at->diffForHumans() : '')}}</td>
                                            <td>
                                                <a href="{{route('admin.customers.show',$user->id)}}"
                                                   class="pr-10"
                                                   data-toggle="tooltip" data-original-title="Details">
                                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                </a>
                                                <!-- <a href="javascript:void(0)" class="text-inverse" title=""
                                                   data-toggle="tooltip" data-original-title="Delete"><i
                                                            class="zmdi zmdi-delete text-danger"></i></a> -->
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
        $(document).ready(function() {
            // Initialize DataTable for datable_1
            $('#datable_1').DataTable();
        });
    </script>
@endpush