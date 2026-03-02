<div class="modal fade" id="adminPasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Change Password') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.password.change') }}" method="post" id="userPasswordModalForm">
                <input type="hidden" name="admin_id_for_change_password" id="admin_id_for_change_password" value="">
                @csrf
                <div class="modal-body">
                    <x-form.password :title="__('Enter new password')" :name="'password'" :id="'password'" :class="'form-control'" :placeholder="__('Enter New password')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button href="{{ route('admin.create') }}" class="cmnBtn btn_5 btn_bg_blue radius-5 update_admin_password">{{__('Change Password')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
