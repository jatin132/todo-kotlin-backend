<?php

namespace App\Http\Controllers;

use App\Models\DurationTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DurationTaskController extends Controller
{
    function createTask(Request $request, $user_id){
        $validation = Validator::make($request->all(), [
            "name" => ['required','string'],
            "time" => ['required','string'],
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
                'uuid' => random_strings(7, "DurationTasks"),
                'name' => $request->get('name'),
                'time' => $request->get('time'),
                'user_id' => $user_id,
            ];

            $durationTasks = DurationTask::create($data);

            return response()->json(['msg'=>'Task added successfully!'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function deleteTask(Request $request, $uuid){
        try {
            $task = DurationTask::where('uuid', $uuid)->first();
            if (!$task) {
                return response()->json(['msg'=>'Task not found!'], 422);
            }

            $task->delete();

            return response()->json(['msg'=>'Task deleted successfully!'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function editTask(Request $request, $uuid){
        $validation = Validator::make($request->all(), [
            "name" => ['required','string'],
            "time" => ['required','string'],
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->toArray(), 422);
        }

        try {
            $task = DurationTask::where('uuid', $uuid)->first();
            if (!$task) {
                return response()->json(['msg'=>'Task not found!'], 422);
            }

            $task->name = $request->get('name');
            $task->time = $request->get('time');

            $task->save();

            return response()->json(['msg'=>'Task edited successfully!'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function getTodayTasks(Request $request, $user_id, $currentDate){
        try {
            $durationTasks = DurationTask::where('user_id', $user_id)
                                            ->whereDate("created_at", $currentDate)
                                            ->orderBy('created_at', 'desc')
                                            ->get();
            if (!$durationTasks) {
                return response()->json(['msg'=>'Tasks not found!'], 422);
            }

            return response()->json($durationTasks);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function getAllTasks(Request $request, $user_id){
        try {
            $durationTasks = DurationTask::where('user_id', $user_id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
            if (!$durationTasks) {
                return response()->json(['msg'=>'Tasks not found!'], 422);
            }

            return response()->json($durationTasks);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }
}
