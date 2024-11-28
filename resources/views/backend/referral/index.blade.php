@extends('backend.layouts.master')
@section('title','Referral || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Referral List</h6>
                     </div>
                     <div class="pull-right">
                        <a href="{{ route('admin.referrals.create') }}" class="btn btn-primary">Create referral</a>
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
                <th>Referral Code</th>
                <th>Discount Amount</th>
                <th>Referral Start Date</th>
                <th>Referral End Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
            <th>S.N.</th>
                <th>Referral Code</th>
                <th>Discount Amount</th>
                <th>Referral Start Date</th>
                <th>Referral End Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
          </tfoot>
          <tbody>
          @foreach($referrals as $referral)   
                <tr>
                   <td>{{$referral->id}}</td>
                   <td>{{$referral->code}}</td>
                  
                   <td>@if($referral->discount_type == 'percentage')
                    {{$referral->discount_amount}}%
                     @else
                    {{$referral->discount_amount}}
                     @endif
                    </td>
                   
                   <td>{{$referral->start_date}} </td>
                   <td>{{$referral->end_date}}</td>
                   <td>@if($referral->status == 1)
                    <span class="label label-success"> Active</span>
                     @else
                     <span class="label label-warning">Inactive</span>
                     @endif
                    </td>
                 
                    <td>
                    <a href="{{ route('admin.referrals.show', $referral->id) }}"
                        class="pr-10"
                        data-toggle="tooltip" data-original-title="Details"><i
                                class="zmdi zmdi-file text-primary" aria-hidden="true"></i></a>
                    <a href="{{ route('admin.referrals.edit', $referral->id) }}"
                        class="pr-10" data-toggle="tooltip" data-original-title="Edit" aria-hidden="true">
                        <i class="zmdi zmdi-edit text-success"></i></a>
                        <form id="delete-form-{{ $referral->id }}" action="{{ route('admin.referrals.destroy', $referral->id) }}" method="POST">
    @csrf
    @method('DELETE')

    <!-- Delete button should be placed here -->
    <a href="#" class="text-inverse delete-warning" title="" data-toggle="tooltip" data-original-title="Delete" aria-hidden="true">
        <i class="zmdi zmdi-delete text-danger"></i>
    </a>
</form>
                              </td>
                   
                    {{-- Delete Modal --}}
                    {{-- <div class="modal fade" id="delModal{{$referral->id}}" tabindex="-1" role="dialog" aria-labelledby="#delModal{{$referral->id}}Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="#delModal{{$referral->id}}Label">Delete referral</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form method="post" action="{{ route('referrals.destroy',$referral->id) }}">
                                @csrf 
                                @method('delete')
                                <button type="submit" class="btn btn-danger" style="margin:auto; text-align:center">Parmanent delete referral</button>
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
    $('.delete-warning').on('click', function (e) {
    e.preventDefault();

    console.log("Delete button clicked"); // Debug message

    // Check if the form ID exists
    var formId = $(this).closest('form').attr('id');
    if (!formId) {
        console.error("Form ID not found"); // Debug message
        return; // Exit the function
    }

    var referralId = formId.split('-').pop();
    console.log("Referral ID:", referralId); // Debug message

    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this referral!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f0c541",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        console.log("Confirm button clicked"); // Debug message
        if (isConfirm) {
            console.log("Submitting form for deletion..."); // Debug message
            $('#' + formId).submit();
            swal("Deleted!", "Your item has been deleted.", "success");
        }
    });

    return false;
});
</script>

@endpush