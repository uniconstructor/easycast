<div class="uk-grid uk-grid-divider uk-text-center">
    <div class="uk-width-1-2">
        <a class="uk-display-block" href="@route('/autopilot')">
            <div class="uk-h1">{{ $menuItems }}</div>
            <div class="uk-margin-top">@lang('Menu items')</div>
        </a>
    </div>
    <div class="uk-width-1-2">
        <a class="uk-display-block" href="@route('/autopilot/widgets')">
            <div class="uk-h1">{{ $widgets }}</div>
            <div class="uk-margin-top">@lang('Widgets')</div>
        </a>
    </div>
</div>