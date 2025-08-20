@extends('seller.layouts.app')

@section('css')
<style>

.password-toggle {
    position: absolute;
    right: 7px;
    left: auto;
    top: 50%;
    cursor: pointer;
    font-size: 20px;
    transform: translateY(-50%);
}
/* .aiz-notify {
    width: 100% !important;
    white-space: normal;
} */
</style>
@endsection

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Manage Profile') }}</h1>
        </div>
      </div>
    </div>
    <form id="updatePorfile" action="{{ route('seller.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="POST">
        @csrf
        <!-- Basic Info-->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Basic Info')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="name">{{ translate('Your Name') }} <span class="text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" name="name" value="{{old('name', $user->name)  }}" id="name" class="form-control" placeholder="{{ translate('Your Name') }}" required>
                        @error('name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="phone">{{ translate('Your Phone') }} <span class="text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" id="phone" class="form-control" placeholder="{{ translate('Your Phone')}}" pattern="^\d{11}$"
                        title="Phone number must be exactly 11 digits"  required>
                        @error('phone')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('Photo') }}</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="photo" value="{{ old('photo', $user->avatar_original)  }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="password">{{ translate('Your Password') }}</label>
                    <div class="col-md-10">
                        <div class="position-relative">
                            <input style="border-radius: 10px;" type="password" class="form-control" placeholder="Password" name="new_password" id="password" >
                            <svg class="password-toggle show-password" id="show-password-icon" width="24" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L4.54852 5.96273C2.11768 8.23575 0.611157 10.5466 0.105573 11.5577C-0.0388552 11.8466 -0.0348927 12.1874 0.116212 12.4728C0.911795 13.9756 2.43155 16.1767 4.44905 17.9657C6.44912 19.7391 9.07199 21.2217 12.0463 21.0043C14.1054 20.965 16.1069 20.3524 17.8291 19.2433L22.2929 23.7071C22.6834 24.0976 23.3166 24.0976 23.7071 23.7071C24.0976 23.3166 24.0976 22.6834 23.7071 22.2929L1.70711 0.292893ZM16.3745 17.7888L14.0529 15.4671C13.137 16.0423 12.0118 16.2303 10.9354 15.9554C9.51816 15.5934 8.4115 14.4868 8.04956 13.0695C7.77466 11.9931 7.96267 10.8679 8.53787 9.95208L5.96358 7.3778C4.02162 9.18305 2.73293 11.0024 2.14239 12.0023C2.8992 13.3055 4.16865 15.044 5.77595 16.4692C7.59406 18.0814 9.71013 19.1781 11.9233 19.0079C11.9434 19.0063 11.9635 19.0054 11.9837 19.0051C13.5332 18.9797 15.0437 18.558 16.3745 17.7888ZM10.0291 11.4433C9.90866 11.8023 9.89029 12.1946 9.98736 12.5747C10.1683 13.2833 10.7217 13.8366 11.4303 14.0176C11.8104 14.1146 12.2026 14.0963 12.5616 13.9758L10.0291 11.4433ZM23.8777 11.5256C23.0808 10.0665 21.5613 7.9214 19.5476 6.15178C17.5484 4.3949 14.9387 2.90282 11.9828 3.00492C11.2049 3.00459 10.4296 3.09396 9.67209 3.27126C9.13434 3.39713 8.80045 3.93511 8.92632 4.47286C9.05219 5.0106 9.59016 5.3445 10.1279 5.21862C10.7408 5.07517 11.3682 5.00346 11.9977 5.00494C12.0107 5.00497 12.0238 5.00474 12.0369 5.00426C14.273 4.9217 16.4061 6.05356 18.2274 7.65411C19.8179 9.05181 21.0762 10.7264 21.8355 11.9857C21.6047 12.3498 21.3179 12.7884 21.0346 13.2066C20.8197 13.5237 20.6117 13.8221 20.4357 14.0622C20.2457 14.3213 20.1312 14.4595 20.0929 14.4978C19.7024 14.8884 19.7024 15.5215 20.0929 15.912C20.4834 16.3026 21.1166 16.3026 21.5071 15.912C21.6688 15.7504 21.8668 15.4927 22.0487 15.2446C22.2446 14.9774 22.4678 14.657 22.6904 14.3283C23.1344 13.6729 23.5951 12.9567 23.8575 12.5194C24.04 12.2152 24.0477 11.837 23.8777 11.5256Z" fill="#7D9A40"/>
                            </svg>

                            <svg  style="display: none" class="password-toggle hide-password" id="hide-password-icon" width="24" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8ZM10 12C10 10.8954 10.8954 10 12 10C13.1046 10 14 10.8954 14 12C14 13.1046 13.1046 14 12 14C10.8954 14 10 13.1046 10 12Z" fill="#7D9A40" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3C8.8711 3 6.22807 4.48937 4.23728 6.25113C2.24678 8.01264 0.822273 10.1194 0.105573 11.5528C-0.0351909 11.8343 -0.0351909 12.1657 0.105573 12.4472C0.822273 13.8806 2.24678 15.9874 4.23728 17.7489C6.22807 19.5106 8.8711 21 12 21C15.1289 21 17.7719 19.5106 19.7627 17.7489C21.7532 15.9874 23.1777 13.8806 23.8944 12.4472C24.0352 12.1657 24.0352 11.8343 23.8944 11.5528C23.1777 10.1194 21.7532 8.01264 19.7627 6.25113C17.7719 4.48937 15.1289 3 12 3ZM5.56272 16.2511C3.98954 14.8589 2.80913 13.2146 2.13142 12C2.80913 10.7854 3.98954 9.14106 5.56272 7.74887C7.3386 6.17729 9.5289 5 12 5C14.4711 5 16.6614 6.17729 18.4373 7.74887C20.0105 9.14106 21.1909 10.7854 21.8686 12C21.1909 13.2146 20.0105 14.8589 18.4373 16.2511C16.6614 17.8227 14.4711 19 12 19C9.5289 19 7.3386 17.8227 5.56272 16.2511Z" fill="#7D9A40" />
                            </svg>
                        </div>
                        @error('new_password')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="confirm_password">{{ translate('Confirm Password') }}</label>
                    <div class="col-md-10">
                        {{-- <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="{{ translate('Confirm Password') }}" > --}}
                        <div class="position-relative">
                            <input style="border-radius: 10px;" type="password" class="form-control" placeholder="{{ translate('Confirm Password') }}" name="confirm_password" id="password_confirmation" d>
                            <svg class="password-toggle show-password" id="show-confirm-password-icon" width="24" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L4.54852 5.96273C2.11768 8.23575 0.611157 10.5466 0.105573 11.5577C-0.0388552 11.8466 -0.0348927 12.1874 0.116212 12.4728C0.911795 13.9756 2.43155 16.1767 4.44905 17.9657C6.44912 19.7391 9.07199 21.2217 12.0463 21.0043C14.1054 20.965 16.1069 20.3524 17.8291 19.2433L22.2929 23.7071C22.6834 24.0976 23.3166 24.0976 23.7071 23.7071C24.0976 23.3166 24.0976 22.6834 23.7071 22.2929L1.70711 0.292893ZM16.3745 17.7888L14.0529 15.4671C13.137 16.0423 12.0118 16.2303 10.9354 15.9554C9.51816 15.5934 8.4115 14.4868 8.04956 13.0695C7.77466 11.9931 7.96267 10.8679 8.53787 9.95208L5.96358 7.3778C4.02162 9.18305 2.73293 11.0024 2.14239 12.0023C2.8992 13.3055 4.16865 15.044 5.77595 16.4692C7.59406 18.0814 9.71013 19.1781 11.9233 19.0079C11.9434 19.0063 11.9635 19.0054 11.9837 19.0051C13.5332 18.9797 15.0437 18.558 16.3745 17.7888ZM10.0291 11.4433C9.90866 11.8023 9.89029 12.1946 9.98736 12.5747C10.1683 13.2833 10.7217 13.8366 11.4303 14.0176C11.8104 14.1146 12.2026 14.0963 12.5616 13.9758L10.0291 11.4433ZM23.8777 11.5256C23.0808 10.0665 21.5613 7.9214 19.5476 6.15178C17.5484 4.3949 14.9387 2.90282 11.9828 3.00492C11.2049 3.00459 10.4296 3.09396 9.67209 3.27126C9.13434 3.39713 8.80045 3.93511 8.92632 4.47286C9.05219 5.0106 9.59016 5.3445 10.1279 5.21862C10.7408 5.07517 11.3682 5.00346 11.9977 5.00494C12.0107 5.00497 12.0238 5.00474 12.0369 5.00426C14.273 4.9217 16.4061 6.05356 18.2274 7.65411C19.8179 9.05181 21.0762 10.7264 21.8355 11.9857C21.6047 12.3498 21.3179 12.7884 21.0346 13.2066C20.8197 13.5237 20.6117 13.8221 20.4357 14.0622C20.2457 14.3213 20.1312 14.4595 20.0929 14.4978C19.7024 14.8884 19.7024 15.5215 20.0929 15.912C20.4834 16.3026 21.1166 16.3026 21.5071 15.912C21.6688 15.7504 21.8668 15.4927 22.0487 15.2446C22.2446 14.9774 22.4678 14.657 22.6904 14.3283C23.1344 13.6729 23.5951 12.9567 23.8575 12.5194C24.04 12.2152 24.0477 11.837 23.8777 11.5256Z" fill="#7D9A40"/>
                            </svg>
                            <svg style="display: none" class="password-toggle hide-password" id="hide-confirm-password-icon" width="24" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8ZM10 12C10 10.8954 10.8954 10 12 10C13.1046 10 14 10.8954 14 12C14 13.1046 13.1046 14 12 14C10.8954 14 10 13.1046 10 12Z" fill="#7D9A40" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3C8.8711 3 6.22807 4.48937 4.23728 6.25113C2.24678 8.01264 0.822273 10.1194 0.105573 11.5528C-0.0351909 11.8343 -0.0351909 12.1657 0.105573 12.4472C0.822273 13.8806 2.24678 15.9874 4.23728 17.7489C6.22807 19.5106 8.8711 21 12 21C15.1289 21 17.7719 19.5106 19.7627 17.7489C21.7532 15.9874 23.1777 13.8806 23.8944 12.4472C24.0352 12.1657 24.0352 11.8343 23.8944 11.5528C23.1777 10.1194 21.7532 8.01264 19.7627 6.25113C17.7719 4.48937 15.1289 3 12 3ZM5.56272 16.2511C3.78886 14.6795 2.60946 13.0351 1.9318 12C2.60946 10.9649 3.78886 9.3205 5.56272 7.74887C7.3386 6.17729 9.5289 5 12 5C14.4711 5 16.6614 6.17729 18.4373 7.74887C20.0105 9.14106 21.1909 10.7854 21.8686 12C21.1909 13.2146 20.0105 14.8589 18.4373 16.2511C16.6614 17.8227 14.4711 19 12 19C9.5289 19 7.3386 17.8227 5.56272 16.2511Z" fill="#7D9A40"/>
                            </svg>
                        </div>
                        @error('confirm_password')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <!-- National Id & Details -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('National Id Details')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <label class="col-md-3 col-form-label" for="no">{{ translate('CNIC Number') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" name="cnic_no" id="cnic_no" class="form-control mb-3"
                                placeholder="{{ translate('Enter 13-digit CNIC number without Hyphen') }}"
                                pattern="^\d{13}"
                                maxlength="13"
                                value="{{ old('cnic_no',  $user->cnic_no ) }}"

                                required>
                        @error('cnic_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="date_of_issue">{{ translate('Date Of Issue') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="date" name="date_of_issue" value="{{ old('date_of_issue', $user->date_of_issue) }}" id="date_of_issue" class="form-control mb-3" required >
                        @error('"date_of_issue')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="date_of_expiry">{{ translate('Date Of Expiry') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="date" name="date_of_expiry" value="{{ old('date_of_expiry', $user->date_of_expiry) }}" id="date_of_expiry" class="form-control mb-3" required>
                        @error('date_of_expiry')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{ translate('Front Side Picture') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="front_side_picture"  value="{{ old('front_side_picture', $user->front_side_picture) }}" class="selected-files ">
                        </div>
                        <div class="file-preview box sm" id="front-side-pic">
                        </div>
                        <div id="file-error-front" class="text-danger" style="display: none;">{{ translate('This field is required.') }}</div>

                        @error('front_side_picture')
                        <small class="form-text text-danger">{{ $message }}</small>
                       @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{ translate('Back Side Picture') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="back_side_picture" value="{{ old('back_side_picture', $user->back_side_picture) }}" class="selected-files" required>
                        </div>
                        <div class="file-preview box sm" id="back-side-pic">
                        </div>
                        <div id="file-error-back" class="text-danger" style="display: none;">{{ translate('This field is required.') }}</div>

                        @error('back_side_picture')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                    </div>
                </div>

                <div class="row">
                    <label class="col-md-3 col-form-label" for="ntn_no">{{ translate('NTN (National Tax Number)') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" name="ntn_no" id="ntn_no" class="form-control mb-3"
                                placeholder="{{ translate('Enter 13-digit NTN number') }}"
                                pattern="^\d{13}"
                                maxlength="13"
                                value="{{ old('ntn_no', $user->ntn_no)  }}"

                                >
                        @error('ntn_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <!-- Payment System -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Bank Details')}}</h5>
            </div>
            <div class="card-body">
                {{-- <div class="row">
                    <label class="col-md-3 col-form-label">{{ translate('Cash Payment') }}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="cash_on_delivery_status" type="checkbox" @if ($user->shop->cash_on_delivery_status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label">{{ translate('Bank Payment') }}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="bank_payment_status" type="checkbox" @if ($user->shop->bank_payment_status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div> --}}
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_name">{{ translate('Bank Name') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->shop->bank_name) }}" id="bank_name" class="form-control mb-3" placeholder="{{ translate('Bank Name')}}" required>
                        @error('bank_name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_name">{{ translate('Account Title') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_name" value="{{ old('bank_acc_name',  $user->shop->bank_acc_name) }}" id="bank_acc_name" class="form-control mb-3" placeholder="{{ translate('Bank Account Name')}}" required>
                        @error('bank_acc_name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_no">{{ translate('Bank Account Number') }} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_no" value="{{ old('bank_acc_no', $user->shop->bank_acc_no ) }}" id="bank_acc_no" class="form-control mb-3" placeholder="{{ translate('Bank Account Number')}}" required>
                        @error('bank_acc_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                {{-- <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_routing_no">{{ translate('Bank Routing Number') }}</label>
                    <div class="col-md-9">
                        <input type="number" name="bank_routing_no" value="{{ $user->shop->bank_routing_no }}" id="bank_routing_no" lang="en" class="form-control mb-3" placeholder="{{ translate('Bank Routing Number')}}">
                        @error('bank_routing_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div> --}}
            </div>
        </div>

        <div class="form-group mb-0 text-right">
            <button type="submit" class="btn btn-primary">{{translate('Update Profile')}}</button>
        </div>
    </form>

    <br>

    <!-- Address -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Address')}}</h5>
        </div>
        <div class="card-body">
            <div class="row gutters-10">
                @foreach ($addresses as $key => $address)
                    <div class="col-lg-4">
                        <div class="border p-3 pr-5 rounded mb-3 position-relative">
                            <div>
                                <span class="w-50 fw-600">{{ translate('Address') }}:</span>
                                <span class="ml-2">{{ $address->address }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Postal Code') }}:</span>
                                <span class="ml-2">{{ $address->postal_code }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('City') }}:</span>
                                <span class="ml-2">{{ optional($address->city)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('State') }}:</span>
                                <span class="ml-2">{{ optional($address->state)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Country') }}:</span>
                                <span class="ml-2">{{ optional($address->country)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Phone') }}:</span>
                                <span class="ml-2">{{ $address->phone }}</span>
                            </div>
                            @if ($address->set_default)
                                <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                    <span class="badge badge-inline badge-primary">{{ translate('Default') }}</span>
                                </div>
                            @endif
                            <div class="dropdown position-absolute right-0 top-0">
                                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                    <i class="la la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="edit_address('{{$address->id}}')">
                                        {{ translate('Edit') }}
                                    </a>
                                    @if (!$address->set_default)
                                        <a class="dropdown-item" href="{{ route('seller.addresses.set_default', $address->id) }}">{{ translate('Make This Default') }}</a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('seller.addresses.destroy', $address->id) }}">{{ translate('Delete') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-4 mx-auto" onclick="add_new_address()">
                    <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                        <i class="la la-plus la-2x"></i>
                        <div class="alpha-7">{{ translate('Add New Address') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Email -->
    <form action="{{ route('user.change.email') }}" method="POST">
        @csrf
        <div class="card">
          <div class="card-header">
              <h5 class="mb-0 h6">{{ translate('Change your email')}}</h5>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-md-2">
                      <label>{{ translate('Your Email') }}</label>
                  </div>
                  <div class="col-md-10">
                      <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="{{ translate('Your Email')}}" name="email" value="{{ $user->email }}" />
                        <div class="input-group-append">
                           <button type="button" class="btn btn-outline-secondary new-email-verification">
                               <span class="d-none loading">
                                   <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>{{ translate('Sending Email...') }}
                               </span>
                               <span class="default">{{ translate('Verify') }}</span>
                           </button>
                        </div>
                      </div>
                      <div class="form-group mb-0 text-right">
                          <button type="submit" class="btn btn-primary">{{translate('Update Email')}}</button>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </form>

@endsection

@section('modal')
    {{-- New Address Modal --}}
    <div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" id="address-form" role="form" action="{{ route('seller.addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('Address')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <textarea class="form-control mb-3" value="" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('Country')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                            <option value="">{{ translate('Select your country') }}</option>
                                            @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('State')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" value="" required>

                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('City')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>

                                    </select>
                                </div>
                            </div>

                            @if (get_setting('google_map') == 1)
                                <div class="row">
                                    <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}" style="padding: 12px !important">
                                    <div id="map"></div>
                                    <ul id="geoData">
                                        <li style="display: none;">{{ translate('Full Address') }}: <span id="location"></span></li>
                                        <li style="display: none;">{{ translate('Postal Code') }}: <span id="postal_code"></span></li>
                                        <li style="display: none;">{{ translate('Country') }}: <span id="country"></span></li>
                                        <li style="display: none;">{{ translate('Latitude') }}: <span id="lat"></span></li>
                                        <li style="display: none;">{{ translate('Longitude') }}: <span id="lon"></span></li>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-md-3" id="">
                                        <label for="exampleInputuname">{{ translate('Longitude') }}</label>
                                    </div>
                                    <div class="col-md-9" id="">
                                        <input type="text" class="form-control mb-3" id="longitude" name="longitude" readonly="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3" id="">
                                        <label for="exampleInputuname">{{ translate('Latitude') }}</label>
                                    </div>
                                    <div class="col-md-9" id="">
                                        <input type="text" class="form-control mb-3" id="latitude" name="latitude" readonly="">
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-3">
                                    <label style="white-space: nowrap">{{ translate('Postal code')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label style="white-space: nowrap;">{{ translate('Phone')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('+92')}}" name="phone" id="dynamic_phone" value="" required>
                                </div>
                            </div>
                            <div class="form-group text-right">

                                <button type="submit"  onclick="sendAddress(event)" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Address Modal --}}
    <div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="edit_modal_body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        $('#updatePorfile').on('submit', function (e) {
            // Check if the #front-side-pic preview is empty
            if ($('#front-side-pic').is(':empty')) {
                // Show error message
                $('#file-error-front').show();

                // Prevent form submission
                e.preventDefault();
            } else {
                // Hide error message if preview is not empty
                $('#file-error.front').hide();
            }
        });

        $('#updatePorfile').on('submit', function (e) {
            // Check if the #front-side-pic preview is empty
            if ($('#back-side-pic').is(':empty')) {
                // Show error message
                $('#file-error-back').show();

                // Prevent form submission
                e.preventDefault();
            } else {
                // Hide error message if preview is not empty
                $('#file-error.back').hide();
            }
        });


document.getElementById('phone').addEventListener('input', function(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/\D/g, '');

     //     // Truncate to 11 characters if necessary
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }

    // Optional: Validate length
    if (this.value.length !== 11) {
        this.setCustomValidity('Phone number must be exactly 11 digits');
    } else {
        this.setCustomValidity('');
    }
});

document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'dynamic_phone') {
        // Remove any non-digit characters
        e.target.value = e.target.value.replace(/\D/g, '');

        // Truncate to 11 characters if necessary
        if (e.target.value.length > 11) {
            e.target.value = e.target.value.slice(0, 11);
        }

        // Optional: Validate length
        if (e.target.value.length !== 11) {
            e.target.setCustomValidity('Phone number must be exactly 11 digits');
        } else {
            e.target.setCustomValidity('');
        }
    }
});
    document.getElementById('ntn_no').addEventListener('input', function(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/\D/g, '');

        //     // Truncate to 11 characters if necessary
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }

    // Optional: Validate length
    if (this.value.length !== 13) {
        this.setCustomValidity('NTN number must be exactly 13 digits');
    } else {
        this.setCustomValidity('');
    }
    });

     document.getElementById('bank_acc_no').addEventListener('input', function (e) {

        // Truncate to 11 characters if necessary
        if (this.value.length > 24) {
            this.value = this.value.slice(0, 24);
        }

        if (this.value.length !== 24) {
        this.setCustomValidity('Bank Account number must be exactly 24 digits');
        } else {
            this.setCustomValidity('');
        }

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

    document.addEventListener('DOMContentLoaded', (event) => {
        const passwordField = document.getElementById('password_confirmation');
        const showConfirmPasswordIcon = document.getElementById('show-confirm-password-icon');
        const hideConfirmPasswordIcon = document.getElementById('hide-confirm-password-icon');

        showConfirmPasswordIcon.addEventListener('click', () => {
            passwordField.type = 'text';
            showConfirmPasswordIcon.style.display = 'none';
            hideConfirmPasswordIcon.style.display = 'block';
        });

        hideConfirmPasswordIcon.addEventListener('click', () => {
            passwordField.type = 'password';
            showConfirmPasswordIcon.style.display = 'block';
            hideConfirmPasswordIcon.style.display = 'none';
        });
    });


        // function validateMobile() {
        //     const value = mobileInput.value;
        //     if (value.length !== 11) {
        //         mobileError.textContent = 'The mobile number must be 11 digits long.';
        //         return false;
        //     } else {
        //         mobileError.textContent = '';
        //         return true;
        //     }
        // }

        function sendAddress(e) {
            // e.preventDefault();

            isFormModified = false;


//

            // $('#address-form').submit();

        }



        $('.new-email-verification').on('click', function() {
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            var email = $("input[name=email]").val();

            $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if(data.status == 2)
                    AIZ.plugins.notify('warning', data.message);
                else if(data.status == 1)
                    AIZ.plugins.notify('success', data.message);
                else
                    AIZ.plugins.notify('danger', data.message);
            });
        });

        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '{{ route("seller.addresses.edit", ":id") }}';
            url = url.replace(':id', address);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    @if (get_setting('google_map') == 1)
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = parseFloat(response.data.address_data.latitude);
                            long    = parseFloat(response.data.address_data.longitude);
                        }

                        initialize(lat, long, 'edit_');
                    @endif
                }
            });
        }

        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });

        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('seller.get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('seller.get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

    </script>
   <script>
    document.getElementById('cnic_no').addEventListener('input', function (e) {
        let value = e.target.value;
        // Remove any non-digit characters
        e.target.value = value.replace(/\D/g, '');
    });

    document.getElementById('cnic_no').addEventListener('keypress', function (e) {
        // Allow only digits and control keys (e.g., backspace, delete)
        if (e.key >= '0' && e.key <= '9') {
            return true; // Allow digits
        } else {
            e.preventDefault(); // Prevent other keys
        }
    });
</script>

    @if (get_setting('google_map') == 1)

        @include('frontend.partials.google_map')

    @endif

@endsection
