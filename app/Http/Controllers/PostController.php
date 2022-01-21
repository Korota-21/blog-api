<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        return $user->posts;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_all()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $request['user_id'] = $user->id;

        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'pic' => 'nullable'
        ]);
        // inserting all the data on the request

        return Post::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string',
            'body' => 'string',
            'pic' => 'nullable'
        ]);
        //find the the resource
        $post = Post::find($id);

        // if the resource not found
        if (!$post)
            return response([
                'message' => 'error ' . $id . ' not found'
            ]);

        if ($post->user_id !== auth()->user()->id)
            return response([
                "message" => "Unauthorized."
            ], 401);
        //update the resource
        $post->update($request->all());

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find the the resource
        $post = Post::find($id);

        // if the resource not found
        if (!$post)
            return response([
                'message' => 'error post not found'
            ]);

        if ($post->user_id !== auth()->user()->id)
            return response([
                "message" => "Unauthorized."
            ], 401);


        Post::destroy($id);
        return response([],204);
    }
}
