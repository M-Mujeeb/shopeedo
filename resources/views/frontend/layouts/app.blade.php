<!DOCTYPE html>

@php
    $rtl = get_session_language()->rtl;
@endphp

@if ($rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">

    <title>@yield('meta_title', get_setting('website_name') . ' | ' . get_setting('site_motto'))</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">

    @yield('meta')

    @if (!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
        @php
            $meta_image = uploaded_asset(get_setting('meta_image'));
        @endphp
        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ get_setting('meta_title') }}">
        <meta itemprop="description" content="{{ get_setting('meta_description') }}">
        <meta itemprop="image" content="{{ $meta_image }}">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
        <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="{{ $meta_image }}">

        <!-- Open Graph data -->
        <meta property="og:title" content="{{ get_setting('meta_title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ route('home') }}" />
        <meta property="og:image" content="{{ $meta_image }}" />
        <meta property="og:description" content="{{ get_setting('meta_description') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif

    <!-- Favicon -->
    @php
        $site_icon = uploaded_asset(get_setting('site_icon'));
    @endphp
    <link rel="icon" href="{{ $site_icon }}">
    <link rel="apple-touch-icon" href="{{ $site_icon }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> --}}

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css?v=') }}{{ rand(1000, 9999) }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">
    @yield('css')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
    </script>

<style>
    :root{
        --blue: #3490f3;
        --hov-blue: #2e7fd6;
        --soft-blue: rgba(0, 123, 255, 0.15);
        --secondary-base: {{ get_setting('secondary_base_color', '#ffc519') }};
        --hov-secondary-base: {{ get_setting('secondary_base_hov_color', '#dbaa17') }};
        --soft-secondary-base: {{ hex2rgba(get_setting('secondary_base_color', '#ffc519'), 0.15) }};
        --gray: #9d9da6;
        --gray-dark: #8d8d8d;
        --secondary: #919199;
        --soft-secondary: rgba(145, 145, 153, 0.15);
        --success: #85b567;
        --soft-success: rgba(133, 181, 103, 0.15);
        --warning: #f3af3d;
        --soft-warning: rgba(243, 175, 61, 0.15);
        --light: #f5f5f5;
        --soft-light: #dfdfe6;
        --soft-white: #b5b5bf;
        --dark: #292933;
        --soft-dark: #1b1b28;
        --primary: {{ get_setting('base_color', '#d43533') }};
        --hov-primary: {{ get_setting('base_hov_color', '#9d1b1a') }};
        --soft-primary: {{ hex2rgba(get_setting('base_color', '#d43533'), 0.15) }};
    }
    /* body{
        font-family: 'Public Sans', sans-serif;
        font-weight: 400;
    } */

    body {
    font-family: 'Inter', sans-serif !important;
    font-weight: 400;
    }

    @media (max-width: 767px) {
        .sb-chat-btn {
            margin-bottom: 3rem;
        }  
    }

    .sb-chat-btn, .sb-chat .sb-scroll-area .sb-header {
        background-color:  #7d9a40 !important;

        
    }
    .sb-editor .sb-submit {
        color: #7d9a40 !important;
    }
    .sb-editor .sb-bar-icons>div:hover:before {
        color: #7d9a40 !important;
    }
    .pagination .page-link,
    .page-item.disabled .page-link {
        min-width: 32px;
        min-height: 32px;
        line-height: 32px;
        text-align: center;
        padding: 0;
        border: 1px solid var(--soft-light);
        font-size: 0.875rem;
        border-radius: 0 !important;
        color: var(--dark);
    }
    .pagination .page-item {
        margin: 0 5px;
    }

    .aiz-carousel.coupon-slider .slick-track{
        margin-left: 0;
    }

    .form-control:focus {
        border-width: 2px !important;
    }
    .iti__flag-container {
        padding: 2px;
    }
    .modal-content {
        border: 0 !important;
        border-radius: 0 !important;
    }

    .tagify.tagify--focus{
        border-width: 2px;
        border-color: var(--primary);
    }

    #map{
        width: 100%;
        height: 250px;
    }
    #edit_map{
        width: 100%;
        height: 250px;
    }

    .pac-container { z-index: 100000; }

    /* .hov-animate-outline-wrapper {
    position: relative;
    z-index: 0;
} */

.sharing-dialog {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 100vh !important; /* Center vertically */
    margin: auto !important; /* Center horizontally */

}

.cart-btn, .add-to-cart { 
    border-radius: 8px !important;
}
.btn-secondary-base {
    background-color:  #7d9a40 !important;
    border-color:  #7d9a40 !important;
    border-radius: 8px !important;
}

.btn-primary {
    border-radius:8px !important;
}
</style>
@yield('style')
<script>



</script>
@if (get_setting('google_analytics') == 1)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('TRACKING_ID') }}');
    </script>
@endif

@if (get_setting('facebook_pixel') == 1)
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
@endif

@php
    echo get_setting('header_script');
@endphp

</head>
<body>


    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column bg-white">
        @php
            $user = auth()->user();
            $user_avatar = null;
            $carts = [];
            if ($user && $user->avatar_original != null) {
                $user_avatar = uploaded_asset($user->avatar_original);
            }

            $system_language = get_system_language();
        @endphp
        <!-- Header -->
        @include('frontend.inc.nav')

        @yield('content')

        <!-- footer -->
        @include('frontend.inc.footer')

    </div>

    <!-- Floating Buttons -->
    {{-- @include('frontend.inc.floating_buttons') --}}

    <div class="aiz-refresh">
        <div class="aiz-refresh-content"><div></div><div></div><div></div></div>
    </div>


    @if (env("DEMO_MODE") == "On")
        <!-- demo nav -->
        @include('frontend.inc.demo_nav')
    @endif

    <!-- cookies agreement -->
    @php
        $alert_location = get_setting('custom_alert_location');
        $order = in_array($alert_location, ['top-left', 'top-right']) ? 'asc' : 'desc';
        $custom_alerts = App\Models\CustomAlert::where('status', 1)->orderBy('id', $order)->get();
    @endphp

    <div class="aiz-custom-alert {{ get_setting('custom_alert_location') }}">
        @foreach ($custom_alerts as $custom_alert)
            @if($custom_alert->id == 1)
                <div class="aiz-cookie-alert mb-3" style="box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.24);">
                    <div class="p-3 px-lg-2rem rounded-0" style="background: {{ $custom_alert->background_color }};">
                        <div class="text-{{ $custom_alert->text_color }} mb-3">
                            {!! $custom_alert->description !!}
                        </div>
                        <button class="btn btn-block btn-primary rounded-0 aiz-cookie-accept">
                            {{ translate('Ok. I Understood') }}
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-3 custom-alert-box removable-session d-none" data-key="custom-alert-box-{{ $custom_alert->id }}" data-value="removed" style="box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.24);">
                    <div class="rounded-0 position-relative" style="background: {{ $custom_alert->background_color }};">
                        <a href="{{ $custom_alert->link }}" class="d-block h-100 w-100">
                            <div class="@if ($custom_alert->type == 'small') d-flex @endif">
                                <img class="@if ($custom_alert->type == 'small') h-140px w-120px img-fit @else w-100 @endif" src="{{ uploaded_asset($custom_alert->banner) }}" alt="custom_alert">
                                <div class="text-{{ $custom_alert->text_color }} p-2rem">
                                    {!! $custom_alert->description !!}
                                </div>
                            </div>
                        </a>
                        <button class="absolute-top-right bg-transparent btn btn-circle btn-icon d-flex align-items-center justify-content-center text-{{ $custom_alert->text_color }} hov-text-primary set-session" data-key="custom-alert-box-{{ $custom_alert->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".custom-alert-box">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- website popup -->
    @php
        $dynamic_popups = App\Models\DynamicPopup::where('status', 1)->orderBy('id', 'asc')->get();
    @endphp
    @foreach ($dynamic_popups as $key => $dynamic_popup)
        @if($dynamic_popup->id == 1)
            <div class="modal website-popup removable-session d-none" data-key="website-popup" data-value="removed">
                <div class="absolute-full bg-black opacity-60"></div>
                <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md mx-4 mx-md-auto">
                    <div class="modal-content position-relative border-0 rounded-0">
                        <div class="aiz-editor-data">
                            <div class="d-block">
                                <img class="w-100" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="dynamic_popup">
                            </div>
                        </div>
                        <div class="pb-5 pt-4 px-3 px-md-2rem">
                            <h1 class="fs-30 fw-700 text-dark">{{ $dynamic_popup->title }}</h1>
                            <p class="fs-14 fw-400 mt-3 mb-4">{{ $dynamic_popup->summary }}</p>
                            @if ($dynamic_popup->show_subscribe_form == 'on')
                                <form class="" method="POST" action="{{ route('subscribers.store') }}">
                                    @csrf
                                    <div class="form-group mb-0">
                                        <input type="email" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" required>
                                    </div>
                                    <button type="submit" class="btn btn-block mt-3 rounded-0 text-{{ $dynamic_popup->btn_text_color }}" style="background: {{ $dynamic_popup->btn_background_color }};">
                                        {{ $dynamic_popup->btn_text }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="modal website-popup removable-session d-none" data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed">
                <div class="absolute-full bg-black opacity-60"></div>
                <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md mx-4 mx-md-auto">
                    <div class="modal-content position-relative border-0 rounded-0">
                        <div class="aiz-editor-data">
                            <div class="d-block">
                                <img class="w-100" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="dynamic_popup">
                            </div>
                        </div>
                        <div class="pb-5 pt-4 px-3 px-md-2rem">
                            <h1 class="fs-30 fw-700 text-dark">{{ $dynamic_popup->title }}</h1>
                            <p class="fs-14 fw-400 mt-3 mb-4">{{ $dynamic_popup->summary }}</p>
                            <a href="{{ $dynamic_popup->btn_link }}" class="btn btn-block mt-3 rounded-0 text-{{ $dynamic_popup->btn_text_color }}" style="background: {{ $dynamic_popup->btn_background_color }};">
                                {{ $dynamic_popup->btn_text }}
                            </a>
                        </div>
                        <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @include('frontend.partials.modal')

    @include('frontend.partials.account_delete_modal')

    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1 btn-circle bg-gray mr-2 mt-2 d-flex justify-content-center align-items-center" data-dismiss="modal" aria-label="Close" style="background: #ededf2; width: calc(2rem + 2px); height: calc(2rem + 2px);">
                    <span aria-hidden="true" class="fs-24 fw-700" >&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

    @yield('modal')

    <!-- SCRIPTS -->
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="{{ static_asset('assets/js/aiz-core.js?v=') }}{{ rand(1000, 9999) }}"></script>




    @if (get_setting('facebook_chat') == 1)
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                  xfbml            : true,
                  version          : 'v3.3'
                });
              };

              (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat"
          attribution=setup_tool
          page_id="{{ env('FACEBOOK_PAGE_ID') }}">
        </div>
    @endif

    <script>
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>

    <script>

        @if (Route::currentRouteName() == 'home' || Route::currentRouteName() == '/')

            $.post('{{ route('home.section.featured') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.todays_deal') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#todays_deal').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.best_selling') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_best_selling').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.free_shipping') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_free_shipping').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.newest_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_newest').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.auction_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#auction_products').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.home_categories') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_home_categories').html(data);
                AIZ.plugins.slickCarousel();
            });

        @endif

        $(document).ready(function() {


            $('.category-nav-element').each(function(i, el) {

                $(el).on('mouseover', function(){
                    if(!$(el).find('.sub-cat-menu').hasClass('loaded')){
                        $.post('{{ route('category.elements') }}', {
                            _token: AIZ.data.csrf,
                            id:$(el).data('id'
                            )}, function(data){
                            $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                        });
                    }
                });
            });

            if ($('#lang-change').length > 0) {
                $('#lang-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var locale = $this.data('flag');
                        $.post('{{ route('language.change') }}',{_token: AIZ.data.csrf, locale:locale}, function(data){
                            location.reload();
                        });

                    });
                });
            }

            if ($('#currency-change').length > 0) {
                $('#currency-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var currency_code = $this.data('currency');
                        $.post('{{ route('currency.change') }}',{_token: AIZ.data.csrf, currency_code:currency_code}, function(data){
                            location.reload();
                        });

                    });
                });
            }
        });

        $('#search').on('keyup', function(){
            search();
        });

        $('#search').on('focus', function(){
            search();
        });

        function search() {
            var searchKey = $('#search').val();
            if(searchKey.length > 0){
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route('search.ajax') }}', { _token: AIZ.data.csrf, search:searchKey}, function(data){
                    if(data == '0'){
                        // $('.typed-search-box').addClass('d-none');
                        $('#search-content').html(null);
                        $('.typed-search-box .search-nothing').removeClass('d-none').html('{{ translate('Sorry, nothing found for') }} <strong>"'+searchKey+'"</strong>');
                        $('.search-preloader').addClass('d-none');

                    }
                    else{
                        $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                        $('#search-content').html(data);
                        $('.search-preloader').addClass('d-none');
                    }
                });
            }
            else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        $(".aiz-user-top-menu").on("mouseover", function (event) {
            $(".hover-user-top-menu").addClass('active');
        })
        .on("mouseout", function (event) {
            $(".hover-user-top-menu").removeClass('active');
        });

        $(document).on("click", function(event){
            var $trigger = $("#category-menu-bar");
            if($trigger !== event.target && !$trigger.has(event.target).length){
                $("#click-category-menu").slideUp("fast");;
                $("#category-menu-bar-icon").removeClass('show');
            }
        });

        function updateNavCart(view,count){
            $('.cart-count').html(count);
            $('#cart_items').html(view);
        }

        function removeFromCart(key){
            $.post('{{ route('cart.removeFromCart') }}', {
                _token  : AIZ.data.csrf,
                id      :  key
            }, function(data){
                updateNavCart(data.nav_cart_view,data.cart_count);
                $('#cart-details').html(data.cart_view);
                AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())-1);
            });
        }

        function showLoginModal() {
            $('#login_modal').modal();
        }

        function addToCompare(id){
            $.post('{{ route('compare.addToCompare') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#compare').html(data);
                AIZ.plugins.notify('success', "{{ translate('Item has been added to compare list') }}");
                $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html())+1);
            });
        }

        function addToWishList(id){
            @if (Auth::check() && Auth::user()->user_type == 'customer')
                $.post('{{ route('wishlists.store') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                    if(data != 0){
                        $('#wishlist').html(data);
                        AIZ.plugins.notify('success', "{{ translate('Item has been added to wishlist') }}");
                    }
                    else{
                        AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
                    }
                });
            @elseif(Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the WishList.') }}");
            @else
                AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
            @endif
        }

//         function shareList(id) {
//             // console.log('hi');
//             // $('#sharing_Modal').show();
//           prompt('enter');
//         //    console.log("dc");

//             // AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the WishList.') }}");
// }


        function shareList(id) {

        // $('#share_btn').text("bye");

        $('#sharing_Modal1').modal('show');

        }



        function showAddToCartModal(id){
            if(!$('#modal-size').hasClass('modal-lg')){
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.post('{{ route('cart.showCartModal') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('.c-preloader').hide();
                $('#addToCart-modal-body').html(data);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();
                getVariantPrice();
            });
        }

        $('#option-choice-form input').on('change', function(){
            getVariantPrice();
        });

        function getVariantPrice(){
            if($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()){
                $.ajax({
                    type:"POST",
                    url: '{{ route('products.variant_price') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data){
                        $('.product-gallery-thumb .carousel-box').each(function (i) {
                            if($(this).data('variation') && data.variation == $(this).data('variation')){
                                $('.product-gallery-thumb').slick('slickGoTo', i);
                            }
                        })

                        $('#option-choice-form #chosen_price_div').removeClass('d-none');
                        $('#option-choice-form #chosen_price_div #chosen_price').html(data.price);
                        $('#available-quantity').html(data.quantity);
                        $('.input-number').prop('max', data.max_limit);
                        if(parseInt(data.in_stock) == 0 && data.digital  == 0){
                           $('.buy-now').addClass('d-none');
                           $('.add-to-cart').addClass('d-none');
                           $('.out-of-stock').removeClass('d-none');
                        }
                        else{
                           $('.buy-now').removeClass('d-none');
                           $('.add-to-cart').removeClass('d-none');
                           $('.out-of-stock').addClass('d-none');
                        }

                        AIZ.extra.plusMinus();
                    }
                });
            }
        }

        function checkAddToCartValidity(){
            var names = {};
            $('#option-choice-form input:radio').each(function() { // find unique names
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() { // then count them
                count++;
            });

            if($('#option-choice-form input:radio:checked').length == count){
                return true;
            }

            return false;
        }

        function addToCart(){
            @if (Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the Cart.') }}");
                return false;
            @endif

            if(checkAddToCartValidity()) {
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type:"POST",
                    url: '{{ route('cart.addToCart') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data){
                       $('#addToCart-modal-body').html(null);
                       $('.c-preloader').hide();
                       $('#modal-size').removeClass('modal-lg');
                       $('#addToCart-modal-body').html(data.modal_view);
                       AIZ.extra.plusMinus();
                       AIZ.plugins.slickCarousel();
                       updateNavCart(data.nav_cart_view,data.cart_count);
                    }
                });

                if ("{{ get_setting('facebook_pixel') }}" == 1){
                    // Facebook Pixel AddToCart Event
                    fbq('track', 'AddToCart', {content_type: 'product'});
                    // Facebook Pixel AddToCart Event
                }
            }
            else{
                AIZ.plugins.notify('warning', "{{ translate('Please choose all the options') }}");
            }
        }

        function buyNow(){
            @if (Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the Cart.') }}");
                return false;
            @endif

            if(checkAddToCartValidity()) {
                $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type:"POST",
                    url: '{{ route('cart.addToCart') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data){
                        if(data.status == 1){
                            $('#addToCart-modal-body').html(data.modal_view);
                            updateNavCart(data.nav_cart_view,data.cart_count);
                            window.location.replace("{{ route('cart') }}");
                        }
                        else{
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data.modal_view);
                        }
                    }
               });
            }
            else{
                AIZ.plugins.notify('warning', "{{ translate('Please choose all the options') }}");
            }
        }

        function bid_single_modal(bid_product_id, min_bid_amount){
            @if (Auth::check() && (isCustomer() || isSeller()))
                var min_bid_amount_text = "({{ translate('Min Bid Amount: ') }}"+min_bid_amount+")";
                $('#min_bid_amount').text(min_bid_amount_text);
                $('#bid_product_id').val(bid_product_id);
                $('#bid_amount').attr('min', min_bid_amount);
                $('#bid_for_product').modal('show');
            @elseif (Auth::check() && isAdmin())
                AIZ.plugins.notify('warning', '{{ translate('Sorry, Only customers & Sellers can Bid.') }}');
            @else
                $('#login_modal').modal('show');
            @endif
        }

        function clickToSlide(btn,id){
            $('#'+id+' .aiz-carousel').find('.'+btn).trigger('click');
            $('#'+id+' .slide-arrow').removeClass('link-disable');
            var arrow = btn=='slick-prev' ? 'arrow-prev' : 'arrow-next';
            if ($('#'+id+' .aiz-carousel').find('.'+btn).hasClass('slick-disabled')) {
                $('#'+id).find('.'+arrow).addClass('link-disable');
            }
        }

        function goToView(params) {
            document.getElementById(params).scrollIntoView({behavior: "smooth", block: "center"});
        }

        function copyCouponCode(code){
            navigator.clipboard.writeText(code);
            AIZ.plugins.notify('success', "{{ translate('Coupon Code Copied') }}");
        }

        $(document).ready(function(){
            $('.cart-animate').animate({margin : 0}, "slow");

            $({deg: 0}).animate({deg: 360}, {
                duration: 2000,
                step: function(now) {
                    $('.cart-rotate').css({
                        transform: 'rotate(' + now + 'deg)'
                    });
                }
            });

            setTimeout(function(){
                $('.cart-ok').css({ fill: '#d43533' });
            }, 2000);

        });

        function nonLinkableNotificationRead(){
            $.get('{{ route('non-linkable-notification-read') }}',function(data){
                $('.unread-notification-count').html(data);
            });
        }
    </script>


    <script type="text/javascript">
        if ($('input[name=country_code]').length > 0){
            // Country Code
            var isPhoneShown = true,
                countryData = window.intlTelInputGlobals.getCountryData(),
                input = document.querySelector("#phone-code");

            for (var i = 0; i < countryData.length; i++) {
                var country = countryData[i];
                if (country.iso2 == 'bd') {
                    country.dialCode = '88';
                }
            }

            var iti = intlTelInput(input, {
                separateDialCode: true,
                utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
                onlyCountries: @php echo get_active_countries()->pluck('code') @endphp,
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    if (selectedCountryData.iso2 == 'bd') {
                        return "01xxxxxxxxx";
                    }
                    return selectedCountryPlaceholder;
                }
            });

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

            input.addEventListener("countrychange", function(e) {
                // var currentMask = e.currentTarget.placeholder;
                var country = iti.getSelectedCountryData();
                $('input[name=country_code]').val(country.dialCode);

            });

            function toggleEmailPhone(el) {
                if (isPhoneShown) {
                    $('.phone-form-group').addClass('d-none');
                    $('.email-form-group').removeClass('d-none');
                    $('input[name=phone]').val(null);
                    $('input[name=phone]').prop('required',false);
                    $('input[name=email]').prop('required', true);
                    isPhoneShown = false;
                    $(el).html('*{{ translate('Use Phone Number Instead') }}');
                } else {
                    $('.phone-form-group').removeClass('d-none');
                    $('.email-form-group').addClass('d-none');
                    $('input[name=email]').val(null);
                    $('input[name=email]').prop('required', false);
                    $('input[name=phone]').prop('required', true)
                    isPhoneShown = true;
                    $(el).html('<i>*{{ translate('Use Email Instead') }}</i>');
                }
            }
        }
    </script>

    <script>
        var acc = document.getElementsByClassName("aiz-accordion-heading");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>

    <script>
        function showFloatingButtons() {
            document.querySelector('.floating-buttons-section').classList.toggle('show');;
        }
    </script>

    @if (env("DEMO_MODE") == "On")
        <script>
            var demoNav = document.querySelector('.aiz-demo-nav');
            var menuBtn = document.querySelector('.aiz-demo-nav-toggler');
            var lineOne = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--1');
            var lineTwo = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--2');
            var lineThree = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--3');
            menuBtn.addEventListener('click', () => {
                toggleDemoNav();
            });

            function toggleDemoNav() {
                // demoNav.classList.toggle('show');
                demoNav.classList.toggle('shadow-none');
                lineOne.classList.toggle('line-cross');
                lineTwo.classList.toggle('line-fade-out');
                lineThree.classList.toggle('line-cross');
                if ($('.aiz-demo-nav-toggler').hasClass('show')) {
                    $('.aiz-demo-nav-toggler').removeClass('show');
                    demoHideOverlay();
                }else{
                    $('.aiz-demo-nav-toggler').addClass('show');
                    demoShowOverlay();
                }
            }

            $('.aiz-demos').click(function(e){
                if (!e.target.closest('.aiz-demos .aiz-demo-content')) {
                    toggleDemoNav();
                }
            });

            function demoShowOverlay(){
                $('.top-banner').removeClass('z-1035').addClass('z-1');
                $('.top-navbar').removeClass('z-1035').addClass('z-1');
                $('header').removeClass('z-1020').addClass('z-1');
                $('.aiz-demos').addClass('show');
            }

            function demoHideOverlay(cls=null){
                if($('.aiz-demos').hasClass('show')){
                    $('.aiz-demos').removeClass('show');
                    $('.top-banner').delay(800).removeClass('z-1').addClass('z-1035');
                    $('.top-navbar').delay(800).removeClass('z-1').addClass('z-1035');
                    $('header').delay(800).removeClass('z-1').addClass('z-1020');
                }
            }


        </script>
    @endif
<script>

    // Popular products
     let page = 1;
     let limit = 5;
 function loadBestSellingProducts() {
    $.ajax({
        url: "{{ route('all_products') }}",
        type: 'GET',
        data: {
            page: page,
            limit: limit,
        },
        success: function(products) {
            products.forEach(function(product) {

                var auctionUrl = `product/${product.slug}`;

                var discountHTML = '';
                var previous_price = '' ;
                if (product.discount_per > 0) {
                    discountHTML = `<span class=" ">${product.discount_per}%</span>`;
                    previous_price = ` <del class="d-block fw-400 text-secondary">${product.pervious_price}</del>`;
                }


                $('#product-list').append(`

                        <div class="  px-3 position-relative has-transition hov-animate-outline border-right border-top border-bottom  border-left" style="flex: 1 1 18%; margin-bottom:10px; z-index:1">
                        <div class="aiz-card-box h-auto bg-white py-3 hov-scale-img m-1" >
                            <div class="position-relative h-140px h-md-200px img-fit overflow-hidden">
                                <a href="${auctionUrl}" class="d-block h-100">
                                    <img class="lazyload mx-auto img-fit has-transition"
                                        src="${product.thumbnail_url}"
                                        alt="${product.name}" title="${product.name}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </a>
                                <div class="absolute-top-right aiz-p-hov-icon">
                                    <a href="javascript:void(0)" class="hov-svg-white" onclick="addToWishList(${product.id})"
                                        data-toggle="tooltip" data-title="{{ translate('Add to wishlist') }}" data-placement="left">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.4" viewBox="0 0 16 14.4">
                                            <g id="_51a3dbe0e593ba390ac13cba118295e4" data-name="51a3dbe0e593ba390ac13cba118295e4"
                                                transform="translate(-3.05 -4.178)">
                                                <path id="Path_32649" data-name="Path 32649"
                                                    d="M11.3,5.507l-.247.246L10.8,5.506A4.538,4.538,0,1,0,4.38,11.919l.247.247,6.422,6.412,6.422-6.412.247-.247A4.538,4.538,0,1,0,11.3,5.507Z"
                                                    transform="translate(0 0)" fill="#919199" />
                                                <path id="Path_32650" data-name="Path 32650"
                                                    d="M11.3,5.507l-.247.246L10.8,5.506A4.538,4.538,0,1,0,4.38,11.919l.247.247,6.422,6.412,6.422-6.412.247-.247A4.538,4.538,0,1,0,11.3,5.507Z"
                                                    transform="translate(0 0)" fill="#919199" />
                                            </g>
                                        </svg>
                                    </a>
                                </div>
                                <a class="cart-btn absolute-bottom-left w-100 h-35px aiz-p-hov-icon text-white fs-13 fw-700 d-flex flex-column justify-content-center align-items-center ${product.cart_added ? 'active' : ''}"
                                    href="javascript:void(0)"
                                    style="background-color:#7D9A40"
                                    onclick="showAddToCartModal(${product.id})">
                                    <span class="cart-btn-text">
                                        {{ translate('Add to Cart') }}
                                    </span>
                                    <span><i class="las la-2x la-shopping-cart"></i></span>
                                </a>
                            </div>
                            <div class="pt-1 pl-1 text-left">
                                <h3 class="fw-400 fs-20 text-truncate-2 lh-1-4 mb-0 h-35px">
                                    <a href="" class="d-block text-reset fw-500 hov-text-primary"
                                        title="${product.name}">${product.name}</a>
                                </h3>
                                <span  class=" text-primary fw-700">${product.discounted_price}</span>
                                ${discountHTML}
                                ${ previous_price}
                                <div class="d-flex justify-content-between mt-1">
                                    <div>
                                    <svg width="92" height="16" viewBox="0 0 92 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.62981 0.3038C7.46952 0.481892 6.89963 1.51483 6.36535 2.60119L5.36804 4.57801L3.08845 4.88077C1.32534 5.1301 0.719829 5.29038 0.417072 5.57533C-0.348724 6.26989 -0.152823 6.71512 1.78838 8.58509L3.58711 10.2948L3.23093 12.3072C2.76789 14.7827 2.76789 15.3526 3.1775 15.7266C3.71178 16.2074 4.22825 16.0828 6.38316 14.943L8.41341 13.8566L10.5149 14.943C12.7411 16.1006 13.3109 16.2252 13.774 15.7088C14.1658 15.2814 14.148 14.943 13.7028 12.3963L13.3288 10.2592L15.1275 8.54947C17.0687 6.6795 17.229 6.34113 16.5166 5.62876C16.196 5.30819 15.7508 5.16572 14.5042 4.98762C11.156 4.52458 11.6369 4.82734 10.408 2.44091C9.82034 1.28331 9.21483 0.250372 9.07235 0.161325C8.66274 -0.0880032 7.91475 -0.0167656 7.62981 0.3038Z" fill="#FDCC0D"/>
                                    <path d="M26.3652 0.161325C26.2227 0.250372 25.635 1.28331 25.0473 2.44091L24.0143 4.5602L21.6813 4.89858C19.7401 5.16572 19.2771 5.29038 18.9387 5.62876C18.2263 6.34113 18.2798 6.46579 20.5059 8.65632L22.1266 10.277L21.7348 12.6456C21.2895 15.2814 21.3608 15.7444 22.2156 15.9581C22.6074 16.065 23.1417 15.8691 24.887 14.9786L27.0597 13.8566L29.09 14.943C31.2449 16.1006 31.8326 16.2252 32.2956 15.7088C32.6874 15.2814 32.6696 14.5868 32.26 12.2894L31.9038 10.4194L33.6847 8.62071C35.0204 7.2494 35.4479 6.69731 35.4479 6.34113C35.4479 5.46847 34.9848 5.25476 32.4381 4.89858L30.0873 4.57801L29.09 2.60119C28.5557 1.51483 27.9858 0.481892 27.8255 0.3038C27.5406 -0.0167656 26.8104 -0.0880032 26.3652 0.161325Z" fill="#FDCC0D"/>
                                    <path d="M44.7799 0.304102C44.6018 0.482195 44.0497 1.51513 43.5332 2.60149L42.5893 4.57831L40.3098 4.88107C37.6918 5.25506 37.2288 5.45097 37.2288 6.288C37.2288 6.76885 37.4959 7.10722 38.9563 8.58539L40.6837 10.3129L40.3454 12.4144C39.9358 14.9433 39.9358 15.4776 40.3632 15.7981C40.9153 16.1899 41.3961 16.0653 43.5332 14.9433L45.5991 13.8569L47.665 14.9433C49.8021 16.0653 50.2829 16.1899 50.835 15.7981C51.2624 15.4776 51.2624 14.9433 50.8528 12.4144L50.5144 10.3129L52.2419 8.58539C53.7023 7.10722 53.9694 6.76885 53.9694 6.288C53.9694 5.45097 53.5064 5.25506 50.8884 4.88107L48.591 4.57831L47.5225 2.4234C46.9526 1.24799 46.3827 0.215056 46.258 0.143819C45.884 -0.105509 45.1004 -0.0164642 44.7799 0.304102Z" fill="#FDCC0D"/>
                                    <path d="M63.3726 0.304554C63.2123 0.482646 62.6424 1.51558 62.1081 2.60194L61.1108 4.57877L58.76 4.89933C56.2133 5.25552 55.7502 5.46923 55.7502 6.34188C55.7502 6.69806 56.1777 7.25015 57.5134 8.62146L59.2943 10.4202L58.9381 12.2902C58.5107 14.6588 58.5107 15.3533 58.9203 15.7273C59.4546 16.2082 59.9532 16.0835 62.0903 14.9437L64.1206 13.8752L66.3111 14.9794C68.0564 15.852 68.6085 16.0657 68.9825 15.9589C69.8373 15.7451 69.9086 15.2643 69.4633 12.6463L69.0715 10.2777L70.6922 8.65708C72.9183 6.46654 72.9718 6.34188 72.2594 5.62951C71.921 5.29114 71.458 5.16647 69.5168 4.89933L67.1838 4.56096L66.1508 2.44166C65.5631 1.28406 64.9576 0.251126 64.7973 0.162081C64.3165 -0.0872478 63.6575 -0.0160122 63.3726 0.304554Z" fill="#FDCC0D"/>
                                    <path d="M82.1081 0.161325C81.9656 0.250372 81.3601 1.28331 80.7724 2.44091C79.5613 4.82734 80.0422 4.52458 76.6941 4.98762C75.4474 5.16572 75.0022 5.30819 74.6816 5.62876C73.9692 6.34113 74.1295 6.6795 76.0707 8.53166L77.8873 10.277L77.4955 12.3963C77.0324 14.9964 77.0324 15.3526 77.442 15.7266C77.9763 16.2074 78.475 16.0828 80.6833 14.943L82.767 13.8566L84.8151 14.943C86.97 16.1006 87.5755 16.2252 88.0385 15.7088C88.4303 15.2814 88.4125 14.7293 87.9851 12.3072L87.6111 10.3126L89.4098 8.58509C91.351 6.71512 91.5469 6.26989 90.799 5.57533C90.4784 5.29038 89.8729 5.1301 88.1098 4.88077L85.8302 4.57801L84.8329 2.60119C84.2986 1.51483 83.7287 0.481892 83.5684 0.3038C83.2835 -0.0167656 82.5355 -0.0880032 82.1081 0.161325Z" fill="#FDCC0D"/>
                                    </svg>
                                    <span>(2)</span>
                                    </div>




                                </div>

                            </div>
                        </div>
                    </div>


                `);
            });


        }
    });
}
    $('#load-more').click(function() {
            page++;
            loadBestSellingProducts();
        });

        $(document).ready(function() {
            loadBestSellingProducts(); // Load initial set of products
        });

</script>

<script>
    function show_order_details(order_id) {
    $('#order-details-modal-body').html(null);

    if (!$('#modal-size').hasClass('modal-lg')) {
        $('#modal-size').addClass('modal-lg');
    }

    $.post('{{ route('orders.details') }}', {
        _token: AIZ.data.csrf,
        order_id: order_id
    }, function(data) {
        $('#order-details-modal-body').html(data);
        $('#order_details').modal();
        $('.c-preloader').hide();
        AIZ.plugins.bootstrapSelect('refresh');
    });
    }
        const loginIcon = document.getElementById('login-icon');
        const authBtn = document.getElementById('auth-btn');
        // Show authBtn when hovering over loginIcon
        loginIcon.addEventListener('mouseover', function() {

        authBtn.classList.remove('d-none');
        });

        // Keep authBtn visible when hovering over it
        authBtn.addEventListener('mouseover', function() {
        authBtn.classList.remove('d-none');
        });

        // Hide authBtn when mouse leaves both loginIcon and authBtn
        loginIcon.addEventListener('mouseout', function() {
        setTimeout(function() {
        if (!authBtn.matches(':hover') && !loginIcon.matches(':hover')) {
        authBtn.classList.add('d-none');
        }
        }, 100); // Slight delay to check hover
        });

        authBtn.addEventListener('mouseout', function() {
        setTimeout(function() {
        if (!authBtn.matches(':hover') && !loginIcon.matches(':hover')) {
        authBtn.classList.add('d-none');
        }
        }, 100); // Slight delay to check hover
        });

</script>


<script>

    function getCurrentLocation() {
    // First, check if geolocation is supported
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        return;
    }

    // Request location permission from user
    navigator.geolocation.getCurrentPosition(
        (position) => {
            // Success! We got the position
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Store these coordinates
            console.log(`Location: ${latitude}, ${longitude}`);
            
            // You can save this to your application
            saveLocation(latitude, longitude);
        },
        (error) => {
            // Handle any errors
            console.error('Error getting location:', error);
        }
    );
}

function saveLocation(latitude, longitude) {
    // Save to localStorage for later use

    alert(latitude,longitude);
    const locationData = {
        latitude,
        longitude,
        timestamp: new Date().toISOString()
    };
    localStorage.setItem('customerLocation', JSON.stringify(locationData));
    
}

</script>


    @yield('script')

    @php
        echo get_setting('footer_script');
    @endphp

</body>
</html>
