<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>

<body>
    {{-- <livewire:counter /> --}}

    {!! livewire(App\Http\Livewire\Counter::class) !!}


    {{-- @livewireScripts() --}}
</body>

</html>

<?php
function livewire($class)
{
    $component = new $class();

    return Blade::render($component->render(), getProperties($component));
}

function getProperties($component)
{
    $properties = [];

    $reflectedProperties = (new ReflectionClass($component))->getProperties(ReflectionProperty::IS_PUBLIC);

    foreach ($reflectedProperties as $property) {
        $properties[$property->getName()] = $property->getValue($component);
    }

    dd($properties);
    
    return $properties;
}
