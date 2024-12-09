<?php

namespace App\Http\Livewire;

class Todos
{
    public $draft = '';
    public $todos = ['One Todo', 'Another Todo'];

    public function addTodo()
    {
        $this->todos[] = $this->draft;

        $this->draft = '';
    }

    public function render()
    {
        return <<<'blade'
            <div class="todos">
                <input type="text" wire:model="draft">
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
