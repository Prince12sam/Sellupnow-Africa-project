<!-- Country Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Add New Department') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.department')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <x-notice.general-notice :description="__('Notice: If you select inactive status the department will not show while create a ticket.')" :class="'mb-5'" />
                    <x-form.text :title="__('Department')" :type="__('text')" :name="'name'" :id="'name'" :placeholder="__('Enter department name')"/>
                    <x-form.active-inactive :title="__('Select Status')" />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Submit')}}</button>
                    <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>
