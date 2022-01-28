<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            return $user;

    }
}
