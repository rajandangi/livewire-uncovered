<?php

use App\Livewire;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome');

Route::post('livewire', function () {
    // Get a Component from snapshot
    $component = (new Livewire)->fromSnapshot(request('snapshot'));

    // Call a method on a component
    if ($method = request('callMethod')) {
        (new Livewire)->callMethod($component, $method);
    }

    if ([$property, $value] = request('updateProperty')) {
        (new Livewire)->updateProperty($component, $property, $value);
    }

    [$html, $snapshot] = (new Livewire)->toSnapshot($component);

    // Turn the component back into snapshot and get the HTML
    return [
        'html' => $html,
        'snapshot' => $snapshot,
    ];
});

// Custom blade directive
Blade::directive('livewire', function ($expression) {
    return "<?php echo (new App\Livewire)->initialRender({$expression}); ?>";
});
