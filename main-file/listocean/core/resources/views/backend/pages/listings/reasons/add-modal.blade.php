<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Add New Reason') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.report.reason.all')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <x-form.text :title="__('Title')" :type="__('text')" :name="'title'" :id="'title'" :value="old('title', '')" :placeholder="__('Enter Reason title')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5 add_reason">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
