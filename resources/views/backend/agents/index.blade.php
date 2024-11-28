@extends('backend.layouts.master')
@section('title','Agents | Wishmytour Admin')
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
                        <h6 class="panel-title txt-dark">Agents</h6>
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
                                        <th>Last 3 mnths</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Joined at</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>Last 3 mnths</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Joined at</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
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
    <style type="text/css">
        #datable_1 {
            width: 100% !important;
        }
    </style>
@endpush

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable for datable_1
        $('#datable_1').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
            responsive: true,
            ajax: {
                url: "{{ route('admin.agents.data') }}",
                data: function (d) {
                    // Optionally send additional parameters
                    d.from_month = $('#from_month').val();
                    d.to_month = $('#to_month').val();
                }
            },
            columns: [
                {
                    data: 'last_3_months_paid',
                    name: 'last_3_months_paid',
                    render: function (data, type, row) {
                        return '<a href="' + '{{ route('admin.agents.commissions', ':id') }}'.replace(':id', row.user_id) + '">' + data + '</a>';
                    }
                },
                {
                    data: 'fullname',
                    name: 'fullname',
                    render: function (data, type, row) {
                        return data; // Full name is already combined
                    }
                },
                { data: 'agent_code', name: 'agent_code' },
                { data: 'mobile', name: 'mobile' },
                { data: 'email', name: 'email' },
                { data: 'formatted_date', name: 'formatted_date' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        return '<span class="no-margin-switcher"><input type="checkbox" class="js-switch toggle-status" data-id="' + row.user_id + '" ' + (data ? 'checked' : '') + '></span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<a href="{{ route('admin.agents.show', '') }}/' + row.user_id + '" class="pr-10" data-toggle="tooltip" data-original-title="Details"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>';
                    }
                }
            ],
            language: {
                emptyTable: "No data available in table",
                processing: "Loading..."
            },
            drawCallback: function() {
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                elems.forEach(function(html) {
                    new Switchery(html, { color: '#2ecd99', secondaryColor: '#dedede', size: 'small' });
                });
            }
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            new Switchery(html, { color: '#2ecd99', secondaryColor: '#dedede', size: 'small' });
        });

        // Handle status toggle
        $('.toggle-status').change(function() {
            var agentId = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0;
            var statusText = status === 0 ? 'inactive' : 'active';

            $.ajax({
                url: '{{ route("admin.agents.toggle-status") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: agentId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        //alert('Status updated successfully.');
                        // Show a toast notification
                        $.toast({
                            heading: 'Status updated successfully.',
                            text: 'Agent is now ' + statusText + '.',
                            position: 'top-right',
                            loaderBg: '#f0c541',
                            icon: 'success',
                            hideAfter: 3500,
                            stack: 6
                        });
                    } else {
                        //alert('Failed to update status.');
                        // Show a toast notification
                        $.toast({
                            heading: 'Failed to update status',
                            text: 'Agent status update failed.',
                            position: 'top-right',
                            loaderBg: '#f0c541',
                            icon: 'success',
                            hideAfter: 3500,
                            stack: 6
                        });
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