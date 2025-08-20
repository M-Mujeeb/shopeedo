@extends('frontend.layouts.user_panel')
@section('panel-style')
<style>
    .card .card-body {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #map {
        width: 100%;
        height: 250px;
    }

    .aiz-table thead tr:first-child th:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.aiz-table thead tr:first-child th:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;

}

    /* tbody {
    padding-left:19px !important;
    padding-right:19px !important;} */
</style>
@endsection
@section('panel_content')

@php


$products_categories = array();
foreach($order->orderDetails as $key => $orderDetail){
if($orderDetail->product != null){
array_push($products_categories,$orderDetail->product->main_category->is_quick);
}
}
$quick = in_array(1, $products_categories) ? 1 : 0;

@endphp
<!-- Order id -->
<div class="aiz-titlebar mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="fs-20 fw-700 text-dark">{{ translate('Order id') }}: {{ $order->code }}</h1>
        </div>
    </div>
</div>
@php
$startTimeInMinutes = 60;
$startTimeInSeconds = $startTimeInMinutes * 60;
@endphp
<!-- Order Confirmation Text-->
@if($thankYou)
<div class="text-center py-4 mb-0">
    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" class=" mb-3">
        <g id="Group_23983" data-name="Group 23983" transform="translate(-978 -481)">
            <circle id="Ellipse_44" data-name="Ellipse 44" cx="18" cy="18" r="18" transform="translate(978 481)"
                fill="#85b567" />
            <g id="Group_23982" data-name="Group 23982" transform="translate(32.439 8.975)">
                <rect id="Rectangle_18135" data-name="Rectangle 18135" width="11" height="3" rx="1.5"
                    transform="translate(955.43 487.707) rotate(45)" fill="#fff" />
                <rect id="Rectangle_18136" data-name="Rectangle 18136" width="3" height="18" rx="1.5"
                    transform="translate(971.692 482.757) rotate(45)" fill="#fff" />
            </g>
        </g>
    </svg>
    <h1 class="mb-2 fs-28 fw-500 text-success">{{ translate('Thank You for Your Order!')}}</h1>
    <p class="fs-13 text-soft-dark">{{ translate('A copy of your order summary has been sent to') }} <strong>{{
            json_decode($order->shipping_address)->email }}</strong></p>
</div>
@endif

{{-- delivery Time status --}}
@if($quick == 1)
@if( $order->delivery_status != "cancelled" && $order->delivery_status != 'delivered' )
<div id="map"></div>
@endif
<div class="card shadow-none border rounded-3 p-4 " style="display: none" id="card-quick">
    <div class="d-flex justify-content-between  ">
        <h3 class="fw-400" id="heading-time">Delivery Time</h3>
        <p class="fs-24" style="" id="status">Preparing your order</p>
        {{-- <h3 class="fw-400" id="heading-time"></h3>
        <p class="fs-24" style="" id="status"></p> --}}

    </div>

    @if($order->delivery_status != "pending" )
    <div>
        <h2 class="mb-3 fs-26" id="timer"><span id="start-range" class="fs-26">45</span>-<span id="end-range"
                class="fs-26">60</span> mins</h2>
        <div class="d-flex justify-content-between" style="width: 60%; gap:10px;">
            <div class="rounded-4" id="confirmed" style="background-color:#7D9A40;  width:152px; height:12px "></div>
            <div class="rounded-4" id="picked" style="background-color:#D9D9D9;  width:152px; height:12px "></div>
            <div class="rounded-4" id="on_the_way" style="background-color:#D9D9D9;  width:152px; height:12px "></div>
            <div class="rounded-4" id="delivered" style="background-color:#D9D9D9;  width:152px; height:12px "></div>
        </div>
    </div>
    @endif
    <div class="mt-3" style="color:#000000B0" id="tagline">
        When it’s ready, the delivery will be on its way to you.
    </div>
</div>
@else
<div class="card shadow-none border rounded-3 p-4 d-none">
    <h2 class="mb-3">3-5 days</h2>
</div>
@endif
{{--
@else
<h2 class="mb-3">3-5 days</h2> --}}


<!-- Order Summary -->
<div class="card rounded-0 shadow-none border mb-4">
    <div class="card-header border-bottom-0">
        <h5 class="fs-16 fw-700 p-2 text-dark mb-0">{{ translate('Order Summary') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-lg-6">
                <table class="table-borderless table">
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Order Code') }}:</td>
                        <td>{{ $order->code }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Customer') }}:</td>
                        <td>{{ json_decode($order->shipping_address)->name }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Email') }}:</td>
                        @if ($order->user_id != null)
                        <td>{{ $order->user->email }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Shipping address') }}:</td>
                        <td>{{ json_decode($order->shipping_address)->address }},
                            {{ json_decode($order->shipping_address)->city }},
                            @if(isset(json_decode($order->shipping_address)->state)) {{
                            json_decode($order->shipping_address)->state }} - @endif
                            @if(isset(json_decode($order->shipping_address)->postal_code))
                            {{ json_decode($order->shipping_address)->postal_code }} @endif,
                            {{-- {{ json_decode($order->shipping_address)->country }} --}}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-6">
                <table class="table-borderless table">
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Order date') }}:</td>
                        <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Order status') }}:</td>
                        <td>{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Total order amount') }}:</td>
                        {{-- <td>{{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax') +
                            $platform_fees) }}
                        </td> --}}
                        <td>{{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Shipping method') }}:</td>
                        <td>{{ translate('Flat shipping rate') }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Payment method') }}:</td>
                        <td>{{ ucfirst(translate(str_replace('_', ' ', $order->payment_type))) }}</td>
                    </tr>
                    <tr>
                        <td class="text-main text-bold">{{ translate('Additional Info') }}</td>
                        <td class="">{{ $order->additional_info }}</td>
                    </tr>
                    @if ($order->tracking_code)
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Tracking code') }}:</td>
                        <td>{{ $order->tracking_code }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>



<!-- Order Details -->
<div class="row gutters-16">
    <div class="col-md-9">
        <div class="card rounded-0 shadow-none border mt-2 mb-4">
            <div class="card-header border-bottom-0">
                <h5 class="fs-16 fw-700 p-2 text-dark mb-0">{{ translate('Order Details') }}</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="aiz-table table">
                    <thead class="text-gray fs-12">
                        <tr>
                            <th class="pl-2">#</th>
                            <th width="30%">{{ translate('Product') }}</th>
                            <th data-breakpoints="md">{{ translate('Variation') }}</th>
                            <th>{{ translate('Quantity') }}</th>
                            <th data-breakpoints="md">{{ translate('Delivery Type') }}</th>
                            <th>{{ translate('Price') }}</th>
                            @if (addon_is_activated('refund_request'))
                            <th data-breakpoints="md">{{ translate('Refund') }}</th>
                            @endif

                            <th data-breakpoints="md" class="text-center pr-2">{{ translate('Review') }}</th>
                            {{-- <th data-breakpoints="md">{{ translate('Delivery Boy ') }}</th> --}}

                        </tr>
                    </thead>
                    <tbody class="fs-13">
                        @foreach ($order->orderDetails as $key => $orderDetail)
                        <tr>
                            <td class="pl-2">{{ sprintf('%02d', $key+1) }}</td>
                            <td>
                                @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{
                                    $orderDetail->product->getTranslation('name') }}</a>
                                @elseif($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank">{{
                                    $orderDetail->product->getTranslation('name') }}</a>
                                @else
                                <strong>{{ translate('Product Unavailable') }}</strong>
                                @endif
                            </td>
                            <td>
                                {{ $orderDetail->variation }}
                            </td>
                            <td>
                                {{ $orderDetail->quantity }}
                            </td>
                            <td>
                                @if ($order->shipping_type != null && $order->shipping_type == 'home_delivery')
                                {{ translate('Home Delivery') }}
                                @elseif ($order->shipping_type == 'pickup_point')
                                @if ($order->pickup_point != null)
                                {{ $order->pickup_point->name }} ({{ translate('Pickip Point') }})
                                @else
                                {{ translate('Pickup Point') }}
                                @endif
                                @elseif($order->shipping_type == 'carrier')
                                @if ($order->carrier != null)
                                {{ $order->carrier->name }} ({{ translate('Carrier') }})
                                <br>
                                {{ translate('Transit Time').' - '.$order->carrier->transit_time }}
                                @else
                                {{ translate('Carrier') }}
                                @endif
                                @endif
                            </td>
                            <td class="fw-700">{{ single_price($orderDetail->price) }}</td>
                            @if (addon_is_activated('refund_request'))
                            @php
                            $no_of_max_day = get_setting('refund_request_time');
                            $last_refund_date = $orderDetail->created_at->addDays($no_of_max_day);

                            $today_date = Carbon\Carbon::now();

                            @endphp
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->refundable != 0 &&
                                        $orderDetail->refund_request == null && $today_date <= $last_refund_date &&
                                            $orderDetail->payment_status == 'paid' && $orderDetail->delivery_status ==
                                            'delivered')
                                            <a href="{{ route('refund_request_send_page', $orderDetail->id) }}"
                                                class="btn btn-primary btn-sm rounded-0">{{ translate('Send') }}</a>
                                            @elseif ($orderDetail->refund_request != null &&
                                            $orderDetail->refund_request->refund_status == 0)
                                            <b class="text-info">{{ translate('Pending') }}</b>
                                            @elseif ($orderDetail->refund_request != null &&
                                            $orderDetail->refund_request->refund_status == 2)
                                            <b class="text-success">{{ translate('Rejected') }}</b>
                                            @elseif ($orderDetail->refund_request != null &&
                                            $orderDetail->refund_request->refund_status == 1)
                                            <b class="text-success">{{ translate('Approved') }}</b>
                                            @elseif ($orderDetail->product->refundable != 0)
                                            <b>{{ translate('N/A') }}</b>
                                            @else
                                            <b>{{ translate('Non-refundable') }}</b>
                                            @endif
                                    </td>
                                    @endif
                                    <td class="text-xl-right pr-2">
                                        @if ($orderDetail->delivery_status == 'delivered')
                                        <a href="javascript:void(0);"
                                            onclick="product_review('{{ $orderDetail->product_id }}')"
                                            class="btn btn-primary btn-sm rounded-0"> {{ translate('Review') }} </a>
                                        @else
                                        <span class="text-danger">{{ translate('Not Delivered Yet') }}</span>
                                        @endif
                                    </td>
                                    {{-- <td class="text-xl-right pr-2">
                                        @if ($orderDetail->delivery_status == 'delivered')
                                        <a href="javascript:void(0);"
                                            onclick="product_review('{{ $orderDetail->product_id }}')"
                                            class="btn btn-primary btn-sm rounded-0"> {{ translate('Review') }} </a>
                                        @else
                                        <span class="text-danger">{{ translate('Not Delivered Yet') }}</span>
                                        @endif
                                    </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Ammount -->
    <div class="col-md-3">
        <div class="card rounded-0 shadow-none border mt-2">
            <div class="card-header border-bottom-0">
                <b class="fs-16 fw-700 p-2 text-dark">{{ translate('Order Amount') }}</b>
            </div>
            <div class="card-body pb-0">
                <table class="table-borderless table">
                    <tbody>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Subtotal') }}</td>
                            <td class="text-right">
                                <span class="strong-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping') }}</td>
                            <td class="text-right">
                                @php

                                $new_shipping_cost = $order->combinedOrder->shipping_cost != null;
                                @endphp
                                {{-- <span class="text-italic">{{
                                    single_price($order->orderDetails->sum('shipping_cost')) }}</span> --}}
                                <span class="text-italic">{{ single_price($new_shipping_cost ?
                                    $order->combinedOrder->shipping_cost : $order->orderDetails->sum('shipping_cost'))
                                    }}</span>

                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Tax') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Coupon') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('platform fees') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($platform_fees) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total') }}</td>
                            <td class="text-right">
                                <strong>{{ single_price($order->grand_total) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if ($order->payment_status == 'unpaid' && $order->delivery_status == 'pending' && $order->manual_payment == 0)
        <button @if(addon_is_activated('offline_payment')) onclick="select_payment_type({{ $order->id }})" @else
            onclick="online_payment({{ $order->id }})" @endif class="btn btn-block btn-primary">
            {{ translate('Make Payment') }}
        </button>
        @endif
    </div>
</div>
<!-- Delivery Boy Details -->
<div class="row gutters-16">
    <div class="col-md-9">
        <div class="card rounded-0 shadow-none border mt-2 mb-4">
            <div class="card-header border-bottom-0">
                <h5 class="fs-16 fw-700 p-2 text-dark mb-0">{{ translate('Delivery Boy Details') }}</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="aiz-table table">
                    <thead class="text-gray fs-12">
                        <tr>
                            <th class="pl-2">#</th>
                            <th width="30%">{{ translate('Name') }}</th>
                            <th data-breakpoints="md">{{ translate('Email') }}</th>
                            <th data-breakpoints="md" class="text-center pr-2">{{ translate('Review') }}</th>
                            {{-- <th data-breakpoints="md" class="text-center pr-2">{{ translate('Delivery Boy ') }}
                            </th> --}}

                        </tr>
                    </thead>
                    <tbody class="fs-14" id="delivery_boy_details">
                        @if($order->delivery_boy != null)
                        <tr>
                            <td class="pl-2">1</td>
                            <td>{{ $order->delivery_boy->name }}</td>
                            <td>{{ $order->delivery_boy->email }}</td>
                            <td class="text-xl-right pr-2">
                                @if ($order->delivery_status == 'delivered')
                                <a href="javascript:void(0);"
                                    onclick="delivery_review('{{ $order->delivery_boy->id }}', '{{ $order->id }}')"
                                    class="btn btn-primary btn-sm rounded-0"> {{ translate('Review') }} </a>
                                @else
                                <span class="text-danger">{{ translate('Not Delivered Yet') }}</span>
                                @endif
                            </td>

                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Ammount -->
    {{-- <div class="col-md-3">
        <div class="card rounded-0 shadow-none border mt-2">
            <div class="card-header border-bottom-0">
                <b class="fs-16 fw-700 p-2 text-dark">{{ translate('Order Amount') }}</b>
            </div>
            <div class="card-body pb-0">
                <table class="table-borderless table">
                    <tbody>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Subtotal') }}</td>
                            <td class="text-right">
                                <span class="strong-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->orderDetails->sum('shipping_cost'))
                                    }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Tax') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Coupon') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('platform fees') }}</td>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($platform_fees) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total') }}</td>
                            <td class="text-right">
                                <strong>{{ single_price($order->grand_total) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if ($order->payment_status == 'unpaid' && $order->delivery_status == 'pending' && $order->manual_payment == 0)
        <button @if(addon_is_activated('offline_payment')) onclick="select_payment_type({{ $order->id }})" @else
            onclick="online_payment({{ $order->id }})" @endif class="btn btn-block btn-primary">
            {{ translate('Make Payment') }}
        </button>
        @endif
    </div> --}}
</div>


@endsection

@section('modal')
<!-- Product Review Modal -->
<div class="modal fade" id="product-review-modal">
    <div class="modal-dialog">
        <div class="modal-content" id="product-review-modal-content">

        </div>
    </div>
</div>

<!-- Product Review Modal -->
<div class="modal fade" id="delivery-review-modal">
    <div class="modal-dialog">
        <div class="modal-content" id="delivery-review-modal-content">

        </div>
    </div>
</div>

<!-- Select Payment Type Modal -->
<div class="modal fade" id="payment_type_select_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Select Payment Type') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="order_id" name="order_id" value="{{ $order->id }}">
                <div class="row">
                    <div class="col-md-2">
                        <label>{{ translate('Payment Type') }}</label>
                    </div>
                    <div class="col-md-10">
                        <div class="mb-3">
                            <select class="form-control aiz-selectpicker rounded-0" onchange="payment_modal(this.value)"
                                data-minimum-results-for-search="Infinity">
                                <option value="">{{ translate('Select One') }}</option>
                                <option value="online">{{ translate('Online payment') }}</option>
                                <option value="offline">{{ translate('Offline payment') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-sm btn-primary rounded-0 transition-3d-hover mr-1"
                        id="payment_select_type_modal_cancel" data-dismiss="modal">{{ translate('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Online payment Modal -->
<div class="modal fade" id="online_payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Make Payment') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body gry-bg px-3 pt-3" style="overflow-y: inherit;">
                <form class="" action="{{ route('order.re_payment') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('Payment Method') }}</label>
                        </div>
                        <div class="col-md-10">
                            <div class="mb-3">
                                <select class="form-control selectpicker rounded-0" data-live-search="true"
                                    name="payment_option" required>
                                    @include('partials.online_payment_options')
                                    @if (get_setting('wallet_system') == 1 && (auth()->user()->balance >=
                                    $order->grand_total))
                                    <option value="wallet">{{ translate('Wallet') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-right">
                        <button type="button" class="btn btn-sm btn-secondary rounded-0 transition-3d-hover mr-1"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-0 transition-3d-hover mr-1">{{
                            translate('Confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- offline payment Modal -->
<div class="modal fade" id="offline_order_re_payment_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Offline Order Payment') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="offline_order_re_payment_modal_body"></div>
        </div>
    </div>
</div>

@endsection


@section('script')
<script type="text/javascript">
    $(document).ready(function(){
            updateDeliveryStatus();
        });

        function product_review(product_id) {
            $.post('{{ route('product_review_modal') }}', {
                _token: '{{ @csrf_token() }}',
                product_id: product_id
            }, function(data) {
                $('#product-review-modal-content').html(data);
                $('#product-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
        }

        function delivery_review(delivery_id, order_id) {
            $.post('{{ route('delivery_review_modal') }}', {
                _token: '{{ @csrf_token() }}',
                delivery_id: delivery_id,
                order_id: order_id
            }, function(data) {
                $('#delivery-review-modal-content').html(data);
                $('#delivery-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
        }

        function select_payment_type(id) {
            $('#payment_type_select_modal').modal('show');
        }

        function payment_modal(type) {
            if (type == 'online') {
                $("#payment_select_type_modal_cancel").click();
                online_payment();
            } else if (type == 'offline') {
                $("#payment_select_type_modal_cancel").click();
                $.post('{{ route('offline_order_re_payment_modal') }}', {
                    _token: '{{ csrf_token() }}',
                    order_id: '{{ $order->id }}'
                }, function(data) {
                    $('#offline_order_re_payment_modal_body').html(data);
                    $('#offline_order_re_payment_modal').modal('show');
                });
            }
        }

        function online_payment() {
            $('input[name=customer_package_id]').val();
            $('#online_payment_modal').modal('show');
        }

</script>
<script>
    // // Get timer value from PHP
    // let timerInSeconds = <?php echo $startTimeInSeconds; ?>;

    // // Function to format time as MM:SS
    // function formatTime(seconds) {
    //     const minutes = Math.floor(seconds / 60);
    //     const secs = seconds % 60;
    //     return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    // }

    // // Function to update the timer
    // function updateTimer() {
    //     if (timerInSeconds <= 0) {
    //         clearInterval(timerInterval); // Stop the timer when it reaches 0
    //         // document.getElementById("timer").innerText = "Time's up!";
    //         $('$start-range').text('00');
    //         $('$end-range').text('00');

    //         return;
    //     }

    //     // Update the timer display
    //     // document.getElementById("timer").innerText = `Timer: ${formatTime(timerInSeconds)}`;

    //     // Check if it's a 5-minute interval
    //         if (timerInSeconds % 10 === 0) { // 300 seconds = 5 minutes
    //             console.log("5 minutes have passed!");

    //         let start_range = $('#start-range').text(); // Get the current text
    //         let end_range = $('#end-range').text(); // Get the current text

    //         // Convert to a number if the value is not '0'
    //         start_range = parseInt(start_range, 10);
    //         end_range = parseInt(end_range, 10);

    //         // Decrease the start range by 5, but only if it's greater than 0
    //         if (start_range > 0) {
    //         start_range -= 5;
    //         $('#start-range').text(start_range); // Set the updated value in the span
    //         }

    //         // Decrease the end range by 5, but only if it's greater than 0
    //         if (end_range > 0) {
    //         end_range -= 5;
    //         $('#end-range').text(end_range); // Set the updated value in the span
    //         }

    //         // console.log("5 minutes have passed!");
    //         updateDeliveryStatus();

    //         }



    //     timerInSeconds--; // Decrement the timer
    // }

    // Run the timer every second
    const timerInterval = setInterval(updateDeliveryStatus, 60000);
    function updateDeliveryStatus() {
    $.ajax({
        // url: "{{ route('purchase_history.details.status', $order->id) }}", // URL must be a string
        // method: 'GET', // Correct HTTP method should be a string
        // success: function(response) {
        //     let start_range = 45; // Get the current text
        //     let end_range = 60; // Get the current text
        //     // alert(response.over_time);
        //     if(response.over_time == false){
        //         // start_range = parseInt(start_range, 10);
        //         // end_range = parseInt(end_range, 10);
        //         if(start_range > 0 ){
        //             // response.time_difference
        //             start_range -= response.time_difference ;
        //             $('#start-range').text(start_range);
        //         }
        //         if(end_range > 0){
        //             end_range -= response.time_difference;
        //             $('#end-range').text(end_range);
        //         }
        //     } else {
        //         $('#start-range').text(0);
        //         $('#end-range').text(0);
        //     }
        url: "{{ route('purchase_history.details.status', $order->id) }}",
        method: 'GET',
        success: function(response) {
            const tbody = document.getElementById('delivery_boy_details');
           
            let start_range = 45;
            let end_range = 60;

            if(response.over_time == false) {
                // Update ranges with zero-clamping
                start_range = Math.max(start_range - response.time_difference, 0);
                end_range = Math.max(end_range - response.time_difference, 0);

                $('#start-range').text(start_range);
                $('#end-range').text(end_range);
            } else {
                $('#start-range').text(0);
                $('#end-range').text(0);
            }

            if(response.delivery_status == 'confirmed'){
                $('#card-quick').show();
                $('#heading-time').text('Delivering Time');
                $('#status').text('Preparing Your Order');
                $('#confirmed').css('background-color', '#7D9A40');
                $('#on_the_way').css('background-color', '#D9D9D9');
                $('#picked').css('background-color', '#D9D9D9');
                $('#delivered').css('background-color', '#D9D9D9');

            } else if(response.delivery_status == 'picked_up'){
                $('#card-quick').show();

                $('#picked').css('background-color', '#7D9A40');
                $('#confirmed').css('background-color', '#7D9A40');
                $('#on_the_way').css('background-color', '#D9D9D9');
                $('#delivered').css('background-color', '#D9D9D9');
                $('#status').text('Order Picked Up');

            }else if(response.delivery_status == 'on_the_way'){
                $('#card-quick').show();

                $('#picked').css('background-color', '#7D9A40');
                $('#on_the_way').css('background-color', '#7D9A40');
                $('#confirmed').css('background-color', '#7D9A40');
                $('#delivered').css('background-color', '#D9D9D9');

                $('#status').text('Order On The Way');

            }else if(response.delivery_status == 'delivered'){
                $('#card-quick').show();

                $('#on_the_way').css('background-color', '#7D9A40');
                $('#picked').css('background-color', '#7D9A40');
                $('#delivered').css('background-color', '#7D9A40');
                $('#confirmed').css('background-color', '#7D9A40');
                $('#status').hide();
                $('#heading-time').hide();
                $('#tagline').hide();
                $('#timer').text('Delivered');

            }

            if (response.delivery_boy) {
            tbody.innerHTML = ''; 
            const deliveryBoy = response.delivery_boy;
            const orderId = response.order_id;
            const deliveryStatus = response.delivery_status;
           
            let html = `
            <tr>
            <td class="pl-2">1</td>
            <td>${deliveryBoy.name}</td>
            <td>${deliveryBoy.email}</td>
            <td class="text-xl-right pr-2">
            ${deliveryStatus === 'delivered' 
            ? `<a href="javascript:void(0);" onclick="delivery_review('${deliveryBoy.id}', '${orderId}')" class="btn btn-primary btn-sm rounded-0">Review</a>`
            : `<span class="text-danger">Not Delivered Yet</span>`
            }
            </td>
            </tr>
            `;

            tbody.innerHTML = html;
            $('table.footable > tbody > tr > td').each(function() {
    this.style.setProperty('display', 'table-cell', 'important');
});
            }

            // else {
            //     $('#status').text('Waiting');
            // }

            // alert(response.delivery_status); // Display the delivery status
        },
        error: function() {
            alert('Error occurred while fetching delivery status.');
        }
    });
        }

</script>


<script>
    // Initialize the map and markers
    function initialize() {
        const seller = { lat: {{ $sellerLat }}, lng: {{ $sellerLng }} };
        const customer = { lat: {{ $customerLat }}, lng: {{ $customerLng }} };

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14,
            center: seller,
        });

        const deliveryStatus = "picked_up"; // Hardcoded for testing
        let riderMarker = null;
        let currentPosition = seller;
        let animationActive = false;

        // Seller Marker (if order not picked up)
        if (deliveryStatus !== 'picked_up') {
            new google.maps.Marker({ position: seller, map, title: "Seller" });
        }

        // Customer Marker
        new google.maps.Marker({ position: customer, map, title: "Customer" });

        // Rider Marker
        if (deliveryStatus === 'picked_up') {
            riderMarker = new google.maps.Marker({
                position: seller,
                map: map,
                icon: {
                    url: "{{ static_asset('svg/rider_icon.svg') }}",
                    scaledSize: new google.maps.Size(40, 40),
                    anchor: new google.maps.Point(20, 20),
                },
                title: "Rider",
            });

            drawRoute(map, seller, customer);
            startLocationTracking(riderMarker, seller);
        }
    }

    // ✅ Function to draw route along roads using Directions Service
    function drawRoute(map, origin, destination) {
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: "#7d9a40",
                strokeOpacity: 0.8,
                strokeWeight: 4,
            },
        });

        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
        }, (response, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsRenderer.setDirections(response);
            } else {
                console.error("Directions request failed: " + status);
            }
        });
    }

    // ✅ Animate Rider Movement
    function animateMovement(riderMarker, currentPosition, newPosition, duration = 2000) {
        if (!riderMarker || animationActive) return;

        animationActive = true;
        const startTime = Date.now();
        const startLat = currentPosition.lat;
        const startLng = currentPosition.lng;
        const deltaLat = newPosition.lat - startLat;
        const deltaLng = newPosition.lng - startLng;

        function update() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const intermediatePos = {
                lat: startLat + deltaLat * progress,
                lng: startLng + deltaLng * progress,
            };

            riderMarker.setPosition(intermediatePos);

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                currentPosition.lat = newPosition.lat;
                currentPosition.lng = newPosition.lng;
                animationActive = false;
            }
        }

        requestAnimationFrame(update);
    }

    // ✅ Fetch New Position Every 10 Seconds
    function startLocationTracking(riderMarker, currentPosition) {
        setInterval(() => {
            $.ajax({
                url: "{{ route('current_lat_long_delivery', $order->id) }}",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: (response) => {
                    if (response?.lat && response?.lng) {
                        const newPosition = {
                            lat: parseFloat(response.lat),
                            lng: parseFloat(response.lng),
                        };

                        const distance = google.maps.geometry.spherical.computeDistanceBetween(
                            new google.maps.LatLng(currentPosition),
                            new google.maps.LatLng(newPosition)
                        );

                        if (distance > 10) {
                            animateMovement(riderMarker, currentPosition, newPosition);
                        }
                    }
                },
                error: (error) => console.error("AJAX error:", error),
            });
        }, 10000);
    }
</script>

<!-- Load Google Maps with Geometry Library -->
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_API_KEY') }}&libraries=geometry,places&language=en&callback=initialize"
    async defer>
</script>

{{-- @if (get_setting('google_map') == 1)
@include('frontend.partials.google_map')
@endif --}}
@endsection
