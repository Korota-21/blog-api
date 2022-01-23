<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the user posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //the auth user
        $user_id = auth()->user()->id;

        //the model user to call its posts
        $user = User::find($user_id);
        $to_display = [];
        foreach($user->posts as $post){
            array_push($to_display,$this->post_display($post));
        }

        return $to_display;
    }


    /**
     * help in display posts in specific way
     *
     * @return Array
     */
    public function post_display($post)
    {
        $response = [
            "id" => $post->id,
            "title" => $post->title,
            "body" => $post->body,
            "created_at" => $post->created_at,
            "updated_at" =>$post->updated_at,
            "user_id" => $post->user_id,
            "username" => $post->user->name
        ];

        return $response;
    }



    /**
     * Display a listing of the posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_all()
    {

        // تحتاج تحسين بالاداء
        $to_display = [];
        foreach(Post::all() as $post){
            array_push($to_display,$this->post_display($post));
        }
      return $to_display;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // auth user
        $user = auth()->user();
        // add the user id to the request
        $request['user_id'] = $user->id;
        //checking request
        $post_contant = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'user_id' => 'required',
        ]);

        // inserting post data
        $post = Post::create($post_contant);

        return response(['message'=>"created successfully"],201);
    }

    /**
     * Display the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find the the resource
        $post = Post::find($id);

        // if the resource not found
        if (!$post)
            return response([
                'message' => 'error post not found'
            ]);
        return $this->post_display($post);
    }

    /**
     * Update the specified post in storage.
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
        // check if the auth user is the same user
        if ($post->user_id !== auth()->user()->id)
        return response([
            "message" => "Forbidden."
        ], 403);
        //update the resource
        $post->update($request->all());

        return response(['message'=>"updated successfully"], 204);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find the the post
        $post = Post::find($id);

        // if the post not found
        if (!$post)
            return response([
                'message' => 'error post not found'
            ]);
        //if the post writer not the auth user
        if ($post->user_id !== auth()->user()->id)
            return response([
                "message" => "Forbidden."
            ], 403);
        $images =  $post->images;
        foreach ($images as $image) {
            app(ImageController::class)->destroy($image->id);
        }

        //delete the post
        Post::destroy($id);
        return response(['message'=>"deleted successfully"], 204);
    }
}
