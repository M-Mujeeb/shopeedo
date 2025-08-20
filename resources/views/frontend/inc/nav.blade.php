    <!-- Top Bar Banner -->
    @php
        $topbar_banner = get_setting('topbar_banner');
        $topbar_banner_medium = get_setting('topbar_banner_medium');
        $topbar_banner_small = get_setting('topbar_banner_small');
        $topbar_banner_asset = uploaded_asset($topbar_banner);

    @endphp
    @if ($topbar_banner != null)
        <div class="position-relative top-banner removable-session z-1035 d-none" data-key="top-banner"
            data-value="removed">
            <a href="{{ get_setting('topbar_banner_link') }}" class="d-block text-reset h-40px h-lg-60px">
                <!-- For Large device -->
                <img src="{{ $topbar_banner_asset }}" class="d-none d-xl-block img-fit h-100"
                    alt="{{ translate('topbar_banner') }}">
                <!-- For Medium device -->
                <img src="{{ $topbar_banner_medium != null ? uploaded_asset($topbar_banner_medium) : $topbar_banner_asset }}"
                    class="d-none d-md-block d-xl-none img-fit h-100" alt="{{ translate('topbar_banner') }}">
                <!-- For Small device -->
                <img src="{{ $topbar_banner_small != null ? uploaded_asset($topbar_banner_small) : $topbar_banner_asset }}"
                    class="d-md-none img-fit h-100" alt="{{ translate('topbar_banner') }}">
            </a>
            <button class="btn text-white h-100 absolute-top-right set-session" data-key="top-banner"
                data-value="removed" data-toggle="remove-parent" data-parent=".top-banner">
                <i class="la la-close la-2x"></i>
            </button>
        </div>
    @endif

    <!-- Top Bar -->
    <div class="top-navbar d-none d-lg-block bg-white z-1035 h-35px h-sm-auto  sticky-top  z-1020 bg-white p-3">
        <div class="container">
            <div class="row">
                <div class="col">
                    {{-- @auth

                    @else --}}
                    <div class="d-none d-xl-block mr-0">
                        <div class="d-flex align-items-center justify-content-center nav-links-color fw-500" style="gap: 30px; font-size:15px">
                            <a href="{{url('/rider')}}">Become a Rider</a>
                            @if (addon_is_activated('affiliate_system'))
                                <a class="" href="{{ route('affiliate.apply') }}">
                                    {{ translate('Be an affiliate partner') }}
                                </a>
                            @endif
                            <a href="{{ route('seller.index') }}" class="d-inline-block border-width-2">{{ translate('Become a Seller') }}</a>
                        </div>
                    </div>
                {{-- @endauth --}}
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row align-items-center">
                {{-- <div class="col-lg-3 col-sm-12 align-items-center ">
                    <ul
                        class="list-inline d-flex justify-content-between justify-content-lg-start mb-0 align-items-center">
                        <!-- Language switcher -->
                        @if (get_setting('show_language_switcher') == 'on')
                            <li class="list-inline-item dropdown mr-4" id="lang-change">

                                <a href="javascript:void(0)" class="dropdown-toggle text-dark fs-12 py-2"
                                    data-toggle="dropdown" data-display="static">
                                    <span class="">{{ $system_language->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-left">
                                    @foreach (get_all_active_language() as $key => $language)
                                        <li>
                                            <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                                class="dropdown-item @if ($system_language->code == $language->code) active @endif">
                                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                                    class="mr-1 lazyload" alt="{{ $language->name }}" height="11">
                                                <span class="language">{{ $language->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        <!-- Currency Switcher -->
                        @if (get_setting('show_currency_switcher') == 'on')
                            <li class="list-inline-item dropdown ml-auto ml-lg-0 mr-0" id="currency-change">
                                @php
                                    $system_currency = get_system_currency();
                                @endphp

                                <a href="javascript:void(0)" class="dropdown-toggle text-dark fs-12 py-2"
                                    data-toggle="dropdown" data-display="static">
                                    {{ $system_currency->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left">
                                    @foreach (get_all_active_currency() as $key => $currency)
                                        <li>
                                            <a class="dropdown-item @if ($system_currency->code == $currency->code) active @endif"
                                                href="javascript:void(0)"
                                                data-currency="{{ $currency->code }}">{{ $currency->name }}
                                                ({{ $currency->symbol }})
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif


                        <li class="list-inline-item dropdown ml-2 ml-lg-0 mr-0" id="currency-change">
                            <div id="location" style="margin-left: 12px;">
                                <span id="location-text">Detecting your location...</span>
                            </div>
                        </li>


                    </ul>
                </div> --}}
                <div class="col-lg-3 col-sm-12 align-items-center">
                     <!-- Header Logo -->
                     <div class="col-auto pl-0 pr-3 d-flex align-items-center">
                        <a class="d-block py-20px mr-3 ml-0" href="{{ route('home') }}">
                            @php
                                $header_logo = get_setting('header_logo');
                            @endphp
                            @if ($header_logo != null)
                                <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}"
                                    class="mw-100 h-30px h-md-40px" height="40">
                            @else
                                <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                                    class="mw-100 h-30px h-md-40px" height="40">
                            @endif
                        </a>
                    </div>
                </div>





         <div class="col-6 d-flex {{ auth()->check() ? 'align-items-start' : 'align-items-center' }}" style="gap: 30px " >

{{-- {{ auth()->check() ? 0.8 : 1 }} --}}
                    <div class="" style="flex-grow: 1 !important;">

                        <div class="front-header-search d-flex align-items-center bg-white w-100 d-none d-lg-block">
                            <div class="w-100">
                                <form action="{{ route('search') }}" method="GET" class="stop-propagation d-flex align-items-center w-100">
                                    <div class="d-flex position-relative align-items-center w-100"
                                        style="border-radius: 10px; background-color:#F5F5F5; padding:7px 12px 7px 20px">
                                        <input type="text" class="border-0 fs-13 flex-grow-1" style="background-color: transparent" id="search"
                                            name="keyword"
                                            @isset($query) value="{{ $query }}" @endisset
                                            placeholder="What are you looking for?" autocomplete="off">
                                        <button type="submit" class="ms-2" style="border: none; background-color: transparent;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 20L16.2223 16.2156M18.3158 11.1579C18.3158 13.0563 17.5617 14.8769 16.2193 16.2193C14.8769 17.5617 13.0563 18.3158 11.1579 18.3158C9.2595 18.3158 7.43886 17.5617 6.0965 16.2193C4.75413 14.8769 4 13.0563 4 11.1579C4 9.2595 4.75413 7.43886 6.0965 6.0965C7.43886 4.75413 9.2595 4 11.1579 4C13.0563 4 14.8769 4.75413 16.2193 6.0965C17.5617 7.43886 18.3158 9.2595 18.3158 11.1579V11.1579Z" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                                <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                                    style="min-height: 200px">
                                    <div class="search-preloader absolute-top-center">
                                        <div class="dot-loader">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <div class="search-nothing d-none p-3 text-center fs-16"></div>
                                    <div id="search-content" class="text-left"></div>
                                </div>
                            </div>
                        </div>
                    </div>
         </div>

                <div class="col-3  d-none d-lg-block">
                    <div class="d-flex  align-items-center justify-content-end" style="gap:18px">
                    <!-- Wishlist -->
                    <div class="d-none d-lg-block " >
                        <div class="" id="wishlist">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.wishlist')
                        </div>
                    </div>
                    {{-- cart --}}
                    {{-- bg-black-10 --}}
                    <div class="d-none d-xl-block mr-0 has-transition " data-hover="dropdown">
                        <div class="nav-cart-box dropdown h-100" id="cart_items">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.cart')
                        </div>
                    </div>


                    <ul class="list-inline mb-0 h-100 d-flex justify-content-end align-items-center">
                        <div class="d-none d-xl-block ml-auto mr-0">
                            @auth
                                <span
                                    class="d-flex align-items-center nav-user-info py-20px @if (isAdmin()) ml-5 @endif"
                                    id="nav-user-info">
                                    <!-- Image -->
                                    <span
                                        class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                                        @if ($user->avatar_original != null)
                                            <img src="{{ $user_avatar }}" class="img-fit h-100"
                                                alt="{{ translate('avatar') }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                        @else
                                            <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image"
                                                alt="{{ translate('avatar') }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                        @endif
                                    </span>
                                    <!-- Name -->
                                    <h4 class="h5 fs-14 fw-700 text-dark ml-2 mb-0">{{ $user->name }}</h4>
                                </span>
                            @else
                                <!--Login & Registration -->
                            <div class="position-relative " id="login-icon">
                                <span class="d-flex align-items-center nav-user-info ">
                                    <!-- Image -->
                                    <span
                                    {{-- nav-user-img --}}
                                        class="size-30px rounded-circle overflow-hidden d-flex align-items-center justify-content-center ">
                                        {{-- <svg width="34" height="33" viewBox="0 0 34 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.6909 13C20.6909 14.0609 20.2695 15.0783 19.5193 15.8284C18.7692 16.5786 17.7518 17 16.6909 17C15.6301 17 14.6126 16.5786 13.8625 15.8284C13.1123 15.0783 12.6909 14.0609 12.6909 13C12.6909 11.9391 13.1123 10.9217 13.8625 10.1716C14.6126 9.42143 15.6301 9 16.6909 9C17.7518 9 18.7692 9.42143 19.5193 10.1716C20.2695 10.9217 20.6909 11.9391 20.6909 13ZM18.6909 13C18.6909 13.5304 18.4802 14.0391 18.1051 14.4142C17.7301 14.7893 17.2214 15 16.6909 15C16.1605 15 15.6518 14.7893 15.2767 14.4142C14.9016 14.0391 14.6909 13.5304 14.6909 13C14.6909 12.4696 14.9016 11.9609 15.2767 11.5858C15.6518 11.2107 16.1605 11 16.6909 11C17.2214 11 17.7301 11.2107 18.1051 11.5858C18.4802 11.9609 18.6909 12.4696 18.6909 13Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6909 5C10.6159 5 5.69092 9.925 5.69092 16C5.69092 22.075 10.6159 27 16.6909 27C22.7659 27 27.6909 22.075 27.6909 16C27.6909 9.925 22.7659 5 16.6909 5ZM7.69092 16C7.69092 18.09 8.40392 20.014 9.59892 21.542C10.4384 20.4401 11.5211 19.5471 12.7626 18.9327C14.004 18.3183 15.3707 17.9991 16.7559 18C18.1233 17.9984 19.473 18.3091 20.702 18.9084C21.9311 19.5077 23.0071 20.3797 23.8479 21.458C24.7144 20.3216 25.2978 18.9952 25.5498 17.5886C25.8019 16.182 25.7153 14.7355 25.2974 13.369C24.8795 12.0024 24.1421 10.755 23.1464 9.73004C22.1507 8.70503 20.9252 7.93186 19.5713 7.47451C18.2174 7.01716 16.7741 6.88877 15.3608 7.09997C13.9474 7.31117 12.6047 7.85589 11.4437 8.68905C10.2827 9.52222 9.33673 10.6199 8.68415 11.8912C8.03157 13.1625 7.6911 14.571 7.69092 16ZM16.6909 25C14.6248 25.0033 12.6211 24.2926 11.0189 22.988C11.6637 22.0646 12.5221 21.3107 13.521 20.7905C14.5199 20.2702 15.6297 19.999 16.7559 20C17.8681 19.999 18.9644 20.2635 19.9539 20.7713C20.9434 21.2792 21.7973 22.0158 22.4449 22.92C20.8304 24.267 18.7936 25.0033 16.6909 25Z" fill="black"/>
                                            </svg> --}}
                                            <svg width="23" height="23" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.636 7.27308C13.636 8.23751 13.2529 9.16243 12.5709 9.84438C11.889 10.5263 10.9641 10.9094 9.99964 10.9094C9.03522 10.9094 8.1103 10.5263 7.42835 9.84438C6.7464 9.16243 6.36328 8.23751 6.36328 7.27308C6.36328 6.30866 6.7464 5.38374 7.42835 4.70178C8.1103 4.01983 9.03522 3.63672 9.99964 3.63672C10.9641 3.63672 11.889 4.01983 12.5709 4.70178C13.2529 5.38374 13.636 6.30866 13.636 7.27308ZM11.8178 7.27308C11.8178 7.75529 11.6263 8.21776 11.2853 8.55873C10.9443 8.89971 10.4819 9.09126 9.99964 9.09126C9.51743 9.09126 9.05497 8.89971 8.714 8.55873C8.37302 8.21776 8.18146 7.75529 8.18146 7.27308C8.18146 6.79087 8.37302 6.32841 8.714 5.98743C9.05497 5.64646 9.51743 5.4549 9.99964 5.4549C10.4819 5.4549 10.9443 5.64646 11.2853 5.98743C11.6263 6.32841 11.8178 6.79087 11.8178 7.27308Z" fill="black"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 0C4.47727 0 0 4.47727 0 10C0 15.5227 4.47727 20 10 20C15.5227 20 20 15.5227 20 10C20 4.47727 15.5227 0 10 0ZM1.81818 10C1.81818 11.9 2.46636 13.6491 3.55273 15.0382C4.31586 14.0365 5.30014 13.2247 6.42876 12.6661C7.55739 12.1076 8.79982 11.8174 10.0591 11.8182C11.3022 11.8167 12.5292 12.0992 13.6465 12.644C14.7638 13.1888 15.742 13.9815 16.5064 14.9618C17.294 13.9287 17.8244 12.7229 18.0535 11.4442C18.2827 10.1654 18.204 8.85049 17.8241 7.60817C17.4441 6.36585 16.7738 5.23186 15.8686 4.30003C14.9634 3.36821 13.8493 2.66533 12.6185 2.24955C11.3877 1.83378 10.0756 1.71707 8.79078 1.90907C7.50593 2.10107 6.28526 2.59627 5.22979 3.35369C4.17432 4.11111 3.31437 5.10897 2.72112 6.26472C2.12786 7.42047 1.81835 8.70088 1.81818 10ZM10 18.1818C8.12173 18.1849 6.30013 17.5387 4.84364 16.3527C5.42983 15.5133 6.21018 14.8279 7.11825 14.355C8.02633 13.882 9.03523 13.6355 10.0591 13.6364C11.0702 13.6355 12.0668 13.8759 12.9663 14.3376C13.8659 14.7992 14.6422 15.4689 15.2309 16.2909C13.7632 17.5155 11.9115 18.1848 10 18.1818Z" fill="black"/>
                                                </svg>


                                    </span>
                                </span>
                                <div class="position-absolute registeration_menu d-none  bg-white  top-100 border-top" style="background-color: #F3F3F3; top:30px;" id="auth-btn" >
                                    <a href="{{ route('user.login') }}"
                                    {{-- opacity-60 hov-opacity-100  --}}
                                    class="text-dark user-top-menu-name  fs-13 d-block py-2 px-2 ">{{ translate('Login') }}</a>
                                  <a href="{{ route('user.registration') }}"
                                    class="text-dark fs-13 user-top-menu-name d-block py-2 px-2 ">{{ translate('Registration') }}</a>
                                </div>
                                {{-- <div class="position-absolute d-none" id="auth-btn" >
                                    <a href="{{ route('user.login') }}"
                                    class="text-dark opacity-60 hov-opacity-100  fs-12 d-inline-block border-soft-light pr-2 ml-3">{{ translate('Login') }}</a>
                                <a href="{{ route('user.registration') }}"
                                    class="text-dark opacity-60 hov-opacity-100  fs-12 d-inline-block py-2 pl-2">{{ translate('Registration') }}</a>
                                </div> --}}
                            </div>

                            @endauth
                            <div class="hover-user-top-menu position-absolute top-100 right-2 z-3"
                                style="right: -66px;">
                                <div class="container">
                                    <div class="position-static ">
                                        <div class="aiz-user-top-menu bg-white rounded-0 border-top shadow-sm"
                                            style="width:220px;">
                                            <ul class="list-unstyled no-scrollbar mb-0 text-left">
                                                @if (isAdmin())
                                                    <li class="user-top-nav-element border border-top-0"
                                                        data-id="1">
                                                        <a href="{{ route('admin.dashboard') }}"
                                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 16 16">
                                                                <path id="Path_2916" data-name="Path 2916"
                                                                    d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                                                    fill="#b5b5c0" />
                                                            </svg>
                                                            <span
                                                                class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="user-top-nav-element border border-top-0"
                                                        data-id="1">
                                                        <a href="{{ route('dashboard') }}"
                                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 16 16">
                                                                <path id="Path_2916" data-name="Path 2916"
                                                                    d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                                                    fill="#b5b5c0" />
                                                            </svg>
                                                            <span
                                                                class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                                        </a>
                                                    </li>
                                                @endif

                                                @if (isCustomer())
                                                    <li class="user-top-nav-element border border-top-0"
                                                        data-id="1">
                                                        <a href="{{ route('purchase_history.index') }}"
                                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 16 16">
                                                                <g id="Group_25261" data-name="Group 25261"
                                                                    transform="translate(-27.466 -542.963)">
                                                                    <path id="Path_2953" data-name="Path 2953"
                                                                        d="M14.5,5.963h-4a1.5,1.5,0,0,0,0,3h4a1.5,1.5,0,0,0,0-3m0,2h-4a.5.5,0,0,1,0-1h4a.5.5,0,0,1,0,1"
                                                                        transform="translate(22.966 537)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2954" data-name="Path 2954"
                                                                        d="M12.991,8.963a.5.5,0,0,1,0-1H13.5a2.5,2.5,0,0,1,2.5,2.5v10a2.5,2.5,0,0,1-2.5,2.5H2.5a2.5,2.5,0,0,1-2.5-2.5v-10a2.5,2.5,0,0,1,2.5-2.5h.509a.5.5,0,0,1,0,1H2.5a1.5,1.5,0,0,0-1.5,1.5v10a1.5,1.5,0,0,0,1.5,1.5h11a1.5,1.5,0,0,0,1.5-1.5v-10a1.5,1.5,0,0,0-1.5-1.5Z"
                                                                        transform="translate(27.466 536)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2955" data-name="Path 2955"
                                                                        d="M7.5,15.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5"
                                                                        transform="translate(23.966 532)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2956" data-name="Path 2956"
                                                                        d="M7.5,21.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5"
                                                                        transform="translate(23.966 529)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2957" data-name="Path 2957"
                                                                        d="M7.5,27.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5"
                                                                        transform="translate(23.966 526)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2958" data-name="Path 2958"
                                                                        d="M13.5,16.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                                        transform="translate(20.966 531.5)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2959" data-name="Path 2959"
                                                                        d="M13.5,22.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                                        transform="translate(20.966 528.5)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2960" data-name="Path 2960"
                                                                        d="M13.5,28.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                                        transform="translate(20.966 525.5)"
                                                                        fill="#b5b5bf" />
                                                                </g>
                                                            </svg>
                                                            <span
                                                                class="user-top-menu-name has-transition ml-3">{{ translate('Purchase History') }}</span>
                                                        </a>
                                                    </li>
                                                    <li class="user-top-nav-element border border-top-0"
                                                        data-id="1">
                                                        <a href="{{ route('digital_purchase_history.index') }}"
                                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.001"
                                                                height="16" viewBox="0 0 16.001 16">
                                                                <g id="Group_25262" data-name="Group 25262"
                                                                    transform="translate(-1388.154 -562.604)">
                                                                    <path id="Path_2963" data-name="Path 2963"
                                                                        d="M77.864,98.69V92.1a.5.5,0,1,0-1,0V98.69l-1.437-1.437a.5.5,0,0,0-.707.707l1.851,1.852a1,1,0,0,0,.707.293h.172a1,1,0,0,0,.707-.293l1.851-1.852a.5.5,0,0,0-.7-.713Z"
                                                                        transform="translate(1318.79 478.5)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Path_2964" data-name="Path 2964"
                                                                        d="M67.155,88.6a3,3,0,0,1-.474-5.963q-.009-.089-.015-.179a5.5,5.5,0,0,1,10.977-.718,3.5,3.5,0,0,1-.989,6.859h-1.5a.5.5,0,0,1,0-1l1.5,0a2.5,2.5,0,0,0,.417-4.967.5.5,0,0,1-.417-.5,4.5,4.5,0,1,0-8.908.866.512.512,0,0,1,.009.121.5.5,0,0,1-.52.479,2,2,0,1,0-.162,4l.081,0h2a.5.5,0,0,1,0,1Z"
                                                                        transform="translate(1324 486)"
                                                                        fill="#b5b5bf" />
                                                                </g>
                                                            </svg>
                                                            <span
                                                                class="user-top-menu-name has-transition ml-3">{{ translate('Downloads') }}</span>
                                                        </a>
                                                    </li>
                                                    @if (get_setting('conversation_system') == 1)
                                                        <li class="user-top-nav-element border border-top-0"
                                                            data-id="1">
                                                            <a href="{{ route('conversations.index') }}"
                                                                class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" viewBox="0 0 16 16">
                                                                    <g id="Group_25263" data-name="Group 25263"
                                                                        transform="translate(1053.151 256.688)">
                                                                        <path id="Path_3012" data-name="Path 3012"
                                                                            d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                                                                            transform="translate(-1178 -341)"
                                                                            fill="#b5b5bf" />
                                                                        <path id="Path_3013" data-name="Path 3013"
                                                                            d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                                                                            transform="translate(-1182 -337)"
                                                                            fill="#b5b5bf" />
                                                                        <path id="Path_3014" data-name="Path 3014"
                                                                            d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                                            transform="translate(-1181 -343.5)"
                                                                            fill="#b5b5bf" />
                                                                        <path id="Path_3015" data-name="Path 3015"
                                                                            d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1"
                                                                            transform="translate(-1181 -346.5)"
                                                                            fill="#b5b5bf" />
                                                                    </g>
                                                                </svg>
                                                                <span
                                                                    class="user-top-menu-name has-transition ml-3">{{ translate('Conversations') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (get_setting('wallet_system') == 1)
                                                        <li class="user-top-nav-element border border-top-0"
                                                            data-id="1">
                                                            <a href="{{ route('wallet.index') }}"
                                                                class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="16" height="16"
                                                                    viewBox="0 0 16 16">
                                                                    <defs>
                                                                        <clipPath id="clip-path1">
                                                                            <rect id="Rectangle_1386"
                                                                                data-name="Rectangle 1386"
                                                                                width="16" height="16"
                                                                                fill="#b5b5bf" />
                                                                        </clipPath>
                                                                    </defs>
                                                                    <g id="Group_8102" data-name="Group 8102"
                                                                        clip-path="url(#clip-path1)">
                                                                        <path id="Path_2936" data-name="Path 2936"
                                                                            d="M13.5,4H13V2.5A2.5,2.5,0,0,0,10.5,0h-8A2.5,2.5,0,0,0,0,2.5v11A2.5,2.5,0,0,0,2.5,16h11A2.5,2.5,0,0,0,16,13.5v-7A2.5,2.5,0,0,0,13.5,4M2.5,1h8A1.5,1.5,0,0,1,12,2.5V4H2.5a1.5,1.5,0,0,1,0-3M15,11H10a1,1,0,0,1,0-2h5Zm0-3H10a2,2,0,0,0,0,4h5v1.5A1.5,1.5,0,0,1,13.5,15H2.5A1.5,1.5,0,0,1,1,13.5v-9A2.5,2.5,0,0,0,2.5,5h11A1.5,1.5,0,0,1,15,6.5Z"
                                                                            fill="#b5b5bf" />
                                                                    </g>
                                                                </svg>
                                                                <span
                                                                    class="user-top-menu-name has-transition ml-3">{{ translate('My Wallet') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li class="user-top-nav-element border border-top-0"
                                                        data-id="1">
                                                        <a href="{{ route('support_ticket.index') }}"
                                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16.001" viewBox="0 0 16 16.001">
                                                                <g id="Group_25259" data-name="Group 25259"
                                                                    transform="translate(-316 -1066)">
                                                                    <path id="Subtraction_184"
                                                                        data-name="Subtraction 184"
                                                                        d="M16427.109,902H16420a8.015,8.015,0,1,1,8-8,8.278,8.278,0,0,1-1.422,4.535l1.244,2.132a.81.81,0,0,1,0,.891A.791.791,0,0,1,16427.109,902ZM16420,887a7,7,0,1,0,0,14h6.283c.275,0,.414,0,.549-.111s-.209-.574-.34-.748l0,0-.018-.022-1.064-1.6A6.829,6.829,0,0,0,16427,894a6.964,6.964,0,0,0-7-7Z"
                                                                        transform="translate(-16096 180)"
                                                                        fill="#b5b5bf" />
                                                                    <path id="Union_12" data-name="Union 12"
                                                                        d="M16414,895a1,1,0,1,1,1,1A1,1,0,0,1,16414,895Zm.5-2.5V891h.5a2,2,0,1,0-2-2h-1a3,3,0,1,1,3.5,2.958v.54a.5.5,0,1,1-1,0Zm-2.5-3.5h1a.5.5,0,1,1-1,0Z"
                                                                        transform="translate(-16090.998 183.001)"
                                                                        fill="#b5b5bf" />
                                                                </g>
                                                            </svg>
                                                            <span
                                                                class="user-top-menu-name has-transition ml-3">{{ translate('Support Ticket') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                                    <a href="{{ route('logout') }}"
                                                        class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="15.999" viewBox="0 0 16 15.999">
                                                            <g id="Group_25503" data-name="Group 25503"
                                                                transform="translate(-24.002 -377)">
                                                                <g id="Group_25265" data-name="Group 25265"
                                                                    transform="translate(-216.534 -160)">
                                                                    <path id="Subtraction_192"
                                                                        data-name="Subtraction 192"
                                                                        d="M12052.535,2920a8,8,0,0,1-4.569-14.567l.721.72a7,7,0,1,0,7.7,0l.721-.72a8,8,0,0,1-4.567,14.567Z"
                                                                        transform="translate(-11803.999 -2367)"
                                                                        fill="#d43533" />
                                                                </g>
                                                                <rect id="Rectangle_19022" data-name="Rectangle 19022"
                                                                    width="1" height="8" rx="0.5"
                                                                    transform="translate(31.5 377)" fill="#d43533" />
                                                            </g>
                                                        </svg>
                                                        <span
                                                            class="user-top-menu-name text-primary has-transition ml-3">{{ translate('Logout') }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
            </div>
        </div>
    </div>
    {{-- <div class="container">
        <div class="row">
            <div class="col-9"></div>
            <div class="col-3 d-flex justify-content-end align-items-center">
                <div class="d-none d-xl-block ml-auto mr-0">
                    @if (get_setting('helpline_number'))
                        <!-- Helpline -->
                        <li class="list-inline-item ml-3 pl-3 pr-0">
                            <a href="tel:{{ get_setting('helpline_number') }}"
                                class="text-secondary fs-12 d-inline-block py-2">
                                <span>{{ translate('Helpline') }}</span>
                                <span>{{ get_setting('helpline_number') }}</span>
                            </a>
                        </li>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}




    <header class="@if (get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 bg-white">
        <!-- Search Bar -->
        <div class="position-relative logo-bar-area border-bottom border-md-none z-1025">
            <div class="container">
                <div class="d-flex align-items-center">
                    <!-- top menu sidebar button -->
                    <button type="button" class="btn d-lg-none mr-3 mr-sm-4 p-0 active" data-toggle="class-toggle"
                        data-target=".aiz-top-menu-sidebar">
                        <svg id="Component_43_1" data-name="Component 43 – 1" xmlns="http://www.w3.org/2000/svg"
                            width="16" height="16" viewBox="0 0 16 16">
                            <rect id="Rectangle_19062" data-name="Rectangle 19062" width="16" height="2"
                                transform="translate(0 7)" fill="#919199" />
                            <rect id="Rectangle_19063" data-name="Rectangle 19063" width="16" height="2"
                                fill="#919199" />
                            <rect id="Rectangle_19064" data-name="Rectangle 19064" width="16" height="2"
                                transform="translate(0 14)" fill="#919199" />
                        </svg>

                    </button>
                    <!-- Header Logo -->
                    <div class="col-auto pl-0 pr-3 d-flex align-items-center d-lg-none">
                        <a class="d-block py-20px mr-3 ml-0" href="{{ route('home') }}">
                            @php
                                $header_logo = get_setting('header_logo');
                            @endphp
                            @if ($header_logo != null)
                                <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}"
                                    class="mw-100 h-30px h-md-40px" height="40">
                            @else
                                <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                                    class="mw-100 h-30px h-md-40px" height="40">
                            @endif
                        </a>
                    </div>
                    <!-- Search Icon for small device -->
                    <div class="d-lg-none ml-auto mr-0">
                        <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle"
                            data-target=".front-header-search">
                            <i class="las la-search la-flip-horizontal la-2x"></i>
                        </a>
                    </div>
                    <!-- Search field -->
                    <div class="flex-grow-1 front-header-search d-flex align-items-center bg-white mx-xl-5 d-lg-none">
                        <div class="position-relative flex-grow-1 px-3 px-lg-0">
                            <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                                <div class="d-flex position-relative align-items-center">
                                    <div class="d-lg-none" data-toggle="class-toggle"
                                        data-target=".front-header-search">
                                        <button class="btn px-2" type="button"><i
                                                class="la la-2x la-long-arrow-left"></i></button>
                                    </div>
                                    <div class="search-input-box">
                                        <input type="text"
                                            class="border border-soft-light form-control fs-14 hov-animate-outline"
                                            id="search" name="keyword"
                                            @isset($query)
                                            value="{{ $query }}"
                                        @endisset
                                            placeholder="{{ translate('I am shopping for...') }}" autocomplete="off">

                                        <svg id="Group_723" data-name="Group 723" xmlns="http://www.w3.org/2000/svg"
                                            width="20.001" height="20" viewBox="0 0 20.001 20">
                                            <path id="Path_3090" data-name="Path 3090"
                                                d="M9.847,17.839a7.993,7.993,0,1,1,7.993-7.993A8,8,0,0,1,9.847,17.839Zm0-14.387a6.394,6.394,0,1,0,6.394,6.394A6.4,6.4,0,0,0,9.847,3.453Z"
                                                transform="translate(-1.854 -1.854)" fill="#b5b5bf" />
                                            <path id="Path_3091" data-name="Path 3091"
                                                d="M24.4,25.2a.8.8,0,0,1-.565-.234l-6.15-6.15a.8.8,0,0,1,1.13-1.13l6.15,6.15A.8.8,0,0,1,24.4,25.2Z"
                                                transform="translate(-5.2 -5.2)" fill="#b5b5bf" />
                                        </svg>
                                    </div>




                                </div>
                            </form>
                            <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                                style="min-height: 200px">
                                <div class="search-preloader absolute-top-center">
                                    <div class="dot-loader">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                                <div class="search-nothing d-none p-3 text-center fs-16">

                                </div>
                                <div id="search-content" class="text-left">

                                </div>
                            </div>
                        </div>
                    </div>






                    <!-- Search box -->
                    <div class="d-none d-lg-none ml-3 mr-0">
                        <div class="nav-search-box">
                            <a href="#" class="nav-box-link">
                                <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                            </a>
                        </div>
                    </div>
                    <!-- Compare -->
                    {{-- <div class="d-none d-lg-block ml-1 mr-0">
                        <div class="circle" id="compare">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.compare')
                        </div>
                    </div> --}}
                    <!-- Wishlist -->
                    {{-- <div class="d-none d-lg-block mr-3" style="margin-left: 36px;">
                        <div class="circle" id="wishlist">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.wishlist')
                        </div>
                    </div> --}}
                    {{-- @if (!isAdmin())
                        <!-- Notifications -->
                        <ul class="list-inline mb-0 h-100 d-none d-xl-flex justify-content-end align-items-center">
                            <li class="list-inline-item ml-3 mr-1 pr-3 pl-0 dropdown">
                                <a class="dropdown-toggle no-arrow text-secondary fs-12" data-toggle="dropdown"
                                    href="javascript:void(0);" role="button" aria-haspopup="false"
                                    aria-expanded="false">
                                    <div class="circle">
                                        <div class="position-relative d-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.668" height="13"
                                                viewBox="0 0 14.668 16">
                                                <path id="_26._Notification" data-name="26. Notification"
                                                    d="M8.333,16A3.34,3.34,0,0,0,11,14.667H5.666A3.34,3.34,0,0,0,8.333,16ZM15.06,9.78a2.457,2.457,0,0,1-.727-1.747V6a6,6,0,1,0-12,0V8.033A2.457,2.457,0,0,1,1.606,9.78,2.083,2.083,0,0,0,3.08,13.333H13.586A2.083,2.083,0,0,0,15.06,9.78Z"
                                                    transform="translate(-0.999)" fill="#fff" />
                                            </svg>
                                            @if (Auth::check() && count($user->unreadNotifications) > 0)
                                                <span
                                                    class="badge badge-primary badge-inline badge-pill absolute-top-right--10px">{{ count($user->unreadNotifications) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </a>

                                @auth
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg py-0 rounded-0">
                                        <div class="p-3 bg-light border-bottom">
                                            <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                                        </div>
                                        <div class="px-3 c-scrollbar-light overflow-auto " style="max-height:300px;">
                                            <ul class="list-group list-group-flush">
                                                @forelse($user->unreadNotifications as $notification)
                                                    <li class="list-group-item">
                                                        @if ($notification->type == 'App\Notifications\OrderNotification')
                                                            @if ($user->user_type == 'customer')
                                                                <a href="{{ route('purchase_history.details', encrypt($notification->data['order_id'])) }}"
                                                                    class="text-secondary fs-12">
                                                                    <span class="ml-2">
                                                                        {{ translate('Order code: ') }}
                                                                        {{ $notification->data['order_code'] }}
                                                                        {{ translate('has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                                                    </span>
                                                                </a>
                                                            @elseif ($user->user_type == 'seller')
                                                                <a href="{{ route('seller.orders.show', encrypt($notification->data['order_id'])) }}"
                                                                    class="text-secondary fs-12">
                                                                    <span class="ml-2">
                                                                        {{ translate('Order code: ') }}
                                                                        {{ $notification->data['order_code'] }}
                                                                        {{ translate('has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </li>
                                                @empty
                                                    <li class="list-group-item">
                                                        <div class="py-4 text-center fs-16">
                                                            {{ translate('No notification found') }}
                                                        </div>
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        <div class="text-center border-top">
                                            <a href="{{ route('all-notifications') }}"
                                                class="text-secondary fs-12 d-block py-2">
                                                {{ translate('View All Notifications') }}
                                            </a>
                                        </div>
                                    </div>
                                @endauth
                            </li>
                        </ul>
                    @endif --}}

                    {{-- <div class="d-none d-xl-block mr-0 has-transition bg-black-10" data-hover="dropdown">
                        <div class="nav-cart-box dropdown h-100" id="cart_items">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.cart')
                        </div>
                    </div> --}}

                    {{-- <div class="d-none d-xl-block ml-auto mr-0">
                        @auth
                            <span
                                class="d-flex align-items-center nav-user-info py-20px @if (isAdmin()) ml-5 @endif"
                                id="nav-user-info">
                                <!-- Image -->
                                <span
                                    class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                                    @if ($user->avatar_original != null)
                                        <img src="{{ $user_avatar }}" class="img-fit h-100"
                                            alt="{{ translate('avatar') }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    @else
                                        <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image"
                                            alt="{{ translate('avatar') }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    @endif
                                </span>
                                <!-- Name -->
                                <h4 class="h5 fs-14 fw-700 text-dark ml-2 mb-0">{{ $user->name }}</h4>
                            </span>
                        @else
                            <!--Login & Registration -->
                            <span class="d-flex align-items-center nav-user-info ml-3">
                                <!-- Image -->
                                <span
                                    class="size-40px rounded-circle overflow-hidden border d-flex align-items-center justify-content-center nav-user-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19.902" height="20.012"
                                        viewBox="0 0 19.902 20.012">
                                        <path id="fe2df171891038b33e9624c27e96e367"
                                            d="M15.71,12.71a6,6,0,1,0-7.42,0,10,10,0,0,0-6.22,8.18,1.006,1.006,0,1,0,2,.22,8,8,0,0,1,15.9,0,1,1,0,0,0,1,.89h.11a1,1,0,0,0,.88-1.1,10,10,0,0,0-6.25-8.19ZM12,12a4,4,0,1,1,4-4A4,4,0,0,1,12,12Z"
                                            transform="translate(-2.064 -1.995)" fill="#91919b" />
                                    </svg>
                                </span>
                                <a href="{{ route('user.login') }}"
                                    class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block border-right border-soft-light border-width-2 pr-2 ml-3">{{ translate('Login') }}</a>
                                <a href="{{ route('user.registration') }}"
                                    class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block py-2 pl-2">{{ translate('Registration') }}</a>
                            </span>
                        @endauth
                    </div> --}}


                </div>
            </div>

            <!-- Loged in user Menus -->
            <div class="hover-user-top-menu position-absolute top-100 left-0 right-0 z-3">
                <div class="container">
                    <div class="position-static float-right">
                        <div class="aiz-user-top-menu bg-white rounded-0 border-top shadow-sm" style="width:220px;">
                            {{-- <ul class="list-unstyled no-scrollbar mb-0 text-left">
                                @if (isAdmin())
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16">
                                                <path id="Path_2916" data-name="Path 2916"
                                                    d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                                    fill="#b5b5c0" />
                                            </svg>
                                            <span
                                                class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                        </a>
                                    </li>
                                @else
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('dashboard') }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16">
                                                <path id="Path_2916" data-name="Path 2916"
                                                    d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                                    fill="#b5b5c0" />
                                            </svg>
                                            <span
                                                class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (isCustomer())
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('purchase_history.index') }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16">
                                                <g id="Group_25261" data-name="Group 25261"
                                                    transform="translate(-27.466 -542.963)">
                                                    <path id="Path_2953" data-name="Path 2953"
                                                        d="M14.5,5.963h-4a1.5,1.5,0,0,0,0,3h4a1.5,1.5,0,0,0,0-3m0,2h-4a.5.5,0,0,1,0-1h4a.5.5,0,0,1,0,1"
                                                        transform="translate(22.966 537)" fill="#b5b5bf" />
                                                    <path id="Path_2954" data-name="Path 2954"
                                                        d="M12.991,8.963a.5.5,0,0,1,0-1H13.5a2.5,2.5,0,0,1,2.5,2.5v10a2.5,2.5,0,0,1-2.5,2.5H2.5a2.5,2.5,0,0,1-2.5-2.5v-10a2.5,2.5,0,0,1,2.5-2.5h.509a.5.5,0,0,1,0,1H2.5a1.5,1.5,0,0,0-1.5,1.5v10a1.5,1.5,0,0,0,1.5,1.5h11a1.5,1.5,0,0,0,1.5-1.5v-10a1.5,1.5,0,0,0-1.5-1.5Z"
                                                        transform="translate(27.466 536)" fill="#b5b5bf" />
                                                    <path id="Path_2955" data-name="Path 2955"
                                                        d="M7.5,15.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5"
                                                        transform="translate(23.966 532)" fill="#b5b5bf" />
                                                    <path id="Path_2956" data-name="Path 2956"
                                                        d="M7.5,21.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5"
                                                        transform="translate(23.966 529)" fill="#b5b5bf" />
                                                    <path id="Path_2957" data-name="Path 2957"
                                                        d="M7.5,27.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5"
                                                        transform="translate(23.966 526)" fill="#b5b5bf" />
                                                    <path id="Path_2958" data-name="Path 2958"
                                                        d="M13.5,16.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                        transform="translate(20.966 531.5)" fill="#b5b5bf" />
                                                    <path id="Path_2959" data-name="Path 2959"
                                                        d="M13.5,22.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                        transform="translate(20.966 528.5)" fill="#b5b5bf" />
                                                    <path id="Path_2960" data-name="Path 2960"
                                                        d="M13.5,28.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                        transform="translate(20.966 525.5)" fill="#b5b5bf" />
                                                </g>
                                            </svg>
                                            <span
                                                class="user-top-menu-name has-transition ml-3">{{ translate('Purchase History') }}</span>
                                        </a>
                                    </li>
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('digital_purchase_history.index') }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.001" height="16"
                                                viewBox="0 0 16.001 16">
                                                <g id="Group_25262" data-name="Group 25262"
                                                    transform="translate(-1388.154 -562.604)">
                                                    <path id="Path_2963" data-name="Path 2963"
                                                        d="M77.864,98.69V92.1a.5.5,0,1,0-1,0V98.69l-1.437-1.437a.5.5,0,0,0-.707.707l1.851,1.852a1,1,0,0,0,.707.293h.172a1,1,0,0,0,.707-.293l1.851-1.852a.5.5,0,0,0-.7-.713Z"
                                                        transform="translate(1318.79 478.5)" fill="#b5b5bf" />
                                                    <path id="Path_2964" data-name="Path 2964"
                                                        d="M67.155,88.6a3,3,0,0,1-.474-5.963q-.009-.089-.015-.179a5.5,5.5,0,0,1,10.977-.718,3.5,3.5,0,0,1-.989,6.859h-1.5a.5.5,0,0,1,0-1l1.5,0a2.5,2.5,0,0,0,.417-4.967.5.5,0,0,1-.417-.5,4.5,4.5,0,1,0-8.908.866.512.512,0,0,1,.009.121.5.5,0,0,1-.52.479,2,2,0,1,0-.162,4l.081,0h2a.5.5,0,0,1,0,1Z"
                                                        transform="translate(1324 486)" fill="#b5b5bf" />
                                                </g>
                                            </svg>
                                            <span
                                                class="user-top-menu-name has-transition ml-3">{{ translate('Downloads') }}</span>
                                        </a>
                                    </li>
                                    @if (get_setting('conversation_system') == 1)
                                        <li class="user-top-nav-element border border-top-0" data-id="1">
                                            <a href="{{ route('conversations.index') }}"
                                                class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 16 16">
                                                    <g id="Group_25263" data-name="Group 25263"
                                                        transform="translate(1053.151 256.688)">
                                                        <path id="Path_3012" data-name="Path 3012"
                                                            d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                                                            transform="translate(-1178 -341)" fill="#b5b5bf" />
                                                        <path id="Path_3013" data-name="Path 3013"
                                                            d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                                                            transform="translate(-1182 -337)" fill="#b5b5bf" />
                                                        <path id="Path_3014" data-name="Path 3014"
                                                            d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                            transform="translate(-1181 -343.5)" fill="#b5b5bf" />
                                                        <path id="Path_3015" data-name="Path 3015"
                                                            d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1"
                                                            transform="translate(-1181 -346.5)" fill="#b5b5bf" />
                                                    </g>
                                                </svg>
                                                <span
                                                    class="user-top-menu-name has-transition ml-3">{{ translate('Conversations') }}</span>
                                            </a>
                                        </li>
                                    @endif

                                    @if (get_setting('wallet_system') == 1)
                                        <li class="user-top-nav-element border border-top-0" data-id="1">
                                            <a href="{{ route('wallet.index') }}"
                                                class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="16"
                                                    height="16" viewBox="0 0 16 16">
                                                    <defs>
                                                        <clipPath id="clip-path1">
                                                            <rect id="Rectangle_1386" data-name="Rectangle 1386"
                                                                width="16" height="16" fill="#b5b5bf" />
                                                        </clipPath>
                                                    </defs>
                                                    <g id="Group_8102" data-name="Group 8102"
                                                        clip-path="url(#clip-path1)">
                                                        <path id="Path_2936" data-name="Path 2936"
                                                            d="M13.5,4H13V2.5A2.5,2.5,0,0,0,10.5,0h-8A2.5,2.5,0,0,0,0,2.5v11A2.5,2.5,0,0,0,2.5,16h11A2.5,2.5,0,0,0,16,13.5v-7A2.5,2.5,0,0,0,13.5,4M2.5,1h8A1.5,1.5,0,0,1,12,2.5V4H2.5a1.5,1.5,0,0,1,0-3M15,11H10a1,1,0,0,1,0-2h5Zm0-3H10a2,2,0,0,0,0,4h5v1.5A1.5,1.5,0,0,1,13.5,15H2.5A1.5,1.5,0,0,1,1,13.5v-9A2.5,2.5,0,0,0,2.5,5h11A1.5,1.5,0,0,1,15,6.5Z"
                                                            fill="#b5b5bf" />
                                                    </g>
                                                </svg>
                                                <span
                                                    class="user-top-menu-name has-transition ml-3">{{ translate('My Wallet') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('support_ticket.index') }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.001"
                                                viewBox="0 0 16 16.001">
                                                <g id="Group_25259" data-name="Group 25259"
                                                    transform="translate(-316 -1066)">
                                                    <path id="Subtraction_184" data-name="Subtraction 184"
                                                        d="M16427.109,902H16420a8.015,8.015,0,1,1,8-8,8.278,8.278,0,0,1-1.422,4.535l1.244,2.132a.81.81,0,0,1,0,.891A.791.791,0,0,1,16427.109,902ZM16420,887a7,7,0,1,0,0,14h6.283c.275,0,.414,0,.549-.111s-.209-.574-.34-.748l0,0-.018-.022-1.064-1.6A6.829,6.829,0,0,0,16427,894a6.964,6.964,0,0,0-7-7Z"
                                                        transform="translate(-16096 180)" fill="#b5b5bf" />
                                                    <path id="Union_12" data-name="Union 12"
                                                        d="M16414,895a1,1,0,1,1,1,1A1,1,0,0,1,16414,895Zm.5-2.5V891h.5a2,2,0,1,0-2-2h-1a3,3,0,1,1,3.5,2.958v.54a.5.5,0,1,1-1,0Zm-2.5-3.5h1a.5.5,0,1,1-1,0Z"
                                                        transform="translate(-16090.998 183.001)" fill="#b5b5bf" />
                                                </g>
                                            </svg>
                                            <span
                                                class="user-top-menu-name has-transition ml-3">{{ translate('Support Ticket') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                    <a href="{{ route('logout') }}"
                                        class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15.999"
                                            viewBox="0 0 16 15.999">
                                            <g id="Group_25503" data-name="Group 25503"
                                                transform="translate(-24.002 -377)">
                                                <g id="Group_25265" data-name="Group 25265"
                                                    transform="translate(-216.534 -160)">
                                                    <path id="Subtraction_192" data-name="Subtraction 192"
                                                        d="M12052.535,2920a8,8,0,0,1-4.569-14.567l.721.72a7,7,0,1,0,7.7,0l.721-.72a8,8,0,0,1-4.567,14.567Z"
                                                        transform="translate(-11803.999 -2367)" fill="#d43533" />
                                                </g>
                                                <rect id="Rectangle_19022" data-name="Rectangle 19022" width="1"
                                                    height="8" rx="0.5" transform="translate(31.5 377)"
                                                    fill="#d43533" />
                                            </g>
                                        </svg>
                                        <span
                                            class="user-top-menu-name text-primary has-transition ml-3">{{ translate('Logout') }}</span>
                                    </a>
                                </li>
                            </ul> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Bar -->
        <div class="d-none d-lg-block position-relative bg-primary h-50px">
            <div class="container h-100">
                <div class="d-flex h-100">
                    <!-- Categoty Menu Button -->
                    {{-- <div class="d-none d-xl-block all-category has-transition bg-black-10" id="category-menu-bar">
                        <div class="px-3 h-100"
                            style="padding-top: 12px;padding-bottom: 12px; width:270px; cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-700 fs-16 text-white mr-3">{{ translate('Categories') }}</span>
                                    <a href="{{ route('categories.all') }}" class="text-reset">
                                        <span
                                            class="d-none d-lg-inline-block text-white hov-opacity-80">({{ translate('See All') }})</span>
                                    </a>
                                </div>
                                <i class="las la-angle-down text-white has-transition" id="category-menu-bar-icon"
                                    style="font-size: 1.2rem !important"></i>
                            </div>
                        </div>
                    </div> --}}
                    <!-- Header Menus -->
                    @php
                        $nav_txt_color =
                            get_setting('header_nav_menu_text') == 'light' ||
                            get_setting('header_nav_menu_text') == null
                                ? 'text-white'
                                : 'text-dark';
                    @endphp
                    {{-- ml-xl-4 --}}
                    <div class=" w-100 " >
                        <div class="d-flex align-items-center justify-content-center justify-content-xl-start h-100">
                            <div class="container">
                                <div class="d-flex position-relative">
                                    <div class="position-static">
                                        @include('frontend.' . get_setting('homepage_select') . '.partials.category_menu')
                                    </div>
                                </div>
                            </div>
                            {{-- <ul class="list-inline mb-0 pl-0 hor-swipe c-scrollbar-light">
                                @if (get_setting('header_menu_labels') != null)
                                    @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
                                        <li class="list-inline-item mr-0 animate-underline-white">
                                            <a href="{{ json_decode(get_setting('header_menu_links'), true)[$key] }}"
                                                class="fs-13 px-3 py-3 d-inline-block fw-700 {{ $nav_txt_color }} header_menu_links hov-bg-black-10
                                            @if (url()->current() == json_decode(get_setting('header_menu_links'), true)[$key]) active @endif">
                                                {{ translate($value) }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul> --}}
                        </div>
                    </div>
                    <!-- Cart -->
                    {{-- <div class="d-none d-xl-block align-self-stretch ml-5 mr-0 has-transition bg-black-10"
                        data-hover="dropdown">
                        <div class="nav-cart-box dropdown h-100" id="cart_items" style="width: max-content;">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.cart')
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- Categoty Menus -->
            {{-- <div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 z-3 d-none"
                style="top:200% !important" id="click-category-menu">
                <div class="container">
                    <div class="d-flex position-relative">
                        <div class="position-static">
                            @include('frontend.' . get_setting('homepage_select') . '.partials.category_menu')
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </header>

    <!-- Top Menu Sidebar -->
    <div class="aiz-top-menu-sidebar collapse-sidebar-wrap sidebar-xl sidebar-left d-lg-none z-1035">
        <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle"
            data-target=".aiz-top-menu-sidebar" data-same=".hide-top-menu-bar"></div>
        <div class="collapse-sidebar c-scrollbar-light text-left">
            <button type="button" class="btn btn-sm p-4 hide-top-menu-bar" data-toggle="class-toggle"
                data-target=".aiz-top-menu-sidebar">
                <i class="las la-times la-2x text-primary"></i>
            </button>
            @auth
                <span class="d-flex align-items-center nav-user-info pl-4">
                    <!-- Image -->
                    <span class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                        @if ($user->avatar_original != null)
                            <img src="{{ $user_avatar }}" class="img-fit h-100" alt="{{ translate('avatar') }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                        @else
                            <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image"
                                alt="{{ translate('avatar') }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                        @endif
                    </span>
                    <!-- Name -->
                    <h4 class="h5 fs-14 fw-700 text-dark ml-2 mb-0">{{ $user->name }}</h4>
                </span>
            @else
                <!--Login & Registration -->
                <span class="d-flex align-items-center nav-user-info pl-4">
                    <!-- Image -->
                    <span
                        class="size-40px rounded-circle overflow-hidden border d-flex align-items-center justify-content-center nav-user-img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19.902" height="20.012"
                            viewBox="0 0 19.902 20.012">
                            <path id="fe2df171891038b33e9624c27e96e367"
                                d="M15.71,12.71a6,6,0,1,0-7.42,0,10,10,0,0,0-6.22,8.18,1.006,1.006,0,1,0,2,.22,8,8,0,0,1,15.9,0,1,1,0,0,0,1,.89h.11a1,1,0,0,0,.88-1.1,10,10,0,0,0-6.25-8.19ZM12,12a4,4,0,1,1,4-4A4,4,0,0,1,12,12Z"
                                transform="translate(-2.064 -1.995)" fill="#91919b" />
                        </svg>
                    </span>
                    <a href="{{ route('user.login') }}"
                        class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block border-right border-soft-light border-width-2 pr-2 ml-3">{{ translate('Login') }}</a>
                    <a href="{{ route('user.registration') }}"
                        class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block py-2 pl-2">{{ translate('Registration') }}</a>
                </span>
            @endauth
            <hr>
            <ul class="mb-0 pl-3 pb-3 h-100">
                @if (get_setting('header_menu_labels') != null)
                    @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
                        <li class="mr-0">
                            <a href="{{ json_decode(get_setting('header_menu_links'), true)[$key] }}"
                                class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                            @if (url()->current() == json_decode(get_setting('header_menu_links'), true)[$key]) active @endif">
                                {{ translate($value) }}
                            </a>
                        </li>
                    @endforeach
                @endif
                @auth
                    @if (isAdmin())
                        <hr>
                        <li class="mr-0">
                            <a href="{{ route('admin.dashboard') }}"
                                class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links">
                                {{ translate('My Account') }}
                            </a>
                        </li>
                    @else
                        <hr>
                        <li class="mr-0">
                            <a href="{{ route('dashboard') }}"
                                class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                                {{ areActiveRoutes(['dashboard'], ' active') }}">
                                {{ translate('My Account') }}
                            </a>
                        </li>
                    @endif
                    @if (isCustomer())
                        <li class="mr-0">
                            <a href="{{ route('all-notifications') }}"
                                class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                                {{ areActiveRoutes(['all-notifications'], ' active') }}">
                                {{ translate('Notifications') }}
                            </a>
                        </li>
                        <li class="mr-0">
                            <a href="{{ route('wishlists.index') }}"
                                class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                                {{ areActiveRoutes(['wishlists.index'], ' active') }}">
                                {{ translate('Wishlist') }}
                            </a>
                        </li>
                        <li class="mr-0">
                            <a href="{{ route('compare') }}"
                                class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                                {{ areActiveRoutes(['compare'], ' active') }}">
                                {{ translate('Compare') }}
                            </a>
                        </li>
                    @endif
                    <hr>
                    <li class="mr-0">
                        <a href="{{ route('logout') }}"
                            class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-primary header_menu_links">
                            {{ translate('Logout') }}
                        </a>
                    </li>
                @endauth
            </ul>
            <br>
            <br>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>

{{-- @section('script')


    <script>
        function showLocation(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const locationText = document.getElementById("location-text");


            const apiKey = '4fa638e1eabc4d4e93e2cce1cd5ef3fc';
            const url = `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        const result = data.results[0];
                        const components = result.components;
                        const city = components.city || components.state_district ||
                            components.village;
                        const country = components.country;


                        if (city && country) {
                            locationText.textContent = `${city}, ${country}`;
                        } else {
                            locationText.textContent = 'Location not found';
                        }
                    } else {
                        locationText.textContent = 'No results found';
                    }
                })
                .catch(error => {
                    locationText.textContent = 'Geocoding error: ' + error;
                });
        }

        function showError(error) {
            const locationText = document.getElementById("location-text");
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    locationText.textContent = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    locationText.textContent = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    locationText.textContent = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    locationText.textContent = "An unknown error occurred.";
                    break;
            }
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showLocation, showError);
            } else {
                document.getElementById("location-text").textContent = "Geolocation is not supported by this browser.";
            }
        }
        window.onload = getLocation;
    </script>

@endsection --}}
