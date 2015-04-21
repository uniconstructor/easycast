@extend('theme:layout.php')


<h2>Collection item detail view</h2>

<p>
    Please override this view to your needs.
</p>

<table>
@foreach($item as $key => $val)

<div>
    <strong>{{ $key }}</strong>
</div>
<p>
    {{ is_string($val) ? $val : json_encode($val) }}
</p>

@endforeach
</table>