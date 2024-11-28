<!-- Footer -->
<footer class="footer container-fluid pl-30 pr-30">
    <div class="row">
        <div class="col-sm-12">
            <p>{{date('Y')}} &copy; Wishmytour. Pampered by ProperWebTechnologies</p>
        </div>
    </div>
</footer>
<!-- /Footer -->

</div>
<!-- /Main Content -->

</div>
<!-- /#wrapper -->

<!-- JavaScript -->

<!-- jQuery -->
<script src="{{asset('public/backend/vendors/bower_components/jquery/dist/jquery.min.js')}}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{asset('public/backend/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

<!-- Data table JavaScript -->
<script src="{{asset('public/backend/vendors/bower_components/datatables/media/js/jquery.dataTables.min.js')}}"></script>

<!-- Slimscroll JavaScript -->
<script src="{{asset('public/backend/dist/js/jquery.slimscroll.js')}}"></script>

<!-- Sweet-Alert  -->
<script src="{{asset('public/backend/vendors/bower_components/sweetalert/dist/sweetalert.min.js')}}"></script>

<!-- Jquery Toast  -->
<script src="{{asset('public/backend/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js')}}"></script>

<!-- Init JavaScript -->
<script src="{{asset('public/backend/dist/js/init.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        // Handle dropdown-toggle click event
        $('.zmdi-notifications').on('click', function (e) {
            e.preventDefault(); // Prevent the default action

            // Mark all notifications as read
            $.ajax({
                url: "{{ route('admin.notifications.markAllAsRead') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        // Update UI if needed
                        $('.top-nav-icon-badge').text('0'); // Update notification count to 0
                    } else {
                        // Handle error
                        console.error('Failed to mark all notifications as read');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.error('Failed to mark all notifications as read');
                }
            });
        });

        /*Slimscroll*/
        $('.nicescroll-bar').slimscroll({height:'100%',color: '#878787', disableFadeOut : true,borderRadius:0,size:'4px',alwaysVisible:false});
    });
</script>

@stack('scripts')


