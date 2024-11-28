<style>

  .message-nicescroll-bar { height:50px!important}

  .alert-dropdown {
    max-height: 300px; /* Define a maximum height for the dropdown menu */
    overflow-y: auto; /* Enable vertical scrolling */
  }

  .message-nicescroll-bar { 
    height: 50px !important; /* Adjust the height of the message container as needed */
  }
</style>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="mobile-only-brand pull-left">
    <div class="nav-header pull-left">
      <div class="logo-wrap">
        <a href="{{ route('admin.dashboard') }}">
          <img class="brand-img" src="{{asset('public/backend/dist/img/logo.png')}}" alt="brand"/>
          <span class="brand-text">Wishmytour</span>
        </a>
      </div>
    </div>
    <a id="toggle_nav_btn" class="toggle-left-nav-btn inline-block ml-20 pull-left" href="javascript:void(0);"><i class="zmdi zmdi-menu"></i></a>
  </div>
  <div id="mobile_only_nav" class="mobile-only-nav pull-right">
    <ul class="nav navbar-right top-nav pull-right">
      <li class="dropdown alert-drp">
        
       
                  @php
                  $allnotifications = \App\Helpers\NotificationHelper::getMessagesByUserId(34);
                  
                  @endphp
                  @php
                  use App\Models\Notification;

                  $unreadNotificationsCount = Notification::unreadNotificationsCount(auth()->id());
                  @endphp
       
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="zmdi zmdi-notifications top-nav-icon"></i>
    <span class="top-nav-icon-badge">{{ $unreadNotificationsCount }}</span>
</a>

<ul class="dropdown-menu alert-dropdown" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut">
    <li>
        <div class="notification-box-head-wrap">
            <span class="notification-box-head pull-left inline-block">Notifications</span>
            <!-- <a class="txt-danger pull-right clear-notifications inline-block" href="javascript:void(0)">Clear All</a> -->
            <div class="clearfix"></div>
            <hr class="light-grey-hr ma-0"/>
        </div>
    </li>
    
    <!-- Dynamic list items for notifications -->
    @foreach($allnotifications as $notification)
    <li>
        <div class="streamline message-nicescroll-bar">
            <div class="sl-item">
                <a href="javascript:void(0)">
                    <div class="sl-content">
                        <span class="inline-block capitalize-font pull-left truncate head-notifications">
                            {{ $notification->message }}
                        </span>
                        <span class="inline-block font-11 pull-right notifications-time">
                            {{ $notification->created_at->format('h:i A') }}
                        </span>
                        <div class="clearfix"></div>
                        <p class="truncate">{{ $notification->description }}</p>
                    </div>
                </a>
            </div>
            <hr class="light-grey-hr ma-0"/>
        </div>
    </li>
    @endforeach

    <!-- Other dropdown menu items -->
    <li>
        <div class="notification-box-bottom-wrap">
            <hr class="light-grey-hr ma-0"/>
            <a class="block text-center read-all" href="{{ route('admin.notifications') }}">Read All</a>
         

            <div class="clearfix"></div>
        </div>
    </li>
</ul>
      </li>
      <li class="dropdown auth-drp">
        <a href="#" class="dropdown-toggle pr-0" data-toggle="dropdown"><img src="{{asset('public/backend/dist/img/user1.png')}}" alt="user_auth" class="user-auth-img img-circle"/><span class="user-online-status"></span></a>
        <ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
{{--          <li>--}}
{{--            <a href="profile.html"><i class="zmdi zmdi-account"></i><span>Profile</span></a>--}}
{{--          </li>--}}
{{--          <li>--}}
{{--            <a href="#"><i class="zmdi zmdi-card"></i><span>my balance</span></a>--}}
{{--          </li>--}}
{{--          <li>--}}
{{--            <a href="inbox.html"><i class="zmdi zmdi-email"></i><span>Inbox</span></a>--}}
{{--          </li>--}}
{{--          <li>--}}
{{--            <a href="#"><i class="zmdi zmdi-settings"></i><span>Settings</span></a>--}}
{{--          </li>--}}
{{--          <li class="divider"></li>--}}
{{--          <li class="sub-menu show-on-hover">--}}
{{--            <a href="#" class="dropdown-toggle pr-0 level-2-drp"><i class="zmdi zmdi-check text-success"></i> available</a>--}}
{{--            <ul class="dropdown-menu open-left-side">--}}
{{--              <li>--}}
{{--                <a href="#"><i class="zmdi zmdi-check text-success"></i><span>available</span></a>--}}
{{--              </li>--}}
{{--              <li>--}}
{{--                <a href="#"><i class="zmdi zmdi-circle-o text-warning"></i><span>busy</span></a>--}}
{{--              </li>--}}
{{--              <li>--}}
{{--                <a href="#"><i class="zmdi zmdi-minus-circle-outline text-danger"></i><span>offline</span></a>--}}
{{--              </li>--}}
{{--            </ul>--}}
{{--          </li>--}}
{{--          <li class="divider"></li>--}}
          <li>
            <a href="{{ route('admin.logout') }}"
               onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
              <i class="zmdi zmdi-power"></i><span>{{ __('Logout') }}</span></a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
@push('scripts')


@endpush
