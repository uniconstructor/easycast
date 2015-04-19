<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{{ $frontend->meta['title'] }}</title>

        <meta name="description" content="{{ $frontend->meta['description'] }}">
        <meta name="keywords" content="{{ $frontend->meta['keywords'] }}" />

        <!-- themes default assets -->
        @assets(['theme:assets/css/site.css', 'theme:assets/js/dom.js'])
        <!-- dynamic assets -->
        @assets($frontend->assets)
        <!-- autopilot forntend header event-->
        @trigger('autopilot.frontend.header')
    </head>
    <body>

        <div class="site-header">
            <div class="wrapper">
                <h1><a href="@route('/')">{{ $frontend['settings/title'] ? $frontend['settings/title'] : 'Autopilot' }}</a></h1>
                <nav class="site-main-nav">
                    @menu('main')
                </nav>
            </div>
        </div>

        @widgets?('top')
        <div class="site-top">
            <div class="wrapper">
                <div class="grid-cells-medium-1-4 grid-cells-1-1 grid-center">
                    @widgets('top')
                </div>
            </div>
        </div>
        @end

        <div class="site-content">
            <div class="wrapper">
                <div class="grid">

                    @widgets?('sidebar-left')
                    <div class="grid-cell-medium-1-4 site-layout-left">
                        <div class="grid-cells-1-1">
                            @widgets('sidebar-left')
                        </div>
                    </div>
                    @end

                    <div class="grid-cell-medium grid-cell-1-1 site-layout-main">
                        {{ $content_for_layout }}
                    </div>

                    @widgets?('sidebar-right')
                    <div class="grid-cell-medium-1-4 site-layout-right">
                        <div class="grid-cells-1-1">
                            @widgets('sidebar-right')
                        </div>
                    </div>
                    @end

                </div>
            </div>
        </div>

        @widgets?('footer')
        <div class="site-footer">
            <div class="wrapper">
                <div class="grid-cells-medium-1-4 grid-cells-1-1 grid-center">
                    @widgets('footer')
                </div>
            </div>
        </div>
        @end

        @trigger('autopilot.frontend.footer')
    </body>
</html>