@if(isset($settings['content']) && count($settings['content']))


    @foreach($settings['content'] as $content)
    <section>
    {%

        switch($content['type']) {

            case 'region':

                if (trim($content['value'])) {
                    $cockpit->module('regions')->render($content['value']);
                }

                break;

            case 'markdown':

                echo markdown($content['value']);

                break;

            case 'media':

                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $content['value'])) {
                %}
                    <img src="{{ $app->pathToUrl($content['value']) }}">
                {%
                } elseif(preg_match('/\.(mp4|mpeg|webv|ogv)$/i', $content['value'])) {
                %}
                    <video src="{{ $app->pathToUrl($content['value']) }}"></video>
                {%
                } else {
                    echo $content['value'];
                }

                break;

            case 'gallery':

                if (count($content['value'])) {

                    foreach($content['value'] as &$item) {
                    %}
                        <img src="{{ $app->pathToUrl($item['path']) }}" alt="{{ $item['title'] }}">
                    {%
                    }
                }

                break;

            case 'markdown':

                echo markdown($content['value']);

                break;

            default:
                echo is_string($content['value']) ? $content['value'] : json_encode($content['value']);
        }

    %}
    </section>
    @endforeach

@endif