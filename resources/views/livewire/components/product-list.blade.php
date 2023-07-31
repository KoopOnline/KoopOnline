<div>
    <ul>
        @foreach ($productResults as $results)
            <li>{{$results['name']}}</li>
        @endforeach
    </ul>
    {{ $productResults->links() }}
</div>

