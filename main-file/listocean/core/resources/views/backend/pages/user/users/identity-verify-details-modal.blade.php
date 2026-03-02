<!-- State Edit Modal -->
<div class="modal fade" id="userIdentityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Verify User Identity') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" id="user_identity_details">
                </div>
                <div class="modal-footer">
                    <button type="button" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    @can('user-verify-decline')
                    <button type="submit" class="cmnBtn btn_5 btn_bg_danger radius-5 user_identity_decline">{{__('Decline Verify Identity')}}</button>
                    @endcan
                    @can('user-verify-status')
                    <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5 user_verify_status">{{__('Update Verify Identity Status')}}</button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>
