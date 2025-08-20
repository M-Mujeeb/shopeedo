<div class="card-columns">
    @foreach ($categories->childrenCategories as $key => $category)
        @php
            $fileName = $category->coverImage->file_name ?? ''; // Set default value if coverImage or file_name is not set
        @endphp
        <div class="card shadow-none border-0 ">

            <ul class="list-unstyled ">
                <li class="fs-14 fw-700 ">
                    <div class="d-flex align-items-center" style="gap:15px">
                        <img src="{{ $fileName ? static_asset($fileName) : static_asset('uploads/all/fruit_and_vegetables.png') }}" width="40" height="40" alt="{{ $category->getTranslation('name') }}" style="object-fit: cover;">
                    <a class="text-reset hov-text-primary" href="{{ route('products.category', $category->slug) }}">
                        {{ $category->getTranslation('name') }}
                    </a>
                  </div>
                </li>
                @if($category->childrenCategories->count())
                    @foreach ($category->childrenCategories as $key => $child_category)
                        <li class="mb-2 fs-14 text-center ml-2 ">
                            @php
                            $fileName = $category->coverImage->file_name ?? ''; // Set default value if coverImage or file_name is not set
                        @endphp
                            <img src="{{ $fileName ? static_asset($fileName) : static_asset('uploads/all/fruit_and_vegetables.png') }}" width="40" height="40" alt="{{ $category->getTranslation('name') }}" style="object-fit: cover;">

                            <a class="text-reset hov-text-primary animate-underline-primary " style="white-space: nowrap !important" href="{{ route('products.category', $child_category->slug) }}">
                                {{ $child_category->getTranslation('name') }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>


    @endforeach
</div>