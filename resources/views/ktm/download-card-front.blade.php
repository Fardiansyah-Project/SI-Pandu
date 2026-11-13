<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1011, initial-scale=1">
    <style>
        {!! $cardStyles !!}

        html,
        body {
            width: 1011px;
            height: 639px;
            margin: 0;
            overflow: hidden;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    @include('ktm.partials.card-front', [
        'logoSource' => $logoSource,
        'photoSource' => $photoSource,
        'topWaveSource' => $topWaveSource,
        'bottomWaveSource' => $bottomWaveSource,
    ])
</body>

</html>
