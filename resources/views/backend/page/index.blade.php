@extends('backend.layouts.master')
@section('title','Page || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Page List</h6>
                     </div>
                     <div class="pull-right">
                        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">Create Page</a>
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
                <th>Page Title</th>
                <th>Meta Title</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
                <th>S.N.</th>
                <th>Page Title</th>
                <th>Meta Title</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
          </tfoot>
          <tbody>
          @foreach($pages as $page)   
                <tr>
                   <td>{{$page->id}}</td>
                   <td>{{$page->title}}</td>
                   <td>{{$page->meta_title}}</td>
                  
                   <td>@if($page->is_published == 1)
                    <span class="label label-success"> Active</span>
                     @else
                     <span class="label label-warning">Inactive</span>
                     @endif
                    </td>
                 
                    <td>
                    <a href="{{ route('admin.pages.show', $page->id) }}"
                        class="pr-10"
                        data-toggle="tooltip" data-original-title="Details"><i
                                class="zmdi zmdi-file text-primary" aria-hidden="true"></i></a>
                    <a href="{{ route('admin.pages.edit', $page->id) }}"
                        class="pr-10" data-toggle="tooltip" data-original-title="Edit" aria-hidden="true">
                        <i class="zmdi zmdi-edit text-success"></i></a>
                        {{--    <form id="delete-form-{{ $page->id }}" action="{{ route('admin.pages.destroy', $page->id) }}" method="POST">
    @csrf
    @method('DELETE')

    <!-- Delete button should be placed here -->
    <a href="#" class="text-inverse delete-warning" title="" data-toggle="tooltip" data-original-title="Delete" aria-hidden="true">
        <i class="zmdi zmdi-delete text-danger"></i>
    </a>
</form>--}}
                              </td>
                   
                    {{-- Delete Modal --}}
                    {{-- <div class="modal fade" id="delModal{{$page->id}}" tabindex="-1" role="dialog" aria-labelledby="#delModal{{$page->id}}Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="#delModal{{$page->id}}Label">Delete page</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form method="post" action="{{ route('pages.destroy',$page->id) }}">
                                @csrf 
                                @method('delete')
                                <button type="submit" class="btn btn-danger" style="margin:auto; text-align:center">Parmanent delete page</button>
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

    var pageId = formId.split('-').pop();
    console.log("Page ID:", pageId); // Debug message

    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this page!",
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