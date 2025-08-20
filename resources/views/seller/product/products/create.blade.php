@extends('seller.layouts.app')


@section('css')
    <style>
        .btn-soft-primary {
            background-color: #7D9A40;
            color:white;
        }
        .col-from-label {
            font-size: 14px;
            color: #000000;
        }
        .bg-soft-secondary-new {
            background-color:#7EA91B!important;
             color:#FFFFFF;
        }
        input[type="radio"] {
         display: none !important;
        }


    </style>
@endsection
@section('panel_content')
<!-- Bootstrap Modal -->
<div class="modal fade" id="clearconfirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Are you sure you want to clear?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="modal-buttons d-flex justify-content-center gap-3">
            <button type="button" class="btn w-100" style="background-color:rgb(143, 151, 171); color:white" data-bs-dismiss="modal">Cancel</button>
            <button  class="btn btn-danger w-100" onclick="clearTempdata()" style=""> Yes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
    <div class="page-content mx-0">
        <div class="aiz-titlebar mt-2 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3">{{ translate('Add Your Product') }}</h1>
                </div>
                <div class="col text-right">
                    {{-- onclick="clearTempdata()" --}}
                    <button class="btn btn-xs btn-soft-primary"  data-bs-toggle="modal" data-bs-target="#clearconfirmationModal" >
                        {{ translate('Clear All') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Meassages -->
        @if ($errors->any())
            <div class="alert alert-danger " style="padding-bottom:0 !important">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Data type -->
        <input type="hidden" id="data_type" value="physical">

        <form class="" action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    @csrf
                    <input type="hidden" name="added_by" value="seller">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3  col-from-label">{{ translate('Product Name') }} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" value="{{ old('name') }}" class="form-control" name="name"
                                    {{-- onchange="update_sku()" --}}
                                        placeholder="{{ translate('Product Name') }}"  required>
                                </div>
                            </div>
                            <div class="form-group row" id="brand">
                                <label class="col-md-3  col-from-label">{{ translate('Brand') }}</label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id"
                                        data-live-search="true">
                                        <option value="">{{ translate('Select Brand') }}</option>
                                        @foreach (\App\Models\Brand::all() as $brand)
                                            <option value="{{ $brand->id  }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('SKU') }}
                                </label>
                                <div class="col-md-8">
                                    <input type="text" value="{{ old('sku') }}" placeholder="{{ translate('SKU') }}" name="sku"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Unit') }} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="unit" id="unit_id"
                                        data-live-search="true" required>
                                        <option value="" selected disabled>{{ translate('Select Unit') }}</option>
                                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>{{ translate('kg') }}</option>
                                        <option value="pc" {{ old('unit') == 'pc' ? 'selected' : '' }}>{{ translate('pc') }}</option>
                                        <option value="lbs" {{ old('unit') == 'lbs' ? 'selected' : ''  }}>{{ translate('lbs') }}</option>
                                        <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>{{ translate('g') }}</option>
                                        <option value="oz" {{ old('unit') == 'oz' ? 'selected' : '' }}>{{ translate('oz') }}</option>



                                    </select>
                                    {{-- <input type="text" class="form-control" name="unit"
                                        placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" required> --}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Weight') }}
                                    {{-- <small>({{ translate('In Kg') }})</small> --}}
                                </label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="weight" step="0.01" value="{{ old('weight',0.00) }}"
                                        placeholder="Weight (e.g.kg)">
                                </div>
                            </div>
                            <div class="form-group row">
                                {{-- <span class="text-danger">*</span> --}}
                                <label class="col-md-3 col-from-label" style="white-space:nowrap">{{ translate('Minimum Purchase Qty') }} <span class="text-danger">*</span> </label>
                                <div class="col-md-8">
                                    <input type="number" lang="en" class="form-control" name="min_qty" value="{{ old('min_qty', 1) }}"
                                        min="1" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Tags') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control aiz-tag-input" name="tags[]"
                                        placeholder="{{ translate('Type and hit enter to add a tag') }}" value="{{ old('tags') ? implode(',', old('tags')) : '' }}">
                                </div>
                            </div>
                            @if (addon_is_activated('pos_system'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Barcode') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="barcode" value="{{ old('barcode') }}"
                                            placeholder="{{ translate('Barcode') }}">
                                    </div>
                                </div>
                            @endif
                            {{-- @if (addon_is_activated('refund_request'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Refundable') }}</label>
                                    <div class="col-md-8">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="refundable" checked value="1">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @endif --}}
                            <input type="hidden" name="refundable"  value="1">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Description') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                                <div class="col-md-12">
                                    <textarea class="aiz-text-editor" name="description" value ="{{ old('description') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Variation') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{ translate('Colors') }}" disabled>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" name="colors[]"
                                        data-selected-text-format="count" id="colors" multiple  {{ old('colors_active') ? '' : 'disabled' }}>
                                        @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                        <option
                                        value="{{ $color->code }}"
                                        {{ in_array($color->code, old('colors', [])) ? 'selected' : '' }}
                                        data-content="<span>
                                            <span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span>
                                            <span>{{ $color->name }}</span>
                                        </span>">
                                        {{ $color->name }}
                                    </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input value="1" type="checkbox" name="colors_active"
                                        {{ old('colors_active') ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{ translate('Attributes') }}"
                                        disabled>
                                </div>
                                <div class="col-md-8">
                                    <select name="choice_attributes[]" id="choice_attributes"
                                        class="form-control aiz-selectpicker" data-live-search="true"
                                        data-selected-text-format="count" multiple
                                        data-placeholder="{{ translate('Choose Attributes') }}">
                                        @foreach (\App\Models\Attribute::all() as $key => $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                                </p>
                                <br>
                            </div>

                            <div class="customer_choice_options" id="customer_choice_options">

                            </div>
                            <div class="sku_combination" id="sku_combination">

                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product price + stock') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Unit price') }} <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" lang="en" min="0" value="{{ old('unit_price') }}" step="0.01"
                                        placeholder="{{ translate('Unit price') }}" name="unit_price" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label"
                                    for="start_date">{{ translate('Discount Date Range') }} </label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control aiz-date-range" name="date_range" value="{{ old('date_range') }}"
                                        placeholder="{{ translate('Select Date') }}" data-time-picker="true"
                                        data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group row">
                                {{-- <span class="text-danger">*</span> --}}
                                <label class="col-md-3 col-from-label">{{ translate('Discount') }} </label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" value="{{ old('discount', 0) }}" step="0.01"
                                        placeholder="{{ translate('Discount') }}" name="discount" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control aiz-selectpicker" name="discount_type">
                                        <option value="amount" {{ old('discount_type') == 'amount' ? 'selected' : '' }}>{{ translate('Flat') }}</option>
                                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : ''  }}>{{ translate('Percent') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div id="show-hide-div">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="number" lang="en" min="0" value="{{ old('current_stock') }}" step="1"
                                            placeholder="{{ translate('Quantity') }}" name="current_stock"
                                            class="form-control" required>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('External link') }}
                                </label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{ translate('External link') }}"
                                        name="external_link" class="form-control" value="{{ old('external_link') }}">
                                    <small
                                        class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('External link button text') }}
                                </label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{ translate('External link button text') }}"
                                        name="external_link_btn" value="{{ old('external_link_btn') }}" class="form-control">
                                    <small
                                        class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                                </div>
                            </div>
                            <br>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Gallery Images') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary-new font-weight-medium">
                                                {{ translate('Browse') }}
                                            </div>
                                        </div>
                                        <div class="form-control file-amount">
                                            {{ translate('Choose File') }}
                                        </div>
                                        <input type="hidden" name="photos" class="selected-files"
                                               value="{{ old('photos') }}">
                                    </div>
                                    <div class="file-preview box sm">
                                        @if(old('photos', $existingPhotos ?? false))
                                            <!-- Render existing photos preview if old photos or existing photos are set -->
                                            @foreach(explode(',', old('photos')) as $photo)
                                                <div class="file-preview-item">
                                                    <img src="{{ $photo }}" alt="Image preview" style="width: 50px; height: 50px;">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <small class="text-muted">{{translate('These images are visible in product details page gallery. Minimum dimensions required: 900px width X 900px height.')}}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Thumbnail Image') }} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image" required>
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary-new font-weight-medium">
                                                {{ translate('Browse') }}
                                            </div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="thumbnail_img" class="selected-files"
                                               value="{{ old('thumbnail_img') }}" required>
                                    </div>
                                    <div class="file-preview box sm" id="thumbnail">
                                        @if(old('thumbnail_img'))
                                            <!-- Show a preview of the image if an old value or existing photo is set -->
                                            <div class="file-preview-item "  >
                                                <img src="{{ old('thumbnail_img') }}" alt="Image preview" style="width: 50px; height: 50px;">
                                            </div>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{translate("This image is visible in all product box. Minimum dimensions required: 195px width X 195px height. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.")}}</small>
                                    <div id="file-error-thumbnail" class="text-danger" style="display: none;">{{ translate('This field is required.') }}</div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Videos') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Video Provider') }}</label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="video_provider" id="video_provider">
                                        <option value="youtube">{{ translate('Youtube') }}</option>
                                        <option value="dailymotion">{{ translate('Dailymotion') }}</option>
                                        <option value="vimeo">{{ translate('Vimeo') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Video Link') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="video_link"
                                        placeholder="{{ translate('Video Link') }}">
                                </div>
                            </div>
                        </div>
                    </div> --}}


                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Document Specification') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                    for="signinSrEmail">{{ translate('Document Specification') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="document" data-must-be-pdf="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary-new font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="pdf" class="selected-files" >
                                    </div>
                                    <div class="file-preview box sm">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('SEO Meta Tags') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}"
                                        placeholder="{{ translate('Meta Title') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                                <div class="col-md-8">
                                    <textarea name="meta_description" rows="8" class="form-control" value="{{ old('meta_description') }}"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                    for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary-new font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="meta_img" class="selected-files" value="{{ old('meta_img', $existingPhotos ?? '') }}">
                                    </div>
                                    <div class="file-preview box sm">
                                        @if(old('meta_img', $existingPhotos ?? false))
                                        <!-- Show a preview of the image if an old value or existing photo is set -->
                                        <div class="file-preview-item">
                                            <img src="{{ old('meta_img', $existingPhotos) }}" alt="Image preview" style="width: 50px; height: 50px;">
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Frequently Bought Products --}}
                    {{-- <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Frequently Bought') }}</h5>
                        </div>
                        <div class="w-100">
                            <div class="d-flex my-3">
                                <div class="align-items-center d-flex mar-btm ml-4 mr-5 radio">
                                    <input id="fq_bought_select_products" type="radio" name="frequently_bought_selection_type" value="product" onchange="fq_bought_product_selection_type()" checked >
                                    <label for="fq_bought_select_products" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Product')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input id="fq_bought_select_category" type="radio" name="frequently_bought_selection_type" value="category" onchange="fq_bought_product_selection_type()">
                                    <label for="fq_bought_select_category" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Category')}}</label>
                                </div>
                            </div>

                            <div class="px-3 px-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="fq_bought_select_product_div">

                                            <div id="selected-fq-bought-products">

                                            </div>

                                            <button
                                                type="button"
                                                class="btn btn-block border border-dashed hov-bg-soft-secondary-new fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                                onclick="showFqBoughtProductModal()">
                                                <i class="las la-plus"></i>
                                                <span class="ml-2">{{ translate('Add More') }}</span>
                                            </button>
                                        </div>


                                        <div class="fq_bought_select_category_div d-none">
                                            <div class="form-group row">
                                                <label class="col-md-2 col-from-label">{{translate('Category')}}</label>
                                                <div class="col-md-10">
                                                    <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Select a Category')}}" name="fq_bought_product_category_id" data-live-search="true" required>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                                            @foreach ($category->childrenCategories as $childCategory)
                                                                @include('categories.child_category', ['child_category' => $childCategory])
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Category') }} <span class="text-danger">*</span></h5>
                            {{-- <h6 class="float-right fs-13 mb-0">
                                {{ translate('Select Main') }}
                                <span class="position-relative main-category-info-icon">
                                    <i class="las la-question-circle fs-18 text-info"></i>
                                    <span class="main-category-info bg-soft-info p-2 position-absolute d-none border">{{ translate('This will be used for commission based calculations and homepage category wise product Show.') }}</span>
                                </span>
                            </h6> --}}
                        </div>
                        <div class="card-body">
                            <div class="h-300px overflow-auto c-scrollbar-light">
                            {{-- data-radio-name="category_id" --}}
                                <ul class="hummingbird-treeview-converter list-unstyled" data-checkbox-name="category_ids[]"   >
                                    @foreach ($categories as $category)
                                    <li id="{{ $category->id }}">{{ $category->getTranslation('name') }}</li>
                                        @foreach ($category->childrenCategories as $childCategory)
                                            @include('backend.product.products.child_category', ['child_category' => $childCategory])
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                            <div id="category-error" class="text-danger" style="display: none;">{{ translate('This field is required.') }}</div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{ translate('Shipping Configuration') }}
                            </h5>
                        </div>

                        <div class="card-body">
                            @if (get_setting('shipping_type') == 'product_wise_shipping')
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Free Shipping') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="free" checked>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                {{-- <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Flat Rate') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="flat_rate">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flat_rate_shipping_div" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{ translate('Shipping cost') }}</label>
                                        <div class="col-md-6">
                                            <input type="number" lang="en" min="0" value="0"
                                                step="0.01" placeholder="{{ translate('Shipping cost') }}"
                                                name="flat_shipping_cost" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{translate('Is Product Quantity Mulitiply')}}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="is_quantity_multiplied" value="1">
                                            <span></span>
                                        </label>
                                    </div>
                                </div> --}}
                            @else
                                <p>
                                    {{ translate('Shipping configuration is maintained by Admin.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Low Stock Quantity Warning') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{ translate('Quantity') }}
                                </label>
                                <input type="number" name="low_stock_quantity" value="1" min="0"
                                    step="1" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{ translate('Stock Visibility State') }}
                            </h5>
                        </div>

                        <div class="card-body">

                            {{-- <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Show Stock Quantity') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="quantity" checked>
                                        <span></span>
                                    </label>
                                </div>
                            </div> --}}

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Show Stock With Text Only') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="text">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Hide Stock') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="hide">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Cash On Delivery') }}</h5>
                        </div>
                        <div class="card-body">
                            @if (get_setting('cash_payment') == '1')
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Status') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="cash_on_delivery" value="1" checked="">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Cash On Delivery activation is maintained by Admin.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Estimate Shipping Time') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{ translate('Shipping Days') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="est_shipping_days" min="1"
                                        step="1" placeholder="{{ translate('Shipping Days') }}">
                                    {{-- <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">{{ translate('Days') }}</span>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('VAT & Tax') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                <label for="name">
                                    {{ $tax->name }}
                                    <input type="hidden" value="{{ $tax->id }}" name="tax_id[]">
                                </label>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="number" lang="en" min="0" value="0" step="0.01"
                                            placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <select class="form-control aiz-selectpicker" name="tax_type[]">
                                            <option value="amount">{{ translate('Flat') }}</option>
                                            <option value="percent">{{ translate('Percent') }}</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mar-all text-right mb-2">
                        <button type="submit" name="button" value="publish"
                            class="btn btn-primary bg-soft-secondary-new" style="border:none">{{ translate('Save Product') }}</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@section('modal')
	<!-- Frequently Bought Product Select Modal -->
    @include('modals.product_select_modal')
@endsection

@section('script')
<!-- Treeview js -->
<script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

<script type="text/javascript">
    // $(document).ready(function() {
    //     $("#treeview").hummingbird();

    //     $('#treeview input:checkbox').on("click", function (){
    //         let $this = $(this);
    //         if ($this.prop('checked') && ($('#treeview input:radio:checked').length == 0)) {
    //             let val = $this.val();
    //             // $('#treeview input:radio[value='+val+']').prop('checked',true);
    //         }
    //     });
    // });

    $(document).ready(function() {
    // Initialize treeview
    $("#treeview").hummingbird();

    // Retrieve old values from the backend
    var oldValues = @json(old('category_ids', []));

    // Loop through checkboxes and set checked if value is in oldValues
    $('#treeview input:checkbox').each(function() {
        let $this = $(this);
        if (oldValues.includes($this.val())) {
            $this.prop('checked', true);
        }
    });

    // On checkbox click, handle additional logic if needed
    $('#treeview input:checkbox').on("click", function (){
        let $this = $(this);
        if ($this.prop('checked') && ($('#treeview input:radio:checked').length == 0)) {
            let val = $this.val();
            // Additional logic here if needed
        }
    });
});

    // $('#choice_form').on('submit', function(event){
    //     if($('input["category_ids[]":checked]').length === 0){
    //         event.preventDefault();
    //         $('category-error').show();
    //         $('html', 'body').animate({
    //             scrollTop: $('category-error').offset().top - 100

    //         }, 500);
    //     }
    // });

    $('#choice_form').on('submit', function(event) {
        // Hide previous error message
        // $('#category-error').hide();

        // Check if at least one category is selected
        if ($('input[name="category_ids[]"]:checked').length === 0) {
            event.preventDefault(); // Prevent form submission
            $('#category-error').show(); // Show error message
            // Optionally, scroll to the error message
            $('html, body').animate({
                scrollTop: $('#category-error').offset().top - 100
            }, 500);
        }
    });

    $("[name=shipping_type]").on("change", function() {
        $(".product_wise_shipping_div").hide();
        $(".flat_rate_shipping_div").hide();
        if ($(this).val() == 'product_wise') {
            $(".product_wise_shipping_div").show();
        }
        if ($(this).val() == 'flat_rate') {
            $(".flat_rate_shipping_div").show();
        }

    });

    function add_more_customer_choice_option(i, name) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '{{ route('seller.products.add-more-choice-option') }}',
            data: {
                attribute_id: i
            },
            success: function(data) {
                var obj = JSON.parse(data);
                $('#customer_choice_options').append('\
                    <div class="form-group row">\
                        <div class="col-md-3">\
                            <input type="hidden" name="choice_no[]" value="' + i + '">\
                            <input type="text" class="form-control" name="choice[]" value="' + name +
                    '" placeholder="{{ translate('Choice Title') }}" readonly>\
                        </div>\
                        <div class="col-md-8">\
                            <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_' + i + '[]" multiple>\
                                ' + obj + '\
                            </select>\
                        </div>\
                    </div>');
                AIZ.plugins.bootstrapSelect('refresh');
            }
        });


    }
    // old

    $('input[name="colors_active"]').on('change', function() {
        if (!$('input[name="colors_active"]').is(':checked')) {
            $('#colors').prop('disabled', true);
            AIZ.plugins.bootstrapSelect('refresh');
        } else {
            $('#colors').prop('disabled', false);
            AIZ.plugins.bootstrapSelect('refresh');
        }
        update_sku();
    });

    // $('#choice_form').on('submit', function (e) {

    //         // Check if the #front-side-pic preview is empty
    //         if ($('#thumbnail').is(':empty')) {
    //             // Show error message
    //             $('#file-error-thumbnail').show();

    //             // Prevent form submission
    //             e.preventDefault();
    //         } else {
    //             // Hide error message if preview is not empty
    //             $('#file-error-thumbnail').hide();
    //         }
    //     });

    $('#choice_form').on('submit', function (e) {
    // Check if the hidden input is empty or undefined
    let thumbnailValue = $('input[name="thumbnail_img"]').val();

    if (!thumbnailValue || thumbnailValue.trim() === '') {
        // Show error message
        $('#file-error-thumbnail').show();
        $('html, body').animate({
            scrollTop: $('#thumbnail').offset().top - 150 // 150px offset from top to give some space
        }, 500);
        // Prevent form submission
        e.preventDefault();
        return false;
    } else {
        // Hide error message if value exists
        $('#file-error-thumbnail').hide();
    }
});

    // new

//     $('input[name="product_variation"]').on('change', function() {
//     if (!$('input[name="product_variation"]').is(':checked')) {
//         // Disable both Colors and Attributes
//         $('#colors').prop('disabled', true);
//         $('#choice_attributes').prop('disabled', true);
//         AIZ.plugins.bootstrapSelect('refresh');
//     } else {
//         // Enable both Colors and Attributes
//         $('#colors').prop('disabled', false);
//         $('#choice_attributes').prop('disabled', false);
//         AIZ.plugins.bootstrapSelect('refresh');
//     }
//     update_sku();
// });


    $(document).on("change", ".attribute_choice", function() {
        update_sku();
    });

    $('#colors').on('change', function() {
            update_sku();
        });

    $('input[name="unit_price"]').on('keyup', function() {
        update_sku();
    });

    // $('input[name="name"]').on('keyup', function() {
    //     update_sku();
    // });

    function delete_row(em) {
        $(em).closest('.form-group row').remove();
        update_sku();
    }

    function delete_variant(em) {
        $(em).closest('.variant').remove();
    }

    function update_sku() {
        $.ajax({
            type: "POST",
            url: '{{ route('seller.products.sku_combination') }}',
            data: $('#choice_form').serialize(),
            success: function(data) {
                $('#sku_combination').html(data);
                AIZ.uploader.previewGenerate();
                AIZ.plugins.sectionFooTable('#sku_combination');
                // if (data.trim().length > 1) {
                //     $('#show-hide-div').hide();
                // } else {
                //     $('#show-hide-div').show();
                // }
            }
        });
    }

    $('#choice_attributes').on('change', function() {
        $('#customer_choice_options').html(null);
        $.each($("#choice_attributes option:selected"), function() {
            add_more_customer_choice_option($(this).val(), $(this).text());
        });
        update_sku();
    });

    function fq_bought_product_selection_type(){
        var productSelectionType = $("input[name='frequently_bought_selection_type']:checked").val();
        if(productSelectionType == 'product'){
            $('.fq_bought_select_product_div').removeClass('d-none');
            $('.fq_bought_select_category_div').addClass('d-none');
        }
        else if(productSelectionType == 'category'){
            $('.fq_bought_select_category_div').removeClass('d-none');
            $('.fq_bought_select_product_div').addClass('d-none');
        }
    }

    function showFqBoughtProductModal() {
        $('#fq-bought-product-select-modal').modal('show', {backdrop: 'static'});
    }

    function filterFqBoughtProduct() {
        var searchKey = $('input[name=search_keyword]').val();
        var fqBroughCategory = $('select[name=fq_brough_category]').val();
        $.post('{{ route('seller.product.search') }}', { _token: AIZ.data.csrf, product_id: null, search_key:searchKey, category:fqBroughCategory, product_type:"physical" }, function(data){
            $('#product-list').html(data);
            AIZ.plugins.sectionFooTable('#product-list');
        });
    }

    function addFqBoughtProduct() {
        var selectedProducts = [];
        $("input:checkbox[name=fq_bought_product_id]:checked").each(function() {
            selectedProducts.push($(this).val());
        });

        var fqBoughtProductIds = [];
        $("input[name='fq_bought_product_ids[]']").each(function() {
            fqBoughtProductIds.push($(this).val());
        });

        var productIds = selectedProducts.concat(fqBoughtProductIds.filter((item) => selectedProducts.indexOf(item) < 0))

        $.post('{{ route('seller.get-selected-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
            $('#fq-bought-product-select-modal').modal('hide');
            $('#selected-fq-bought-products').html(data);
            AIZ.plugins.sectionFooTable('#selected-fq-bought-products');
        });
    }

    // this function is in include file but due to changes I put it here
    function clearTempdata() {
        var data_type = $('#data_type').val();
        localStorage.setItem('tempdataproduct_'+data_type, '{}');
		localStorage.setItem('tempload_'+data_type, 'no');
        isFormModified = false;
        isSubmitting = false;
		location.reload();
    }



</script>

{{-- @include('partials.product.product_temp_data') --}}

@endsection
