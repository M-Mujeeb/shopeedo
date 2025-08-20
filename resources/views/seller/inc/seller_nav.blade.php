<div class="aiz-topbar px-15px px-lg-25px d-flex align-items-stretch justify-content-between">
    <div class="d-flex">
        <div class="aiz-topbar-nav-toggler d-flex align-items-center justify-content-start mr-2 mr-md-3 ml-0" data-toggle="aiz-mobile-nav">
            <button class="aiz-mobile-toggler">
                <span></span>
            </button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-stretch flex-grow-xl-1">
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            <div class="d-flex justify-content-around align-items-center align-items-stretch">
                <div class="aiz-topbar-item">
                    <div class="d-flex align-items-center">
                        {{-- <a class="btn btn-icon btn-circle btn-light" href="{{ route('home')}}" target="_blank" title="{{ translate('Browse Website') }}">
                            <i class="las la-globe"></i>
                        </a> --}}
                        <nav aria-label="breadcrumb" >
                            @if(Request::is('seller/dashboard'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                            </ol>
                            @endif
                            @if(Request::is('seller/products'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.products') }}">Product Listing</a></li>

                            </ol>
                            @endif
                            @if(Request::is('seller/product/create'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.products.create') }}">Add Products</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/product/*/edit'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.products.create') }}">Add Products</a></li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.products.edit', $product->id) }}">Edit  {{ $product->name }}</a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/product-bulk-upload/index'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.product_bulk_upload.index') }}">Product Bulk Upload</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/categories-wise-product-discount'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.categories_wise_product_discount') }}">Category-Wise Product Discount</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/reviews'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.reviews') }}">Product Reviews</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/uploads'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.uploaded-files.index') }}">Uploaded Files</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/digitalproducts'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.digitalproducts') }}">Add Digital Products</a></li>

                            </ol>
                            @endif
                            @if(Request::is('seller/digitalproducts/create'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Product</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.digitalproducts') }}">Add Digital Products</a></li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.uploaded-files.index') }}">Create Digital Products</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/coupon'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage Product</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.coupon.index') }}">Coupon</a></li>

                            </ol>
                            @endif

                            @if(Request::is('seller/coupon/create'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage Product</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.coupon.index') }}">Coupon</a></li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.coupon.create') }}">Create Coupon</a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/orders'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage orders</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.orders.index') }}">Manage orders</a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/refund-request'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage orders</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.vendor_refund_request') }}">Manage Refund</a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/shop'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage orders</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.shop.index') }}">Shop Settings</a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/money-withdraw-requests'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Payments</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.money_withdraw_requests.index') }}">Money Withdrawal</a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/commission-history'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Payments</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.commission-history.index') }}">Commission History </a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/payments'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                <li class="breadcrumb-item ">Manage Payments</li>
                                <li class="breadcrumb-item active"><a href="{{ route('seller.payments.index') }}">Payment History </a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/conversations'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage Payments</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.conversations.index') }}">Messages </a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/conversations/show/*'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage Payments</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.conversations.index') }}">Messages </a></li>
                                <li class="breadcrumb-item ">Messages</li>


                            </ol>
                            @endif

                            @if(Request::is('seller/support_ticket'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage Payments</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.support_ticket.index') }}">Support Ticket </a></li>


                            </ol>
                            @endif

                            @if(Request::is('seller/support_ticket/show/*'))
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item "><a href="{{ route('seller.dashboard') }}">Seller Dashboard</a></li>
                                {{-- <li class="breadcrumb-item ">Manage Payments</li> --}}
                                <li class="breadcrumb-item active"><a href="{{ route('seller.support_ticket.index') }}">Support Ticket </a></li>
                                <li class="breadcrumb-item ">Ticket Conversation </li>



                            </ol>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
            @if (addon_is_activated('pos_system'))
                <div class="d-flex justify-content-around align-items-center align-items-stretch ml-3">
                    <div class="aiz-topbar-item">
                        <div class="d-flex align-items-center">
                            <a class="btn btn-icon btn-circle btn-light" href="{{ route('poin-of-sales.seller_index') }}" target="_blank" title="{{ translate('POS') }}">
                                <i class="las la-print"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="d-flex justify-content-around align-items-center align-items-stretch">

             <!-- Notifications -->
             <div class="aiz-topbar-item mr-3">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon p-0 d-flex justify-content-center align-items-center">
                            <span class="d-flex align-items-center position-relative">
                                <i class="las la-bell fs-24"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                @endif
                            </span>
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xl py-0">
                        <div class="notifications">
                            <ul class="nav nav-tabs nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link text-dark active" data-toggle="tab" data-type="order" href="javascript:void(0);"
                                        data-target="#orders-notifications" role="tab" id="orders-tab">{{ translate('Orders') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark" data-toggle="tab" data-type="seller" href="javascript:void(0);"
                                        data-target="#sellers-notifications" role="tab" id="sellers-tab">{{ translate('Products') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark" data-toggle="tab" data-type="seller" href="javascript:void(0);"
                                        data-target="#payouts-notifications" role="tab" id="sellers-tab">{{ translate('Payouts') }}</a>
                                </li>
                            </ul>
                            <div class="tab-content c-scrollbar-light overflow-auto" style="height: 75vh; max-height: 400px; overflow-y: auto;">
                                <div class="tab-pane active" id="orders-notifications" role="tabpanel">
                                    <x-unread_notification :notifications="auth()->user()->unreadNotifications()->where('type', 'App\Notifications\OrderNotification')->take(20)->get()" />
                                </div>
                                <div class="tab-pane" id="sellers-notifications" role="tabpanel">
                                    <x-unread_notification :notifications="auth()->user()->unreadNotifications()->where('type', 'like', '%shop%')->take(20)->get()" />
                                </div>
                                <div class="tab-pane" id="payouts-notifications" role="tabpanel">
                                    <x-unread_notification :notifications="auth()->user()->unreadNotifications()->where('type', 'App\Notifications\PayoutNotification')->take(20)->get()" />
                                </div>
                            </div>
                        </div>

                        <div class="text-center border-top">
                            <a href="{{ route('seller.all-notification') }}" class="text-reset d-block py-2">
                                {{ translate('View All Notifications') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- language --}}
            {{-- @php
                if(Session::has('locale')){
                    $locale = Session::get('locale', Config::get('app.locale'));
                }
                else{
                    $locale = env('DEFAULT_LANGUAGE');
                }
            @endphp
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown " id="lang-change">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon">
                            <img src="{{ static_asset('assets/img/flags/'.$locale.'.png') }}" height="11">
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xs">

                        @foreach (\App\Models\Language::where('status', 1)->get() as $key => $language)
                            <li>
                                <a href="javascript:void(0)" data-flag="{{ $language->code }}" class="dropdown-item @if($locale == $language->code) active @endif">
                                    <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" class="mr-2">
                                    <span class="language">{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div> --}}

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow text-dark" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="avatar avatar-sm mr-md-2">
                                <img
                                    src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                >
                            </span>
                            <span class="d-none d-md-block">
                                <span class="d-block fw-500">{{Auth::user()->name}}</span>
                                <span class="d-block small opacity-60">{{Auth::user()->user_type}}</span>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-md">
                        <a href="{{ route('seller.profile.index') }}" class="dropdown-item">
                            <i class="las la-user-circle"></i>
                            <span>{{translate('Profile')}}</span>
                        </a>

                        <a href="{{ route('logout')}}" class="dropdown-item">
                            <i class="las la-sign-out-alt"></i>
                            <span>{{translate('Logout')}}</span>
                        </a>
                    </div>
                </div>
            </div><!-- .aiz-topbar-item -->
        </div>
    </div>
</div><!-- .aiz-topbar -->
