<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Http\Resources\TaskResource;



class taskController extends Controller
{
    //function pour lister les taches
    public function index(){
        return TaskResource::collection(Task::all());
    }

    //function pour voir une task de maniere individuel
    public function show(int $id){
        try{
            $task = Task::findOrFail($id);
            $tasks = new TaskResource($task);
            return $tasks;
        }
        catch(\Exception $exception){
            return response()-> json([$exception-> getMessage()], 500);
        }
    }

    //function pour creer une task
    public function store(Request $request){

        //on prend le user connecter
        $user = auth() -> user();

        //gestion des potentiels erreur
      

            //validation des donnees avec validator
            $validator = Validator::make($request -> all(),[
                'title'=> 'required|string|max:50',
                'description' => 'required|string |',
                'is_completed'=> 'nullable|boolean',
                'start_date' => 'required|date ',
                'end_date' => 'required|date',
                'priority' => 'required|string|in:low,high',
            ]);

            //si la validation echoue
            if($validator->fails()){
                return response()->json([
                    'error'=> $validator ->errors()
                    ],422);
            }
        try{
            //dans le cas contraire, on creer le task
            $task = Task::create([
                'title'=>$request -> title,
                'description'=> $request -> description,
                'is_completed' => $request -> is_completed,
                'start_date'=> $request -> start_date,
                'end_date'=> $request -> end_date,
                'priority'=> $request -> priority,
                'user_id' => $user-> id
            ]);
        
            //on return une resource
            return new TaskResource($task);
        }

        //on capture les erreurs potentiels
        catch(\Exception $exception){
            return response() -> json($exception -> getMessage(), 500);
        }
    }

    //functionn pour mettre à jour une task
    public function update(Request $request, Task $task){

        try{
            $user = auth() -> user();

            //on verifie si le user connecter est le l'auteur du task
            if(auth()->user()->id !== $task->user_id){
                return response()-> json(['error' => 'You are not authorized to update this task'], 403);
            }
            else{
                $task -> update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'is_completed' => $request->is_completed,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'priority' => $request -> priority,
                    'user_id' => $user -> id
                ]);
                return response() -> json([
                    'Message' => "Task update successfully"
                ]);
            }
        }
        catch(\Exception $exception){
            return response()-> json(['error' => $exception-> getMessage()], 500);
        }

    }

    //function pour supprimer une task
    public function destroy($id){
        $task = Task::findOrFail($id);
        
        //on verifie si le user conneter est l'auteur  du task
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
