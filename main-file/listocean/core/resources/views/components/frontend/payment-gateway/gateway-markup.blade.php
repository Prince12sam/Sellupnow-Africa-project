<div class="modal fade" id="paymentGatewayModal" tabindex="-1" aria-labelledby="paymentGatewayModalLabel" aria-hidden="true">
    <div class="modal-dialog ab">
        <form action="{{ route('user.wallet.deposit') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="paymentGatewayModalLabel">{{ $title ?? '' }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-form.text
                        :type="'number'"
                        :title="__('Enter Deposit Amount')"
                        :name="'amount'"
                        :id="'amount'"
                        :placeholder="__('Max Limit: '). get_static_option('deposit_amount_limitation_for_user') ?? '3000' " />
                    <div class="confirm-payment payment-border">
                        <div class="single-checkbox">
                            <div class="checkbox-inlines">
                                <label class="checkbox-label" for="check2">
                                    {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm() !!}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-wrapper d-flex align-items-center gap-3">
                        <button type="button" class="red-global-close-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <x-btn.submit-btn :title="__('Deposit')" :class="'red-global-btn deposit_amount_to_wallet'" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

