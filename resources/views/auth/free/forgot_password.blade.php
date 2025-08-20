@extends('auth.layouts.authentication')

@section('css')
    <style>
        .btn-primary:hover {
            background-color: #7D9A40 !important;
        }
        .back-btn a:hover {
            color: #7D9A40;
        }
        .back-btn {
            border: 1px solid #7D9A40;
            width: 100%;
        }
        .field-radius {
            border-radius: 8px !important;
        }
    </style>
@endsection

@section('content')
@if(Session::has('success'))
    <div id="toast-success" style="display: none;">{{ Session::get('success') }}</div>
@endif

@if(Session::has('error'))
    <div id="toast-error" style="display: none;">{{ Session::get('error') }}</div>
@endif
 <section class="bg-white overflow-hidden" style="min-height:100vh;">
    <div class="container">
        <div class="row" style="min-height: 100vh; ">
            <!-- Left Side Image-->
            <div class="col-xxl-6 col-lg-7 p-0 p-md-3">
                <div class="" >
                    <div class="d-md-none  " style="background-color: #7D9A40; height: 350px " >
                        <svg class="ml-3 md-query" style="margin-top:30%" width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="72" height="72" rx="8" fill="white"/>
                            <path d="M20.6859 16.1976C20.41 16.523 20.5151 16.9724 21.3295 19.1883C22.2621 21.7141 22.4591 22.024 22.9845 21.807C23.5361 21.5901 23.5099 21.0942 22.8269 19.2812C22.4985 18.398 22.2358 17.6542 22.2358 17.6232C22.2358 17.5922 23.0896 17.5612 24.1272 17.5612H26.0187L26.4784 18.8319C27.6605 22.1479 34.3331 40.3863 34.4644 40.6963C34.5564 40.8822 34.6877 41.0836 34.7666 41.1456C34.8454 41.2076 41.137 41.2696 48.7422 41.2696C62.3894 41.2696 62.5602 41.2696 62.8229 40.9597C63.1512 40.5723 63.1512 40.3089 62.8491 39.9835C62.6258 39.7355 61.4831 39.72 49.1362 39.72C38.5232 39.72 35.6335 39.6735 35.5415 39.5186C35.4102 39.3481 33.7552 34.8544 29.1711 22.2099C28.002 19.0178 26.9644 16.3061 26.8593 16.1976C26.5703 15.9342 20.9092 15.9342 20.6859 16.1976Z" fill="#7EA91B"/>
                            <path d="M33.4665 22.475C32.5996 22.5679 32.3763 22.6609 32.2844 22.9708C32.153 23.4512 33.9 28.0379 34.2809 28.2394C34.5042 28.3633 39.1934 28.4098 49.4649 28.4098C65.6998 28.4098 64.9248 28.4563 64.9248 27.5575C64.9248 26.6588 65.6735 26.7053 49.6225 26.7053H34.9114L34.4911 25.4966C34.2546 24.8458 34.0576 24.257 34.0576 24.1795C34.0576 24.1175 41.0585 24.071 49.6225 24.071H65.1744L65.4239 23.7456C65.6998 23.4047 65.6735 22.9553 65.3451 22.6454C65.1875 22.4905 62.0351 22.444 49.57 22.4285C41.006 22.4285 33.7555 22.444 33.4665 22.475Z" fill="#7EA91B"/>
                            <path d="M15.0123 24.2556C10.9274 24.4105 7.45972 24.581 7.3021 24.6275C7.10508 24.6739 7 24.8134 7 24.9993C7 25.1698 7.0394 25.3093 7.10508 25.3093C7.15762 25.3093 10.9536 25.4797 15.5377 25.6967C20.1218 25.9136 24.128 26.084 24.4433 26.084H24.9949V24.9993V23.9147L23.7208 23.9301C23.0115 23.9456 19.0973 24.0851 15.0123 24.2556Z" fill="#7EA91B"/>
                            <path d="M22.3021 27.6496C21.0805 27.7115 18.3222 27.8355 16.1943 27.9285C11.1242 28.1609 10.9272 28.1919 11.1899 28.7033C11.2687 28.8427 11.9386 28.9357 13.7118 29.0442C18.401 29.3076 25.1393 29.6485 25.6515 29.6485H26.1769V28.5638V27.4791L25.3625 27.5101C24.9028 27.5101 23.5368 27.5721 22.3021 27.6496Z" fill="#7EA91B"/>
                            <path d="M35.7394 31.2138C35.608 31.2913 35.5029 31.5392 35.5029 31.7561C35.5029 32.252 37.0397 36.4823 37.3156 36.7303C37.4732 36.8852 40.2841 36.9317 50.3455 36.9317C62.6135 36.9317 63.1915 36.9162 63.3885 36.6528C63.6775 36.2809 63.6643 35.9555 63.3754 35.6456C63.1521 35.3976 62.075 35.3821 50.6476 35.3821H38.1693L37.6833 34.127C37.4206 33.4452 37.2105 32.8099 37.2105 32.7324C37.2105 32.6549 42.5958 32.5929 50.6476 32.5929C60.9191 32.5929 64.1241 32.5464 64.2423 32.407C64.4787 32.128 64.4393 31.5857 64.1635 31.3068C63.9402 31.0588 62.7711 31.0433 49.9383 31.0433C40.6912 31.0588 35.8838 31.1053 35.7394 31.2138Z" fill="#7EA91B"/>
                            <path d="M17.1143 32.1427C13.1738 32.2821 9.31209 32.4216 8.53713 32.4216C7.38125 32.4371 7.10542 32.4836 7.03974 32.685C7.00034 32.809 7.01347 32.9794 7.07915 33.0569C7.14482 33.1344 11.0065 33.3358 15.6694 33.5063C20.3323 33.6767 25.0084 33.8627 26.0855 33.9247L28.0163 34.0176V32.9174V31.8173L26.1511 31.8327C25.1135 31.8482 21.0548 31.9877 17.1143 32.1427Z" fill="#7EA91B"/>
                            <path d="M25.6515 35.7065C24.2461 35.7685 21.1068 35.8925 18.69 35.9854C13.2127 36.2024 12.9106 36.2489 12.9106 36.6208C12.9106 37.0237 12.6085 37.0082 27.5298 37.659L29.8547 37.752V36.6518V35.5361L29.0404 35.5671C28.5806 35.5671 27.057 35.629 25.6515 35.7065Z" fill="#7EA91B"/>
                            <path d="M27.4257 39.6739C26.9923 39.6894 23.2751 39.8134 19.1507 39.9528C9.01049 40.2782 9.64097 40.2317 9.79859 40.7121C9.89053 40.991 10.6655 41.0375 19.8731 41.3474C23.8793 41.4869 28.1219 41.6264 29.304 41.6883L31.4319 41.7813V40.6811V39.5654L29.8294 39.5964C28.9363 39.6119 27.8592 39.6429 27.4257 39.6739Z" fill="#7EA91B"/>
                            <path d="M37.5252 44.1371C37.1574 44.4316 36.1592 46.3995 36.1592 46.8179C36.1592 47.0038 36.3037 47.2673 36.4744 47.3912C36.921 47.7476 37.2231 47.5152 37.7354 46.4615L38.1426 45.6092H46.1155H54.0884L54.4431 46.353C54.9553 47.4222 55.2574 47.7166 55.6515 47.4997C56.2163 47.1898 56.2032 46.7869 55.5333 45.4388C55.1918 44.7725 54.8109 44.1371 54.6795 44.0597C54.5219 43.9667 51.2775 43.9047 46.1155 43.9047C39.0357 43.9047 37.7485 43.9357 37.5252 44.1371Z" fill="#7EA91B"/>
                            <path d="M33.2034 48.3826C31.2857 48.9405 30.1561 51.2028 30.6815 53.3722C31.404 56.2699 34.4907 57.2616 36.277 55.1697C37.1308 54.178 37.2753 53.7751 37.2753 52.3495C37.2753 51.3733 37.2096 50.9704 36.9863 50.4745C36.2639 48.8785 34.6352 47.9797 33.2034 48.3826ZM35.0818 50.2886C35.5677 50.645 36.0143 51.6057 36.0275 52.3185C36.0275 53.4032 35.2131 54.5189 34.2674 54.7049C33.6632 54.8133 32.7043 54.3949 32.3497 53.8681C31.7192 52.9229 31.7586 51.5282 32.4416 50.6915C32.9145 50.1026 33.1772 49.9632 33.9784 49.9632C34.4119 49.9477 34.7665 50.0562 35.0818 50.2886Z" fill="#7EA91B"/>
                            <path d="M56.5447 48.5385C53.7338 49.6852 53.5105 54.3649 56.19 55.9144C56.9518 56.3638 58.5018 56.3328 59.2242 55.8834C60.4326 55.1241 61.2338 53.3576 61.05 51.8236C60.8661 50.2275 59.7496 48.7554 58.4755 48.4145C57.6743 48.1976 57.3459 48.2131 56.5447 48.5385ZM59.1585 50.6924C59.6314 51.2812 59.6708 51.4052 59.6708 52.2265C59.6708 53.4041 59.2767 54.1479 58.423 54.5198C57.4641 54.9537 56.5972 54.6438 55.9667 53.6521C55.3494 52.6913 55.6121 51.0488 56.4921 50.3205C56.9124 49.9796 57.0832 49.9331 57.8188 49.9796C58.6069 50.0416 58.6857 50.088 59.1585 50.6924Z" fill="#7EA91B"/>
                            </svg>

                    </div>
                    <div class="d-md-block d-none overflow-hidden" style="height: 700px" >
                        
                        <img src="{{ uploaded_asset(get_setting('forgot_password_page_image')) }}" alt="" style="max-width:581px;height: 100%; object-fit: cover; " class="d-md-block d-none rounded-4">
                    </div>
                    {{-- <img src="{{ uploaded_asset(get_setting('forgot_password_page_image')) }}" alt="" style="max-width:581px; height:757px" class="img-fit h-100 d-md-block d-none rounded-4"> --}}
                </div>
            </div>

            <!-- Right Side -->
            <div class="col-xxl-6 col-lg-5 p-0 p-md-3">
                <div class="right-content">
                    <div class="row align-items-center justify-content-center justify-content-lg-start h-100">
                        <div class="col-xxl-8 p-4 p-lg-6">
                            <!-- Site Icon -->
                            {{-- <div class="size-48px mb-3 mx-auto mx-lg-0">
                                <img src="{{ uploaded_asset(get_setting('site_icon')) }}" alt="{{ translate('Site Icon')}}" class="img-fit h-100">
                            </div> --}}

                            <!-- Titles -->
                            <div class=" text-lg-left">
                                <h1 class="fs-34 fw-700 text-primary">{{ translate('Forget Password ') }}</h1>
                                <h5 class="fs-14 fw-400 text-dark">
                                    {{ addon_is_activated('otp_system') ?
                                        translate('Enter your email address to recover your password.') :
                                            translate('Enter your email address to recover your password.') }}
                                </h5>
                            </div>

                            <!-- Send password reset link or code form -->
                            <div class="pt-2 pt-lg-2 bg-white">
                                <div class="">
                                    <form id="emailForm" class="form-default" role="form" action="{{ route('password.email') }}" method="POST">
                                        @csrf
                                            <div class="form-group">
                                                <label for="email" class="fs-15 fw-400">{{ translate('Email Address') }}</label>
                                                <input type="email" class="field-radius form-control{{ $errors->has('email') ? ' is-invalid' : '' }} rounded-0"
                                                       value="{{ old('email') }}"
                                                       placeholder="{{ translate('ali@example.com') }}"
                                                       name="email"
                                                       id="email"
                                                       required
                                                       autocomplete="off">
                                                <div id="email-error" class="invalid-feedback" style="display: none;">
                                                    Please enter a valid email address.
                                                </div>
                                            </div>
                                        <!-- Submit Button -->
                                        <div class="mb-4 mt-4">
                                            <button type="submit" class="btn btn-primary btn-block fw-400 fs-17 field-radius">{{ translate('Continue') }}</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- Go Back -->
                                <button class="btn back-btn text-center field-radius">
                                    <a href="{{ url()->previous() }}" class="fs-17 fw-400 d-flex align-items-center justify-content-center">
                                        {{-- <i class="las la-arrow-left fs-20 mr-1"></i> --}}
                                        {{ translate('Back') }}
                                    </a>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#emailForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const emailField = $('#email');
            const emailError = $('#email-error');
            const email = emailField.val();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(co|com)$/;

            if (!emailPattern.test(email)) {
                emailError.show();
                emailField.addClass('is-invalid');
            } else {
                emailError.hide();
                emailField.removeClass('is-invalid');
                this.submit(); // Submit the form if the email is valid
            }
        });
    });

    // Display success message if exists
    var successMessage = document.getElementById('toast-success');
    if (successMessage) {
        Toastify({
            text: successMessage.textContent,
            duration: 2000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: '#33cc33',
            stopOnFocus: true,
        }).showToast();
    }

    // Display error message if exists
    var errorMessage = document.getElementById('toast-error');
    if (errorMessage) {
        Toastify({
            text: errorMessage.textContent,
            duration: 2000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: '#ff3300',
            stopOnFocus: true,
        }).showToast();
    }
    </script>

@endsection
