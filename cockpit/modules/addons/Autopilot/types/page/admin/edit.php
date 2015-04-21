<div class="uk-form-row" ng-init="data.content = data.content || [{'type':'html', 'value':''}]">
    <label><span class="uk-badge app-badge">@lang('Content')</span></label>
    <hr>
    <div class="uk-margin-top">
        <contentfield options='{"type": "multifield"}' ng-model="data.content"></contentfield>
    </div>
</div>