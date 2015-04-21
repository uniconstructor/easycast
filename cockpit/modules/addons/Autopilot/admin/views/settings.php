@start('header')

    {{ $app->assets(['autopilot:admin/assets/settings.js'], $app['cockpit/version']) }}

    <script>

        var SETTINGS = {{ json_encode($settings) }};

    </script>

@end('header')

<div data-ng-controller="settings" ng-cloak>

    <h1>
        <a href="@route("/autopilot")">Autopilot</a> /
        @lang('Settings')
    </h1>

    <div class="uk-grid">
        <div class="uk-width-medium-2-3">
           <div class="app-panel">

                <form class="uk-form" data-ng-submit="save()">

                    <p>
                        <span class="uk-badge app-badge">@lang('Site')</span>
                    </p>

                    <div class="uk-form-row">
                        <span class="uk-text-small uk-text-upper">@lang('Maintenance Mode')</span>
                        <div class="uk-margin-small-top">
                            <button type="button" class="uk-button uk-button-large" ng-class="settings.maintenance ? 'uk-button-primary':''" ng-click="(settings.maintenance = !settings.maintenance)">
                                <span ng-class="settings.maintenance ? 'uk-icon-check':'uk-icon-ban'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-text-small uk-text-upper">@lang('Site cache')</span>
                        <div class="uk-margin-small-top">
                            <button type="button" class="uk-button uk-button-large" ng-class="settings.cache ? 'uk-button-primary':''" ng-click="(settings.cache = !settings.cache)">
                                <span ng-class="settings.cache ? 'uk-icon-check':'uk-icon-ban'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-text-small uk-text-upper">@lang('Title')</span>
                        <div class="uk-margin-small-top">
                            <input type="text" class="uk-form-large uk-width-1-1" ng-model="settings.title">
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-text-small uk-text-upper">@lang('Description')</span>
                        <div class="uk-margin-small-top">
                            <textarea class="uk-form-large uk-width-1-1" ng-model="settings.description" style="min-height:100px;">

                            </textarea>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <button type="submit" class="uk-button uk-button-primary uk-button-large">@lang('Save')</button> &nbsp;
                        <a href="@route('/autopilot')">@lang('Close')</a>
                    </div>

                </form>
           </div>
        </div>
    </div>

</div>