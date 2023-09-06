<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddProjectTaskController extends Controller
{
    function addTaskToProject(Request $request){
        try {
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }
}
