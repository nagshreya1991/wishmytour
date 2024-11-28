@extends('backend.layouts.master')
@section('title','Packages || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Packages</h6>
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
              <th>S.N.</th>
              <th>Package Name</th>
              <th>Vendor Name</th>
            
              <th>Starting Amount</th>
              <th>Created On</th>
              <th>Origin</th>
              <th>Status</th>
              <th>Verified</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
            <th>S.N.</th>
              <th>Package Name</th>
              <th>Vendor Name</th>
             
              <th>Starting Amount</th>
              <th>Created On</th>
              <th>Origin</th>
              <th>Status</th>
              <th>Verified</th>
              <th>Action</th>
              </tr>
          </tfoot>
          <tbody>
            @foreach($allpackages as $package)   
                <tr>
                    <td>{{$package->id}}</td>
                    <td>{{$package->name}}</td>
                    <td>{{$package->vendor_name}}</td>
                    <td>{{$package->starting_price}}</td>
                    <td>{{$package->created_at}}</td>
                    <td>{{ $package->origin_city_name}}</td>
                    <td>{{$package->status_text}}</td>
                    <td>@if($package->admin_verified == 1)
                    <span class="label label-success"> Verified</span>
                     @else
                     <span class="label label-warning">Not Verified</span>
                     @endif
                    </td>
                    <td>
                      <a href="{{route('admin.packages.view',$package->id)}}"
                                                   class="pr-10"
                                                   data-toggle="tooltip" data-original-title="Details">
                                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="text-inverse" title=""
                                                   data-toggle="tooltip" data-original-title="Delete"><i
                                                            class="zmdi zmdi-delete text-danger"></i></a>
                    </td>
                    {{-- Delete Modal --}}
                    {{-- <div class="modal fade" id="delModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="#delModal{{$user->id}}Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="#delModal{{$user->id}}Label">Delete user</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form method="post" action="{{ route('users.destroy',$user->id) }}">
                                @csrf 
                                @method('delete')
                                <button type="submit" class="btn btn-danger" style="margin:auto; text-align:center">Parmanent delete user</button>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div> --}}
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