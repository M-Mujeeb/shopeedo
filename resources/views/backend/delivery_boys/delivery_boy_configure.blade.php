@extends('backend.layouts.app')

@section('content')
    <style>
        #map {
            width: 100%;
            height: 250px;
        }
    </style>

    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Payment Configuration') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="delivery_boy_payment_type">

                            <label class="col-md-4 col-from-label">
                                {{ translate('Monthly Salary') }}
                            </label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="delivery_boy_payment_type" value="salary"
                                        @if (get_setting('delivery_boy_payment_type') == 'salary') checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row" id="salary_div" style="display: none;">
                            <label class="col-sm-4 col-from-label">{{ translate('Salary Amount') }}</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="types[]" value="delivery_boy_salary">
                                <div class="input-group">
                                    <input type="number" name="delivery_boy_salary" class="form-control"
                                        value="{{ get_setting('delivery_boy_salary') ? get_setting('delivery_boy_salary') : '0' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">
                                            {{ \App\Models\Currency::find(get_setting('system_default_currency'))->code }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-from-label">
                                {{ translate('Per Order Commission') }}
                            </label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="delivery_boy_payment_type" value="commission"
                                        @if (get_setting('delivery_boy_payment_type') == 'commission') checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row" id="commission_div" style="display: none;">
                            <label class="col-sm-4 col-from-label">{{ translate('Commission Rate') }}</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="types[]" value="delivery_boy_commission">
                                <div class="input-group">
                                    <input type="number" name="delivery_boy_commission" class="form-control"
                                        value="{{ get_setting('delivery_boy_commission') ? get_setting('delivery_boy_commission') : '0' }}">
                                    {{-- <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">
                                            {{ \App\Models\Currency::find(get_setting('system_default_currency'))->code }}
                                        </span>
                                    </div> --}}
                                    <div>
                                    <input type="hidden" name="types[]" value="delivery_boy_commission_type">
                                    <select name="delivery_boy_commission_type" id="delivery_boy_commission_type" class="form-control">
                                        <option value="percentage" {{ get_setting('delivery_boy_commission_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="flat" {{ get_setting('delivery_boy_commission_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" >
                            <label class="col-sm-4 col-from-label">{{ translate('Maximum Collection') }}</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="types[]" value="delivery_boy_max_collection">
                                <div class="input-group">
                                    <input type="number" name="delivery_boy_max_collection" class="form-control"
                                        value="{{ get_setting('delivery_boy_max_collection') ? get_setting('delivery_boy_max_collection') : '0' }}">
                                    <div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" >
                            <label class="col-sm-4 col-from-label">{{ translate('Maximum Amount for Cash on Delivery') }}</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="types[]" value="max_cod_amount">
                                <div class="input-group">
                                    <input type="number" name="max_cod_amount" class="form-control"
                                        value="{{ get_setting('max_cod_amount') ? get_setting('max_cod_amount') : '0' }}">
                                    <div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Notification Configuration') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="delivery_boy_mail_notification">

                            <label class="col-md-4 col-from-label">
                                {{ translate('Send Mail') }}
                            </label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="delivery_boy_mail_notification" value="1"
                                        @if (get_setting('delivery_boy_mail_notification') == '1') checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="delivery_boy_otp_notification">

                            <label class="col-md-4 col-from-label">
                                {{ translate('Send OTP') }}
                            </label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="delivery_boy_otp_notification" value="1"
                                        @if (get_setting('delivery_boy_otp_notification') == '1') checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Pickup Location For Delivery Boy') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @if (get_setting('google_map') == 1)
                            <div class="row">
                                <input id="searchInput" class="controls" type="text"
                                    placeholder="{{ translate('Enter a location') }}">
                                <div id="map"></div>
                                <ul id="geoData">
                                    <li style="display: none;">Full Address: <span id="location"></span></li>
                                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                                    <li style="display: none;">Country: <span id="country"></span></li>
                                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                                </ul>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">Longitude</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="hidden" name="types[]" value="delivery_pickup_longitude">
                                    <input type="text" class="form-control mb-3" id="longitude"
                                        name="delivery_pickup_longitude" readonly=""
                                        value="{{ get_setting('delivery_pickup_longitude') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">Latitude</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="hidden" name="types[]" value="delivery_pickup_latitude">
                                    <input type="text" class="form-control mb-3" id="latitude"
                                        name="delivery_pickup_latitude" readonly=""
                                        value="{{ get_setting('delivery_pickup_latitude') }}">
                                </div>
                            </div>
                        @else
                            <div class="form-group row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">Longitude</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="hidden" name="types[]" value="delivery_pickup_longitude">
                                    <input type="text" class="form-control mb-3" id="longitude"
                                        name="delivery_pickup_longitude"
                                        value="{{ get_setting('delivery_pickup_longitude') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">Latitude</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="hidden" name="types[]" value="delivery_pickup_latitude">
                                    <input type="text" class="form-control mb-3" id="latitude"
                                        name="delivery_pickup_latitude"
                                        value="{{ get_setting('delivery_pickup_latitude') }}">
                                </div>
                            </div>
                        @endif
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('delivery-boys.banner')}}" method="POST" enctype="multipart/form-data" enctype="multipart/form-data" id="banner_form">
        @csrf
    <div class="bg-white p-3 p-sm-2rem">
        <!-- Product Files & Media -->
        <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">{{translate('Delivery Boy Banners')}}</h5>
        <div class="w-100">
            <!-- Gallery Images -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}}</label>
                <div class="col-md-9">
                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                        </div>
                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                        <input type="hidden" name="photos" class="selected-files">
                    </div>
                    <div class="file-preview box sm">
                    </div>
                    <small class="text-muted">{{translate('These images are visible in product details page gallery. Minimum dimensions required: 900px width X 900px height.')}}</small>
                </div>
            </div>
        </div>
        <div class="mt-4 text-right">
            <button type="submit" name="button" value="publish" class="mx-2 btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success action-btn">{{ translate('Save & Publish') }}</button>
        </div>
    </div>
    </form>

    <div class="bg-white p-3 p-sm-2rem mt-4">
        <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
            {{ translate('Uploaded Banners') }}
        </h5>
        <div class="row">
            @php
                $banners = \App\Models\BusinessSetting::where('type', 'delivery_boy_banner')->value('value');
                $banners = $banners ? explode(',', $banners) : [];
            @endphp

            @if($banners)
            @foreach($banners as $banner)
                <div class="col-md-3 mb-3">
                    <div class="position-relative border rounded p-2">
                        <img src="{{ uploaded_asset($banner) }}" class="img-fluid rounded" alt="Banner Image">
                        <form action="{{ route('delivery-boys.banner.delete', $banner) }}" method="POST" class="position-absolute" style="top: 5px; right: 5px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">✕</button>
                        </form>
                    </div>
                </div>
            @endforeach
            @else
                <div class="col-md-12 mb-3">
                    <div class="position-relative border rounded p-2">
                        <p class="text-center m-0 text-muted">{{ translate('No banner uploaded yet.') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Bonus Configuration') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.updateBonuses') }}" method="POST">
                        @csrf

                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>{{ translate('Welcome Bonus (Monthly)') }}</h4>
                            </div>
                            <div class="card-body">
                                <div id="welcome_bonus_div">
                                    @php
                                        $welcomeBonuses = json_decode(get_setting('welcome_bonuses'), true) ?? [];
                                    @endphp
                                    @foreach ($welcomeBonuses as $index => $bonus)
                                        <div class="form-group row">
                                            <div class="col-sm-5">
                                                <input type="number" name="welcome_deliveries[]" class="form-control" placeholder="No. of Deliveries"
                                                    value="{{ $bonus['deliveries'] }}">
                                                    <p>Deliveries</p>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="number" name="welcome_prices[]" class="form-control" placeholder="Bonus Amount (Rs.)"
                                                    value="{{ $bonus['price'] }}">
                                                    <p>Amount</p>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-danger remove-row">-</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-secondary" id="add_welcome_bonus">+ Add Welcome Bonus</button>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>{{ translate('Weekly Bonus') }}</h4>
                            </div>
                            <div class="card-body">
                                <div id="weekly_bonus_div">
                                    @php
                                        $weeklyBonuses = json_decode(get_setting('weekly_bonuses'), true) ?? [];
                                    @endphp
                                    @foreach ($weeklyBonuses as $index => $bonus)
                                        <div class="form-group row">
                                            <div class="col-sm-5">
                                                <input type="number" name="weekly_deliveries[]" class="form-control" placeholder="No. of Deliveries"
                                                    value="{{ $bonus['deliveries'] }}">
                                                    <p>Deliveries</p>
                                            </div>

                                            <div class="col-sm-5">
                                                <input type="number" name="weekly_prices[]" class="form-control" placeholder="Bonus Amount (Rs.)"
                                                    value="{{ $bonus['price'] }}">
                                                    <p>Amount</p>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-danger remove-row">-</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-secondary" id="add_weekly_bonus">+ Add Weekly Bonus</button>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script type="text/javascript">
        (function($) {
            "use strict";
            $(document).ready(function() {
                show_hide_div();
            })

            $("[name=delivery_boy_payment_type]").on("change", function() {
                show_hide_div();
            });

            function show_hide_div() {
                $("#salary_div").hide();
                $("#commission_div").hide();
                if ($("[name=delivery_boy_payment_type]:checked").val() == 'salary') {
                    $("#salary_div").show();
                }
                if ($("[name=delivery_boy_payment_type]:checked").val() == 'commission') {
                    $("#commission_div").show();
                }
            }
        })(jQuery);
    </script>

    @if (get_setting('google_map') == 1)
        <script>
            let default_longtitude = "{{ get_setting('google_map_longtitude') }}";
            let default_latitude = "{{ get_setting('google_map_latitude') }}";

            function initialize(lat = -33.8688, lang = 151.2195, id_format = '') {

                var long = lang;
                var lat = lat;
                if (default_longtitude != '' && default_latitude != '') {
                    long = default_longtitude;
                    lat = default_latitude;
                }

                @if (get_setting('delivery_pickup_latitude'))
                    long = {{ get_setting('delivery_pickup_longitude') }};
                    lat = {{ get_setting('delivery_pickup_latitude') }};
                @endif

                var map = new google.maps.Map(document.getElementById(id_format + 'map'), {
                    center: {
                        lat: lat,
                        lng: long
                    },
                    zoom: 13
                });

                var myLatlng = new google.maps.LatLng(lat, long);

                var input = document.getElementById(id_format + 'searchInput');
                //                console.log(input);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                var autocomplete = new google.maps.places.Autocomplete(input);

                autocomplete.bindTo('bounds', map);

                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    position: myLatlng,
                    anchorPoint: new google.maps.Point(0, -29),
                    draggable: true,
                });

                map.addListener('click', function(event) {
                    marker.setPosition(event.latLng);
                    document.getElementById(id_format + 'latitude').value = event.latLng.lat();
                    document.getElementById(id_format + 'longitude').value = event.latLng.lng();
                    infowindow.setContent('Latitude: ' + event.latLng.lat() + '<br>Longitude: ' + event.latLng.lng());
                    infowindow.open(map, marker);
                });

                google.maps.event.addListener(marker, 'dragend', function(event) {
                    document.getElementById(id_format + 'latitude').value = event.latLng.lat();
                    document.getElementById(id_format + 'longitude').value = event.latLng.lng();
                    infowindow.setContent('Latitude: ' + event.latLng.lat() + '<br>Longitude: ' + event.latLng.lng());
                    infowindow.open(map, marker);
                });

                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();

                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                    /*
                    marker.setIcon(({
                    	url: place.icon,
                    	size: new google.maps.Size(71, 71),
                    	origin: new google.maps.Point(0, 0),
                    	anchor: new google.maps.Point(17, 34),
                    	scaledSize: new google.maps.Size(35, 35)
                    }));
                    */
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                    infowindow.open(map, marker);

                    //Location details
                    for (var i = 0; i < place.address_components.length; i++) {
                        if (place.address_components[i].types[0] == 'postal_code') {
                            document.getElementById('postal_code').innerHTML = place.address_components[i].long_name;
                        }
                        if (place.address_components[i].types[0] == 'country') {
                            document.getElementById('country').innerHTML = place.address_components[i].long_name;
                        }
                    }
                    document.getElementById('location').innerHTML = place.formatted_address;
                    document.getElementById(id_format + 'latitude').value = place.geometry.location.lat();
                    document.getElementById(id_format + 'longitude').value = place.geometry.location.lng();
                });

            }
        </script>

        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_API_KEY') }}&libraries=places&language=en&callback=initialize"
            async defer></script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    function addRow(sectionId) {
                        const div = document.createElement('div');
                        div.classList.add('form-group', 'row');
                        div.innerHTML = `
                            <div class="col-sm-5">
                                <input type="number" name="${sectionId}_deliveries[]" class="form-control" placeholder="No. of Deliveries">
                            </div>
                            <div class="col-sm-5">
                                <input type="number" name="${sectionId}_prices[]" class="form-control" placeholder="Bonus Amount (Rs.)">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger remove-row">-</button>
                            </div>`;
                        document.getElementById(sectionId + '_bonus_div').appendChild(div);
                    }

                    document.getElementById('add_welcome_bonus').addEventListener('click', () => addRow('welcome'));
                    document.getElementById('add_weekly_bonus').addEventListener('click', () => addRow('weekly'));

                    document.addEventListener('click', function (e) {
                        if (e.target.classList.contains('remove-row')) {
                            e.target.closest('.form-group.row').remove();
                        }
                    });
                });
            </script>

<script>
    document.getElementById("banner_form").addEventListener("submit", function(event) {
        var photosInput = document.querySelector(".selected-files");

        if (!photosInput.value) {
            event.preventDefault(); // Prevent form submission
            alert("Please select at least one image before submitting.");
        }
    });
</script>

    @endif
@endsection
