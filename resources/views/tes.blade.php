<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City List from Unit Kerja</title>
</head>
<body>
    <h1>Cities from Unit Kerja</h1>
    <ul>
        @foreach($cities as $city)
            <li>{{ $city }}</li>
        @endforeach
    </ul>
</body>
</html>