@extends('backend.layouts.master')
@section('title', 'Ledger | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Agent Ledger</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.agents') }}"><span>Agents</span></a></li>
                <li><a href="{{ route('admin.agents.show', $agent->id) }}"><span>Agent Details</span></a></li>
                <li class="active"><span>Agent Ledger</span></li>
            </ol>
        </div>
        <!-- /Breadcrumb -->
    </div>
    <!-- /Title -->

    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Agent Ledger</h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="form-wrap">
                            <form id="filterForm" class="mb-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="from_month">From Month:</label>
                                            <input type="month" id="from_month" name="from_month" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="to_month">To Month:</label>
                                            <input type="month" id="to_month" name="to_month" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success btn-block">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table id="table1" class="table table-hover display pb-30">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- DataTables will automatically populate the tbody -->
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th id="totalDebit"></th>
                                        <th id="totalCredit"></th>
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
        #table1 {
            width: 100% !important;
        }

    </style>
@endpush
@push('scripts')
    <script src="{{asset('public/backend/vendors/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('public/backend/vendors/bower_components/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('public/backend/vendors/bower_components/jszip/dist/jszip.min.js')}}"></script>
    <script src="{{asset('public/backend/vendors/bower_components/pdfmake/build/pdfmake.min.js')}}"></script>
    <script src="{{asset('public/backend/vendors/bower_components/pdfmake/build/vfs_fonts.js')}}"></script>

    <script src="{{asset('public/backend/vendors/bower_components/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('public/backend/vendors/bower_components/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var agentId = {{ $agent->id }};
            var agentName = "{{ $agent->first_name }} {{ $agent->last_name }}";
            var agentPAN = "{{ $agent->pan_number }}";
            var agentMobile = "{{ $agent->mobile }}";
            var table1 = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                responsive: true,
                paging: false, // Disable pagination
                searching: false, // Disable filtering
                lengthChange: false, // Disable page length control
                info: false, // Disable the "Showing X to Y of Z entries" information
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Excel'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        action: function (e, dt, button, config) {
                            //var agentId = agentId;
                            var from_month = $('#from_month').val();
                            var to_month = $('#to_month').val();

                            // Construct the URL using the route helper
                            var url = "{{ route('admin.generate-ledger-pdf') }}?agent_id=" + agentId + "&from_month=" + from_month + "&to_month=" + to_month;

                            // Trigger the PDF download
                            window.location.href = url;
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print'
                    }
                ],
                ajax: {
                    url: "{{ route('admin.agent-ledger') }}",
                    data: function (d) {
                        d.agent_id = agentId;
                        d.from_month = $('#from_month').val();
                        d.to_month = $('#to_month').val();
                    },
                    dataSrc: function(json) {
                        // Ensure json.data is returned or handle empty data gracefully
                        return json.data || [];
                    }
                },
                columns: [
                    { data: 'date', name: 'date' },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data, type, row) {
                            // Check if the reference number exists and append it to the description
                            var description = data || '';
                            var referenceNumber = row.reference_number ? '<span style="font-size:11px;">Ref no: ' + row.reference_number + '</span>' : '';

                            // Return formatted description
                            return referenceNumber ? description + '<br>' + referenceNumber : description;
                        }
                    },
                    {
                        data: 'debit', // Use 'debit' field from server response
                        name: 'debit',
                        render: function(data, type, row) {
                            return data ? $.fn.dataTable.render.number(',', '.', 2).display(data) : '';
                        }
                    },
                    {
                        data: 'credit', // Use 'credit' field from server response
                        name: 'credit',
                        render: function(data, type, row) {
                            return data ? $.fn.dataTable.render.number(',', '.', 2).display(data) : '';
                        }
                    }
                ],
                language: {
                    emptyTable: "No data available in table",
                    processing: "Loading..."
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Helper function to parse currency values
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\₹,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Total Debit over all pages
                    var totalDebit = api
                        .column(2)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total Credit over all pages
                    var totalCredit = api
                        .column(3)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer with formatted total values
                    $('#totalDebit').html('₹ ' + $.fn.dataTable.render.number(',', '.', 2).display(totalDebit));
                    $('#totalCredit').html('₹ ' + $.fn.dataTable.render.number(',', '.', 2).display(totalCredit));
                }
            });

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                table1.ajax.reload();
            });
        });
    </script>
@endpush
