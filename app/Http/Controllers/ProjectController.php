<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    function createProject(Request $request, $user_id){
        $validation = Validator::make($request->all(), [
            "project_name" => ['required','string'],
            "project_description" => ['required','string'],
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
                'uuid' => random_strings(7, "projects"),
                'user_id' => $user_id,
                'project_name' => $request->get("project_name"),
                'project_description' => $request->get("project_description"),
            ];

            $newProject = Project::create($data);

            return response()->json(['msg'=>'Project created successfully', "project" => $newProject], 500);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }    
    }

    function deleteProject(Request $request, $uuid){
        try {
            $project = Project::where('uuid', $uuid)->first();
            if (!$project) {
                return response()->json(['msg'=>'Project not found!'], 422);
            }
            
            $project->delete();

            return response()->json(['msg'=>'Project deleted successfully'], 500);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }    
    }

    function editProject(Request $request, $uuid){
        try {
            $project = Project::where('uuid', $uuid)->first();
            if (!$project) {
                return response()->json(['msg'=>'Project not found!'], 422);
            }
            
            $project->project_name = $request->input("project_name");
            // dd($project->project_name);
            $project->project_description = $request->input("project_description");

            $project->save();

            return response()->json(['msg'=>'Project edited successfully'], 500);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }    
    }

    function getProjects(Request $request, $user_id){
        try {
            $userProjects = Project::where('user_id', $user_id)
                                    ->with('members')
                                    ->with(['tasks' => function ($query) {
                                        $query->with('user'); 
                                    }])
                                    ->orderBy('created_at', 'desc')
                                    ->get();
            if (!$userProjects) {
                return response()->json(['msg'=>'User not found!'], 422);
            }

            return response()->json($userProjects, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }    
    }
}
