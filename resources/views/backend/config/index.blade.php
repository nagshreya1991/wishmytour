@extends('backend.layouts.master')
@section('title','Configs || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Config List</h6>
                     </div>
                     <div class="pull-right">
                        <a href="{{ route('admin.config.create') }}" class="btn btn-primary">Create Configs</a>
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
                <th>Title</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
                <th>S.N.</th>
                <th>Title</th>
                <th>Name</th>
                <th>Action</th>
              </tr>
          </tfoot>
          <tbody>
          @foreach($configs as $config)   
                <tr>
                   <td>{{$config->id}}</td>
                   <td>{{$config->title}}</td>
                   <td>{{$config->name}}</td>
                   <td>
                    <a href="{{ route('admin.config.show', $config->id) }}"
                        class="pr-10"
                        data-toggle="tooltip" data-original-title="Details"><i
                                class="zmdi zmdi-file text-primary" aria-hidden="true"></i></a>
                    <a href="{{ route('admin.config.edit', $config->id) }}"
                        class="pr-10" data-toggle="tooltip" data-original-title="Edit" aria-hidden="true">
                        <i class="zmdi zmdi-edit text-success"></i></a>
                       {{-- <form id="delete-form-{{ $config->id }}" action="{{ route('admin.config.destroy', $config->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <!-- Delete button should be placed here -->
                            <a href="#" class="text-inverse delete-warning" title="" data-toggle="tooltip" data-original-title="Delete" aria-hidden="true">
                                <i class="zmdi zmdi-delete text-danger"></i>
                            </a>
                        </form>--}}
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
    $('.delete-warning').on('click', function (e) {
    e.preventDefault();

    console.log("Delete button clicked"); // Debug message

    // Check if the form ID exists
    var formId = $(this).closest('form').attr('id');
    if (!formId) {
        console.error("Form ID not found"); // Debug message
        return; // Exit the function
    }

    var configId = formId.split('-').pop();
    console.log("Config ID:", configId); // Debug message

    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this config!",
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