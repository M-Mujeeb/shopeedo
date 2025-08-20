<!doctype html>
@if(\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Favicon -->
	<link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
	<title>{{ get_setting('website_name').' | '.get_setting('site_motto') }}</title>

	<!-- google font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
    @if(Request::is('seller/product/create'))
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif



	<!-- aiz core css -->
	<link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if(\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
	<link rel="stylesheet" href="{{ static_asset('assets/css/aiz-seller.css') }}">



    <style>
        body {
            font-size: 12px;
        }
        #map{
            width: 100%;
            height: 250px;
        }
        #edit_map{
            width: 100%;
            height: 250px;
        }
        .pac-container{
            z-index: 100000;
        }
        .badge-info {
            background-color:#F0AD4E;
        }
        #bulk-delete-btn {
            display: none;
        }

        .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        padding: 5px;
        margin: 0;
        list-style: none;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
        padding: 2 0.5rem;
        color: #6c757d;
    }
    .breadcrumb {
    background-color: transparent !important;
}
.aiz-mobile-toggler span:after {
    bottom: -7px ;
}


    </style>

    @yield('css')
	<script>
    	var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
	</script>

</head>
<body class="">

	<div class="aiz-main-wrapper">
        @include('seller.inc.seller_sidenav')
		<div class="aiz-content-wrapper">
            @include('seller.inc.seller_nav')
			<div class="aiz-main-content">
				<div class="px-15px px-lg-25px">
                    @yield('panel_content')
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto border-sm-top">
					<p class="mb-0">&copy; {{ get_setting('site_name') }} v{{ get_setting('current_version') }}</p>
				</div>
			</div><!-- .aiz-main-content -->
		</div><!-- .aiz-content-wrapper -->
	</div><!-- .aiz-main-wrapper -->

    @yield('modal')


	<script src="{{ static_asset('assets/js/vendors.js') }}" ></script>
	<script src="{{ static_asset('assets/js/aiz-core.js') }}" ></script>


    @if(Request::is('seller/product/create'))
    <!-- Bootstrap JS and dependencies -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> --}}
    @endif
<script type="text/javascript">

	    @foreach (session('flash_notification', collect())->toArray() as $message)
	        AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}', );
            @if ($message['message'] == translate('Product has been inserted successfully'))
                localStorage.setItem('tempdataproduct', '{}');
                localStorage.setItem('tempload', 'no');
            @endif

	    @endforeach

        $('.dropdown-menu a[data-toggle="tab"]').click(function(e) {
            e.stopPropagation()
            $(this).tab('show')
        });

        if ($('#lang-change').length > 0) {
            $('#lang-change .dropdown-menu a').each(function() {
                $(this).on('click', function(e){
                    e.preventDefault();
                    var $this = $(this);
                    var locale = $this.data('flag');
                    $.post('{{ route('language.change') }}',{_token:'{{ csrf_token() }}', locale:locale}, function(data){
                        location.reload();
                    });

                });
            });
        }
        function menuSearch(){
			var filter, item;
			filter = $("#menu-search").val().toUpperCase();
			items = $("#main-menu").find("a");
			items = items.filter(function(i,item){
				if($(item).find(".aiz-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item).attr('href') !== '#'){
					return item;
				}
			});

			if(filter !== ''){
				$("#main-menu").addClass('d-none');
				$("#search-menu").html('')
				if(items.length > 0){
					for (i = 0; i < items.length; i++) {
						const text = $(items[i]).find(".aiz-side-nav-text")[0].innerText;
						const link = $(items[i]).attr('href');
						 $("#search-menu").append(`<li class="aiz-side-nav-item"><a href="${link}" class="aiz-side-nav-link"><i class="las la-ellipsis-h aiz-side-nav-icon"></i><span>${text}</span></a></li`);
					}
				}else{
					$("#search-menu").html(`<li class="aiz-side-nav-item"><span	class="text-center text-muted d-block">{{ translate('Nothing Found') }}</span></li>`);
				}
			}else{
				$("#main-menu").removeClass('d-none');
				$("#search-menu").html('')
			}
        }

         // Flag to track whether the form has been modified
        let isFormModified = false;
        let isSubmitting = false;

        // Select your input fields (you can adjust the selector to target specific fields)
        const inputs = document.querySelectorAll('input, textarea, select, checkbox');


        // Set the flag to true if any input field is modified
        inputs.forEach((input) => {
    // Use 'input' event for text fields and textareas
        if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
            input.addEventListener('input', () => {
                isFormModified = true;
            });
        }
        // Use 'change' event for selects
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', () => {
                isFormModified = true;
            });
        }
});
          // Set the isSubmitting flag to true when the form is submitted
          const form = document.querySelector('form');
        form.addEventListener('submit', () => {
            isSubmitting = true;
        });

        // Show a confirmation dialog when the user tries to leave
        window.addEventListener('beforeunload', (event) => {
            if (isFormModified && !isSubmitting) {
                // Prevent the default action (leaving the page)
                event.preventDefault();

                var data_type = $('#data_type').val();
                localStorage.setItem('tempdataproduct_' + data_type, '{}');
                localStorage.setItem('tempload_' + data_type, 'no');

                // Modern browsers require this for showing a confirmation dialog
                event.returnValue = '';
            }
        });

</script>
    @yield('script')

</body>
</html>
