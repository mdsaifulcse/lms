<nav class="pcoded-navbar" pcoded-header-position="relative">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">
        <div class="">
            <div class="main-menu-header">
                <img class="img-40" src="{{asset('admin/assets/images/user.png')}}" alt="User-Profile-Image">
                <div class="user-details">
                    <span>{{auth()->user()->name}}</span>
                    {{--<span id="more-details">UX Designer<i class="ti-angle-down"></i></span>--}}
                </div>
            </div>

            <div class="main-menu-content">
                <ul>
                    <li class="more-details">
                        {{--<a href="user-profile.html"><i class="ti-user"></i>View Profile</a>--}}
                        {{--<a href="#!"><i class="ti-settings"></i>Settings</a>--}}
                        <a href="javascript:void(0)" onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();"><i class="ti-layout-sidebar-left"></i>Logout</a>

                        <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Navigation</div>
        <ul class="pcoded-item pcoded-left-item">

        @if($authUser->is_admin) <!--For admin -->
            <li class="pcoded-hasmenu active pcoded-trigger">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard </span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="active">
                        <a href="{{route('users.index')}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">Users</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{route('quizzes.index')}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">Quizzes List</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                </ul>
            </li>

        @elseif(!$authUser->is_admin) <!-- For General User -->

            <li class="pcoded-hasmenu active pcoded-trigger">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Quiz </span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">

                    <li class="active">
                        <a href="{{route('user-quiz.create').'?topic=Bangla'}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">Bangla</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{route('user-quiz.create').'?topic=English'}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">English</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{route('user-quiz.create').'?topic=Math'}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">Math</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>


                </ul>
            </li>

            <li class="pcoded-hasmenu active pcoded-trigger">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Quiz Answer </span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">

                    <li class="active">
                        <a href="{{url('user-quiz-answer').'?topic=Bangla'}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">Bangla</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{url('user-quiz-answer').'?topic=English'}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">English</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{url('user-quiz-answer').'?topic=Math'}}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.default">Math</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>


                </ul>
            </li>
        @endif
        </ul>

    </div>
</nav>