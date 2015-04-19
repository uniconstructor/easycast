<ul id ="menu-{{ @$menuId}}" class="{{ isset($menuId) ? 'uk-nestable' : 'uk-nestable-list' }}" data-menu="{{ $menu }}" >
    @foreach($items as &$item)
    <li id="{{ $item['_id'] }}" class="uk-nestable-list-item js-menu-item">

        <div class="uk-nestable-item uk-visible-hover" ng-class="menues['{{ $menu }}']['{{ $item['_id'] }}'].data.active ? '':'menu-item-inactive'">

            <div class="uk-nestable-handle uk-margin-small-right"></div>
            <div data-nestable-action="toggle"></div>

            <a class="uk-margin-small-left" ng-click="toggleActive('main', '{{ $item['_id'] }}')" ng-class="menues['{{ $menu }}']['{{ $item['_id'] }}'].data.active ? 'uk-icon-circle uk-text-success':'uk-icon-circle-o uk-text-danger'"></a>

            <strong class="uk-margin-small-left" ng-class="menues['{{ $menu }}']['{{ $item['_id'] }}'].data.active ? '':'uk-text-danger'">
                <a class="uk-link-muted" href="@route('/autopilot/menuItem/%s/%s/%s', $menu, $item['type'], $item['_id'])">{{ $item['title'] }}</a>
            </strong>


            <div class="uk-float-right uk-hidden">

                <a href="{{ rtrim($app->baseUrl('site:'), '/') }}@@ menues['{{ $menu }}']['{{ $item['_id'] }}'].slug_path @@" class="uk-text-muted uk-text-small uk-margin-right" target="_blank">
                    @@ menues['{{ $menu }}']['{{ $item['_id'] }}'].slug_path @@
                </a>

                <a class="uk-button uk-button-mini uk-button-primary" href="@route('/autopilot/menuItem/%s/%s/%s', $menu, $item['type'], $item['_id'])"><i class="uk-icon-pencil"></i></a>
                <a class="uk-button uk-button-mini uk-button-danger" ng-click="remove('main', '{{ $item['_id'] }}')"><i class="uk-icon-times"></i></a>

            </div>
        </div>

        @if(isset($item['children']) && count($item['children']))
            @render('autopilot:admin/views/menu.php', ['items'=>$item['children'], 'menu'=>$menu])
        @endif
    </li>
    @endforeach
</ul>