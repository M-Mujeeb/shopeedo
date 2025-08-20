<div class="z-3 sticky-top-lg">
    <div class="card rounded-0 border">

        @php

            $subtotal_for_min_order_amount = 0;
            $subtotal = 0;
            $tax = 0;
            $product_shipping_cost = 0;
            $shipping = 0;
            $coupon_code = null;
            $coupon_discount = 0;
            $total_point = 0;
            $products_categories = array();
            $seller_products = array();


        @endphp
        @foreach ($carts as $key => $cartItem)
            @php
                // dd($cartItem->address->lat);
                $product = get_single_product($cartItem['product_id']);
                // echo $product;
                array_push($products_categories, $product->main_category->is_quick);
                $product_ids = array();
                if(isset($seller_products[$product->user_id])){
                    $product_ids = $seller_products[$product->user_id];
                }
                array_push($product_ids, $cartItem['product_id']);
                $seller_products[$product->user_id] = $product_ids;

                // print_r($products_categories);

                $subtotal_for_min_order_amount += cart_product_price($cartItem, $cartItem->product, false, false) * $cartItem['quantity'];
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $product_shipping_cost = $cartItem['shipping_cost'];
                $shipping += $product_shipping_cost;
                if ((get_setting('coupon_system') == 1) && ($cartItem->coupon_applied == 1)) {
                    $coupon_code = $cartItem->coupon_code;
                    $coupon_discount = $carts->sum('discount');
                }
                if (addon_is_activated('club_point')) {
                    $total_point += $product->earn_point * $cartItem['quantity'];
                }

            @endphp
        @endforeach

    @php

        foreach($seller_products as $key => $seller_product) {
            $sellerId = $key;
            break;
        }



        $shop = App\Models\Shop::where('user_id', $sellerId)->first();



        $user_address = App\Models\Address::where('user_id', auth()->user()->id)
            ->where('set_default', 1)
            ->first();

        $newCost = false;

        if($user_address !=null && $shop->delivery_pickup_latitude != null && $shop->delivery_pickup_longitude != null && $user_address->latitude != null && $user_address->longitude != null){

            $distance = getMultipleRoutes($shop->delivery_pickup_latitude,$shop->delivery_pickup_longitude,$user_address->latitude, $user_address->longitude);

                if (!empty($distance)) {
                    $bestRoute = collect($distance)->sortBy('distance_km')->first();

                    $bestDistance = (float) $bestRoute['distance_km'];
                } else {
                    $bestDistance = 0;
                }

                $per_km = get_setting('per_km');

                $shipping_cost = get_setting('flat_rate_shipping_cost');


                $per_km_cost = $shipping_cost/(float) $per_km;

                if ($bestDistance > (float) $per_km) {
                    $extra_km = $bestDistance - (float) $per_km;
                    $shipping_cost += (float) ($extra_km * (float) $per_km_cost);
                    $newCost = true;
                }


        }


        $one_seller = count($seller_products) == 1 ? true : false;


     @endphp


        @php
        function areAllItemsSame($arr) {
            if (empty($arr)) return false;
            $firstElement = $arr[0];
            return count(array_filter($arr, function($item) use ($firstElement) {
                return $item === $firstElement;
            })) === count($arr);
        }
        if(isset($products_categories)){
            $quick_cart = areAllItemsSame($products_categories);
        }

        @endphp

        <div class="card-header pt-4 pb-1 border-bottom-0">
            <h3 class="fs-16 fw-700 mb-0 p-3">{{ translate('Order Summary') }}</h3>
            <div class="text-right mr-2">
                <!-- Minimum Order Amount -->
                @if (get_setting('minimum_order_amount_check') == 1 && $subtotal_for_min_order_amount < get_setting('minimum_order_amount'))
                    <span class="badge badge-inline badge-warning fs-12 rounded-0 px-2">
                        {{ translate('Minimum Order Amount') . ' ' . single_price(get_setting('minimum_order_amount')) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body pt-2">

            <div class="row gutters-5">
                <!-- Total Products -->
                {{-- <div class="@if (addon_is_activated('club_point')) col-6 @else col-12 @endif">
                    <div class="d-flex align-items-center justify-content-between bg-primary p-2">
                        <span class="fs-13 text-white">{{ translate('Total Products') }}</span>
                        <span class="fs-13 fw-700 text-white">{{ sprintf("%02d", count($carts)) }}</span>
                    </div>
                </div> --}}
                @if (addon_is_activated('club_point'))
                    <!-- Total Clubpoint -->
                    <div class="col-6">
                        <div class="d-flex align-items-center justify-content-between bg-secondary-base p-2">
                            <span class="fs-13 text-white">{{ translate('Total Clubpoint') }}</span>
                            <span class="fs-13 fw-700 text-white">{{ sprintf("%02d", $total_point) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <input type="hidden" id="sub_total" value="{{ $subtotal }}">

            <table class="table my-3">
                <tfoot>
                    <!-- Subtotal -->
                    <tr class="cart-subtotal">
                        <th class="pl-0 fs-14 fw-400 pt-0 pb-2 text-dark border-top-0">{{ translate('Subtotal') }} ({{ sprintf("%02d", count($carts)) }} {{ translate('Products') }})</th>
                        <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0">{{ single_price($subtotal) }}</td>
                    </tr>

                    @if ($proceed != 1)
                     <!-- Tax -->
                     <tr class="cart-tax">
                        <th class="pl-0 fs-14 fw-400 pt-0 pb-2 text-dark border-top-0">{{ translate('Tax') }}</th>
                        <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0">{{ single_price($tax) }}</td>
                    </tr>

                    <!-- Tax -->
                    <tr class="cart-tax">
                        <th class="pl-0 fs-14 fw-400 pt-0 pb-2 text-dark border-top-0">{{ translate('Platform Fees') }}</th>
                        <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0">{{ single_price($platform_fees) }}</td>
                    </tr>
                    <!-- Total Shipping -->
                    <tr class="cart-shipping">
                        <th class="pl-0 fs-14 fw-400 pt-0 pb-2 text-dark border-top-0">{{ translate('Total Shipping') }}</th>
                        {{-- <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0"><span style="{{ $newCost ? 'text-decoration: line-through' : '' }}; margin-right: 5px" >{{ single_price($shipping)}} </span>{{ $newCost ? single_price($shipping_cost) : '' }}</td> --}}
                        <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0"><span style="" >{{$newCost ? single_price($shipping_cost) : single_price($shipping)}} </span></td>

                    </tr>
                    @endif
                    <!-- Redeem point -->
                    @if (Session::has('club_point'))
                        <tr class="cart-club-point">
                            <th class="pl-0 fs-14 fw-400 pt-0 pb-2 text-dark border-top-0">{{ translate('Redeem point') }}</th>
                            <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0">{{ single_price(Session::get('club_point')) }}</td>
                        </tr>
                    @endif
                    <!-- Coupon Discount -->
                    @if ($coupon_discount > 0)
                        <tr class="cart-coupon-discount">
                            <th class="pl-0 fs-14 fw-400 pt-0 pb-2 text-dark border-top-0">{{ translate('Coupon Discount') }}</th>
                            <td class="text-right pr-0 fs-14 pt-0 pb-2 text-dark border-top-0">{{ single_price($coupon_discount) }}</td>
                        </tr>
                    @endif

                    @php
                        $total = $subtotal + $tax + ($newCost ? $shipping_cost : $shipping) + $platform_fees;
                        if (Session::has('club_point')) {
                            $total -= Session::get('club_point');
                        }
                        if ($coupon_discount > 0) {
                            $total -= $coupon_discount;
                            $subtotal -= $coupon_discount;
                        }
                      
                       
                    @endphp
                    <!-- Total -->
                    <tr class="cart-total">
                        <th class="pl-0 fs-14 text-dark fw-700 border-top-0 pt-3 text-uppercase">{{ translate('Total') }}</th>
                        @if($proceed != 1)
                        <td class="text-right pr-0 fs-16 fw-700 text-primary border-top-0 pt-3">{{ single_price($total) }}</td>
                        @else
                        <td class="text-right pr-0 fs-16 fw-700 text-primary border-top-0 pt-3">{{ single_price($subtotal) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <!-- Coupon System -->
            @if (get_setting('coupon_system') == 1 )
                @if ($coupon_discount > 0 && $coupon_code)
                    <div class="mt-3">
                        <form class="" id="remove-coupon-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="proceed" value="{{ $proceed }}">
                            <div class="input-group">
                                <div class="form-control">{{ $coupon_code }}</div>
                                <div class="input-group-append">
                                    <button type="button" id="coupon-remove"
                                        class="btn btn-primary">{{ translate('Change Coupon') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- <div class="mt-3">
                        <div class="coupon-container">
                            <div class="coupon-list">

                                @foreach($coupons as $coupon)
                                    <div class="coupon-card">
                                        <div class="coupon-icon" style="width:100px">
                                            <img src="{{ static_asset('logs/coupons.jpeg') }}" width="80px" alt="Coupon">
                                        </div>
                                        <div class="coupon-details">
                                            <h3>{{ $coupon['name'] }}</h3>
                                            <p>PKR {{ number_format($coupon['amount'], 2) }}</p>
                                            <button class="apply-btn">Apply</button>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div> --}}



                        <form class="" id="apply-coupon-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="proceed" value="{{ $proceed }}">
                            <div class="input-group">
                                <input type="text" class="form-control rounded-0" name="code"
                                    onkeydown="return event.key != 'Enter';"
                                    placeholder="{{ translate('Have coupon code? Apply here') }}" required>
                                <div class="input-group-append">
                                    <button type="button" id="coupon-apply"
                                        class="btn btn-primary rounded-0 ml-1">{{ translate('Apply') }}</button>
                                </div>
                            </div>
                            @if (!auth()->check())
                                <small>{{ translate('You must Login as customer to apply coupon') }}</small>
                            @endif

                        </form>
                    </div>
                @endif
            @endif

            @php
                date_default_timezone_set('Asia/Karachi');

                $current_time = date('H:i'); // Format: 24-hour format like 09:00, 18:00

                $start_time = '09:00';
                $end_time = '18:00';

                if ($current_time >= $start_time && $current_time < $end_time) {
                $quick_delivery_on = true;
                } else {
                $quick_delivery_on = false;
                }
            @endphp
            @if ($proceed == 1)
            <!-- Continue to Shipping -->
            @if(isset($quick_cart) && $quick_cart == true  && $one_seller == true && $quick_delivery_on == true)
            <div class="mt-4">
                <a href="{{ route('checkout') }}" class="btn btn-primary btn-block fs-14 fw-700 rounded-0 px-4">
                    {{ translate('Proceed to Checkout')}} ({{ sprintf("%02d", count($carts)) }})
                </a>
            </div>
            @else
            {{-- btn btn-primary btn-block --}}
            @if(!$one_seller)
            <h5 class=" fs-14 fw-600 rounded-0 text-center mt-2" style="width: 80%; margin: auto;">
                {{ translate('select same commerce item to proceed or select one seller products')}} <br>  {{ translate('Barahe karam kisi aik dookan say apni cheez kharadyn. Abhi Aik waqt main aik hi dookan say khreedari kar saktay hayn takah app ko baa asani waqt per deliver ki jaiay.')}}  ({{ sprintf("%02d", count($carts)) }})
            </h5>
            @elseif (!$quick_delivery_on)
            <h5 class=" fs-14 fw-600 rounded-0 text-center mt-2 mb-2" style="width: 80%; margin: auto;">
                Delivery is only available from 9 AM to 6 PM ({{ sprintf("%02d", count($carts)) }})
            </h5>
            @endif

            @endif
            @endif

        </div>
    </div>
</div>
