@extends('backend.layouts.master')
@section('title','Dashboard | Wishmytour Admin')
@section('main-content')
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
                    <span class="txt-dark block counter"><span class="counter-anim">{{ $vendorCount }}</span></span>
                    <span class="weight-500 uppercase-font block font-13">vendors</span>
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
                    <span class="txt-dark block counter"><span class="counter-anim">{{ $customerCount }}</span></span>
                    <span class="weight-500 uppercase-font block">customers</span>
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
                    <span class="txt-dark block counter"><span class="counter-anim">{{ $packageCount }}</span></span>
                    <span class="weight-500 uppercase-font block">packages</span>
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
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="panel panel-default card-view pa-0">
        <div class="panel-wrapper collapse in">
          <div class="panel-body pa-0">
            <div class="sm-data-box">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                    <span class="txt-dark block counter"><span class="counter-anim">{{ $bookingCount }}</span></span>
                    <span class="weight-500 uppercase-font block">bookings</span>
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
  </div>
  <!-- /Row -->
@endsection

@push('scripts')
  <!-- Progressbar Animation JavaScript -->
  <script src="{{asset('public/backend/vendors/bower_components/waypoints/lib/jquery.waypoints.min.js')}}"></script>
  <script src="{{asset('public/backend/vendors/bower_components/jquery.counterup/jquery.counterup.min.js')}}"></script>

  <script type="text/javascript">

  </script>
@endpush