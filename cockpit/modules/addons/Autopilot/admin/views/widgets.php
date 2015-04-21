@start('header')

    {{ $app->assets(['autopilot:admin/assets/widgets.js'], $app['cockpit/version']) }}
    {{ $app->assets(['assets:vendor/uikit/js/components/sortable.min.js'], $app['cockpit/version']) }}

    <script>

        var POSITIONS = {{ json_encode($positions) }},
            WIDGETS   = {{ json_encode($widgets) }};

    </script>

@end('header')

<div class="uk-form" data-ng-controller="widgets" ng-cloak>

    <nav class="uk-navbar uk-margin-bottom">
        <span class="uk-navbar-brand">
            <a href="@route("/autopilot")">Autopilot</a> / @lang('Widgets')
        </span>
        <ul class="uk-navbar-nav">
            <li data-uk-dropdown>
                <a title="@lang('Add widget')" data-uk-tooltip="{pos:'right'}"><i class="uk-icon-plus-circle"></i></a>
                <div class="uk-dropdown uk-dropdown-navbar uk-nav-parent-icon">
                    <ul class="uk-nav uk-nav-navbar">
                        <li class="uk-nav-header">@lang("Widget type")</li>
                        @foreach($app->module('autopilot')->widgets as $type => $meta)
                        <li><a href="@route('/autopilot/widget/'.$type)"><i class="uk-icon-cube"></i> @lang($meta['name'])</a></li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>
    </nav>

    <div class="uk-grid uk-grid-divider" data-ng-show="widgets && widgets.length">
        <div class="uk-width-medium-1-4">

            <div class="uk-form-icon uk-margin-bottom uk-width-1-1">
                <i class="uk-icon-filter"></i>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Filter by title...')" data-ng-model="filter">
            </div>


            <ul class="uk-nav uk-nav-side uk-nav-plain">
                <li class="uk-nav-header">@lang("Positions")</li>
                <li ng-class="activepos=='-all' ? 'uk-active':''" ng-click="(activepos='-all')"><a><i class="uk-icon-th"></i>  @lang("All")</a></li>
                <li class="uk-nav-divider"></li>
                <li ng-repeat="position in positions" ng-class="$parent.activepos==position ? 'uk-active':''" ng-click="($parent.activepos=position)">
                    <a>@@ position @@</a>
                </li>
            </ul>


        </div>
        <div class="uk-width-medium-3-4">

           <div class="uk-margin-bottom">
               <span class="uk-badge app-badge">@@ (activepos=='-all' ? '@lang("All widgets")' : activepos) @@</span>
           </div>

           <div id="js-widgets" class="uk-grid uk-grid-width-1-1 uk-grid-width-medium-1-3" data-ng-show="widgets && widgets.length" data-uk-sortable>

               <div class="uk-grid-margin" data-ng-repeat="widget in widgets track by widget._id" data-ng-show="matchTitle(widget.title) && inPosition(widget.position)" data-index="@@ $index @@" data-position="@@ widget.position @@">

                   <div class="app-panel">

                       <a title="@lang('Toggle active status')" ng-click="updateWidget(widget, 'active', !widget.active)"><i ng-class="widget.active ? 'uk-icon-circle uk-text-success':'uk-icon-circle-o uk-text-danger'"></i></a>
                       <a class="uk-link-muted" href="@route('/autopilot/widget')/@@ widget.type @@/@@ widget._id @@"><strong>@@ widget.title @@</strong></a>

                       <div class="uk-margin">
                           <span class="uk-badge app-badge">@@ widget.type @@</span>
                           <span class="uk-badge uk-form-select" ng-class="widget.position ? '':'uk-badge-warning'">
                               @@ widget.position || "@lang('No position')" @@
                               <select ng-model="widget.position" data-position-for="@@ widget._id @@" ng-change="updateWidget(widget, 'position', widget.position)">
                                   <option value="">@lang('No position')</option>
                                   @foreach($positions as $position)
                                        <option value="{{ $position }}">{{ $position }}</option>
                                   @endforeach
                               </select>
                           </span>
                       </div>

                       <div class="app-panel-box docked-bottom">

                           <div class="uk-link" data-uk-dropdown="{mode:'click'}">
                               <i class="uk-icon-bars"></i>
                               <div class="uk-dropdown">
                                   <ul class="uk-nav uk-nav-dropdown uk-nav-parent-icon">
                                       <li><a href="@route('/autopilot/widget')/@@ widget.type @@/@@ widget._id @@"><i class="uk-icon-pencil"></i> @lang('Edit widget')</a></li>
                                       <li><a ng-click="duplicate(widget._id)"><i class="uk-icon-copy"></i> @lang('Duplicate widget')</a></li>
                                       <li class="uk-nav-divider"></li>
                                       <li class="uk-danger"><a data-ng-click="remove($index, widget)" href="#"><i class="uk-icon-minus-circle"></i> @lang('Delete widget')</a></li>
                                   </ul>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>

            </div>

        </div>
    </div>

    <div class="uk-text-center app-panel" data-ng-show="widgets && !widgets.length">
        <h2><i class="uk-icon-cubes"></i></h2>
        <p class="uk-text-large">
            @lang('You don\'t have any widgets created.')
        </p>

        <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">

            <button type="button" class="uk-button uk-button-large uk-button-success">@lang('Create a widget')</button>

            <div class="uk-dropdown uk-dropdown-center uk-nav-parent-icon uk-text-left">
                <ul class="uk-nav uk-nav-navbar">
                    @foreach($app->module('autopilot')->widgets as $type => $meta)
                    <li><a href="@route('/autopilot/widget/'.$type)"><i class="uk-icon-cube"></i> @lang($meta['name'])</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

</div>