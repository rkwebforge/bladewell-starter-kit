@props([
    // The page's name, shown in the tab before the app's.
    'title' => null,
])

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
