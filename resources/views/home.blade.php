{{--
    Deliberately almost nothing. This package exists at the moment to prove two
    capabilities can share a server without fighting over the door, and adding
    an interface before that is settled would mean settling it by accident.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $identity->handle }}</title>
</head>
<body>
    <h1>A home on StreetMesh</h1>
    <p>{{ $identity->did }}</p>

    <nav>
        <ul>
            @foreach ($navigation as $item)
                <li><a href="{{ route($item['route']) }}">{{ $item['label'] }}</a></li>
            @endforeach
        </ul>
    </nav>
</body>
</html>
