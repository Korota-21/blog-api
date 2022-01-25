<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    /**
     * Display a list of users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function usersList(Request $request)
    {
        //Page system
        $page = 1;
        $skip = 0;
        if ($request['page']) {
            $page = $request['page'];
        };
        if ($page > 1) {
            $skip = ($page - 1) * 10;
        }
        $users = User::all()->skip($skip)->take(10);

        return $users;
    }

    /**
     * Display a user by id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(["message" => "user is not found"]);
        }
        if (Auth()->user()->id == $id)
            return $user;
        return response([
            "id" => $user->id,
            "name" => $user->name,
            "created_at" => $user->created_at,

        ]);
    }
}
