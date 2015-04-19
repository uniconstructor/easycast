@start('header')

    {{ $app->assets(['assets:vendor/uikit/js/components/nestable.min.js'], $app['cockpit/version']) }}
    {{ $app->assets(['autopilot:admin/assets/index.js'], $app['cockpit/version']) }}

    <script>

        var MENUES   = {{ json_encode($flattenMenues) }},
            SETTINGS = {{ json_encode($settings) }};

    </script>

    <style>

        .uk-nestable-item {
            background-color: #fff;
            padding: 15px;
        }

        .uk-nestable-item .uk-subnav { margin: 0; }
        .menu-item-inactive { border: 1px #f88192 solid; }

    </style>

@end('header')


<div ng-controller="menu" ng-cloak>

    <nav class="uk-navbar uk-margin-bottom">
        <span class="uk-navbar-brand">Autopilot</span>
        <ul class="uk-navbar-nav">
            <li><a href="@route('/autopilot/settings')" title="@lang('Settings')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-cogs"></i></a></li>
            <li data-uk-dropdown>
                <a href="@route('/autopilot/widgets')" title="@lang('Widgets')" data-uk-tooltip="{pos:'right'}"><i class="uk-icon-cubes"></i></a>
                <div class="uk-dropdown uk-dropdown-navbar">
                    <ul class="uk-nav uk-nav-navbar uk-nav-parent-icon">
                        <li><a href="@route('/autopilot/widgets')"><i class="uk-icon-th"></i> @lang('Overview')</a></li>
                        <li class="uk-nav-header">@lang("Add widget")</li>
                        @foreach($app->module('autopilot')->widgets as $type => $meta)
                        <li><a href="@route('/autopilot/widget/'.$type)"><i class="uk-icon-cube"></i> @lang($meta['name'])</a></li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>
    </nav>

    <div class="uk-grid" data-uk-grid-margin>

        <div class="uk-width-medium-3-4">

            <div class="app-panel">

                <div class="uk-text-center" ng-show="!(menues.main|count)">

                    <h2><i class="uk-icon-link"></i></h2>
                    <p class="uk-text-large">
                        @lang('No menu items.')
                    </p>

                    <a class="uk-button uk-button-success uk-button-large" data-uk-modal="{target:'#modal-link'}">@lang('Create a menu item')</a>

                </div>

                <div ng-show="menues.main|count">

                    <div><span class="uk-badge app-badge uk-text-uppercase">Main menu</span></div>

                    @render('autopilot:admin/views/menu.php', ['menuId'=>'main', 'menu'=>'main', 'items'=>$menues['main']])

                </div>

            </div>

        </div>

        <div class="uk-width-medium-1-4">

            <div class="uk-panel app-panel-box uk-form">

                <div class="uk-margin-small-bottom uk-text-bold">
                    <a class="uk-link-muted" href="{{ $app->base('site:') }}" target="_blank">Site</a>
                </div>
                <div class="uk-text-small uk-margin-bottom uk-text-truncate">
                    <a class="uk-text-muted uk-link-muted" href="{{ $app->base('site:') }}" target="_blank">{{ dirname($app->getSiteUrl(true)) }}</a>
                </div>
                <div class="uk-form-row">
                    <span class="uk-text-small uk-text-upper">@lang('Maintenance Mode')</span>
                    <div class="uk-margin-small-top">
                        <button type="button" class="uk-button uk-button-large uk-width-1-1" ng-class="settings.maintenance ? 'uk-button-primary':''" ng-click="(settings.maintenance = !settings.maintenance)">
                            <span ng-class="settings.maintenance ? 'uk-icon-check':'uk-icon-ban'"></span>
                        </button>
                    </div>
                </div>

                <div class="uk-form-row" ng-show="menues.main|count">

                    <span class="uk-text-small uk-text-upper">@lang('Homepage')</span>
                    <div class="uk-form-controls uk-margin-small-top uk-width-1-1">
                        <div class="uk-form-select uk-width-1-1">

                            <button type="button" class="uk-button uk-button-large uk-width-1-1" ng-show="!menues.main[settings.homepage]['data'].title">
                                @lang('Please select a link...')
                            </button>

                            <span ng-show="menues.main[settings.homepage]['data'].title">
                                <i class="uk-icon-home uk-margin-small-right"></i>
                                @@ menues.main[settings.homepage]['data'].title @@
                            </span>

                            <select id="select-homepage">
                                <option value="">- @lang('Select...') -</option>
                                <option value="@@ id @@" ng-repeat="(id, item) in menues.main">@@ item.data.title @@</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <a class="uk-button uk-button-large uk-button-success uk-width-1-1" data-uk-modal="{target:'#modal-link'}"><i class="uk-icon-plus-circle"></i> <span class="uk-hidden-small">@lang('Create menu item')</span></a>

            </div>
        </div>

    </div>


    <div id="modal-link" class="uk-modal">
        <div class="uk-modal-dialog">
            <a class="uk-modal-close uk-close"></a>

            <h3 class="uk-text-center">@lang('Choose a link type')</h3>

            <p class="uk-text-center uk-text-muted uk-margin-top">
                @lang('Please select a type')
            </p>

            <div class="uk-grid uk-grid-width-1-4 uk-text-center uk-margin-large-top" data-uk-grid-margin data-uk-grid-match>

                @foreach($app->module('autopilot')->types as $type => $meta)
                <div>
                    <a href="@route('/autopilot/menuItem/main/%s', $type)">
                        <p class="uk-text-large">
                            <i class="uk-icon-list-alt"></i>
                        </p>
                        <p>@lang($meta['name'])</p>
                    </a>
                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>
