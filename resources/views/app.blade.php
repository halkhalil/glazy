<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="author" content="Derek Au">
        <meta name="description" content="Glazy">

        <title>{{ config('app.name') }}</title>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:700,300,200" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <link href="{{ elixir('/css/app.css') }}" rel="stylesheet">
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'siteName'  => config('app.name'),
                'apiDomain' => config('app.url').'/api'
            ]) !!}
        </script>
    </head>

    <body>

        <div id="app">
            <app></app>
        </div>

        <script src="{{ elixir('/js/manifest.js') }}"></script>
        <script src="{{ elixir('/js/vendor.js') }}"></script>
        <script src="{{ elixir('/js/app.js') }}"></script>
    </body>
</html>
