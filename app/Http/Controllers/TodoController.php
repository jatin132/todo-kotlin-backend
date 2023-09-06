<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    function createTodo(Request $request, $user_id){
        $validation = Validator::make($request->all(), [
            "title" => ['required','string'],
            "description" => ['required','string'],
            "is_completed" => ['required','integer'],
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->toArray(), 422);
        }
        try {
            $user = User::where('id', $user_id)->first();
            if (!$user) {
                return response()->json(['msg'=>'User not found!'], 422);
            }

            $data = [
                'uuid' => random_strings(7, "Todos"),
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'is_completed' => $request->get('is_completed'),
                'user_id' => $user_id,
            ];

            $durationTasks = Todo::create($data);

            return response()->json(['msg'=>'Todo added successfully!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function deleteTodo(Request $request, $uuid){
        try {
            $todo = Todo::where('uuid', $uuid)->first();
            if (!$todo) {
                return response()->json(['msg'=>'Todo not found!'], 422);
            }

            $todo->delete();

            return response()->json(['msg'=>'Todo deleted successfully!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function editTodo(Request $request, $uuid){
        $validation = Validator::make($request->all(), [
            "title" => ['required','string'],
            "description" => ['required','string'],
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->toArray(), 422);
        }

        try {
            $todo = Todo::where('uuid', $uuid)->first();
            if (!$todo) {
                return response()->json(['msg'=>'Todo not found!'], 422);
            }

            $todo->title = $request->get('title');
            $todo->description = $request->get('description');

            $todo->save();

            return response()->json(['msg'=>'Todo edited successfully!'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function markComplete(Request $request, $uuid){
        $validation = Validator::make($request->all(), [
            "is_completed" => ['required','integer'],
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->toArray(), 422);
        }

        try {
            $Todo = Todo::where('uuid', $uuid)->first();
            if (!$Todo) {
                return response()->json(['msg'=>'Todo not found!'], 422);
            }

            $Todo->is_completed = $request->get('is_completed');

            $Todo->save();

            return response()->json(['msg'=>'Todo edited successfully!'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function getTodayTodos(Request $request, $user_id, $currentDate){
        try {
            $todos = Todo::where('user_id', $user_id)
                                            ->whereDate("created_at", $currentDate)
                                            ->get();
            if (!$todos) {
                return response()->json(['msg'=>'Todos not found!'], 422);
            }

            return response()->json($todos);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function getAllTodos(Request $request, $user_id){
        try {
            $todos = Todo::where('user_id', $user_id)->get();
            if (!$todos) {
                return response()->json(['msg'=>'Todos not found!'], 422);
            }

            return response()->json($todos);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }
}
