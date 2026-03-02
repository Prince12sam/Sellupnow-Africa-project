<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Add New Ticket') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('user.ticket')}}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <x-form.text :title="__('Title')" :type="__('text')" :name="'title'" :id="'title'" :placeholder="__('Enter ticket title')"/>
                    <div class="single-input mb-3">
                        <label for="priority" class="label-title">{{ __('Department') }}</label>
                        <select name="department" id="department" class="form-control select2_activation">
                            <option value="" disabled>{{ __('Select Department') }}</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="single-input mb-3">
                        <label for="priority" class="label-title">{{ __('Priority') }}</label>
                        <select name="priority" id="priority" class="form-control select2_activation">
                            <option value="" disabled>{{ __('Select Priority') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                            <option value="high">{{ __('High') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="low">{{ __('Low') }}</option>
                        </select>
                    </div>
                    <x-form.summernote :title="__('Description')" :name="'description'" :id="'description'" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="red-global-close-btn mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <x-btn.submit-btn :title="__('Create Ticket')" :class="'red-global-btn mt-4 pr-4 pl-4 add_ticket'" />
                </div>
            </form>
        </div>
    </div>
</div>
