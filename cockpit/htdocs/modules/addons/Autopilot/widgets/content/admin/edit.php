<div class="uk-form-row" ng-init="widget.settings.content = widget.settings.content || [{'type':'html', 'value':''}]">
    <label><span class="uk-badge app-badge">@lang('Content')</span></label>
    <hr>
    <div class="uk-margin-top">
        <contentfield options='{"type": "multifield", "allowedfields":["html","wysiwyg", "markdown", "code"]}' ng-model="widget.settings.content"></contentfield>
    </div>
</div>