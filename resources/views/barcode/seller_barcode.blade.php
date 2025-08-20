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
</style>

@endsection

@section('panel_content')

<div class="container">
    <div class="form-group row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Barcode Configuration')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Products')}}</label>
                            <div class="col-md-9">
                                <select class="custom-select form-control aiz-selectpicker" id="userInput">
                                    <option value="">{{ translate('Select Product') }}</option>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->slug }}" data-price="{{ $product->unit_price }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Quantity')}}</label>
                            <div class="col-md-9">
                                <input id="quantity" type="number" class="form-control" value="21" min="1">
                            </div>
                        </div>

                        <div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Barcodes Per Row') }}</label>
    <div class="col-md-9">
        <select class="custom-select" id="barcodes-per-row">
            <option value="1">{{ translate('1') }}</option>
            <option value="2">{{ translate('2') }}</option>
            <option value="3" selected>{{ translate('3') }}</option>
            <option value="4">{{ translate('4') }}</option>
            <option value="5">{{ translate('5') }}</option>
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Orientation') }}</label>
    <div class="col-md-9">
        <select class="custom-select" id="orientation">
            <option value="horizontal">{{ translate('Horizontal') }}</option>
            <option value="vertical">{{ translate('Vertical') }}</option>
        </select>
    </div>
</div>


                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Barcode Type') }}</label>
                            <div class="col-md-9">
                                <select class="custom-select form-control aiz-selectpicker" id="barcodeType" data-live-search="true">
                                    <option value="CODE128">{{ translate('CODE128 auto') }}</option>
                                    <option value="CODE128A">{{ translate('CODE128 A') }}</option>
                                    <option value="CODE128B">{{ translate('CODE128 B') }}</option>
                                    <option value="CODE128C">{{ translate('CODE128 C') }}</option>
                                    <option value="EAN13">{{ translate('EAN13') }}</option>
                                    <option value="EAN8">{{ translate('EAN8') }}</option>
                                    <option value="UPC">{{ translate('UPC') }}</option>
                                    <option value="CODE39">{{ translate('CODE39') }}</option>
                                    <option value="ITF14">{{ translate('ITF14') }}</option>
                                    <option value="ITF">{{ translate('ITF') }}</option>
                                    <option value="MSI">{{ translate('MSI') }}</option>
                                    <option value="MSI10">{{ translate('MSI10') }}</option>
                                    <option value="MSI11">{{ translate('MSI11') }}</option>
                                    <option value="MSI1010">{{ translate('MSI1010') }}</option>
                                    <option value="MSI1110">{{ translate('MSI1110') }}</option>
                                    <option value="pharmacode">{{ translate('Pharmacode') }}</option>
                                </select>

                            </div>
                        </div>
                        <div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Bar Width') }}</label>
    <div class="col-md-9">
        <input id="bar-width" type="range" class="custom-range" min="1" max="4" step="1" value="1">
    </div>
</div>
<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Height') }}</label>
    <div class="col-md-9">
        <input id="bar-height" type="range" class="custom-range" min="10" max="150" step="5" value="50">
    </div>
</div>
<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Margin') }}</label>
    <div class="col-md-9">
        <input id="bar-margin" type="range" class="custom-range" min="0" max="25" step="1" value="10">
    </div>
</div>
<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Background') }}</label>
    <div class="col-md-9">
        <input id="background-color" type="color" class="form-control" value="#FFFFFF">
    </div>
</div>
<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Line Color') }}</label>
    <div class="col-md-9">
        <input id="line-color" type="color" class="form-control" value="#000000">
    </div>
</div>
<!-- Show text -->
<div class="form-group row d-none">
    <div class="col-md-3 col-md-offset-1 col-xs-12 col-xs-offset-0 description-text">
        <p>{{ translate('Show text') }}</p>
    </div>
    <div class="col-md-9 col-xs-12">
        <div class="btn-group btn-group-md border" role="toolbar">
            <button type="button" class="btn btn-default btn-primary display-text" value="true">{{ translate('Show') }}</button>
            <button type="button" class="btn btn-default display-text" value="false">{{ translate('Hide') }}</button>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Show Product Name') }}</label>
    <div class="col-md-9">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-primary active">
                <input type="radio" name="showProductName" value="true" checked> {{ translate('Show') }}
            </label>
            <label class="btn btn-outline-primary">
                <input type="radio" name="showProductName" value="false"> {{ translate('Hide') }}
            </label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-md-3 col-form-label">{{ translate('Show Price') }}</label>
    <div class="col-md-9">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-primary">
                <input type="radio" name="showPrice" value="true"> {{ translate('Show') }}
            </label>
            <label class="btn btn-outline-primary active">
                <input type="radio" name="showPrice" value="false" checked> {{ translate('Hide') }}
            </label>
        </div>
    </div>
</div>




                        <div id="font-options">
    <!-- Text Align -->
    <div class="form-group row">
        <label class="col-md-3 col-form-label">{{ __('Text Align') }}</label>
        <div class="col-md-9">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary">
                    <input type="radio" name="text-align-options" id="align-left" value="left"> {{ __('Left') }}
                </label>
                <label class="btn btn-secondary active">
                    <input type="radio" name="text-align-options" id="align-center" value="center" checked> {{ __('Center') }}
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="text-align-options" id="align-right" value="right"> {{ __('Right') }}
                </label>
            </div>
        </div>
    </div>
    <!-- Font -->
    <div class="form-group row">
        <label class="col-md-3 col-form-label">{{ __('Font') }}</label>
        <div class="col-md-9">
            <select class="custom-select" id="font">
                <option value="monospace" style="font-family: monospace" selected>{{ __('Monospace') }}</option>
                <option value="sans-serif" style="font-family: sans-serif">{{ __('Sans-serif') }}</option>
                <option value="serif" style="font-family: serif">{{ __('Serif') }}</option>
                <option value="fantasy" style="font-family: fantasy">{{ __('Fantasy') }}</option>
                <option value="cursive" style="font-family: cursive">{{ __('Cursive') }}</option>
            </select>
        </div>
    </div>
    <!-- Font Options -->
    <div class="form-group row">
        <label class="col-md-3 col-form-label">{{ __('Font Options') }}</label>
        <div class="col-md-9">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="checkbox" value="bold"> {{ __('Bold') }}
                </label>
                <label class="btn btn-secondary">
                    <input type="checkbox" value="italic"> {{ __('Italic') }}
                </label>
            </div>
        </div>
    </div>
    <!-- Font Size -->
    <div class="form-group row">
        <label class="col-md-3 col-form-label">{{ __('Font Size') }}</label>
        <div class="col-md-9">
            <input id="bar-fontSize" type="range" class="custom-range" min="1" max="36" step="1" value="14">
        </div>
    </div>
    <!-- Text Margin -->
    <div class="form-group row">
        <label class="col-md-3 col-form-label">{{ __('Text Margin') }}</label>
        <div class="col-md-9">
            <input id="bar-text-margin" type="range" class="custom-range" min="-15" max="40" step="1" value="0">
        </div>
    </div>
</div>



                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Barcodes')}}</h5>
            </div>
            <div class="card-body">

                <div class="row">
        <div class="col-12">
            <button id="printButton" onclick="printBarcodes()" class="btn btn-primary mt-3">Print Barcodes</button>
        </div>
    </div>

                <div class="barcode-container">
                    <svg id="barcode"></svg>
                    <span id="invalid">{{ translate('Not valid data for this barcode type!') }}</span>
                </div>

            </div>
        </div>
    </div>

</div>


@endsection

@section('script')
<script src="{{ static_asset('barcodes/JsBarcode.all.js') }}"></script>
<script src="{{ static_asset('barcodes/bootstrap-colorpicker.js') }}"></script>
<script src="{{ static_asset('barcodes/rangeslider.js') }}"></script>
<script src="{{ static_asset('barcodes/print.js') }}"></script>
<script>
//     try {
//     JsBarcode("#barcode", "123456", {  // This creates barcode with "123456"
//         format: "CODE128",
//         width: 2,
//         height: 100,
//         displayValue: true  // Shows text below barcode
//     });
// } catch(e) {
//     document.getElementById('invalid').style.display = 'block';
// }
</script>
@endsection
