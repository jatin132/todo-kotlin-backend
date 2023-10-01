<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class RemoveAddedMembers extends Controller
{
    public function removeMembersToProfile(Request $request, $user_id){
        try {
            $user = User::find($user_id);

            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }

            $added_user_ids = $request->input('added_user_ids'); 

            if (empty($added_user_ids)) {
                return response()->json(['msg' => 'No members provided.'], 400);
            }

            $addedMembers = [];

            foreach ($added_user_ids as $added_user_id) {
                $member = User::find($added_user_id);

                if (!$member) {
                    return response()->json(['msg' => 'Member not found for user ' . $added_user_id], 404);
                }

                if ($user->addedMembers()->wherePivot('user_id', $user_id)->wherePivot('member_user_id', $added_user_id)->exists()) {

                    continue;
                }

                $user->addedMembers()->attach($added_user_id);
                $addedMembers[] = $member->toArray();
            }

            return response()->json(['msg' => 'Members added successfully.', 'added_members' => $addedMembers], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    public function removeMembersToProject(Request $request, $project_id){
        try {
            $project = Project::find($project_id);
    
            if (!$project) {
                return response()->json(['msg' => 'Project not found.'], 404);
            }
    
            $added_user_ids = $request->input('added_user_ids'); 
    
            if (empty($added_user_ids)) {
                return response()->json(['msg' => 'No members provided.'], 400);
            }
    
            $addedMembers = [];
    
            foreach ($added_user_ids as $added_user_id) {
                $member = User::find($added_user_id);
    
                if (!$member) {
                    return response()->json(['msg' => 'Member not found for user ' . $added_user_id], 404);
                }
    
                if ($project->addedMembersToProjects()->wherePivot('project_id', $project_id)->wherePivot('member_id', $added_user_id)->exists()) {

                    continue;
                }
    
                $project->addedMembersToProjects()->attach($added_user_id);
                $addedMembers[] = $member->toArray();
            }
    
            return response()->json(['msg' => 'Members added successfully to project.', 'added_members' => $addedMembers], 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
}
