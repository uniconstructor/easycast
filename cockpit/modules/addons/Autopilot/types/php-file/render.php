<h2>{{ $link['title'] }}</h2>

@if(isset($data['file']) && $data['file'])

    @if($realpath = $app->path($data['file']))
        {{ $app->view($realpath) }}
    @endif

@endif