<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;

class Livewire
{
    function initialRender($class)
    {
        $component = new $class();

        if (method_exists($component, 'mount')) {
            $component->mount();
        }

        [$html, $snapshot] = $this->toSnapshot($component);

        $snapshotAttribute = htmlentities(json_encode($snapshot));

        return <<<HTML
            <div wire:snapshot="{$snapshotAttribute}">
                {$html}
            </div>
            HTML;
    }

    // This is the method turns a component into a snapshot
    function toSnapshot($component)
    {
        $html = Blade::render(
            $component->render(),
            $properties = $this->getProperties($component)
        );

        [$data, $meta] = $this->dehydrateProperties($properties);

        $snapshot = [
            'class' => get_class($component),
            'data' => $data,
            'meta' => $meta,
        ];

        $snapshot = $this->generateChecksum($snapshot);

        return [$html, $snapshot];
    }

    function generateChecksum($snapshot)
    {
        $snapshot['checksum'] = md5(json_encode($snapshot));
        return $snapshot;
    }

    // creates meta data about the properties
    function dehydrateProperties($properties)
    {
        $data = $meta = [];

        foreach ($properties as $key => $value) {
            if ($value instanceof Collection) {
                $value = $value->toArray();
                $meta[$key] = 'collection';
            }
            $data[$key] = $value;
        }

        return [$data, $meta];
    }

    // This method truns a snapshot for a javascript
    function fromSnapshot($snapshot)
    {
        $checksum = $snapshot['checksum'];
        unset($snapshot['checksum']);

        if($checksum !== md5(json_encode($snapshot))) {
            throw new \Exception('Snapshot checksum failed');
        }

        $class = $snapshot['class'];
        $data = $snapshot['data'];
        $meta = $snapshot['meta'];

        $component = new $class();

        $properties = $this->hydrateProperties($data, $meta);

        $this->setProperties($component, $properties);

        return $component;
    }

    function hydrateProperties($data, $meta)
    {
        $properties = [];
        foreach ($data as $key => $value) {
            if (isset($meta[$key]) && $meta[$key] === 'collection') {
                $value = collect($value);
            }
            $properties[$key] = $value;
        }
        return $properties;
    }

    function setProperties($component, $data)
    {
        foreach ($data as $key => $value) {
            $component->$key = $value;
        }
    }

    function getProperties($component)
    {
        $properties = [];

        $reflectedProperties = (new ReflectionClass($component))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectedProperties as $property) {
            $properties[$property->getName()] = $property->getValue($component);
        }

        return $properties;
    }

    function callMethod($component, $method)
    {
        $component->$method();
    }

    function updateProperty($component, $property, $value)
    {
        $component->{$property} = $value;

        $updatedHook = 'updated' . Str::title($property);

        if (method_exists($component, $updatedHook)) {
            $component->{$updatedHook}();
        }
    }
}
