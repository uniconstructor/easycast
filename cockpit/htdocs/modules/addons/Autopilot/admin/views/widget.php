@start('header')

    {{ $app->assets(['autopilot:admin/assets/widget.js'], $app['cockpit/version']) }}

    @trigger('cockpit.content.fields.sources')

    <script>

        var WIDGET    = {{ json_encode($widget) }},
            POSITIONS = {{ json_encode($positions) }};

    </script>

@end('header')

<div data-ng-controller="widget" ng-cloak>

    <h1>
        <a href="@route("/autopilot")">Autopilot</a> /
        <a href="@route("/autopilot/widgets")">Widgets</a> /
        <span class="uk-text-muted" ng-show="!widget.title">@lang('Title')</span>
        <span ng-show="widget.title">@@ widget.title @@</span>
    </h1>

    <form class="uk-form" data-ng-submit="save()">

        <div class="uk-grid uk-margin">

            <div class="uk-width-3-4">

                <div class="uk-form-row">
                    <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Title')" data-ng-model="widget.title" required>
                </div>

                <div class="app-panel uk-margin">

                    <ul id="widget-views" class="uk-switcher">
                        <li>

                            @if($typeview)
                                @render($typeview)
                            @endif

                            @trigger('autopilot.widget.edit.main', [$type])

                        </li>
                        <li>

                            <strong>@lang('Menues')</strong>
                            <hr>

                            <div class="uk-grid uk-grid-width-1-3 uk-margin-large-bottom" data-uk-grid-margin data-uk-grid-match>
                                @foreach($menues as $name => $menu)

                                <div>
                                    <div class="uk-margin-bottom">
                                        <span class="uk-badge app-badge">{{ $name }}</span>
                                    </div>
                                    @if(count($menu))
                                        <ul class="uk-list uk-list-space uk-text-small">
                                        @foreach($menu as $id => $link)
                                        <li>
                                            <input type="checkbox" id="{{ $id }}" class="js-link-acl">
                                            <span style="margin-left: {{ (5 + ($link['level'] * 10)) }}px">{{ $link['data']['title'] }}</span>
                                        </li>
                                        @endforeach
                                        </ul>
                                    @else
                                        <span class="uk-text-muted">@lang('No links yet.')</span>
                                    @endif
                                </div>

                            @endforeach
                            </div>


                            <strong>@lang('Pattern')</strong>
                            <hr>
                            <textarea class="uk-width-1-1" ng-model="widget.ac_patterns" style="height:250px;"></textarea>
                        </li>
                    </ul>


                </div>

                <div class="uk-margin">
                    <button class="uk-button uk-button-large uk-button-success">Save</button> &nbsp; <a href="@route('/autopilot/widgets')">@lang('Cancel')</a>
                </div>

            </div>

            <div class="uk-width-1-4 uk-form">

                <div class="uk-panel app-panel-box">

                    <div class="uk-form-row">
                        <ul class="uk-nav uk-nav-side uk-nav-plain uk-nav-parent-icon" data-uk-switcher="{connect:'#widget-views', toggle:'>*:not(.uk-nav-header)'}">
                            <li class="uk-nav-header">@lang("Settings")</li>
                            <li class="uk-active"><a><i class="uk-icon-cog"></i> @lang('General')</a></li>
                            <li><a><i class="uk-icon-eye"></i> @lang('Visibility')</a></li>
                        </ul>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Status')</label>
                        <div class="uk-margin-small-top">
                            <button type="button" class="uk-button uk-button-large uk-width-1-1" ng-class="widget.active ? 'uk-button-success':'uk-button-danger'" ng-click="(widget.active = !widget.active)">
                                <span ng-class="widget.active ? 'uk-icon-check':'uk-icon-ban'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Position')</label>
                        <div class="uk-margin-small-top">
                            <select class="uk-form-large uk-width-1-1" ng-model="widget.position">
                                @foreach($positions as $position)
                                <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @trigger('autopilot.widget.edit.aside', [$type])

            </div>
        </div>
    </form>

</div>
