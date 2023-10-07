<?php

namespace App\Http\Controllers;

use App\Models\AccountUpdate;
use App\Models\DurationTask;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotifications(Request $request, $receiver_id){
        try {
            $receiver = User::find($receiver_id);
            if (!$receiver) {
                return response()->json(['msg' => 'Receiver not found.'], 404);
            }
            
            $accountUpdates = AccountUpdate::where('receiver_id', $receiver_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
            if ($accountUpdates->isEmpty()) {
                return response()->json(['msg' => 'Account updates not found.'], 404);
            }
            
            $notifications = $accountUpdates->map(function ($accountUpdate) use ($receiver) {
                $sender = User::find($accountUpdate->sender_id);
                $project = Project::find($accountUpdate->project_id);
                $task = DurationTask::find($accountUpdate->task_id);
                $todo = Todo::find($accountUpdate->todo_id);
                
                // Check if the sender is not the receiver
                if ($sender && $sender->id !== $receiver->id) {
                    $sender_username = $sender->username;
                    $sender_profile_photo_url = $sender->profile_photo;
                    $task_name = $task ? $task->name : null;
                    $project_name = $project ? $project->project_name : null;
                    $todo = $todo ? $todo->title : null;
            
                    return [
                        'accountUpdate' => $accountUpdate,
                        'sender_username' => $sender_username,
                        'receiver_username' => $receiver->username,
                        'sender_profile_photo_url' => $sender_profile_photo_url,
                        'project_name' => $project_name,
                        'task_name' => $task_name,
                        'todo' => $todo,
                    ];
                }
                
                return null;    
            })->filter()->values();
            
            return response()->json(['notifications' => $notifications]);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
}
