@php
    $cart_added = [];
@endphp
<div class="aiz-card-box h-auto bg-white py-3 hov-scale-img">
    <div class="position-relative h-140px h-md-200px img-fit overflow-hidden">
        @php
            $product_url = route('product', $product->slug);
            if ($product->auction_product == 1) {
                $product_url = route('auction-product', $product->slug);
            }
        @endphp
        <!-- Image -->
        <a href="{{ $product_url }}" class="d-block h-100">
            <img class="lazyload mx-auto img-fit has-transition"
                src="{{ get_image($product->thumbnail) }}"
                alt="{{ $product->getTranslation('name') }}" title="{{ $product->getTranslation('name') }}"
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
        </a>
        <!-- Discount percentage tag -->
        @if (discount_in_percentage($product) > 0)
            <span class="absolute-top-left bg-primary ml-1 mt-1 fs-11 fw-700 text-white w-35px text-center"
                style="padding-top:2px;padding-bottom:2px;">-{{ discount_in_percentage($product) }}%</span>
        @endif
        <!-- Wholesale tag -->
        @if ($product->wholesale_product)
            <span class="absolute-top-left fs-11 text-white fw-700 px-2 lh-1-8 ml-1 mt-1"
                style="background-color: #455a64; @if (discount_in_percentage($product) > 0) top:25px; @endif">
                {{ translate('Wholesale') }}
            </span>
        @endif
        @if ($product->auction_product == 0)
            <!-- wishlisht & compare icons -->
            <div class="absolute-top-right aiz-p-hov-icon">
                <a href="javascript:void(0)" class="hov-svg-white" onclick="addToWishList({{ $product->id }})"
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
                {{-- <a href="javascript:void(0)" class="hov-svg-white" onclick="addToCompare({{ $product->id }})"
                    data-toggle="tooltip" data-title="{{ translate('Add to compare') }}" data-placement="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path id="_9f8e765afedd47ec9e49cea83c37dfea" data-name="9f8e765afedd47ec9e49cea83c37dfea"
                            d="M18.037,5.547v.8a.8.8,0,0,1-.8.8H7.221a.4.4,0,0,0-.4.4V9.216a.642.642,0,0,1-1.1.454L2.456,6.4a.643.643,0,0,1,0-.909L5.723,2.227a.642.642,0,0,1,1.1.454V4.342a.4.4,0,0,0,.4.4H17.234a.8.8,0,0,1,.8.8Zm-3.685,4.86a.642.642,0,0,0-1.1.454v1.661a.4.4,0,0,1-.4.4H2.84a.8.8,0,0,0-.8.8v.8a.8.8,0,0,0,.8.8H12.854a.4.4,0,0,1,.4.4V17.4a.642.642,0,0,0,1.1.454l3.267-3.268a.643.643,0,0,0,0-.909Z"
                            transform="translate(-2.037 -2.038)" fill="#919199" />
                    </svg>
                </a> --}}
            </div>
            <!-- add to cart -->
            <a class="cart-btn absolute-bottom-left w-100 h-35px aiz-p-hov-icon text-white fs-13 fw-700 d-flex flex-column justify-content-center align-items-center @if (in_array($product->id, $cart_added)) active @endif"
                href="javascript:void(0)"
                style="background-color:#7D9A40"
                onclick="showAddToCartModal({{ $product->id }})">
                <span class="cart-btn-text">
                    {{ translate('Add to Cart') }}
                </span>
                <span><i class="las la-2x la-shopping-cart"></i></span>
            </a>
        @endif
        @if (
            $product->auction_product == 1 &&
                $product->auction_start_date <= strtotime('now') &&
                $product->auction_end_date >= strtotime('now'))
            <!-- Place Bid -->
            @php
                $carts = get_user_cart();
                if (count($carts) > 0) {
                    $cart_added = $carts->pluck('product_id')->toArray();
                }
                $highest_bid = $product->bids->max('amount');
                $min_bid_amount = $highest_bid != null ? $highest_bid + 1 : $product->starting_bid;
            @endphp
            <a class="cart-btn absolute-bottom-left w-100 h-35px aiz-p-hov-icon text-white fs-13 fw-700 d-flex flex-column justify-content-center align-items-center @if (in_array($product->id, $cart_added)) active @endif"
                href="javascript:void(0)" onclick="bid_single_modal({{ $product->id }}, {{ $min_bid_amount }})">
                <span class="cart-btn-text">{{ translate('Place Bid') }}</span>
                <span><i class="las la-2x la-gavel"></i></span>
            </a>
        @endif
    </div>
    {{-- p-2 p-md-3  --}}
    <div class="pt-1 pl-1 text-left">
        <!-- Product name -->
        {{-- text-center --}}
        <h3 class="fw-400  fs-20 text-truncate-2 lh-1-4 mb-0 h-35px ">
            <a href="{{ $product_url }}" class="d-block fw-500 text-reset hov-text-primary"
                title="{{ $product->getTranslation('name') }}">{{ $product->getTranslation('name') }}</a>
        </h3>
        {{-- justify-content-center mt-3 --}}
        <div class="fs-14 d-flex  ">
            @if ($product->auction_product == 0)
                <!-- Previous price -->
                @if (home_base_price($product) != home_discounted_base_price($product))
                    <div class="disc-amount has-transition">
                        <del class="fw-400 text-secondary mr-1">{{ home_base_price($product) }}</del>
                    </div>
                @endif
                <!-- price -->
                <div class="">
                    <span class="fw-700 text-primary">{{ home_discounted_base_price($product) }}</span>
                </div>

            @endif
            @if ($product->auction_product == 1)
                <!-- Bid Amount -->
                <div class="">
                    <span class="fw-700 text-primary">{{ single_price($product->starting_bid) }}</span>
                </div>
            @endif
        </div>
        {{-- <h1>{{ $product->average_rating }}</h1>
        <h2>{{  $product->total_review }}</h2> --}}
        <div class="d-flex justify-content-between mt-1">
            @if($product->average_rating == 5)
            <div class="d-flex align-items-center" style="gap: 4px">

                <img src="{{static_asset('uploads/all/five_star_img.png')}}" alt="">

                <span class="">({{   $product->total_review }})</span>

            </div>
            @elseif($product->average_rating == 4)
            <div class="d-flex align-items-center" style="gap: 4px">

                <img src="{{static_asset('uploads/all/four_star_img.png')}}" alt="">

                <span class="">({{   $product->total_review }})</span>
            </div>
            @elseif($product->average_rating == 3)
            <div class="d-flex align-items-center" style="gap: 4px">

                <img src="{{static_asset('uploads/all/three_star_img.png')}}" alt="">

            <span class="">({{   $product->total_review }})</span>
            </div>
            @elseif($product->average_rating == 2)
            <div class="d-flex align-items-center" style="gap: 4px">

                <img src="{{static_asset('uploads/all/two_star_img.png')}}" alt="">

                <span class="">({{   $product->total_review }})</span>
            </div>

            @elseif($product->average_rating == 1)
            <div class="d-flex align-items-center" style="gap: 4px">

                <img src="{{static_asset('uploads/all/one_star_img.png')}}" alt="">

                <span class="">({{   $product->total_review }})</span>
            </div>
            @else
               
                <div  class="d-flex align-items-center" style="gap:4px">
                <img src="{{static_asset('uploads/all/no_star_img.png')}}" alt="">
                <span class="">({{ 0 }})</span>
                </div>
            @endif


            <svg width="40" height="23" viewBox="0 0 40 23" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.37074 0.17185C2.50329 0.609486 2.40169 1.8208 3.18319 2.41474L3.45671 2.62574L5.27759 2.66482L7.09066 2.70389L7.39544 2.94615C7.95812 3.39161 8.11441 4.02462 7.80963 4.61855C7.52048 5.18904 7.30166 5.27501 6.01219 5.3219C4.85558 5.36879 4.66802 5.41568 4.30854 5.7439C3.69897 6.29095 3.76931 7.26 4.45702 7.76016C4.75399 7.97898 5.16818 8.04931 6.17631 8.05713C6.98906 8.05713 7.1141 8.07276 7.34855 8.23687C8.24727 8.84644 8.13786 10.1046 7.14536 10.5657C6.85621 10.6986 6.58268 10.7142 3.91779 10.7142C0.682404 10.7142 0.643329 10.722 0.268212 11.2222C-0.231944 11.8708 -0.00531088 12.8555 0.721479 13.2072C1.04971 13.3635 1.17475 13.3713 4.0272 13.3713C6.94999 13.3713 6.99688 13.3791 7.29385 13.551C7.66115 13.7542 7.96593 14.2778 7.96593 14.6998C7.96593 15.1453 7.66115 15.6376 7.26259 15.8486C6.9578 16.0127 6.82495 16.0284 5.83245 16.0284C4.87903 16.0284 4.69147 16.0518 4.3945 16.1925C3.46452 16.6379 3.43326 17.9977 4.33198 18.5057C4.64458 18.6854 4.66021 18.6854 7.45014 18.662L10.2557 18.6464L10.5058 18.0524C11.2482 16.2784 12.8268 15.0671 14.7024 14.8327C15.9919 14.6686 17.125 15.0359 18.0081 15.9033C18.7271 16.6145 19.1413 17.5992 19.1413 18.6073V18.998H23.1113H27.0735L27.2141 18.5057C27.6596 16.9896 29.0741 15.5595 30.6605 15.0359C31.5123 14.7545 32.7627 14.7389 33.4739 14.9968C34.9197 15.5282 35.834 16.6926 36.0059 18.2322L36.0606 18.7245L36.4123 18.7011C37.1704 18.6464 37.8581 18.1853 38.4051 17.3647C39.0303 16.4191 39.2179 15.7627 39.7337 12.6758C40.0228 10.9799 40.0619 10.4016 39.9212 9.89364C39.7884 9.43256 39.2335 8.21343 38.6786 7.19748C37.522 5.08745 36.764 4.1731 35.7246 3.6495C34.8806 3.22749 34.3257 3.1259 32.4736 3.07119L30.8403 3.01649L30.7465 2.58667C30.4808 1.26594 29.457 0.351593 28.0034 0.148405C27.5892 0.0937004 23.4786 0.0546246 15.5464 0.0311813L3.70679 -8.01086e-05L3.37074 0.17185ZM14.3038 5.09526C14.4992 5.29064 14.4523 5.59542 14.2023 5.80642C14.0694 5.92365 13.8818 5.94709 13.0456 5.94709H12.0453L11.9984 6.25969C11.975 6.43162 11.9281 6.71296 11.9047 6.88489L11.8578 7.19748H12.6236C13.2254 7.19748 13.4129 7.22874 13.5302 7.33034C13.6943 7.47882 13.7021 7.58823 13.5849 7.84612C13.452 8.12746 13.1941 8.21343 12.4283 8.21343H11.7327L11.6937 8.4635C11.6624 8.60417 11.6077 9.01055 11.553 9.37004C11.4436 10.1359 11.2795 10.4016 10.9043 10.4016C10.5918 10.4016 10.4667 10.2844 10.4667 9.9796C10.4667 9.54197 11.0841 5.40786 11.1701 5.24375C11.3107 4.97804 11.5217 4.93896 12.8737 4.93896C14.0538 4.93115 14.1475 4.93896 14.3038 5.09526ZM18.1019 5.04837C18.3598 5.12652 18.5786 5.25156 18.7037 5.40786C19.3679 6.19717 19.0084 7.74453 18.0159 8.33065C17.8909 8.40098 17.8987 8.4635 18.1175 9.0887C18.4067 9.90145 18.4145 9.96397 18.1644 10.2062C17.9534 10.4251 17.5705 10.4641 17.4298 10.2922C17.3751 10.2297 17.2032 9.81549 17.0469 9.37004L16.7577 8.5651L16.1951 8.54165C15.5621 8.51821 15.6089 8.45569 15.4995 9.42474C15.4448 9.94834 15.3042 10.2766 15.1166 10.3547C14.8587 10.4485 14.6086 10.4016 14.4914 10.2219C14.3898 10.0578 14.4054 9.8233 14.7024 7.70545C15.1166 4.72796 14.9759 4.93115 16.578 4.93115C17.3595 4.93115 17.8284 4.97022 18.1019 5.04837ZM23.3145 5.03274C23.4942 5.1656 23.502 5.51727 23.3223 5.71264C23.1973 5.8455 23.0566 5.86894 22.0485 5.88457L20.9075 5.90802L20.8606 6.10339C20.8293 6.2128 20.7824 6.46288 20.759 6.66607L20.7121 7.02555L21.6108 7.05681C22.4001 7.08026 22.5252 7.1037 22.6268 7.22874C22.7831 7.45538 22.7596 7.65075 22.5408 7.86175C22.3611 8.04931 22.3063 8.05713 21.4623 8.05713H20.5636L20.4777 8.64325C20.4308 8.97148 20.3917 9.28407 20.3917 9.34659C20.3917 9.44819 20.5792 9.46382 21.5092 9.46382C22.7362 9.46382 22.8143 9.48726 22.8143 9.90145C22.8143 10.3625 22.658 10.4016 21.0013 10.4016C19.407 10.4016 19.2976 10.3703 19.2976 9.94834C19.2976 9.51071 19.9072 5.41568 19.9931 5.25156C20.0478 5.15778 20.1651 5.04837 20.2588 5.0093C20.548 4.89207 23.1504 4.91552 23.3145 5.03274ZM27.5971 5.064C27.7846 5.22812 27.7768 5.51727 27.5814 5.71264C27.4408 5.85331 27.3235 5.86894 26.2998 5.86894C25.0181 5.86894 25.1275 5.80642 25.0338 6.58792L24.9869 7.04118H25.7527C26.7999 7.04118 27.0578 7.18967 26.925 7.69764C26.8546 7.97116 26.5499 8.05713 25.6668 8.05713C25.2135 8.05713 24.8462 8.06494 24.8384 8.07276C24.8384 8.08839 24.7915 8.40098 24.7368 8.7761L24.643 9.46382H25.7527C26.6593 9.46382 26.8859 9.48726 26.9875 9.58886C27.1594 9.75297 27.1438 10.0265 26.9562 10.2297C26.7999 10.3938 26.7374 10.4016 25.276 10.4016C23.7756 10.4016 23.7521 10.4016 23.6349 10.2219C23.5333 10.0578 23.5489 9.83112 23.8537 7.66638C24.2835 4.66544 24.0725 4.93115 26.0341 4.93115C27.2454 4.93115 27.472 4.94678 27.5971 5.064ZM33.849 5.01711C34.4273 5.14215 35.0525 5.46257 35.4276 5.82987C35.9747 6.36128 37.5767 9.11214 37.5846 9.52634C37.5846 9.7686 37.3892 10.1281 37.1782 10.2766C37.0219 10.386 36.5452 10.4016 33.5052 10.4016H30.0119L30.0431 10.1828C30.09 9.94834 30.7074 5.08745 30.7074 4.99367C30.7074 4.89989 33.3801 4.92334 33.849 5.01711Z" fill="#7D9A40"/>
            <path d="M15.984 6.04053C15.9214 6.30624 15.7808 7.33 15.7808 7.46285C15.7808 7.56445 15.9058 7.58789 16.5466 7.58789C17.375 7.58008 17.5782 7.51756 17.8205 7.15025C17.9924 6.89236 18.0158 6.35313 17.8674 6.14994C17.7345 5.9702 17.2812 5.87642 16.5857 5.8686C16.0621 5.8686 16.0152 5.88423 15.984 6.04053Z" fill="#7D9A40"/>
            <path d="M1.12003 5.54804C0.424499 5.97005 0.22131 6.86877 0.674577 7.50178C0.862135 7.75967 1.44044 8.05664 1.75304 8.05664C2.06564 8.05664 2.64394 7.75967 2.8315 7.50178C3.44888 6.64995 2.81587 5.39956 1.76085 5.39956C1.51078 5.39956 1.27633 5.45426 1.12003 5.54804Z" fill="#7D9A40"/>
            <path d="M14.038 16.4977C12.5219 17.0369 11.5294 18.4749 11.6623 19.9363C11.8889 22.4683 15.2103 23.0232 16.8905 20.8037C18.0236 19.3189 17.6954 17.3417 16.1949 16.5993C15.5932 16.3101 14.6867 16.2633 14.038 16.4977Z" fill="#7D9A40"/>
            <path d="M31.1452 16.4265C30.2778 16.6375 29.2384 17.5049 28.8633 18.3333C28.4178 19.3024 28.4413 20.2167 28.9336 21.0373C29.5354 22.0454 30.7623 22.444 32.0283 22.0532C32.9818 21.7563 33.732 21.0998 34.1774 20.1698C34.3963 19.7088 34.4197 19.5915 34.4197 18.8804C34.4197 18.1848 34.3963 18.0598 34.2087 17.7081C33.7554 16.8563 33.0286 16.4109 32.0361 16.3718C31.7157 16.3562 31.3172 16.3796 31.1452 16.4265Z" fill="#7D9A40"/>
            </svg>


        </div>
    </div>
</div>
