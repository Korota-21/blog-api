<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function profile(){
        $user =auth()->user();
        $response = [
            'name' => $user->name,
            'email' =>$user->email
        ];
        return $response;
    }


    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        //create token
        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return Response($response, 201);
    }
    public function login(Request $request)
    {
        //  return $request;
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        // Check email
        $user = User::where('email', $fields['email'])->first();
        if (!$user)
            return response([
                'message' => 'The email is not registered'
            ], 401);

        // Check password
        if (!Hash::check($fields['password'], $user->password))
            return response([
                'message' => 'the email and the password dose not match'
            ], 401);

        //create token
        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];


        return Response($response, 201);
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'logged out'
        ];
    }
}
