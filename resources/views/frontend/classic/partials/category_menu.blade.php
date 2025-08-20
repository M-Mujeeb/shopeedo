{{-- bg-white --}}
{{-- border-top --}}
<div class="aiz-category-menu rounded-0 " id="category-sidebar" >
    <ul class="list-unstyled categories no-scrollbar mb-0 text-left d-flex ">
        @foreach (get_level_zero_categories()->take(10) as $key => $category)
            @php
                $category_name = $category->getTranslation('name');
            @endphp
            {{-- border border-top-0 --}}
            <li class="category-nav-element " data-id="{{ $category->id }}" >
                <a href="{{ route('products.category', $category->slug) }}"
                    class="text-truncate text-white px-4 fs-14 d-block hov-column-gap-1">
                    {{-- <img class="cat-image lazyload mr-2 opacity-60" src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src="{{ isset($category->catIcon->file_name) ? my_asset($category->catIcon->file_name) : static_asset('assets/img/placeholder.jpg') }}"
                        width="16" alt="{{ $category_name }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"> --}}
                    <span class="cat-name has-transition">{{ $category_name }}</span>
                </a>

                <div class="sub-cat-menu c-scrollbar-light border p-4 shadow-none"
                style="width: 500px;border-radius: 40px; ">
                <div class="c-preloader text-center absolute-center">
                    <i class="las la-spinner la-spin la-3x opacity-70"></i>
                </div>
            </div>

            </li>

        @endforeach
    </ul>
</div>

