@props([
    // The page's name, shown in the tab before the app's.
    'title' => null,
])

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>
{{-- Inline so the theme is on <html> before anything is drawn. Printed raw: it's the app's own file, never user input. --}}
<script>{!! file_get_contents(resource_path('js/theme-boot.js')) !!}</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
