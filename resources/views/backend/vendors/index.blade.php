@extends('backend.layouts.master')
@section('title','Vendors | Wishmytour Admin')
@section('main-content')
<!-- Add this to the head section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">

<!-- Add this before the closing body tag -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>

    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Vendors</h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table id="datable_1" class="table table-hover display pb-30">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Joined at</th>
                                            <th>Admin Verified</th>
                                            <th>Bank Verified</th>
                                            <th>GST Verified</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Joined at</th>
                                            <th>Admin Verified</th>
                                            <th>Bank Verified</th>
                                            <th>GST Verified</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($vendors as $vendor)
                                            <tr>
                                                <td>{{ $vendor->vendor_name ?? '-' }}</td>
                                                <td>{{ $vendor->email }}</td>
                                                <td>{{ $vendor->created_at ? $vendor->created_at->diffForHumans() : '' }}</td>
                                                <td>
                                                    <span class="label label-{{ $vendor->is_verified ? 'success' : 'warning' }}">
                                                        {{ $vendor->is_verified ? 'Verified' : 'Not Verified' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="label label-{{ $vendor->bank_verified ? 'success' : 'warning' }}">
                                                        {{ $vendor->bank_verified ? 'Verified' : 'Not Verified' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="label label-{{ $vendor->gst_verified ? 'success' : 'warning' }}">
                                                        {{ $vendor->gst_verified ? 'Verified' : 'Not Verified' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="no-margin-switcher">
                                                        <input type="checkbox" class="js-switch toggle-status" data-id="{{ $vendor->id }}" {{ $vendor->status ? 'checked' : '' }}>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details">
                                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                    </a>
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

        // Initialize Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            new Switchery(html, { color: '#2ecd99', secondaryColor: '#dedede', size: 'small' });
        });

        // Handle status toggle
        $('.toggle-status').change(function() {
            var vendorId = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("admin.vendors.toggleStatus") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: vendorId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        alert('Status updated successfully.');
                    } else {
                        alert('Failed to update status.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
@endpush
