<div class="option_values_form_copier" style="display:none">
    <div class="content">
        @foreach (config('translatable.locales') as $code)
        <div class="form-group">
            <label class="col-md-2">
                {{__('variation::dashboard.options.form.title')}} - {{ $code }}
            </label>
            <div class="col-md-9">
                <input type="text" name="option_value_title[{{$code}}][]" class="form-control" data-name="option_value_title.{{$code}}">
                <div class="help-block"></div>
            </div>
        </div>
        @endforeach
        <div class="form-group">
            <label class="col-md-2">
                {{__('variation::dashboard.options.form.status')}}
            </label>
            <div class="col-md-9">
                <input type="checkbox" class="ischecked" name="option_value_status[]" value="1" onclick="checkFunction()">
                <input type="hidden" class="isUnchecked" name="option_value_status[]" value="0" checked>
            </div>
        </div>
        <div class="form-group">
            <a href="javascript:;" class="remove_html btn red">
                <i class="fa fa-times"></i>
            </a>
        </div>
        <hr>
    </div>
</div>
