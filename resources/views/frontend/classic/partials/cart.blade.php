@php
    $total = 0;
    $carts = get_user_cart();
    if (count($carts) > 0) {
        foreach ($carts as $key => $cartItem) {
            $product = get_single_product($cartItem['product_id']);
            $total = $total + cart_product_price($cartItem, $product, false) * $cartItem['quantity'];
        }
    }
@endphp
<!-- Cart button with cart count -->
<a href="javascript:void(0)" class="d-flex align-items-center text-dark  h-100" data-toggle="dropdown"
    data-display="static" title="{{ translate('Cart') }}" >
    <span class="position-relative d-inline-block" style="">
        {{-- <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.75742 15.1218C7.10535 15.1218 7.3874 14.8397 7.3874 14.4918C7.3874 14.1439 7.10535 13.8618 6.75742 13.8618C6.40949 13.8618 6.12744 14.1439 6.12744 14.4918C6.12744 14.8397 6.40949 15.1218 6.75742 15.1218Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.5772 15.1218C15.9251 15.1218 16.2072 14.8397 16.2072 14.4918C16.2072 14.1439 15.9251 13.8618 15.5772 13.8618C15.2293 13.8618 14.9472 14.1439 14.9472 14.4918C14.9472 14.8397 15.2293 15.1218 15.5772 15.1218Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1.71753 1.26221H4.23745L6.1274 11.9719H16.2071" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.12739 8.61231H15.9488C16.0216 8.61236 16.0923 8.58716 16.1486 8.541C16.205 8.49484 16.2436 8.43057 16.2579 8.35914L17.3919 2.68931C17.401 2.64359 17.3999 2.59641 17.3886 2.55117C17.3773 2.50594 17.3561 2.46377 17.3265 2.42772C17.297 2.39167 17.2598 2.36264 17.2176 2.34271C17.1754 2.32279 17.1294 2.31247 17.0828 2.3125H4.86743" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg> --}}
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.42853 20.9998C7.8723 20.9998 8.23205 20.5928 8.23205 20.0907C8.23205 19.5887 7.8723 19.1816 7.42853 19.1816C6.98475 19.1816 6.625 19.5887 6.625 20.0907C6.625 20.5928 6.98475 20.9998 7.42853 20.9998Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.6766 20.9998C19.1203 20.9998 19.4801 20.5928 19.4801 20.0907C19.4801 19.5887 19.1203 19.1816 18.6766 19.1816C18.2328 19.1816 17.873 19.5887 17.873 20.0907C17.873 20.5928 18.2328 20.9998 18.6766 20.9998Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 1H4.2141L6.62468 16.4547H19.4811" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6.62463 11.6056H19.1516C19.2445 11.6057 19.3346 11.5694 19.4065 11.5027C19.4783 11.4361 19.5276 11.3434 19.5458 11.2403L20.9922 3.0584C21.0038 2.99243 21.0024 2.92435 20.988 2.85907C20.9736 2.79379 20.9466 2.73294 20.9089 2.68092C20.8711 2.6289 20.8237 2.587 20.7699 2.55825C20.7162 2.52949 20.6574 2.5146 20.5979 2.51465H5.01758" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <span class="badge badge-primary badge-inline badge-pill absolute-top-right--10px">{{ count($carts) > 0 ? count($carts) : 0 }}</span>
    </span>
    {{-- <span class="d-none d-xl-block ml-2 fs-14 fw-700 text-black">{{ single_price($total) }}</span> --}}


    {{-- <span class="d-none d-xl-block ml-2 fs-14 fw-700 text-danger">{{ single_price($total) }}</span>
    <span class="nav-box-text d-none d-xl-block ml-2 text-dark fs-12">
        

        (<span class="cart-count">{{ count($carts) > 0 ? count($carts) : 0 }}</span> {{ translate('Items') }})

    </span> --}}
</a>

<!-- Cart Items -->
<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg p-0 stop-propagation rounded-0">
    @if (isset($carts) && count($carts) > 0)
        <div class="fs-16 fw-700 text-soft-dark pt-4 pb-2 mx-4 border-bottom" style="border-color: black !important;">
            {{ translate('Cart Items') }}
        </div>
        <!-- Cart Products -->
        <ul class="h-360px overflow-auto c-scrollbar-light list-group list-group-flush mx-1">
            @foreach ($carts as $key => $cartItem)
                @php
                    $product = get_single_product($cartItem['product_id']);
                @endphp
                @if ($product != null)
                    <li class="list-group-item border-0 hov-scale-img">
                        <span class="d-flex align-items-center">
                            <a href="{{ route('product', $product->slug) }}"
                                class="text-reset d-flex align-items-center flex-grow-1">
                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                    class="img-fit lazyload size-60px has-transition"
                                    alt="{{ $product->getTranslation('name') }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                <span class="minw-0 pl-2 flex-grow-1">
                                    <span class="fw-700 fs-13 text-dark mb-2 text-truncate-2"
                                        title="{{ $product->getTranslation('name') }}">
                                        {{ $product->getTranslation('name') }}
                                    </span>
                                    <span class="fs-14 fw-400 text-secondary">{{ $cartItem['quantity'] }}x</span>
                                    <span
                                        class="fs-14 fw-400 text-secondary">{{ cart_product_price($cartItem, $product) }}</span>
                                </span>
                            </a>
                            <span class="">
                                <button onclick="removeFromCart({{ $cartItem['id'] }})"
                                    class="btn btn-sm btn-icon stop-propagation">
                                    <i class="la la-close fs-18 fw-600 text-secondary"></i>
                                </button>
                            </span>
                        </span>
                    </li>
                @endif
            @endforeach
        </ul>
        <!-- Subtotal -->
        {{-- "border-color: #e5e5e5 !important;" --}}
        <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between mx-4"
            style=>
            <span class="fs-14 fw-400 text-secondary">{{ translate('Subtotal') }}</span>
            <span class="fs-16 fw-700 text-dark">{{ single_price($total) }}</span>
        </div>
        <!-- View cart & Checkout Buttons -->
        <div class="py-3 text-center border-top mx-4" style="border-color: #e5e5e5 !important;">
            <div class="row gutters-10 justify-content-center">
                <div class="col-sm-6 mb-2">
                    <a href="{{ route('cart') }}" class="btn btn-secondary-base btn-sm btn-block rounded-4 text-white">
                        {{ translate('View cart') }}
                    </a>
                </div>
                {{-- @if (Auth::check())
                    <div class="col-sm-6">
                        <a href="{{ route('checkout.shipping_info') }}"
                            class="btn btn-primary btn-sm btn-block rounded-4">
                            {{ translate('Checkout') }}
                        </a>
                    </div>
                @endif --}}
            </div>
        </div>
    @else
        <div class="text-center p-3">
            <i class="las la-frown la-3x opacity-60 mb-3"></i>
            <h3 class="h6 fw-700">{{ translate('Your Cart is empty') }}</h3>
        </div>
    @endif
</div>
