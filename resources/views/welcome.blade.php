<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>

<body>
    {{-- <livewire:counter /> --}}

    @livewire(App\Http\Livewire\Counter::class)

    <script src="livewire.js"></script>
</body>

</html>
