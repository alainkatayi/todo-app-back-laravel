<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class authController extends Controller
{

    //function pour la creation d'un user
    public function register(Request $request){
        //validation des donnees
        $validator= Validator::make($request -> all(),[
            'name' => 'required | string |max:50',
            'email' => 'required | string| email | max:255 | unique:users',
            'password' => 'required | string | min:6 | confirmed',
        ]);
        
        //s'il ya erreur, on revoi un message d'erreur
        if ($validator -> fails()){
           // return response() ->json($validator -> errors(), 403);
           return response() -> json([
                'error' => [
                    'name' => implode($validator -> errors() -> get('name')),
                    'email' => implode($validator -> errors() -> get('email')),
                    'password' => implode($validator -> errors() -> get('password'))
                ]
            ], 403);
        }

        try{
            
        //dans le cas contraire, on creer le user
            $user = User::create([
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => Hash::make($request -> password),
            ]);

            //on creer aussi le token
            $token = $user->createToken('token')->plainTextToken;
            $user['token'] = $token;

            //on envoie le token et le user
            return response() -> json([
                'message' => 'Inscription reussi',
                'user' => $user
            ], 200);

        }
        //casd'erreur
        catch(\Exception $exception){
            return response()-> json(['error' => $exception -> getMessage() ]);
        }

    }

    //function pour la connexion
    public function login(Request $request){

        $validationData = Validator::make($request ->all(),[
            'email' => 'required | string |email',
            'password' => 'required | string'
        ]);

        if ($validationData -> fails()){
            return response()-> json($validationData -> errors(), 403);
        }
        $credenstials = ['email' => $request -> email, 'password' => $request -> password];
        
        try{
            if(!auth()->attempt($credenstials)){
                return response() -> json([
                    'error' => "Email or Password "
                ], 400);
            }

            $user = User::where('email', $request -> email)->firstOrFail();
            $token = $user -> createToken('token') -> plainTextToken;
            $user['token'] = $token;

            return response() -> json([
                'Message' => 'Login Success',
                'User' => $user
            ], 201);
        }
        
        catch(\Exception $exception){
            return response()-> json([
                'error' => [
                    $exception -> getMessage()

                ]
                ], 500);
        }
    }

    //function pour la deconnexion du user
    public function logout(Request $request){

        try{
            $request->user()->currentAccessToken(null)->delete();
            return response() -> json([
                'message' => "User has been logged out successfully"
            ], 200);
        }
        catch(\Exception $exception){
            return response() -> json([
                "error" => $exception -> getMessage(),
            ]);
        }
    }
}
