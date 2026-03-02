<div class="modal fade" id="editReasonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Edit Reason') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.report.reason.edit')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reason_id" id="reason_id" value="">
                <div class="modal-body">
                    <x-form.text :title="__('Title')" :type="__('text')" :name="'title'" :id="'edit_title'" :value="''" :placeholder="__('Enter Reason Title')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5 edit_reason">{{ __('Update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
