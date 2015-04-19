{%
    $slug    = isset($slug)    ? $slug : '';
    $level   = isset($level)   ? $level : 0;
    $options = isset($options) ? $options : [];

    $ulClass   = [];
    $ulClass[] = ($level===0 && isset($options['class'])) ? $options['class'] : '';
%}

<ul class="{{ implode(' ', $ulClass) }}" data-level="{{ $level }}">

    @foreach($items as &$item)

        @if($item['active'])

        <li class="{{ (strpos($frontend['route'].'/', $slug.'/'.$item['slug'].'/')===0) ? 'active':'' }}" data-level="{{ $level }}">

            @if($item["type"]=="link")
                <a href="{{ (strpos($item['link'], '://') === false) ? $app->baseUrl($item['link']) : $item['link'] }}"><span>{{ $item['title'] }}</span></a>
            @else
                <a href="@route($slug.'/'.$item['slug'])"><span>{{ $item['title'] }}</span></a>
            @endif

            @if(isset($item['children']) && count($item['children']))
                @render('autopilot:renderers/menu.php', ['items'=>$item['children'],'slug'=>$slug.'/'.$item['slug'], 'level'=> $level+1, 'options' => $options])
            @endif

            @if($app['meta/admin'])
            <script type="autopilot/resource" data-type="menu-link" data-id="{{ $item['_id'] }}"></script>
            @endif
        </li>

        @endif

    @endforeach

</ul>