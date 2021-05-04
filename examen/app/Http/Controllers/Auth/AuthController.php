<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(),[ 
            'email'     => 'required',
            'password'  => 'required|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user =  User::find(Auth::user()->id);

        

            $token = Str::random(60);
          
            $user->api_token =  $token;
            $user->save();

            return response()->json(['message'  => 'Inicio de sesión correctamente','user'=>Auth::user(),'token'=>  $token], 200);
        }

        return response()->json(['message'  => 'Datos incorrectos'], 200);
        
    }

    public function logout(Request $request,$token)
    {
        $user =  User::firstWhere('api_token',$token);

        //dd($user);

        Auth::logout($user);

        $user->api_token =  null;
        $user->save();

        return response()->json(['message'  => 'Se ha cerrado sesión correctamente'], 200);
    }

    public function register(Request $request)
    {
        //dd($request->all());
        
        $validation = Validator::make($request->all(),[ 
            'name'     => 'required|max:250',
            'email'      => 'required|email|unique:App\Models\User',
        ]);

        if($validation->fails()){
            return response()->json([
            'messages'  => $validation->errors(),
            ], 200);
        }
        else
        {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' =>Hash::make($request->input('password')),
                'api_token' => Str::random(60),
            ]);
    
            return response()->json(['user'  => $user], 200);
        }

       
        
    }
}
