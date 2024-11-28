<div class="fixed-sidebar-left">
    <ul class="nav navbar-nav side-nav nicescroll-bar">
        <li class="navigation-header">
            <span>Main</span>
            <i class="zmdi zmdi-more"></i>
        </li>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <div class="pull-left"><i class="zmdi zmdi-landscape mr-20"></i><span
                            class="right-nav-text">Dashboard</span></div>
                <div class="clearfix"></div>
            </a>
        </li>
        <li>
            <a href="javascript:void(0);" class="{{ request()->is(['admin/vendors','admin/customers','admin/agents*','admin/commissions']) ? 'active' : '' }}" data-toggle="collapse" data-target="#user_dr" aria-expanded="{{ request()->is(['admin/vendors', 'admin/customers', 'admin/agents*', 'admin/commissions']) ? 'true' : 'false' }}">
                <div class="pull-left"><i class="zmdi zmdi-accounts mr-20"></i><span class="right-nav-text">Users </span>
                </div>
                <div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="user_dr" class="collapse collapse-level-1 {{ request()->is(['admin/vendors','admin/customers','admin/agents*','admin/commissions']) ? 'in' : '' }}">
                <li>
                    <a href="{{route('admin.vendors')}}" class="{{ request()->is('admin/vendors') ? 'active-page' : '' }}">Vendors</a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="{{ request()->is(['admin/agents*','admin/commissions']) ? 'active-page' : '' }}" data-toggle="collapse" data-target="#agents_dr_lv2">Agents<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
                    <ul id="agents_dr_lv2" class="collapse collapse-level-2 {{ request()->is(['admin/agents*','admin/commissions']) ? 'in' : '' }}">
                        <li>
                            <a href="{{route('admin.agents')}}" class="{{ request()->is('admin/agents*') ? 'active-page' : '' }}">Agents</a>
                        </li>
                        <li>
                            <a href="{{route('admin.commissions')}}" class="{{ request()->is('admin/commissions') ? 'active-page' : '' }}">Commissions</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{route('admin.customers')}}" class="{{ request()->is('admin/customers') ? 'active-page' : '' }}">Customers</a>
                </li>
            </ul>
        </li>
        <!---Package-->
        <li>
            <a href="javascript:void(0);" class="{{ request()->is(['admin/packages','admin/inclusions','admin/exclusions','admin/locations']) ? 'active' : '' }}" data-toggle="collapse" data-target="#package_dr">
                <div class="pull-left"><i class="zmdi zmdi-collection-image mr-20"></i><span class="right-nav-text">Packages </span>
                </div>
                <div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="package_dr" class="collapse collapse-level-1 {{ request()->is(['admin/packages','admin/inclusions','admin/exclusions','admin/locations']) ? 'in' : '' }}">
                <li>
                    <a href="{{route('admin.packages')}}" class="{{ request()->is('admin/packages') ? 'active-page' : '' }}">Packages</a>
                </li>
                <li>
                    <a href="{{route('admin.inclusions.index')}}" class="{{ request()->is('admin/inclusions') ? 'active-page' : '' }}">Inclusions</a>
                </li>
                <li>
                    <a href="{{route('admin.exclusions.index')}}" class="{{ request()->is('admin/exclusions') ? 'active-page' : '' }}">Exclusions</a>
                </li>
               
            </ul>
        </li>
        <!---Booking-->
        <li>
            <a href="javascript:void(0);" class="{{ request()->is(['admin/booking*']) ? 'active' : '' }}" data-toggle="collapse" data-target="#booking_dr">
                <div class="pull-left"><i class="icon-list mr-20"></i><span class="right-nav-text">Bookings </span></div>
                <div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="booking_dr" class="collapse collapse-level-1 {{ request()->is(['admin/booking*']) ? 'in' : '' }}">
                <li>
                    <a href="{{ route('admin.booking') }}" class="{{ request()->is('admin/booking') ? 'active-page' : '' }}">Bookings</a>
                </li>
                <li>
                    <a href="{{ route('admin.booking.refund-booking') }}" class="{{ request()->is('admin/refund-booking') ? 'active-page' : '' }}">payments</a>
                </li>
                <li>
                    <a href="{{ route('admin.booking.refund-booking') }}" class="{{ request()->is('admin/refund-booking') ? 'active-page' : '' }}">Refunds</a>
                </li>
            </ul>
        </li>
         <!---Page-->
         <li>
            <a href="{{route('admin.pages.index')}}" class="{{ request()->is('admin/pages') ? 'active' : '' }}">
                <div class="pull-left"><i class="fa fa-list-alt"></i><span class="right-nav-text" style="padding-left: 25px;">Pages</span></div>
                <div class="clearfix"></div>
            </a>
        </li>  
         <!---Contact Form List-->
         <li>
            <a href="{{route('admin.contact.index')}}" class="{{ request()->is('admin/contact') ? 'active' : '' }}">
                <div class="pull-left"><i class="fa fa-list-alt"></i><span class="right-nav-text" style="padding-left: 25px;">Contact List</span></div>
                <div class="clearfix"></div>
            </a>
        </li>  
        <!---Settings / Configs-->
        <li>
            <a href="javascript:void(0);" class="{{ request()->is(['admin/coupons','admin/referrals','admin/config']) ? 'active' : '' }}" data-toggle="collapse" data-target="#setting_dr">
                <div class="pull-left"><i class="ti-settings mr-20"></i><span class="right-nav-text">Setting </span>
                </div>
                <div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="setting_dr" class="collapse collapse-level-1 {{ request()->is(['admin/coupons','admin/referrals','admin/config']) ? 'in' : '' }}">
                <li>
                    <a href="{{route('admin.coupons.index')}}" class="{{ request()->is('admin/coupons') ? 'active-page' : '' }}">Coupons</a>
                </li>
                <li>
                    <a href="{{route('admin.referrals.index')}}" class="{{ request()->is('admin/referrals') ? 'active-page' : '' }}">Referrals</a>
                </li>
                <li>
                    <a href="{{route('admin.config.index')}}" class="{{ request()->is('admin/config') ? 'active-page' : '' }}">Config</a>
                </li>
               
            </ul>
        </li>
        <li>
            <a href="javascript:void(0);" class="{{ request()->is(['admin/vendors','admin/customers','admin/agents*','admin/commissions']) ? 'active' : '' }}" data-toggle="collapse" data-target="#user_dr" aria-expanded="{{ request()->is(['admin/vendors', 'admin/customers', 'admin/agents*', 'admin/commissions']) ? 'true' : 'false' }}">
                <div class="pull-left"><i class="zmdi zmdi-accounts mr-20"></i><span class="right-nav-text">Tax compliance </span>
                </div>
                <div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="user_dr" class="collapse collapse-level-1 {{ request()->is(['admin/vendors','admin/customers','admin/agents*','admin/commissions']) ? 'in' : '' }}">
                <li>
                    <a href="{{route('admin.vendors')}}" class="{{ request()->is('admin/vendors') ? 'active-page' : '' }}">Vendors</a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="{{ request()->is(['admin/agents*','admin/commissions']) ? 'active-page' : '' }}" data-toggle="collapse" data-target="#agents_dr_lv2">Agents<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
                    <ul id="agents_dr_lv2" class="collapse collapse-level-2 {{ request()->is(['admin/agents*','admin/commissions']) ? 'in' : '' }}">
                        <li>
                            <a href="{{route('admin.agents')}}" class="{{ request()->is('admin/agents*') ? 'active-page' : '' }}">Agents</a>
                        </li>
                        <li>
                            <a href="{{route('admin.commissions')}}" class="{{ request()->is('admin/commissions') ? 'active-page' : '' }}">Commissions</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{route('admin.customers')}}" class="{{ request()->is('admin/customers') ? 'active-page' : '' }}">Customers</a>
                </li>
            </ul>
        </li>
    </ul>
</div>




