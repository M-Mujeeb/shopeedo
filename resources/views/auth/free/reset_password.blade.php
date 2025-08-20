@extends('auth.layouts.authentication')
@section('css')
    <style>
           .btn-primary:hover {
                background-color: #7D9A40 !important;
            }
            .back-btn a:hover {

                color:#7D9A40;
            }
            .back-btn {
                border: 1px solid #7D9A40;
                width: 100%;
            }
            .form-control {
                color:black;
            }
            .otp-button {
                max-width:160px;
                border-radius: 8px ;
                border:1px solid #7D9A40 ;
                /* border-color: ; */
                color: #7D9A40 ;
                background-color: white

            }
            #sendOtpBtn:disabled {
            background-color: #D0D5DD;
            color: white;
            cursor: not-allowed ;
            border-color: #D0D5DD;
            /* text-decoration: line-through; */
        }
        .field-radius {
                border-radius: 8px !important;
            }
    </style>

@section('content')
    <div class="aiz-main-wrapper d-flex flex-column justify-content-center bg-white">
        <section class="bg-white overflow-hidden" style="min-height:100vh;">
            <div class="container">
            <div class="row" style="min-height:100vh;" >
                <!-- Left Side Image-->
                <div class="col-xxl-6 col-lg-7 p-0 p-md-3">
                    <div class=" " >
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
                        {{-- <img src="{{ uploaded_asset(get_setting('seller_login_page_image')) }}" alt="" style="max-width:581px; height:757px" class="img-fit h-100 d-md-block d-none rounded-4"> --}}
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-xxl-6 col-lg-5 p-0 p-md-3">
                    <div class="right-content">
                        <div class="row align-items-center justify-content-center justify-content-lg-start h-100">
                            <div class="col-xxl-8 p-4 p-lg-5">
                                <!-- Titles -->
                                <div class=" text-lg-left">
                                    <h1 class="fs-34 fw-700 text-primary" >{{ translate('Verify OTP') }}</h1>
                                    <h5 class="fs-14 fw-400 text-dark">
                                        {{ translate('Enter 4 digit one time password sent to your email address') }}
                                    </h5>
                                </div>
                                <div id="otp-error" class="text-danger mb-2" style="display:none;"></div>

                                <!-- Reset password form -->
                                <div class="pt-2 pt-lg-2 bg-white">
                                    <div class="">
                                        <form class="form-default" id="otp-form" role="form" action="{{ route('password.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" id="" value="{{ $user_id }}">
                                            <div>
                                                <div class="form-group d-flex justify-content-between">
                                                    <input type="number" name="otp_1" class="form-control text-center fw-700 fs-28 field-radius" required autofocus min="0" max="9" style="color:black; width:81.65px; height:58px" oninput="handleInput(event, 'otp_2')" onkeydown="handleKeydown(event, 'otp_1')">
                                                    <input type="number" name="otp_2" class="form-control text-center fw-700 fs-28 field-radius" required min="0" max="9" style="color:black; width:81.65px; height:58px" oninput="handleInput(event, 'otp_3')" onkeydown="handleKeydown(event, 'otp_1')">
                                                    <input type="number" name="otp_3" class="form-control text-center fw-700 fs-28 field-radius" required min="0" max="9" style="color:black; width:81.65px; height:58px" oninput="handleInput(event, 'otp_4')" onkeydown="handleKeydown(event, 'otp_2')">
                                                    <input type="number" name="otp_4" class="form-control text-center fw-700 fs-28 field-radius" required min="0" max="9" style="color:black; width:81.65px; height:58px" oninput="handleInput(event)" onkeydown="handleKeydown(event, 'otp_3')">
                                       </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                             <div class="d-flex justify-content-between align-items-center text-black" >
                                                    <span class="fs-12">OTP expires in </span>
                                                    <div id="timer" class="fw-600 fs-12 ml-2"> 02:00</div>
                                               </div>

                                                <button type="button" id="sendOtpBtn" class="py-2 px-3 fs-15  otp-button field-radius" disabled onclick="resendOtp()">
                                                    <svg class="mr-2" width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M21.3147 1.18501C21.5195 1.38987 21.6072 1.66757 21.5776 1.93475C21.5693 2.00964 21.5517 2.08371 21.525 2.15512L15.115 20.4693C14.9909 20.8241 14.6627 21.0671 14.2872 21.0824C13.9117 21.0977 13.5648 20.8823 13.4122 20.5388L9.8887 12.611L1.96088 9.08752C1.61742 8.93487 1.40195 8.58803 1.41727 8.21249C1.43259 7.83696 1.6756 7.50882 2.03035 7.38466L20.3445 0.974692C20.4164 0.947795 20.4909 0.930227 20.5663 0.921987C20.6217 0.915897 20.6772 0.914911 20.7322 0.918874C20.9445 0.934039 21.1524 1.02275 21.3147 1.18501ZM17.178 4.02538L4.81833 8.35125L10.3802 10.8232L17.178 4.02538ZM11.6765 12.1195L18.4743 5.32174L14.1485 17.6814L11.6765 12.1195Z" fill="#7D9A40"/>
                                                    </svg>
                                                    Resend OTP
                                                </button>

                                            </div>
                                        </div>

                                            <div class="mb-4 mt-4">
                                                <button type="submit" class="btn btn-primary btn-block fw-400 fs-17 field-radius ">{{ translate('Confirm') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Go Back -->
                                    <button class="btn back-btn text-center field-radius" style="border:1px solid #7D9A40; " >
                                        <a href="{{ url()->previous() }}" class="fs-17 fw-400 d-flex align-items-center justify-content-center " >
                                            {{-- <i class="las la-arrow-left fs-20 mr-1"></i> --}}
                                            {{ translate('Back ')}}
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

    function handleInput(event, nextName) {
    const current = event.target;
    if (current.value.length > 1) {
        current.value = current.value.slice(0, 1);
    }
    if (current.value.length === 1 && nextName) {
        document.getElementsByName(nextName)[0].focus();
    }
}

function handleKeydown(event, prevName) {
    const current = event.target;
    if (event.key === 'Backspace' && current.value.length === 0 && prevName) {
        document.getElementsByName(prevName)[0].focus();
}
}

    function updateButtonFill() {
        var button = document.getElementById('sendOtpBtn');
        var svg = button.querySelector('svg');
        var fillColor = button.disabled ? 'white' : '#7D9A40';
        svg.querySelector('path').setAttribute('fill', fillColor);
    }

    let timer;
    let countdown = 120; // 2 minutes in seconds

    function startTimer() {
        timer = setInterval(() => {
            countdown--;
            let minutes = Math.floor(countdown / 60);
            let seconds = countdown % 60;
            document.getElementById('timer').textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            updateButtonFill();

            if (countdown <= 0) {
                clearInterval(timer);
                document.getElementById('sendOtpBtn').disabled = false;
                updateButtonFill();
            }
        }, 1000);
        updateButtonFill();
    }

    function resendOtp() {
        const userId = '{{ $user_id }}';

        $.ajax({
            url: '{{ url("/otp/resend") }}/' + userId,
            method: 'get',
            success: function(response) {
                Toastify({
                    text: "OTP has been resent",
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4CAF50"
                }).showToast();
                countdown = 120; // Reset countdown
                document.getElementById('sendOtpBtn').disabled = true;
                startTimer(); // Restart timer
            },
            error: function(error) {
                alert('Failed to resend OTP');
            }
        });
    }

    $('#otp-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route('password.update') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = '{{ url('password/reset/confirm/submit') }}/' + response.user_id;
                }let errorMessage = '';
                    if (response.status === 'time') {
                        errorMessage = 'Verification code timeout. Please request a new code.';
                    } else if (response.status === 'mismatch') {
                        errorMessage = 'Invalid OTP';
                    }

                    if (errorMessage) {
                        $('#otp-error').text(errorMessage).show();
                        $('input[name^="otp"]').val('');
                        // setTimeout(function() {
                        // $('#otp-error').fadeOut();
                        // },2000);

                    }

                     $('input[name^="otp"]').on('input', function() {
                        $('#otp-error').fadeOut();
                         });

            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('An error occurred. Please try again.');
            }
        });
    });

    startTimer();
</script>
@endsection
