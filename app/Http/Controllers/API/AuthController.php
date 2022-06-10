<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' =>'required|confirmed',
            'user_dyn' =>'max:255',
            'telefono' =>'required|max:255',
            'celular' =>'required|max:255',
            'agentePadre' =>'max:255',
            'status' => 'max:1'
        ]);

        $roleDefaul = "agente";

        $validatedData['password'] = Hash::make($request->password);
        $user = User::create($validatedData); 
        $accessToken = $user->createToken('authToken', [$roleDefaul])->accessToken;
        $userRole = Role::create([
        'user_id' => $user->id, 
        'role' => $roleDefaul
        ]); 
    

        return response([
            'user' => $user,
            'access_token' => $accessToken,
        ]);

    }

    public function login (Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if(!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials'], 401);
        } else {
                if (auth()->user()->status == 'A') {
                    $userRole = auth()->user()->role()->first();

                    $accessToken = auth()->user()->createToken('authToken', [$userRole->role])->accessToken;
                    return response([
                        'user' => auth()->user(),
                        'role' => $userRole,
                        'email_verified' => auth()->user()->email_verified,
                        'status' => auth()->user()->status,
                        'access_token' => $accessToken
                    ], 200);
                } else {
                    return response(['message' => 'User Invalid'], 401);
                }
        }
    }
    public function isvalid (Request $request)
    {
        $token = $request->user()->token() ? true :  false;
        return response(['isvalid' => $token], 200);
    }
        
}
