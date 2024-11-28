@extends('backend.layouts.master')
@section('title', 'Commissions | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Agent Commissions</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.agents') }}"><span>Agents</span></a></li>
                <li><a href="{{route('admin.agents.show', $agent->id)}}"><span>profile</span></a></li>
                <li class="active"><span>commissions</span></li>
            </ol>
        </div>
        <!-- /Breadcrumb -->
    </div>
    <!-- /Title -->
    <!-- Row -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim">{{ $last3Months }}</span></span>
                                        <span class="weight-500 uppercase-font block">last 3 months</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-calender data-right-rep-icon txt-light-grey"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim">{{ $totalBookings }}</span></span>
                                        <span class="weight-500 uppercase-font block font-13">bookings</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-user-following data-right-rep-icon txt-light-grey"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim">{{ $totalPaidCommissions }}</span></span>
                                        <span class="weight-500 uppercase-font block">paid</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-people data-right-rep-icon txt-light-grey"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim">{{ $totalDueCommissions }}</span></span>
                                        <span class="weight-500 uppercase-font block">due</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-directions data-right-rep-icon txt-light-grey"></i>
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
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Commissions</h6>
                    </div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="tab-struct custom-tab-1 mt-40">
                            <ul role="tablist" class="nav nav-tabs" id="myTabs_7">
                                <li class="active" role="presentation"><a aria-expanded="true" data-toggle="tab"
                                                                          role="tab" id="open_tab_7"
                                                                          href="#open">open</a></li>
                                <li role="presentation" class=""><a data-toggle="tab" id="invoiced_tab_7" role="tab"
                                                                    href="#invoiced"
                                                                    aria-expanded="false">invoiced</a></li>
                                <li role="presentation" class=""><a data-toggle="tab" id="paid_tab_7" role="tab"
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
                                                    <th>Date</th>
                                                    <th>Booking</th>
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
                                                    <th>Date</th>
                                                    <th>Booking</th>
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
                                    <div class="table-wrap">
                                        <div class="table-responsive">
                                            <table id="datable_invoiced" class="table table-hover display pb-30">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Booking</th>
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
                                                    <th>Date</th>
                                                    <th>Booking</th>
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
                                                    <th>Month</th>
                                                    <th>Booking</th>
                                                    <th>Cancelled</th>
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
                                                    <th>Month</th>
                                                    <th>Booking</th>
                                                    <th>Cancelled</th>
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
    <script src="{{asset('public/backend/vendors/bower_components/waypoints/lib/jquery.waypoints.min.js')}}"></script>
    <script src="{{asset('public/backend/vendors/bower_components/jquery.counterup/jquery.counterup.min.js')}}"></script>
    <script type="text/javascript">
        var agentId = {{ $agent->id }};
        $(document).ready(function () {
            var tableOpen = $('#datable_open').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true, // Add this line for responsive design
                ajax: {
                    url: "{{ route('admin.agent-open-commissions') }}", // Adjust route as needed
                    data: function (d) {
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                        d.agentId = agentId;
                    }
                },
                columns: [
                    {
                        data: 'formatted_date',
                        name: 'formatted_date',
                    },
                    {
                        data: 'booking_number',
                        name: 'booking_number',
                        render: function (data, type, row) {
                            return '<a href="' + '{{ route('admin.booking.view', ':id') }}'.replace(':id', row.booking_id) + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'basic_amount',
                        name: 'basic_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'cancelled_amount',
                        name: 'cancelled_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'commission',
                        name: 'commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'group_commission',
                        name: 'group_commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'total_commission',
                        name: 'total_commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    }
                ],
                language: {
                    // Customize the language settings here if needed
                    emptyTable: "No data available in table",
                    processing: "Loading..."
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var columns = [2, 3, 4, 5, 6, 7]; // The indexes of your amount columns

                    columns.forEach(function (colIdx) {
                        var total = api
                            .column(colIdx)
                            .data()
                            .reduce(function (a, b) {
                                return parseFloat(a) + parseFloat(b);
                            }, 0);

                        $(api.column(colIdx).footer()).html('₹ ' + total.toFixed(2));
                    });
                }
            });
            var tableInvoiced = $('#datable_invoiced').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true, // Add this line for responsive design
                ajax: {
                    url: "{{ route('admin.agent-invoiced-commissions') }}", // Adjust route as needed
                    data: function (d) {
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                        d.agentId = agentId;
                    }
                },
                columns: [
                    {
                        data: 'formatted_date',
                        name: 'formatted_date',
                    },
                    {
                        data: 'booking_number',
                        name: 'booking_number',
                        render: function (data, type, row) {
                            return '<a href="' + '{{ route('admin.booking.view', ':id') }}'.replace(':id', row.booking_id) + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'basic_amount',
                        name: 'basic_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'cancelled_amount',
                        name: 'cancelled_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'commission',
                        name: 'commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'group_commission',
                        name: 'group_commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    },
                    {
                        data: 'total_commission',
                        name: 'total_commission',
                        render: $.fn.dataTable.render.number(',', '.', 2, '₹ ')
                    }
                ],
                language: {
                    // Customize the language settings here if needed
                    emptyTable: "No data available in table",
                    processing: "Loading..."
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var columns = [2, 3, 4, 5, 6, 7]; // The indexes of your amount columns

                    columns.forEach(function (colIdx) {
                        var total = api
                            .column(colIdx)
                            .data()
                            .reduce(function (a, b) {
                                return parseFloat(a) + parseFloat(b);
                            }, 0);

                        $(api.column(colIdx).footer()).html('₹ ' + total.toFixed(2));
                    });
                }
            });
            var tablePaid = $('#datable_paid').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true, // Add this line for responsive design
                ajax: {
                    url: "{{ route('admin.agent-processed-commissions') }}",
                    data: function (d) {
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                        d.agentId = agentId;
                    }
                },
                columns: [
                    {
                        data: 'month',
                        name: 'month',
                    },
                    {
                        data: 'basic_amount',
                        name: 'basic_amount',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {
                        data: 'cancelled_amount',
                        name: 'cancelled_amount',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {
                        data: 'commission',
                        name: 'commission',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {
                        data: 'incentive',
                        name: 'incentive',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {
                        data: 'total_commission',
                        name: 'total_commission',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {
                        data: 'tds',
                        name: 'tds',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        render: $.fn.dataTable.render.number(',', '.', 0, '₹ ')
                    },
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
                ],
                language: {
                    // Customize the language settings here if needed
                    emptyTable: "No data available in table",
                    processing: "Loading..."
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var columns = [1, 2, 3, 4, 5, 6, 7]; // The indexes of your amount columns

                    columns.forEach(function (colIdx) {
                        var total = api
                            .column(colIdx)
                            .data()
                            .reduce(function (a, b) {
                                return parseFloat(a) + parseFloat(b);
                            }, 0);

                        $(api.column(colIdx).footer()).html('₹ ' + total.toFixed(0));
                    });
                }
            });


            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                tablePaid.ajax.reload();
            });
        });
    </script>
@endpush
