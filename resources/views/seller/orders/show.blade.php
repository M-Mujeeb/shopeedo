@extends('seller.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ static_asset('barcodes/bootstrap-colorpicker.css') }}">
<link rel='stylesheet' href="{{ static_asset('barcodes/rangeslider.css') }}">
<link rel='stylesheet' href="{{ static_asset('barcodes/print.css') }}">
<script src="{{ static_asset('barcodes/prefixfree.js') }}"></script>

<style>
    .dropdown-toggle::after {
    border: 0;
    /* \f107 */
    content: "";
    font-family: "Line Awesome Free";
    font-weight: 900;
    font-size: 80%;
    margin-left: 0.3rem;
}
.a4-page {
    width: auto;
    min-height: auto;
    margin: auto;
    padding: 15mm;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    page-break-after: always;
}
</style>

@endsection

@section('panel_content')



    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
        </div>

        <div class="card-body">
            <div class="row gutters-5 mb-3">
                <div class="col text-md-left text-center">
                </div>
                @php
                    $delivery_status = $order->delivery_status;
                    $payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
                @endphp
                @if (get_setting('product_manage_by_admin') == 0)
                    <div class="col-md-3 ml-auto">
                        <label for="update_payment_status">{{ translate('Payment Status') }}</label>
                        @if (($order->payment_type == 'cash_on_delivery' || (addon_is_activated('offline_payment') == 1 && $order->manual_payment == 1)) && $payment_status == 'unpaid')
                            <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                                id="update_payment_status">
                                <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>
                                    {{ translate('Unpaid') }}</option>
                                <option value="paid" @if ($payment_status == 'paid') selected @endif>
                                    {{ translate('Paid') }}</option>
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ translate($payment_status) }}" disabled>
                        @endif
                    </div>
                    <div class="col-md-3 ml-auto">
                        <label for="update_delivery_status">{{ translate('Delivery Status') }}</label>
                        @if ($delivery_status != 'delivered' && $delivery_status != 'cancelled')
                            <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                                id="update_delivery_status">
                                <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                    {{ translate('Pending') }}</option>
                                <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                                    {{ translate('Confirmed') }}</option>
                                <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>
                                    {{ translate('Picked Up') }}</option>
                                <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>
                                    {{ translate('On The Way') }}</option>
                                <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                    {{ translate('Delivered') }}</option>
                                <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>
                                    {{ translate('Cancel') }}</option>
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}" disabled>
                        @endif
                    </div>
                    <div class="col-md-3 ml-auto">
                        <label for="update_tracking_code">
                            {{ translate('Tracking Code (optional)') }}
                        </label>
                        <input type="text" class="form-control" id="update_tracking_code"
                            value="{{ $order->tracking_code }}">
                    </div>
                @endif
            </div>
            <div class="row gutters-5 mt-2">
                <div class="col text-md-left text-center">
                    @if(json_decode($order->shipping_address))
                        <address id="address-data">
                            <strong class="text-main">
                                {{ json_decode($order->shipping_address)->name }}
                            </strong><br>
                            {{ json_decode($order->shipping_address)->email }}<br>
                            {{ json_decode($order->shipping_address)->phone }}<br>
                            {{ json_decode($order->shipping_address)->address }}
                            {{-- , {{ json_decode($order->shipping_address)->city }},
                             @if(isset(json_decode($order->shipping_address)->state)) 
                             {{ json_decode($order->shipping_address)->state }} -
                              @endif {{ json_decode($order->shipping_address)->postal_code }}<br>
                            {{ json_decode($order->shipping_address)->country }} --}}
                        </address>
                    @else
                        <address>
                            <strong class="text-main">
                                {{ $order->user->name }}
                            </strong><br>
                            {{ $order->user->email }}<br>
                            {{ $order->user->phone }}<br>
                        </address>
                    @endif
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }},
                        {{ translate('Amount') }}:
                        {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                        {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}"
                            target="_blank"><img
                                src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt=""
                                height="100"></a>
                    @endif
                </div>
                <div class="col-md-4">
                    <table class="ml-auto">
                        <tbody>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order #') }}</td>
                                <td class="text-info text-bold text-right">{{ $order->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Status') }}</td>
                                <td class="text-right">
                                    @if ($delivery_status == 'delivered')
                                        <span
                                            class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                    @else
                                        <span
                                            class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Date') }}</td>
                                <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                            </tr>


                            <tr>
                                <td class="text-main text-bold">{{ translate('Total amount') }}</td>
                                <td class="text-right">
                                    {{ single_price($order->grand_total) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Payment method') }}</td>
                                <td class="text-right">
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}</td>
                            </tr>

                            <tr>
                                <td class="text-main text-bold">{{ translate('Additional Info') }}</td>
                                <td class="text-right">{{ $order->additional_info }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table-bordered aiz-table invoice-summary table">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th data-breakpoints="lg" class="min-col">#</th>
                                <th width="10%">{{ translate('Photo') }}</th>
                                <th class="text-uppercase">{{ translate('Description') }}</th>
                                <th data-breakpoints="lg" class="text-uppercase">{{ translate('Delivery Type') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Qty') }}
                                </th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Price') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-right">
                                    {{ translate('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}"
                                                target="_blank"><img height="50"
                                                    src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                        @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                            <a href="{{ route('auction-product', $orderDetail->product->slug) }}"
                                                target="_blank"><img height="50"
                                                    src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                        @else
                                            <strong>{{ translate('N/A') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                            <strong><a href="{{ route('product', $orderDetail->product->slug) }}"
                                                    target="_blank"
                                                    class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong>
                                            <small>{{ $orderDetail->variation }}</small>
                                        @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                            <strong><a href="{{ route('auction-product', $orderDetail->product->slug) }}"
                                                    target="_blank"
                                                    class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong>
                                        @else
                                            <strong>{{ translate('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->shipping_type != null && $order->shipping_type == 'home_delivery')
                                            {{ translate('Home Delivery') }}
                                        @elseif ($order->shipping_type == 'pickup_point')
                                            @if ($order->pickup_point != null)
                                                {{ $order->pickup_point->getTranslation('name') }}
                                                ({{ translate('Pickup Point') }})
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
                                    <td class="text-center">{{ $orderDetail->quantity }}</td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price / $orderDetail->quantity) }}</td>
                                    <td class="text-center">{{ single_price($orderDetail->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix float-right">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Sub Total') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->sum('price')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Tax') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->sum('tax')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                            </td>
                            <td>
                                {{-- {{ single_price($order->orderDetails->sum('shipping_cost')) }} --}}
                                {{ single_price($order->combinedOrder->shipping_cost) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Coupon') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->coupon_discount) }}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Platform Fees') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($platform_fees) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                {{ single_price($order->grand_total) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <form action="">
                    <input type="hidden" id="address" value={{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}/>
                    <input type="hidden" id="company" value="Shopeedo" />
                </form>

                <form>
                    <input type="hidden" id="address" value="{{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}"/>
                    <input type="hidden" id="company" value="Shopeedo" />
                </form>
                <div class="barcode-container">
                    <svg id="barcode"></svg>
                    {{-- <span id="invalid" >{{ translate('Not valid data for this barcode type!') }}</span> --}}
                </div>
                
                <div class="no-print text-right d-flex justify-content-between align-items-center mt-2">
                <button onclick="printBarcodes()" class="btn btn-primary ">Print Barcodes</button>


                    <a href="{{ route('seller.invoice.download', $order->id) }}" type="button"
                        {{-- <i class="las la-print"></i> --}}
                        class="btn btn-icon btn-light"> <i class="las la-download"></i></a>
                </div>

                {{-- @php
                $platform_fees = get_platform_fees();
                echo $platform_fees;
                @endphp --}}
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="{{ static_asset('barcodes/JsBarcode.all.js') }}"></script>
    <script src="{{ static_asset('barcodes/bootstrap-colorpicker.js') }}"></script>
    <script src="{{ static_asset('barcodes/rangeslider.js') }}"></script>
    <script src="{{ static_asset('barcodes/print.js') }}"></script>
    {{-- <script>

     const address = document.getElementById('address').value;
     const company = document.getElementById('company').value;
     var barcodeData = address + "|" + company;
     let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        barcodeWrapper.append(svg);
    try {
        JsBarcode('#barcode', barcodeData, {
            format: "CODE128",
            width: 2,
            height: 100,
            displayValue: true
        });
    } catch(e) {
        console.error("Barcode generation failed:", e);
        // document.getElementById('invalid').style.display = 'block';
    }
    document.addEventListener('DOMContentLoaded', function() {
    initializeBarcode();
});
    //  alert(address + company);

    </script> --}}

    <script>
        $(document).ready(function() {
    
    // Bind change events to form elements
    // $("#userInput, #barcodeType, #barcodes-per-row, #orientation, #background-color, #line-color, input[name='text-align-options'], #font, input[type='checkbox'], #bar-fontSize, #bar-text-margin, .display-text, input[name='showProductName'], input[name='showPrice']").change(newBarcode);
    // $('#quantity, #bar-width, #bar-height, #bar-margin').on('input change', newBarcode);

    // Initial call to generate barcodes based on default or current settings
    newBarcode();


});

function newBarcode() {
    const address = document.getElementById('address').value;
     const company = document.getElementById('company').value;
     var barcodeData = address + company;
    $(".barcode-container").empty();

    // var selectedOption = $("#userInput").find(":selected");
    // var selectedProductSlug = selectedOption.val();

    // if (!selectedProductSlug) {
    //     return;
    // }

    var selectedProductName = '';
    var selectedProductPrice = {{$order->grand_total}};
    var showProductName = true;
    var showPrice = true;
    var quantity = 1;
    var perRow = 1;
    var orientation = 'horizontal';

    var barcodeType = 'CODE128';
    var barWidth = 1;
    var barHeight = 50;
    var barMargin = 10;
    var backgroundColor = '#FFFFFF';
    var lineColor = '#000000';
    var displayValue = true;
    var textAlign = 'center';
    var font = '18px';
    var fontOptions = "bold";
    var fontSize = 18;
    var textMargin = 5;

    var currentPage = createNewPage();
    var currentRow = createRow(currentPage);
    var barcodeCounter = 0;

    for (let i = 0; i < quantity; i++) {
        if (barcodeCounter >= calculateBarcodesPerPage(perRow, orientation)) {
            currentPage = createNewPage();
            currentRow = createRow(currentPage);
            barcodeCounter = 0;
        }

        if ((orientation === "horizontal" && i % perRow === 0) || (orientation === "vertical" && i % perRow === 0)) {
            if (i !== 0) {
                currentRow = createRow(currentPage);
            }
        }

        let barcodeWrapper = $('<div class="barcode-wrapper"></div>').appendTo(currentRow);
        if (showProductName) {
            $('<div class="product-name"></div>').text(selectedProductName).appendTo(barcodeWrapper);
        }
        if (showPrice) {
            $('<div class="product-price"></div>').text(`Price: $${selectedProductPrice}`).appendTo(barcodeWrapper);
        }
        let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        barcodeWrapper.append(svg);

        JsBarcode(svg, barcodeData, {
            format: barcodeType,
            background: backgroundColor,
            lineColor: lineColor,
            fontSize: fontSize,
            height: barHeight,
            width: barWidth,
            margin: barMargin,
            textMargin: textMargin,
            displayValue: displayValue,
            font: font,
            fontOptions: fontOptions,
            textAlign: textAlign,
            valid: function(valid) {
                if (!valid) {
                    barcodeWrapper.replaceWith('<span>Not valid data for this barcode type!</span>');
                }
            }
        });

        barcodeCounter++;
    }
}
    </script>
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('seller.orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                $('#order_details').modal('hide');
                AIZ.plugins.notify('success', '{{ translate('Order status has been updated') }}');
                location.reload().setTimeOut(500);
            });
        });

        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('seller.orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                $('#order_details').modal('hide');
                //console.log(data);
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                location.reload().setTimeOut(500);
            });
        });
    </script>
@endsection
