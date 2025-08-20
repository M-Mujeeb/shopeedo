@extends('auth.layouts.authentication')
<style>
    .custom-radius{

        border-radius: 8px !important;
    }
    .cstm-height{
        height: 45px !important;
    }
    #login_button:disabled {
        background-color: #D0D5DD;
        color: white;
        border: none;
    }
    .btm-style {
        margin-bottom: 16px;
    }
    a:hover {
    color: #7D9A40 !important;
}
a.text-primary:focus, a.text-primary:hover {
    color: #7D9A40 !important;
}
.form-control.is-invalid {
    background-image:none !important;
}
a.text-primary:focus, a.text-primary:hover {
    color: #7D9A40 !important;
}
.btn-primary:hover{
    background-color: #7D9A40 !important;
}
#login_button:disabled {
    background-color: #D0D5DD !important;
    color: white;
}
.hide-password {
            display: none;
}
@media (max-width: 576px) {
            .container-custom {
                width: 100% !important;
                max-width: 100% !important;
            }
        }



</style>

@section('content')
    <!-- aiz-main-wrapper -->



    <div class="aiz-main-wrapper d-flex flex-column justify-content-center bg-white">
        <section class="bg-white overflow-hidden" style="min-height:100vh;">
            <div class="container container-custom">
            <div class="row" style="min-height: 100vh;">
                <!-- Left Side Image-->
                <div class="col-xxl-6 col-lg-7 p-0 p-md-3"  >
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
                        
                        <img src="{{ uploaded_asset(get_setting('seller_login_page_image')) }}" alt="" style="max-width:581px;height: 100%; object-fit: cover; " class="d-md-block d-none rounded-4">
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-xxl-6 col-lg-5 p-0 p-md-3">
                    <div class="right-content">
                        <div class="row align-items-center justify-content-center justify-content-lg-start h-100">
                            <div class="col-xxl-8 p-4 p-lg-4">
                                <!-- Site Icon -->
                                <!-- <div class="size-48px mb-3 mx-auto mx-lg-0">
                                    <img src="{{ uploaded_asset(get_setting('site_icon')) }}" alt="{{ translate('Site Icon')}}" class="img-fit h-100">
                                </div> -->
                                <!-- Titles -->
                                <div class=" text-lg-left">
                                    <h1 class="fs-34 fw-700 text-primary btm-style">{{ translate('Login')}}</h1>
                                    {{-- <h5 class="fs-14 fw-400 text-dark btm-style">{{ translate('Let’s create your free Shopeedo Business account')}}</h5> --}}

                                </div>
                                <!-- Login form -->
                                <div class=" bg-white">
                                    <div class="">
                                        <form id="login-form" class="form-default" role="form" action="{{ route('login') }}" method="POST">
                                            @csrf
                                            <input type="hidden" value="seller" name="urlrequest">
                                            <div class="form-group">
                                                <label for="email_or_phone" class="fs-15 fw-400 text-soft-dark btm-style" style="margin-bottom: 8px">{{  translate('Mobile Number or Email Address') }}</label>
                                                <input type="text" class="custom-radius cstm-height form-control{{ $errors->has('email') || $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('email') ?: old('phone') }}" placeholder="{{  translate('Enter your email or mobile number') }}" name="email_or_phone" id="email_or_phone" autocomplete="off" required>
                                                @if ($errors->has('email') || $errors->has('phone'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') ?: $errors->first('phone') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- password -->
                                            <div class="form-group" id="password_field" style="display: none;">
                                                <label for="password" class="fs-15 fw-400 text-soft-dark ">{{  translate('Password') }}</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control custom-radius  cstm-height {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="********" name="password" id="password">
                                                    <svg class="password-toggle show-password" id="show-password-icon" width="24" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L4.54852 5.96273C2.11768 8.23575 0.611157 10.5466 0.105573 11.5577C-0.0388552 11.8466 -0.0348927 12.1874 0.116212 12.4728C0.911795 13.9756 2.43155 16.1767 4.44905 17.9657C6.44912 19.7391 9.07199 21.2217 12.0463 21.0043C14.1054 20.965 16.1069 20.3524 17.8291 19.2433L22.2929 23.7071C22.6834 24.0976 23.3166 24.0976 23.7071 23.7071C24.0976 23.3166 24.0976 22.6834 23.7071 22.2929L1.70711 0.292893ZM16.3745 17.7888L14.0529 15.4671C13.137 16.0423 12.0118 16.2303 10.9354 15.9554C9.51816 15.5934 8.4115 14.4868 8.04956 13.0695C7.77466 11.9931 7.96267 10.8679 8.53787 9.95208L5.96358 7.3778C4.02162 9.18305 2.73293 11.0024 2.14239 12.0023C2.8992 13.3055 4.16865 15.044 5.77595 16.4692C7.59406 18.0814 9.71013 19.1781 11.9233 19.0079C11.9434 19.0063 11.9635 19.0054 11.9837 19.0051C13.5332 18.9797 15.0437 18.558 16.3745 17.7888ZM10.0291 11.4433C9.90866 11.8023 9.89029 12.1946 9.98736 12.5747C10.1683 13.2833 10.7217 13.8366 11.4303 14.0176C11.8104 14.1146 12.2026 14.0963 12.5616 13.9758L10.0291 11.4433ZM23.8777 11.5256C23.0808 10.0665 21.5613 7.9214 19.5476 6.15178C17.5484 4.3949 14.9387 2.90282 11.9828 3.00492C11.2049 3.00459 10.4296 3.09396 9.67209 3.27126C9.13434 3.39713 8.80045 3.93511 8.92632 4.47286C9.05219 5.0106 9.59016 5.3445 10.1279 5.21862C10.7408 5.07517 11.3682 5.00346 11.9977 5.00494C12.0107 5.00497 12.0238 5.00474 12.0369 5.00426C14.273 4.9217 16.4061 6.05356 18.2274 7.65411C19.8179 9.05181 21.0762 10.7264 21.8355 11.9857C21.6047 12.3498 21.3179 12.7884 21.0346 13.2066C20.8197 13.5237 20.6117 13.8221 20.4357 14.0622C20.2457 14.3213 20.1312 14.4595 20.0929 14.4978C19.7024 14.8884 19.7024 15.5215 20.0929 15.912C20.4834 16.3026 21.1166 16.3026 21.5071 15.912C21.6688 15.7504 21.8668 15.4927 22.0487 15.2446C22.2446 14.9774 22.4678 14.657 22.6904 14.3283C23.1344 13.6729 23.5951 12.9567 23.8575 12.5194C24.04 12.2152 24.0477 11.837 23.8777 11.5256Z" fill="#7D9A40"/>
                                                        </svg>

                                                    <svg class="password-toggle hide-password" id="hide-password-icon" width="24" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8ZM10 12C10 10.8954 10.8954 10 12 10C13.1046 10 14 10.8954 14 12C14 13.1046 13.1046 14 12 14C10.8954 14 10 13.1046 10 12Z" fill="#7D9A40" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3C8.8711 3 6.22807 4.48937 4.23728 6.25113C2.24678 8.01264 0.822273 10.1194 0.105573 11.5528C-0.0351909 11.8343 -0.0351909 12.1657 0.105573 12.4472C0.822273 13.8806 2.24678 15.9874 4.23728 17.7489C6.22807 19.5106 8.8711 21 12 21C15.1289 21 17.7719 19.5106 19.7627 17.7489C21.7532 15.9874 23.1777 13.8806 23.8944 12.4472C24.0352 12.1657 24.0352 11.8343 23.8944 11.5528C23.1777 10.1194 21.7532 8.01264 19.7627 6.25113C17.7719 4.48937 15.1289 3 12 3ZM5.56272 16.2511C3.98954 14.8589 2.80913 13.2146 2.13142 12C2.80913 10.7854 3.98954 9.14106 5.56272 7.74887C7.3386 6.17729 9.5289 5 12 5C14.4711 5 16.6614 6.17729 18.4373 7.74887C20.0105 9.14106 21.1909 10.7854 21.8686 12C21.1909 13.2146 20.0105 14.8589 18.4373 16.2511C16.6614 17.8227 14.4711 19 12 19C9.5289 19 7.3386 17.8227 5.56272 16.2511Z" fill="#7D9A40" />
                                                    </svg>
                                                    {{-- <i class="password-toggle las la-2x la-eye"></i> --}}
                                                </div>
                                                @if ($errors->has('password') )
                                                <div class="invalid-feedback d-block" role="alert">
                                                    {{ $errors->first('password')}}
                                                </div>
                                            @endif

                                            </div>

                                            <div class="row mb-2" id="forgot_password_link" style="display: none;">
                                                <!-- Forgot password -->
                                                <div class="col-12 text-center">
                                                    <a href="{{ route('password.request') }}" class="text-reset fs-15 fw-400 " style="color: #7D9A40 !important">{{ translate('Forgot Password? ')}}</a>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mb-2 mt-2">
                                                <button type="submit" class="btn btn-primary btn-block  fs-17 custom-radius cstm-height" id="login_button" disabled>{{  translate('Continue') }}</button>
                                            </div>
                                        </form>

                                    <!-- Register Now -->
                                    <p class="fs-15 mb-0">
                                        Don&apos;t have an account?
                                        <a href="{{ route('shops.create') }}" class="ml-2 fs-15  animate-underline-primary">{{ translate('Create Account') }}</a>
                                    </p>
                                    <!-- Go Back -->
                                    <!-- <a href="{{ url()->previous() }}" class="mt-3 fs-14 fw-700 d-flex align-items-center text-primary" style="max-width: fit-content;">
                                        <i class="las la-arrow-left fs-20 mr-1"></i>
                                        {{ translate('Back to Previous Page') }}
                                    </a> -->
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
    function togglePasswordField() {
    var emailOrPhone = $('#email_or_phone').val();
    var numberRegex = /^\d{11}$/;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var errorElement = $('#email_or_phone_error');

    if (numberRegex.test(emailOrPhone)) {
        // If input is exactly 11 digits, enable the login button and set a specific action
        $('#email_or_phone').off('input', enforcePhoneNumberFormat);
        $('#email_or_phone').on('input', enforcePhoneNumberFormat);

        $('#password_field').show();
        $('#forgot_password_link').show();
        $('#login_button').text('{{ translate('Login') }}');
        $('#login_button').prop('disabled', false);
        $('#email_or_phone').attr('name', 'phone');

        $('#login-form').attr('action', '{{ route('mobile.otpstatus') }}');
        errorElement.hide();
    } else if (emailRegex.test(emailOrPhone)) {
        // If input is an email, show password field and handle login normally
        $('#email_or_phone').off('input', enforcePhoneNumberFormat);

        $('#password_field').show();
        $('#forgot_password_link').show();
        $('#login_button').text('{{ translate('Login') }}');
        $('#login_button').prop('disabled', false);
        $('#email_or_phone').attr('name', 'email');
        $('#login-form').attr('action', '{{ route('login') }}');
        errorElement.hide();
    } else if (emailOrPhone.trim() !== '') {
        // Invalid input, show error message
        errorElement.find('strong').text('{{ translate('Invalid email or phone number format') }}');
        errorElement.show();
        $('#password_field').hide();
        $('#forgot_password_link').hide();
        $('#login_button').prop('disabled', true);
        $('#login-form').removeAttr('action');
        $('#email_or_phone').off('input', enforcePhoneNumberFormat);
    } else {
        // Empty input
        errorElement.hide();
        $('#password_field').hide();
        $('#forgot_password_link').hide();
        $('#login_button').prop('disabled', true);
        $('#login-form').removeAttr('action');
        $('#email_or_phone').off('input', enforcePhoneNumberFormat);
    }
}

function enforcePhoneNumberFormat(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/\D/g, '');

    // Truncate to 11 characters if necessary
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11);
    }
}

// Call togglePasswordField on input change
$(document).on('input', '#email_or_phone', function() {
    togglePasswordField();
});

// Form submission handling
$('#login-form').on('submit', function(event) {
    var emailOrPhone = $('#email_or_phone').val();
    var numberRegex = /^\d{11}$/;

    if (!numberRegex.test(emailOrPhone) && $('#login_button').text() === '{{ translate('Continue') }}') {
        // Prevent form submission if the input is not exactly 11 digits
        event.preventDefault();
    }
});

// Call togglePasswordField on document ready (in case of browser autocomplete)
$(document).ready(function() {
    togglePasswordField();
});

document.addEventListener('DOMContentLoaded', (event) => {
    const passwordField = document.getElementById('password');
    const showPasswordIcon = document.getElementById('show-password-icon');
    const hidePasswordIcon = document.getElementById('hide-password-icon');

    showPasswordIcon.addEventListener('click', () => {
        passwordField.type = 'text';
        showPasswordIcon.style.display = 'none';
        hidePasswordIcon.style.display = 'block';
    });

    hidePasswordIcon.addEventListener('click', () => {
        passwordField.type = 'password';
        showPasswordIcon.style.display = 'block';
        hidePasswordIcon.style.display = 'none';
    });
});

$(document).ready(function() {
    function checkCredentials() {
        var emailOrPhone = $('#email_or_phone').val();
        var passwordField = $('#password_field');

        if (emailOrPhone !== '') {
            passwordField.show();
        } else {
            passwordField.hide();
        }
    }

    // Initial check on page load
    checkCredentials();

    // Event listener for input change to re-check credentials
    $('#email_or_phone').on('input', function() {
        checkCredentials();
    });

    // Event listener for page load to re-check credentials if fields are autofilled
    $(window).on('load', function() {
        setTimeout(function() {
            checkCredentials();
        }, 100); // Delay to ensure fields are autofilled
    });
});
</script>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000); // 5 seconds
    });
</script>

@endsection
