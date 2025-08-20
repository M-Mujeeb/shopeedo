<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>
            @php
                $title = request()->segment(1) == 'seller' && request()->segment(2) == 'index' ? 'Shopeedo | Seller' : 'Shopeedo | Rider';
            @endphp
            {{ $title }}
        </title>
        <!-- Favicon -->
        <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        {{-- <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> --}}
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">

        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
        @if(get_system_language()->rtl == 1)
        <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
        @endif
        <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
        <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        @yield('style')

    </head>
<body>

    <img src="{{ static_asset('uploads/all/LRqvBfVfu5dGBXoBff6VeCxARwAydT4yMj9a1QMD.png') }}" class="mw-100 h-30px h-md-40px m-5" alt="">

    @yield('content')

    @include('seller.inc.seller_footer')

</body>
</html>
