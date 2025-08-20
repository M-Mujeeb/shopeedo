<div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">{{ translate('Login') }}</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    {{-- <form class="form-default" role="form" action="{{ route('cart.login.submit') }}" method="POST">
                        @csrf

                        @if (addon_is_activated('otp_system'))
                            <!-- Phone -->
                            <div class="form-group phone-form-group mb-1">
                                <input type="tel" id="phone-code"
                                    class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                    value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                            </div>
                            <!-- Country Code -->
                            <input type="hidden" name="country_code" value="">
                            <!-- Email -->
                            <div class="form-group email-form-group mb-1 d-none">
                                <input type="email"
                                    class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    value="{{ old('email') }}" placeholder="{{ translate('Email') }}" name="email"
                                    id="email" autocomplete="off">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!-- Use Email Instead -->
                            <div class="form-group text-right">
                                <button class="btn btn-link p-0 text-primary" type="button"
                                    onclick="toggleEmailPhone(this)"><i>*{{ translate('Use Email Instead') }}</i></button>
                            </div>
                        @else
                            <!-- Email -->
                            <div class="form-group">
                                <input type="email"
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    value="{{ old('email') }}" placeholder="{{ translate('Email') }}" name="email"
                                    id="email" autocomplete="off">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Password -->
                        <div class="form-group">
                            <input type="password" name="password" class="form-control h-auto rounded-0 form-control-lg"
                                placeholder="{{ translate('Password') }}">
                        </div>

                        <!-- Remember Me & Forgot password -->
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span class=opacity-60>{{ translate('Remember Me') }}</span>
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('password.request') }}"
                                    class="text-reset opacity-60 hov-opacity-100 fs-14">{{ translate('Forgot password?') }}</a>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="mb-5">
                            <button type="submit"
                                class="btn btn-primary btn-block fw-600 rounded-0">{{ translate('Login') }}</button>
                        </div>
                    </form> --}}
                    <form class="form-default" role="form" action="{{ route('cart.login.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" value="customer" name="urlrequest">

                        <!-- Email or Phone -->
                        {{-- @if (addon_is_activated('otp_system')) --}}
                            <div class="form-group phone-form-group mb-1">
                                <label for="phone" class="fs-12 fw-700 text-soft-dark">{{  translate('Phone') }}</label>
                                <input type="tel" id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }} rounded-0" value="{{ old('phone') }}" placeholder="" name="phone" title="Phone number must be exactly 11 digits." minlength="11" maxlength="11" autocomplete="off" >
                            </div>

                            <input type="hidden" name="country_code" value="">

                            <div class="form-group email-form-group mb-1 d-none">
                                <label for="email" class="fs-12 fw-700 text-soft-dark">{{  translate('Email') }}</label>
                                <input type="email" class="form-control rounded-0 {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('johndoe@example.com') }}" name="email" id="email" autocomplete="new-email" >
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group text-right">
                                <button class="btn btn-link p-0 text-primary" type="button" onclick="toggleEmailPhone(this)"><i>*{{ translate('Use Email Instead') }}</i></button>
                            </div>
                        {{-- @else --}}
                            {{-- <div class="form-group">
                                <label for="email" class="fs-12 fw-700 text-soft-dark">{{  translate('Email') }}</label>
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} rounded-0" value="{{ old('email') }}" placeholder="{{  translate('johndoe@example.com') }}" name="email" id="email" autocomplete="off" required>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div> --}}
                        {{-- @endif --}}

                        <!-- password -->
                        <div class="form-group">
                            <label for="password" class="fs-12 fw-700 text-soft-dark">{{  translate('Password') }}</label>
                            <div class="position-relative">
                                <input type="password" class="form-control rounded-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password')}}" name="password" id="password" autocomplete="new-password" required>
                                <i class="password-toggle las la-2x la-eye"></i>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <!-- Remember Me -->
                            <div class="col-6">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="has-transition fs-12 fw-400 text-gray-dark hov-text-primary">{{  translate('Remember Me') }}</span>
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                            <!-- Forgot password -->
                            <div class="col-6 text-right">
                                <a href="{{ route('password.request') }}" class="text-reset fs-12 fw-400 text-gray-dark hov-text-primary"><u>{{ translate('Forgot password?')}}</u></a>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-4 mt-4">
                            <button type="submit" class="btn btn-primary btn-block fw-700 fs-14 rounded-2">{{  translate('Login') }}</button>
                        </div>
                    </form>

                    <!-- Register Now -->
                    <div class="text-center mb-3">
                        <p class="text-muted mb-0">{{ translate('Dont have an account?') }}</p>
                        <a href="{{ route('user.registration') }}">{{ translate('Register Now') }}</a>
                    </div>
                    
                    <!-- Social Login -->
                    @if (get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1 || get_setting('apple_login') == 1)
                        <div class="separator mb-3">
                            <span class="bg-white px-3 opacity-60">{{ translate('Or Login With') }}</span>
                        </div>
                        <ul class="list-inline social colored text-center mb-5">
                            <!-- Facebook -->
                            @if (get_setting('facebook_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                        class="facebook">
                                        <i class="lab la-facebook-f"></i>
                                    </a>
                                </li>
                            @endif
                            <!-- Google -->
                            @if (get_setting('google_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                        class="google">
                                        <i class="lab la-google"></i>
                                    </a>
                                </li>
                            @endif
                            <!-- Twitter -->
                            @if (get_setting('twitter_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}"
                                        class="twitter">
                                        <i class="lab la-twitter"></i>
                                    </a>
                                </li>
                            @endif
                            <!-- Apple -->
                            @if (get_setting('apple_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'apple']) }}"
                                        class="apple">
                                        <i class="lab la-apple"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>