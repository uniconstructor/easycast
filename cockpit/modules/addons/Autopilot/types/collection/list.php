@extend('theme:layout.php')


<h2>Collection items list view</h2>

<p>
    Please override this view to your needs.
</p>

@if(count($items))

    @foreach($items as $item)

        @foreach($item as $key => $val)

        <div>
            <strong>{{ $key }}</strong>
        </div>
        <p>
            {{ is_string($val) ? $val : json_encode($val) }}
        </p>

        @endforeach
        <p>
            <a href="@route('%s/item-%s', $link['slug_path'], $item['_id'])">Show detail view</a>
        </p>
        <hr>

    @endforeach

    @if($pages > 1)
    <div class="pagination">
        <?php for($i=1;$i<=$pages;$i++): ?>

            @if($page==$i)
                <span>{{ $i }}</span>
            @else
                <a href="@route('%s?page=%s', $link['slug_path'], $i)">{{ $i }}</a>
            @endif

        <?php endfor; ?>
    </div>
    @endif

@else

    No items.

@endif