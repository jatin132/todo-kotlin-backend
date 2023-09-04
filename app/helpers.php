<?php

use App\Models\DurationTask;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;

/**unique alphanumeric in php */
function random_strings($length_of_string, $model_name){
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $already = false;
    // Shufle the $str_result and returns substring
    // of specified length
    $uuid =  substr(str_shuffle($str_result), 0, $length_of_string);
    // $already = DB::table($model_name)->where("uuid", $uuid)->first();

    if ($model_name == 'User') {
        $already = User::where("uuid", $uuid)->first();
    }

    if ($model_name == 'Project') {
        $already = Project::where("uuid", $uuid)->first();
    }

    if ($model_name == 'DurationTask') {
        $already = DurationTask::where("uuid", $uuid)->first();
    }

    if ($model_name == 'Todo') {
        $already = Todo::where("uuid", $uuid)->first();
    }

    if ($already) {
        return random_strings($length_of_string, $model_name);
    }

    return strtolower($uuid);
}

?>