<?php

namespace App\Http\Livewire;

class Todos
{
    public $draft = '';
    public $todos;

    // This is called when the component is first initialized
    public function mount()
    {
        // Wrap up PHP array in a Laravel collection and gives a lot of useful methods
        $this->todos = collect(['One Todo', 'Another Todo']);
    }

    // This is called when the draft property is updated
    public function updatedDraft()
    {
        $this->draft = strtoupper($this->draft);
    }

    public function addTodo()
    {
        $this->todos->push($this->draft);

        $this->draft = '';
    }

    public function render()
    {
        return <<<'blade'
            <div class="todos">
                <input type="text" wire:model="draft" placeholder="Something New...">
                <button wire:click="addTodo">Add Todo</button>

                <ul>
                    @foreach ($todos as $todo)
                        <li>{{ $todo }}</li>
                    @endforeach
                </ul>
            </div>
        blade;
    }
}
