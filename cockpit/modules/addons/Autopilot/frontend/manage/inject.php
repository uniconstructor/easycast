
<script>

var AUTOPILOT = {
    "cockpitUrl": "{{ $app->pathToUrl('cockpit:') }}",
    "baseUrl"   : "{{ $app->pathToUrl('site:') }}",
    "bench"     : {
        "memory" : "{{ $app('utils')->formatSize(AUTOPILOT_MEMORY_USAGE) }}",
        "time"   : "{{ round(AUTOPILOT_TIME_DURATION * 1000) }} ms"
    }
};

</script>

@assets([ 'autopilot:frontend/assets/autopilot.frontend.css', 'autopilot:frontend/assets/autopilot.frontend.js' ])