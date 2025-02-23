    <!--== Start Header Wrapper ==-->
    <header class="header-area header-default sticky-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-7 col-sm-4 col-md-4 col-lg-2">
                    <div class="header-logo-area">
                        <a href="index.html">
                            <img class="logo-main" src="{{asset('front-assets')}}/img/logo.png" alt="Logo" />
                            <img class="logo-light" src="{{asset('front-assets')}}/img/logo.png" alt="Logo" />
                        </a>
                    </div>
                </div>
                <div class="col-5 col-sm-8 col-md-8 col-lg-10">
                    <div class="header-align">
                        <div class="header-navigation-area navigation-style-two">
                            <ul class="main-menu nav justify-content-center">
                                <li class=""><a href="/">Home</a></li>
                                <li class="has-submenu"><a href="index.html">Pages</a>
                                    <ul class="submenu-nav">
                                        <li><a href="about.html">About</a></li>
                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </li>
                                <li class="has-submenu"><a href="services.html">Services</a>
                                    <ul class="submenu-nav">
                                        <li><a href="services.html">Services</a></li>
                                        <li><a href="service-details.html">Service Details</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{route('frontjoblist')}}">Find Projects</a></li>
    
                                @guest
                                    <li><a href="{{ route('login') }}">Log in</a></li> 
                                    @if (Route::has('register'))
                                        <li><a href="{{ route('register') }}">Register</a></li>
                                    @endif
                                @endguest
    
                            </ul>
                        </div>
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="header-action-area">
                                    <button class="btn-search">
                                        <span class="icon icon-search lnr lnr-magnifier"></span>
                                        <span class="icon icon-search-close lnr lnr-cross"></span>
                                    </button>
                                    <div class="btn-search-content">
                                        <form action="#" method="post">
                                            <div class="form-input-item">
                                                <input type="text" id="search" placeholder="Search...">
                                                <button type="submit" class="btn-src">
                                                    <i class="lnr lnr-magnifier"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <button class="btn-menu d-lg-none">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                @if (Route::has('login'))
                                    @auth
                                        @role('candidate')
                                            <div class="header-navigation-area navigation-style-two">
                                                <ul class="main-menu nav justify-content-center">
                                                    <li class="has-submenu">
                                                        <a href="#" style="display: flex; align-items: center;">
                                                            <span>{{ auth()->user()->name }}</span><i class="fa-solid fa-caret-down mx-2"></i>
                                                        </a>
                                                        <ul class="submenu-nav">
                                                            <li><a href="{{route('profile.edit')}}">Edit Profile</a></li>
                                                            <li><a href="{{ route('view-applied') }}">Applied Jobs</a></li>
                                                            <li>
                                                                <!-- Authentication -->
                                                                <form method="POST" action="{{ route('logout') }}">
                                                                    @csrf
                                                                    <a href="{{route('logout')}}"
                                                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                                                        <span class="align-middle">{{ __('Log Out') }}</span>
                                                                    </a>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        @else
                                            <a class="btn-theme btn-two" href="{{ url('/dashboard') }}">Dashboard</a>
                                        @endrole
                                    @else
                                        <a class="btn-theme btn-two" href="#/">Consultation</a>
                                    @endauth
                                @endif
                            </div>                                                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    