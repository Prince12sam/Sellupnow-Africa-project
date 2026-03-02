<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Edit Role') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.role.edit') }}" method="post" id="editRoleModalForm">
                <input type="hidden" name="role_id" id="role_id">
                @csrf
                <div class="modal-body">
                    <x-form.text :title="__('Enter Role Name')" :type="'text'" :name="'role_name'" :id="'role_name'" :class="'form-control'" :placeholder="__('Enter role name')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

