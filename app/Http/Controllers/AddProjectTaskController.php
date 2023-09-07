<?php

namespace App\Http\Controllers;

use App\Models\DurationTask;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class AddProjectTaskController extends Controller
{
    function addTaskToProject(Request $request, $task_id, $project_id, $user_id){
        try {
            $task = Todo::find($task_id);
            $project = Project::find($project_id);
            $member = User::find($user_id);
    
            if (!$task) {
                return response()->json(['msg' => 'Task not found.'], 404);
            }
    
            if (!$project) {
                return response()->json(['msg' => 'Project not found.'], 404);
            }
    
            if (!$member) {
                return response()->json(['msg' => 'User not found.'], 404);
            }

            
            if ($project->addTasksToProjects()->wherePivot('user_id', $user_id)->wherePivot('task_id', $task_id)->exists()){
                return response()->json(['msg' => 'Member has already added this task.'], 400);
            }
    
            $project->addTasksToProjects()->attach($task_id, ['task_id' => $task_id, 'user_id' => $user_id, 'project_id' => $project_id]);

            return response()->json(['msg' => 'Task added successfully to project.'], 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }    
}
