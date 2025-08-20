<div class="row gutters-16">
    @php
        $physical = false;
        $col_val = 'col-12';
        foreach ($products as $key => $cartItem){
            $product = get_single_product($cartItem);
            if ($product->digital == 0) {
                $physical = true;
                $col_val = 'col-md-6';
            }
        }
    @endphp
    <!-- Product List {{ $col_val }} -->
    <div class="col-md-12">
        <ul class="list-group list-group-flush mb-3">
            @foreach ($products as $key => $cartItem)

                @php
                    // dd($products);
                    $product = get_single_product($cartItem);
                    
                    
                    // dd($product->user->coupons);
                    // geting product coupon 
                    $product_coupons = $product->user->coupons->where('type', 'product_base')
                                      ->where('start_date', '<=', strtotime(date('Y-m-d')))
                                      ->where('end_date', '>=', strtotime(date('Y-m-d')));
                    $filtered_coupons = array();
                    
                    
                    foreach ($product_coupons as $product_coupon) {
                        $details = json_decode($product_coupon->details, true);

                        $filteredDetails = array_filter($details, function ($item) use ($cartItem) {
                            return isset($item['product_id']) && $item['product_id'] == $cartItem;
                        });
                        if($filteredDetails){
                            $coupons_info = ['code' => $product_coupon->code, 'discount' => $product_coupon->discount];
                            array_push($filtered_coupons, $coupons_info);
                        }
                        // dd($filtered_coupons);

                       
                    }
                  
                @endphp
                <li class="list-group-item pl-0 py-3 border-0">
                    <div class="d-flex align-items-center">
                        <span class="mr-2 mr-md-3">
                            <img src="{{ get_image($product->thumbnail) }}"
                                class="img-fit size-60px"
                                alt="{{  $product->getTranslation('name')  }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </span>
                        <span class="fs-14 fw-400 text-dark">
                            <span class="text-truncate-2">{{ $product->getTranslation('name') }}</span>
                            @if ($product_variation[$key] != '')
                                <span class="fs-12 text-secondary">{{ translate('Variation') }}: {{ $product_variation[$key] }}</span>
                            @endif
                        </span> 
                        
                 
                        {{-- @if($filtered_coupons != null)
                        <div class="coupon-container">
                            <div class="coupon-list">
                               
                                @foreach($filtered_coupons as $coupon)
                                    <div class="coupon-card">
                                        <div class="coupon-icon" style="width:100px">
                                            <img src="{{ static_asset('logs/coupons.jpeg') }}" width="80px" alt="Coupon">
                                        </div>
                                        <div class="coupon-details">
                                            <h3>{{ $coupon['code'] }}</h3>
                                            <p>PKR {{ number_format($coupon['discount'], 2) }}</p>
                                            <button class="apply-btn" type="button" data-code="{{ $coupon['code'] }}">Apply</button>
    
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif --}}
                    </div>
                   
                </li>

                
                  
            @endforeach
        </ul>
    </div>

</div>
