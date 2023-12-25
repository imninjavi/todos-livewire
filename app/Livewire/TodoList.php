<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;

    public $name;

    public $search;

    public $editingTodoID;
    public $editingTodoName;

    public function create() 
    {
        // validate
        $validated = $this->validate([
            'name' => 'required|min:3|max:50',
        ]);

        // create the todo
        Todo::create([
            'name' => $validated['name'],
        ]);

        // clear the input
        $this->reset('name');

        // send flash message
        session()->flash('success', 'Created.');

        $this->resetPage();
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        
        if ($todo->completed == false) {
            $todo->completed = true;
        }
        else {
            $todo->completed = false;
        }

        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    public function update()
    {
        $validated = $this->validate([
            'editingTodoName' => 'required|min:3|max:50'
        ]);

        $todo = Todo::find($this->editingTodoID);
        
        $todo->name = $validated['editingTodoName'];
        $todo->save();

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->reset(['editingTodoID', 'editingTodoName']);
    }

    public function delete($todoID)
    {
        try {
            Todo::findOrFail($todoID)->delete();
        } catch (Exception $e) {
            session()->flash('error','Failed to delete todo!');
        }
        
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', '%'.$this->search.'%')->paginate(5),
        ]);
    }
}
