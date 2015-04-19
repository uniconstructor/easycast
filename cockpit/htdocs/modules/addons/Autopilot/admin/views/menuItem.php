@start('header')

    {{ $app->assets(['autopilot:admin/assets/menuItem.js'], $app['cockpit/version']) }}

    @trigger('cockpit.content.fields.sources')

    <script>

        var MENU         = '{{ $menu }}',
            MENUITEM     = {{ json_encode($menuItem) }},
            MENUITEMDATA = {{ json_encode($data) }},
            MENUITEMTYPE = '{{ $type }}';

    </script>

    <style>
        .menu-item-slug {
            display: inline-block;
            height: 30px;
            line-height: 30px;
        }

        .menu-item-slug + input[type="text"] {
            padding: 0;
        }
    </style>

@end('header')

<div data-ng-controller="menuItem" ng-cloak>

    <h1>
        <a href="@route("/autopilot")">Autopilot</a> /
        <span class="uk-text-muted" ng-show="!item.title">@lang('Title')</span>
        <span ng-show="item.title">@@ item.title @@</span>
    </h1>

    <form class="uk-form" data-ng-submit="save()">

        <div class="uk-grid uk-margin">

            <div class="uk-width-3-4">

                <div class="uk-form-row">
                    <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Title')" data-ng-model="item.title" required>
                    <div class="uk-margin-top uk-clearfix">
                        <span class="uk-float-left menu-item-slug uk-text-muted">{{ $slug }}/</span>
                        <input class="uk-float-left uk-width-1-2 uk-form-blank uk-text-muted" type="text" data-ng-model="item.slug" app-slug="item.title" placeholder="@lang('Slug...')" required>
                    </div>
                </div>

                @if($typeview)
                <div class="app-panel uk-margin">
                    @render($typeview)
                    @trigger('autopilot.type.edit.main', [$type])
                </div>
                @endif

                <p class="uk-margin-top">
                    <button class="uk-button uk-button-large uk-button-success">Save</button> &nbsp; <a href="@route('/autopilot')">@lang('Cancel')</a>
                </p>
            </div>

            @if($type!='link')
            <div class="uk-width-1-4 uk-form">

                <div class="uk-panel app-panel-box">

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Status')</label>
                        <div class="uk-margin-small-top">
                            <button type="button" class="uk-button uk-button-large uk-width-1-1" ng-class="item.active ? 'uk-button-success':'uk-button-danger'" ng-click="(item.active = !item.active)">
                                <span ng-class="item.active ? 'uk-icon-check':'uk-icon-ban'"></span>
                            </button>
                        </div>
                    </div>

                    <h4><i class="uk-icon-cog"></i> @lang('Page meta')</h4>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Page description')</label>
                        <textarea class="uk-width-1-1 uk-margin-small-top" ng-model="data.meta.description"></textarea>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Page keywords')</label>
                        <textarea class="uk-width-1-1 uk-margin-small-top" ng-model="data.meta.keywords"></textarea>
                    </div>

                    @trigger('autopilot.type.edit.aside', [$type])
                </div>
            </div>
            @endif
        </div>

    </form>

</div>
