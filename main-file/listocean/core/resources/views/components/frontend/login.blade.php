<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="LoginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="#" method="post">
            @csrf
            <input type="hidden" id="membership_price">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="LoginModalLabel">
                        {{ __('Login to Comment') }}
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="error-message"></div>
                    <div class="input-form">
                        <label class="infoTitle">{{ __('Email Or User Name') }}</label>
                        <input class="form-control radius-10" type="text" name="username" id="username" placeholder="{{ __('Email Or User Name') }}">
                    </div>
                    <div class="single-input mt-4">
                        <label class="label-title mb-2"> {{ __('Password') }} </label>
                        <div class="input-form position-relative">
                            <input class="form-control radius-10" type="password" name="password" id="password" placeholder="{{ __('Type Password') }}">
                            <div class="icon toggle-password position-absolute">
                                <i class="las la-eye-slash"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-column">
                    <div class="btn-wrapper text-center">
                        <button type="submit" class="cmn-btn4 w-100 mb-60 login_to_buy_a_membership">{{ __('Login') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
