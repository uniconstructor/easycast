@foreach($widgets as &$widget)

    <div class="widget widget-position-{{ $position}}">

        @if($app['meta/admin'])
        <script type="autopilot/resource" data-type="widget" data-id="{{ $widget['_id'] }}"></script>
        @endif

        @if(!isset($widget['settings']['title']) || (isset($widget['settings']['title']) && !$widget['settings']['title']))
        <h4 class="widget-title">{{ $widget['title'] }}</h4>
        @endif

        {{ $cockpit->module("autopilot")->renderWidget($widget, $options) }}

    </div>

@endforeach