@extends('backend.layouts.master')

@section('title', 'Commissions | Wishmytour Admin')

@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Agent Commissions</h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="tab-struct custom-tab-1 mt-40">
                            <ul role="tablist" class="nav nav-tabs" id="myTabs_7">
                                <li class="active" role="presentation"><a aria-expanded="true" data-toggle="tab"
                                                                          role="tab" id="home_tab_7"
                                                                          href="#open">open</a></li>
                                <li role="presentation" class=""><a aria-expanded="true" data-toggle="tab"
                                                                          role="tab" id="home_tab_7"
                                                                          href="#invoiced">invoiced</a></li>
                                <li role="presentation" class=""><a data-toggle="tab" id="profile_tab_7" role="tab"
                                                                    href="#paid"
                                                                    aria-expanded="false">paid</a></li>
                            </ul>
                            <div class="tab-content" id="myTabContent_7">
                                <div id="open" class="tab-pane fade active in" role="tabpanel">
                                    <div class="table-wrap">
                                        <div class="table-responsive">
                                            <table id="datable_open" class="table table-hover display pb-30">
                                                <thead>
                                                <tr>
                                                    <th>Agent</th>
                                                    <th>Month</th>
                                                    <th>Basic</th>
                                                    <th>Cancelled</th>
                                                    <th>Net Basic</th>
                                                    <th>Commission</th>
                                                    <th>Incentive</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
                                                    <th>Agent</th>
                                                    <th>Month</th>
                                                    <th>Basic</th>
                                                    <th>Cancelled</th>
                                                    <th>Net Basic</th>
                                                    <th>Commission</th>
                                                    <th>Incentive</th>
                                                    <th>Total</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div id="invoiced" class="tab-pane fade" role="tabpanel">
                                    <div class="form-wrap">
                                                <button id="payoutButton" class="pull-right btn btn-success disabled" disabled>
                                                    Payout
                                                </button>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="table-wrap">
                                        <div class="table-responsive">
                                            <table id="datable_invoiced" class="table table-hover display pb-30">
                                                <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="checkAll"></th>
                                                    <th>Agent</th>
                                                    <th>Month</th>
                                                    <th>Basic</th>
                                                    <th>Cancelled</th>
                                                    <th>Net Basic</th>
                                                    <th>Commission</th>
                                                    <th>Incentive</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th>Agent</th>
                                                    <th>Month</th>
                                                    <th>Basic</th>
                                                    <th>Cancelled</th>
                                                    <th>Net Basic</th>
                                                    <th>Commission</th>
                                                    <th>Incentive</th>
                                                    <th>Total</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div id="paid" class="tab-pane fade" role="tabpanel">
                                    <div class="form-wrap">
                                        <form id="filterForm" class="mb-4">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="from_month">From Month:</label>
                                                        <input type="month" id="from_month" name="from_month"
                                                               class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="to_month">To Month:</label>
                                                        <input type="month" id="to_month" name="to_month"
                                                               class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="submit" class="btn btn-success btn-block">Filter
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="table-wrap">
                                        <div class="table-responsive">
                                            <table id="datable_paid" class="table table-hover display pb-30">
                                                <thead>
                                                <tr>
                                                    <th>Agent</th>
                                                    <th>Month</th>
                                                    <th>Basic Amount</th>
                                                    <th>Commission</th>
                                                    <th>Incentive</th>
                                                    <th>Payable</th>
                                                    <th>TDS</th>
                                                    <th>Processed</th>
                                                    <th>Invoice</th>
                                                    <th>Voucher</th>
                                                </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
                                                    <th>Agent</th>
                                                    <th>Month</th>
                                                    <th>Basic Amount</th>
                                                    <th>Commission</th>
                                                    <th>Incentive</th>
                                                    <th>Payable</th>
                                                    <th>TDS</th>
                                                    <th>Processed</th>
                                                    <th>Invoice</th>
                                                    <th>Voucher</th>
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
            </div>
        </div>
    </div>
    <!-- /Row -->
@endsection

@push('styles')
    <style type="text/css">
        #datable_open {
            width: 100% !important;
        }

        #datable_invoiced {
            width: 100% !important;
        }

        #datable_paid {
            width: 100% !important;
        }
    </style>
@endpush

@push('scripts')

    <script type="text/javascript">
        $(document).ready(function () {
            var tableOpen = $('#datable_open').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true, // Add this line for responsive design
                ajax: {
                    url: "{{ route('admin.open-commissions') }}", // Adjust route as needed
                    data: function (d) {
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                    }
                },
                columns: [
                    {
                        data: 'fullname',
                        name: 'fullname',
                        render: function (data, type, row) {
                            var showUrl = "{{ route('admin.agents.commissions', ':id') }}";
                            showUrl = showUrl.replace(':id', row.user_id);
                            return '<a href="' + showUrl + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'month',
                        name: 'month',
                    },
                    {
                        data: 'total_base_price',
                        name: 'total_base_price',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'total_cancelled',
                        name: 'total_cancelled',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'net_basic',
                        name: 'net_basic',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'commission',
                        name: 'commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'incentive',
                        name: 'incentive',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'total_commission',
                        name: 'total_commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                ],
                language: {
                    // Customize the language settings here if needed
                    emptyTable: "No data available in table",
                    processing: "Loading..."
                }
            });
            var tableInvoiced = $('#datable_invoiced').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true, // Add this line for responsive design
                ajax: {
                    url: "{{ route('admin.invoiced-commissions') }}", // Adjust route as needed
                    data: function (d) {
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return '<input type="checkbox" class="rowCheckbox" value="' + row.user_id + '">';
                        }
                    },
                    {
                        data: 'fullname',
                        name: 'fullname',
                        render: function (data, type, row) {
                            var showUrl = "{{ route('admin.agents.commissions', ':id') }}";
                            showUrl = showUrl.replace(':id', row.user_id);
                            return '<a href="' + showUrl + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'month',
                        name: 'month',
                    },
                    {
                        data: 'total_base_price',
                        name: 'total_base_price',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'total_cancelled',
                        name: 'total_cancelled',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'net_basic',
                        name: 'net_basic',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'commission',
                        name: 'commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'incentive',
                        name: 'incentive',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'total_commission',
                        name: 'total_commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                ],
                language: {
                    // Customize the language settings here if needed
                    emptyTable: "No data available in table",
                    processing: "Loading..."
                }
            });

            // Check All functionality
            $('#checkAll').on('click', function () {
                var isChecked = $(this).is(':checked');
                $('.rowCheckbox').prop('checked', isChecked);
                togglePayoutButton();
            });

            // Toggle Payout button based on row checkbox selection
            $('#datable_invoiced').on('change', '.rowCheckbox', function () {
                togglePayoutButton();
            });

            function togglePayoutButton() {
                if ($('.rowCheckbox:checked').length > 0) {
                    $('#payoutButton').removeClass('disabled');
                    $('#payoutButton').prop('disabled', false);
                } else {
                    $('#payoutButton').addClass('disabled');
                    $('#payoutButton').prop('disabled', true);
                }
            }

            // Payout button click event
            $('#payoutButton').on('click', function () {
                var selectedIds = [];
                $('.rowCheckbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                // Perform payout action with selectedIds
                console.log('Selected IDs for payout:', selectedIds);

                $.ajax({
                    url: "{{ route('admin.process-commissions') }}", // Replace with your route
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Include CSRF token for security
                        ids: selectedIds
                    },
                    success: function(response) {
                        // Handle success response
                        alert('Payout processed successfully');
                        // Optionally, you can reload the table or update the UI
                        tableInvoiced.ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        alert('Error processing payout');
                    }
                });
            });

            var tablePaid = $('#datable_paid').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true, // Add this line for responsive design
                ajax: {
                    url: "{{ route('admin.processed-commissions') }}",
                    data: function (d) {
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                    }
                },
                columns: [
                    {
                        data: 'fullname',
                        name: 'fullname',
                        render: function (data, type, row) {
                            var showUrl = "{{ route('admin.agents.commissions', ':id') }}";
                            showUrl = showUrl.replace(':id', row.user_id);
                            return '<a href="' + showUrl + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'month',
                        name: 'month',
                    },
                    {
                        data: 'total_base_price',
                        name: 'total_base_price',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {data: 'commission', name: 'commission', render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')},
                    {data: 'incentive', name: 'incentive', render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')},
                    {
                        data: 'total_commission',
                        name: 'total_commission',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {data: 'tds', name: 'tds', render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')},
                    {data: 'total_paid', name: 'total_paid', render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')},
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        orderable: false,
                        render: function (data, type, row) {
                            return '<a target="_blank" href="' + row.invoice_url + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'voucher_number',
                        name: 'voucher_number',
                        orderable: false,
                        render: function (data, type, row) {
                            return '<a target="_blank" href="' + row.voucher_url + '">' + data + '</a>';
                        }
                    },
                ]
            });

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                tablePaid.ajax.reload();
            });
        });
    </script>
@endpush
