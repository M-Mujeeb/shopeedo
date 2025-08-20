<a href="{{ route('wishlists.index') }}" class="d-flex align-items-center p-1 text-dark" data-toggle="tooltip"
    data-title="{{ translate('Wishlist') }}" data-placement="top">
    <span class="position-relative d-inline-block">
        {{-- <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.5333 10.8242C10.619 10.8242 9.06665 12.3485 9.06665 14.229C9.06665 15.7471 9.67332 19.35 15.645 22.9922C15.752 23.0567 15.8748 23.0909 16 23.0909C16.1252 23.0909 16.248 23.0567 16.355 22.9922C22.3267 19.35 22.9333 15.7471 22.9333 14.229C22.9333 12.3485 21.3809 10.8242 19.4667 10.8242C17.5524 10.8242 16 12.8877 16 12.8877C16 12.8877 14.4476 10.8242 12.5333 10.8242Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg> --}}

            <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 1.66699C3.239 1.66699 1 3.738 1 6.29311C1 8.3557 1.875 13.251 10.488 18.1995C10.6423 18.2873 10.8194 18.3337 11 18.3337C11.1806 18.3337 11.3577 18.2873 11.512 18.1995C20.125 13.251 21 8.3557 21 6.29311C21 3.738 18.761 1.66699 16 1.66699C13.239 1.66699 11 4.4707 11 4.4707C11 4.4707 8.761 1.66699 6 1.66699Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                
                


        @if (Auth::check() && count(Auth::user()->wishlists) > 0)
            <span
                class="badge badge-primary badge-inline badge-pill absolute-top-right--10px">{{ count(Auth::user()->wishlists) }}</span>
        @endif
    </span>
</a>
