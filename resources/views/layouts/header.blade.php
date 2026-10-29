<div class="page-header">
    <div class="header-wrapper row m-0">

        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"><a href="route{{('/')}}"><img class="img-fluid" src="{{ asset('assets/images/logo/logo.png') }}" alt=""></a></div>
            <div class="toggle-sidebar"><i class="fa fa-angle-right"></i> <i data-feather="align-center"></i></div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0 overflow-visible">
            <ul class="nav-menus">

                <li>
                    <span class="header-search topbar-menu-icon">
                        <img class="img-fluid" src="{{ asset('assets/images/search-light.svg') }}">
                    </span>
                    <form class="form-inline search-full col" action="#" method="get">
                        <div class="form-group w-100">
                            <div class="Typeahead Typeahead--twitterUsers">
                                <div class="u-posRelative">
                                    <input id="auto" class="demo-input Typeahead-input form-control-plaintext w-100 bg-transparent border-0" type="text" placeholder="Search by Route Number, Shipment or Customer Name" name="q" title="" autofocus>
                                    <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading...</span></div><i class="close-search" data-feather="x"></i></div>
                                    <div id="search_list" class="custom-scrollbar animated fadeInRight w-100" style="display:none;position:absolute;z-index:99999;background-color: white;padding:0.75rem 1rem 0.75rem 1rem;">
                                    <ul class="search-dropdown-menu">
                                    </ul>
                                    </div>
                                    <div class="Typeahead-menu"></div>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
            
        </div>
       
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">


            <ul class="nav-menus">
                <!-- <li class="">
                    <div class="add-route-icon">
                        <a href="#"><img src="{{ asset('assets/images/add-icon.png') }}" alt="" class="img-fluid"></a>
                    </div>
                </li>  -->
                 <li class="onhover-dropdown">
                    <div class="notification-box topbar-menu-icon">
                        <img class="img-fluid" src="{{ asset('assets/images/notification.svg') }}"><span id="badge" class="badge rounded-pill badge-secondary">0 </span>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <h6 class="f-18 mb-0 dropdown-title">Notitications </h6>
                        <ul id="notificationlist">
                            <!-- <li class="b-l-primary border-4">
                                <p>Delivery processing <span class="font-danger">10 min.</span></p>
                            </li>
                            <li class="b-l-success border-4">
                                <p>Order Complete<span class="font-success">1 hr</span></p>
                            </li>
                            <li class="b-l-secondary border-4">
                                <p>Tickets Generated<span class="font-secondary">3 hr</span></p>
                            </li>
                            <li class="b-l-warning border-4">
                                <p>Delivery Complete<span class="font-warning">6 hr</span></p>
                            </li>
                            <li><a class="f-w-700" href="#">Check all</a></li> -->
                        </ul>
                    </div>
                </li>
                <li class="profile-nav onhover-dropdown pe-0">
                    <div class="media profile-media topbar-menu-icon">
                        <img class="img-fluid" src="{{ asset('assets/images/administrator-line.svg') }}">

                    </div>
                    
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="{{ route('user.view', Auth::user()->id) }}"><i data-feather="user"></i><span>Account</span></a></li>
                        <!-- <li><a href="#"><i data-feather="mail"></i><span>Inbox</span></a></li>
                        <li><a href="#"><i data-feather="file-text"></i><span>Taskboard</span></a></li>
                        <li><a href="#"><i data-feather="settings"></i><span>Settings</span></a></li>
                        <li><a href="#"><i data-feather="log-in"> </i><span>Log in</span></a></li> -->
                        <form method="POST" action="{{ route('logout') }}">
                             @csrf
                            <li><a  onclick="event.preventDefault(); this.closest('form').submit();" href="route('logout')"><i data-feather="power"></i><span>log out</span></a></li>
                        </form>
                    </ul>
                </li>
            </ul>
        </div>
        <div id="suggesstion-box"></div>
        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">                        
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details">
            <div class="ProfileCard-realName"></div>
            </div>
            </div>
          </script>
        <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
</div>
@section('notification-script')
<script>
    var notificationurl = "<?php echo url('/').'/'.'notificationIconCount';?>";
    var search = "<?php echo url('/').'/'.'search';?>";
    var shipment_view = "<?php echo url('/').'/'.'shipment-view';?>";
    var customer_view = "<?php echo url('/').'/'.'customer-view';?>";
    var app_url =  "<?php echo url('/'); ?>";
    var browserTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    </script>
@endsection