<nav class="navbar header-navbar pcoded-header" header-theme="theme4">
    <div class="navbar-wrapper">
        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="#!">
                <i class="ti-menu"></i>
            </a>
            <a class="mobile-search morphsearch-search" href="#">
                <i class="ti-search"></i>
            </a>
            @if($authUser->is_admin)
            <a href="{{url('/dashboard')}}">
                <img class="img-fluid" src="{{asset('admin/assets/images/logo.png')}}" alt="Theme-Logo" />
            </a>
            @else
                <a href="{{url('/user-dashboard')}}">
                    <img class="img-fluid" src="{{asset('admin/assets/images/logo.png')}}" alt="Theme-Logo" />
                </a>
                @endif
            <a class="mobile-options">
                <i class="ti-more"></i>
            </a>
        </div>
        <div class="navbar-container container-fluid">
            <div>
                <ul class="nav-left">
                    <li>
                        <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                    </li>
                    <li>
                        <a class="main-search morphsearch-search" href="#">
                            <!-- themify icon -->
                            <i class="ti-search"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#!" onclick="javascript:toggleFullScreen()">
                            <i class="ti-fullscreen"></i>
                        </a>
                    </li>
                    <li class="mega-menu-top">
                        <a href="#">
                            Mega
                            <i class="ti-angle-down"></i>
                        </a>
                        <ul class="show-notification row">
                            <li class="col-sm-3">
                                <h6 class="mega-menu-title">Mailbox</h6>
                                <ul class="mega-mailbox">
                                    <li>
                                        <a href="#" class="media">
                                            <div class="media-left">
                                                <i class="ti-folder"></i>
                                            </div>
                                            <div class="media-body">
                                                <h5>Data Backup</h5>
                                                <small class="text-muted">Store your data</small>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav-right">
                    {{--<li class="header-notification lng-dropdown">--}}
                        {{--<a href="#" id="dropdown-active-item">--}}
                            {{--<i class="flag-icon flag-icon-gb m-r-5"></i> English--}}
                        {{--</a>--}}
                        {{--<ul class="show-notification">--}}
                            {{--<li>--}}
                                {{--<a href="#" data-lng="en">--}}
                                    {{--<i class="flag-icon flag-icon-gb m-r-5"></i> English--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    <li class="header-notification">
                        <a href="#!">
                            <i class="ti-bell"></i>
                            <span class="badge">5</span>
                        </a>
                        <ul class="show-notification">
                            <li>
                                <h6>Notifications</h6>
                                <label class="label label-danger">New</label>
                            </li>
                            <li>
                                <div class="media">
                                    <img class="d-flex align-self-center" src="{{asset('admin/assets/images/user.png')}}" alt="Generic placeholder image">
                                    <div class="media-body">
                                        <h5 class="notification-user">John Doe</h5>
                                        <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                        <span class="notification-time">30 minutes ago</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <li class="user-profile header-notification">
                        <a href="#!">
                            <img src="{{asset('admin/assets/images/user.png')}}" alt="User-Profile-Image">
                            <span>{{auth()->user()->name}}</span>
                            <i class="ti-angle-down"></i>
                        </a>
                        <ul class="show-notification profile-notification">
                            {{--<li>--}}
                                {{--<a href="#!">--}}
                                    {{--<i class="ti-settings"></i> Settings--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            <li>
                                <a href="javascript:void(0)" onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                    <i class="ti-layout-sidebar-left"></i> Logout
                                </a>

                                <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- search -->
                <div id="morphsearch" class="morphsearch">
                    <form class="morphsearch-form">
                        <input class="morphsearch-input" type="search" placeholder="Search..." />
                        <button class="morphsearch-submit" type="submit">Search</button>
                    </form>
                    <div class="morphsearch-content">
                        <div class="dummy-column">
                            <h2>People</h2>
                            <a class="dummy-media-object" href="#!">
                                <img class="round" src="http://0.gravatar.com/avatar/81b58502541f9445253f30497e53c280?s=50&amp;d=identicon&amp;r=G" alt="Sara Soueidan" />
                                <h3>Sara Soueidan</h3>
                            </a>
                            <a class="dummy-media-object" href="#!">
                                <img class="round" src="http://1.gravatar.com/avatar/9bc7250110c667cd35c0826059b81b75?s=50&amp;d=identicon&amp;r=G" alt="Shaun Dona" />
                                <h3>Shaun Dona</h3>
                            </a>
                        </div>

                    </div>
                    <!-- /morphsearch-content -->
                    <span class="morphsearch-close"><i class="icofont icofont-search-alt-1"></i></span>
                </div>
                <!-- search end -->
            </div>
        </div>
    </div>
</nav>