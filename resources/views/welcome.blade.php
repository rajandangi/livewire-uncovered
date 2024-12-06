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

    <script>
        // find all elements with wire:snapshot attribute
        document.querySelectorAll('[wire\\:snapshot]').forEach(el => {
            let snapshot = JSON.parse(el.getAttribute('wire:snapshot'));
            console.log(snapshot)
        });
    </script>
</body>

</html>
