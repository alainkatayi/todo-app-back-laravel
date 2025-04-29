<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class taskController extends Controller
{
    //
    public function index(){
        return Article::all();
    }

    public function store(Request $request){
        $user = auth() -> user();
        try{
            $task = Task::create([
                'title' => $request['title'],
                'description' => $request['description'],
                'is_completed' => $request['is_completed'],
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'priority' => $request['priority'],
                'user_id' => $user->id,
            ]);
            return response() -> json([
                'Message' => "Task created successfully",
                "data" => $task
            ], 200);

        }
        catch(\Exception $exception){
            return response() -> json($exception -> getMessage(), 500);
        }


    }

    public function update(Request $request, $id){
        $task = Task::findOrFail($id);

        $validator = Validator::make($request -> all(), [
            'title' => 'required | string | max:100',
            'description' => 'required | string | max:500',
            'is_completed' => 'required | boolean | default:false',
            'start_date' => 'required | date',
            'end_date' => 'required | date',
            'priority' => 'required | string | in:low,medium,high',
        ]);

        if($validator -> fails()){
            return response() -> json([$validator -> errors(), 400]);
        }

        if(auth() -> user() -> id !== $task -> user_id){
            return response() -> json([
                'Message' => "You are not authorized to update this task",
            ], 403);
        }

        $task -> update($validator);
        return response() -> json([
            'Message' => "Task updated successfully",
            "Task" => $task
        ], 200);

    }

    public function destroy($id){
        $task = Task::findOrFail($id);
        if(auth() -> user() -> id !== $task -> user_id){
            return response() -> json([
                'Message' => "You are not authorized to delete this task",
            ], 403);
        }

        $task -> delete();
        return response() -> json([
            'Message' => "Task deleted successfully",
        ]);
    }

    
}
