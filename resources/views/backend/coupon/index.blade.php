@extends('backend.layouts.master')
@section('title','Coupon List || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Coupon List</h6>
                     </div>
                     <div class="pull-right">
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Create Coupon</a>
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
                                            <th>S.N.</th>
                                            <th>Coupon Code</th>
                                            <th>Discount Amount</th>
                                            <th>Coupon Start Date</th>
                                            <th>Coupon End Date</th>
                                            <th>Status</th>
                                            <th>Show Status</th> <!-- New Column -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Coupon Code</th>
                                            <th>Discount Amount</th>
                                            <th>Coupon Start Date</th>
                                            <th>Coupon End Date</th>
                                            <th>Status</th>
                                            <th>Show Status</th> <!-- New Column -->
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach($coupons as $coupon)
                                        <tr>
                                            <td>{{$coupon->id}}</td>
                                            <td>{{$coupon->code}}</td>
                                            <td>@if($coupon->discount_type == 'percentage')
                                                {{$coupon->discount_amount}}%
                                                @else
                                                {{$coupon->discount_amount}}
                                                @endif
                                            </td>
                                            <td>{{$coupon->start_date}}</td>
                                            <td>{{$coupon->end_date}}</td>
                                            <td>
                                                <input type="checkbox" class="js-switch" data-id="{{ $coupon->id }}" {{ $coupon->status == 1 ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="js-switch-show" data-id="{{ $coupon->id }}" {{ $coupon->show_status == 1 ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.coupons.show', $coupon->id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Details"><i class="zmdi zmdi-file text-primary" aria-hidden="true"></i></a>
                                               {{--  <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="pr-10" data-toggle="tooltip" data-original-title="Edit" aria-hidden="true"><i class="zmdi zmdi-edit text-success"></i></a>
                                                 <form id="delete-form-{{ $coupon->id }}" action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="text-inverse delete-warning" title="" data-toggle="tooltip" data-original-title="Delete" aria-hidden="true"><i class="zmdi zmdi-delete text-danger"></i></a>
                                                </form> --}}
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#datable_1').DataTable();

        // Initialize Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            new Switchery(html, { size: 'small' });
        });

        // Initialize Switchery for show status
        var elemsShow = Array.prototype.slice.call(document.querySelectorAll('.js-switch-show'));
        elemsShow.forEach(function(html) {
            new Switchery(html, { size: 'small' });
        });

        // Handle status change
        $('.js-switch').change(function() {
            var status = $(this).prop('checked') === true ? 1 : 0;
            var couponId = $(this).data('id');
            console.log('Coupon ID:', couponId, 'Status:', status); // Debug message

            $.ajax({
                type: "POST",
                dataType: "json",
                url: '{{ route('admin.coupons.toggleStatus') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: couponId,
                    status: status
                },
                success: function(data) {
                    console.log(data.message);
                    swal("Success!", data.message, "success");
                },
                error: function(data) {
                    console.log('Error:', data.responseText); // Detailed error message
                    swal("Error!", "An error occurred while updating the status.", "error");
                }
            });
        });

        // Handle show status change
        $('.js-switch-show').change(function() {
            var showStatus = $(this).prop('checked') === true ? 1 : 0;
            var couponId = $(this).data('id');
            console.log('Coupon ID:', couponId, 'Show Status:', showStatus); // Debug message

            $.ajax({
                type: "POST",
                dataType: "json",
                url: '{{ route('admin.coupons.toggleShowStatus') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: couponId,
                    show_status: showStatus
                },
                success: function(data) {
                    console.log(data.message);
                    swal("Success!", data.message, "success");
                },
                error: function(data) {
                    console.log('Error:', data.responseText); // Detailed error message
                    swal("Error!", "An error occurred while updating the show status.", "error");
                }
            });
        });

        // Handle delete button click
        $('.delete-warning').on('click', function (e) {
            e.preventDefault();

            var formId = $(this).closest('form').attr('id');
            if (!formId) {
                console.error("Form ID not found");
                return;
            }

            var couponId = formId.split('-').pop();

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this coupon!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f0c541",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $('#' + formId).submit();
                    swal("Deleted!", "Your item has been deleted.", "success");
                }
            });

            return false;
        });
    });
</script>
@endpush
