@extends('backend.layouts.master')
@section('title',' All Notifications || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Notifications</h6>
                    </div>
                    <div class="clearfix"></div>
                    <!-- <a href="#" class="btn btn-danger clear-all-notifications">Clear All</a> -->
<a href="#" class="btn btn-danger remove-all-notifications">Remove All</a>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover display pb-30">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Time</th>
                                            <th>Title</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Time</th>
                                            <th>Title</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{($notification->created_at) ? $notification->created_at->diffForHumans() : ''}}</td>
                                            <td>{{$notification->message}}</td>
                                            <td>
                                                <a href="#" class="text-inverse delete-warning" data-notification-id="{{$notification->id}}" title="Delete" data-toggle="tooltip" data-original-title="Delete" aria-hidden="true">
                                                    <i class="zmdi zmdi-delete text-danger"></i>
                                                </a>
                                                <form id="delete-form-{{$notification->id}}" action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST" style="display: none;">
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

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable for datatable_1
        $('#datatable_1').DataTable();
        
        $('.delete-warning').on('click', function (e) {
            e.preventDefault();

            var notificationId = $(this).data('notification-id');

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this notification!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f0c541",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $('#delete-form-' + notificationId).submit();
                    swal("Deleted!", "Your notification has been deleted.", "success");
                }
            });
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        // Handle click event for clearing all notifications
        $('.clear-all-notifications').on('click', function(e) {
            e.preventDefault();
            clearAllNotifications();
        });

        // Handle click event for removing all notifications
        $('.remove-all-notifications').on('click', function(e) {
            e.preventDefault();
            removeAllNotifications();
        });
    });

    function clearAllNotifications() {
        $.ajax({
            url: "{{ route('admin.notifications.clearAll') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Handle success
                    console.log('All notifications cleared.');
                } else {
                    // Handle error
                    console.error('Failed to clear all notifications.');
                }
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Failed to clear all notifications.');
            }
        });
    }

    function removeAllNotifications() {
    $.ajax({
        url: "{{ route('admin.notifications.removeAll') }}",
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Handle success
                console.log('All notifications removed.');
                // Reload the page
                location.reload();
            } else {
                // Handle error
                console.error('Failed to remove all notifications.');
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error('Failed to remove all notifications.');
        }
    });
}
</script>
@endpush
