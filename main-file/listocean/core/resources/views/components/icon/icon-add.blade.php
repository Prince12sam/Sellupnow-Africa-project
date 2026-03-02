<div class="form__input__single">
    <label for="icon" class="d-block form__input__single__label">{{__('Category Icon')}}</label>
    <div class="btn-group icon">
        <button type="button" class="btn btn-primary iconpicker-component">
            <i class="fas fa-exclamation-triangle"></i>
        </button>
        <button  type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                 data-selected="fas fa-phone"
                 data-bs-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">{{__('Toggle Dropdown')}}</span>
        </button>
        <div class="dropdown-menu"></div>
    </div>
    <input type="hidden" class="form__control radius-5" name="icon" id="icon" value="fas fa-exclamation-triangle">
</div>
