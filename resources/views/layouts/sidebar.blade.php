<div class="sidebar-wrapper" sidebar-layout="stroke-svg">
    <div>
        @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin')
            <div class="logo-wrapper"><a href="{{('/    ')}}"><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt=""><img class="img-fluid for-dark"  src="{{ asset('assets/images/logo/logo_dark.png') }}" alt=""></a>
                <div class="back-btn"><i class="fa fa-angle-left"></i></div>
                <div class="toggle-sidebar"><i class="fa fa-angle-right"></i> <i data-feather="align-center"></i></div>
            </div>
            <div class="logo-icon-wrapper"><a href="route{{('/customers')}}"><img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div>
        @elseif(auth()->user()->role->name === 'router' || auth()->user()->role->name === 'dispatcher')
            <div class="logo-wrapper"><a href="{{ route('shipments.createRoute') }}"><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt=""><img class="img-fluid for-dark" src="../assets/images/logo/logo_dark.png" alt=""></a>
                <div class="back-btn"><i class="fa fa-angle-left"></i></div>
                <div class="toggle-sidebar"><i class="fa fa-angle-right"></i></div>
            </div>
            <div class="logo-icon-wrapper"><a href="{{ route('shipments.createRoute') }}"><img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div>
        @endif
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="index.html"><img class="img-fluid" src="{{ asset('assets/images/logo/logo.png') }}" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin' || auth()->user()->role->name === 'router')
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#">
                            <span class="d-flex"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/manage-shipment-icon.png') }}"> </span><span class="title-name"> Manage Customer & Shipment</span></span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('customers.index') }}">
                                    {{ __('Customers') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('shipments.index') }}">
                                    {{ __('Shipments') }}
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('pickup-orders.index') }}">
                                    {{ __('Pickup Orders') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif


                    @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'router' || auth()->user()->role->name === 'dispatcher' || auth()->user()->role->name === 'super-admin')
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#">
                            <span class="d-flex"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/route-icon.png') }}"></span><span class="title-name"> Manage Route</span></span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('shipments.createRoute') }}">Create Route</a></li>
                            <li><a href="{{ route('routes.map') }}">Plan Routes</a></li>
                            <li><a href="{{ route('routes.trackRoute') }}">Track Routes</a></li>
                            <!-- <li><a href="">Track Routes</a></li> -->
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin' || auth()->user()->role->name === 'dispatcher')
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title" href="#">
                                <span class="d-flex"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/manage-user-icon.png') }}"></span><span class="title-name"> Manage User</span></span>
                            </a>
                            <ul class="sidebar-submenu">
                                @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin')
                                    <li><a href="{{ route('users.index') }}">
                                            {{ __('All Users') }}
                                        </a>
                                    </li>
                                @endif
                                <li><a href="{{ route('carrierowner.index') }}">
                                        {{ __('Carrier Owner & Sub-Driver') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin')
                <li class="sidebar-list">
                    <a class="sidebar-title" href="{{ route('trucks.index') }}">
                        <span class="single-page"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/manage-truck-icon.png') }}"></span><span class="title-name"> Manage Trucks</span></span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin')                   
                    <!-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#">
                            <span class="d-flex"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/manage-products-categories-icon.png') }}"></span><span class="title-name"> Manage Products & Categories</span></span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{route('products.index')}}">Products</a></li>
                            <li><a href="{{route('categories.index')}}">Categories</a></li>
                        </ul>
                    </li> -->
                @endif

                @if(auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super-admin')
                    <li class="sidebar-list">
                        <a class=" sidebar-title" href="{{ route('activitylog.index') }}">
                            <span class="single-page"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/active-log-icon.png') }}"></span><span class="title-name"> Activity Logs</span></span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-title" href="{{ route('reports') }}">
                            <span class="single-page"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/system-reports-icon.png') }}"></span><span class="title-name"> System Reports</span></span>
                        </a>
                    </li>
                    
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#">
                            <span class="d-flex"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/btn-icons/setting-icon.png') }}"></span><span class="title-name"> System Settings</span></span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('sms-template.index') }}">SMS Template</a></li>
                            <!-- <li><a href="">Rate List</a></li> -->
                            <li><a href="{{route('warehouse.index')}}">Warehouse Management</a></li>
                            <li><a href="{{route('customerLocation.index')}}">Location</a></li>
                            <li><a href="{{route('rate-setting.edit')}}">Rate List</a></li>
                           
                            <!-- <li><a href="">Location Management</a></li> -->
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-title" href="{{ route('notification.index') }}">
                            <span class="single-page"><span class="title-icon"><img class="img-fluid" alt="" src="{{ asset('assets/images/notification.svg') }}" width="23"></span><span class="title-name">Notifications</span></span>
                        </a>
                    </li>
                    <!-- <li class="sidebar-bottom-text ps-3"><small class="single-page">To be update in future<br/>Manage Products & Categories</small></li> -->
                @endif
            </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>