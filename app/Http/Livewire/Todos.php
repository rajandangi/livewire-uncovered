<?php

namespace App\Http\Livewire;

class Todos
{
    public $draft = '';
    public $todos = [];

    // This is called when the component is first initialized
    public function mount()
    {
        $this->todos = ['One Todo', 'Another Todo'];
    }

    // This is called when the draft property is updated
    public function updatedDraft(){
        $this->draft = strtoupper($this->draft);
    }

    public function addTodo()
    {
        $this->todos[] = $this->draft;

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
