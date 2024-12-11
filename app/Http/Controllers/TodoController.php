<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TodoRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Todo as TodoModel;

class TodoController extends Controller{
    public function add(Request $request){
        $gets=$request->query();
        $list = TodoModel::list($gets);
        return view('Todo',['todolist'=>$list]);
    }

    public function save(TodoRequest $request){
        $post=$request->post();
        $result = TodoModel::add($post);
        if ($result) {
            return redirect()->back()->with('success', 'Todo item added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add todo item.');
        }
    }

    public function toggleDone(Request $request,$id){
        $todo = TodoModel::find($id);
        

        if(!$todo){
            return response()->json(['message'=>'Todo item not found'],404);
        }

        $todo->done = !$todo->done;
        $todo->save();
        return response()->json([
            'message' => 'Todo status updated',
            'done' => $todo->done,
            'id' => $todo->id,
        ]);    
    }

    public function delete($id)
    {
        $result = TodoModel::del($id);
    
        if ($result['error'] == 0) {
            return redirect('/')->with('success', 'Todo item deleted successfully.');
        } else {
            return back()->with('error', 'Failed to delete the Todo item.');
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'todo' => 'required|string|max:255',
        ]);

        $todo = TodoModel::find($id);
        if (!$todo) {
            return redirect('/')->with('error', 'Todo item not found.');
        }

        $todo->todo = $validatedData['todo'];
        if ($todo->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Todo item updated successfully.',
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update Todo item.',
        ], 500);
    
    
    }

    
}