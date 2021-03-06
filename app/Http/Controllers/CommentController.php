<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post_id)
    {
        // auth user
        $user = auth()->user();
        // add the user id to the request
        $request['user_id'] = $user->id;
        $request['post_id'] = $post_id;
        //checking request
        $req_contant = $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|integer|exists:posts,id',
            'user_id' => 'required|integer',
            'parent_id' => "integer|exists:comments,id"
        ]);
        // check if the comment have a parent
        if ($req_contant['parent_id']) {
            $parent_comment = Comment::find($req_contant['parent_id']);
            // check if the parent comment belongs to the same post

            if ($parent_comment->post->id != $req_contant['post_id']) {
                return response(
                    [
                        "error" => "Invalid Data"
                    ]
                ,422);
            }

            // // check if the parent comment has a parent comment
            // if ($parent_comment->parent_id != null) {
            //     // let the comment take the same parent of it's inserted parent
            //     $req_contant['parent_id'] = $parent_comment->parent_id;
            // }

        }
        // inserting post data
        $comment = Comment::create($req_contant);

        return response(['message' => "created successfully"], 201);
    }




    /**
     * Display a list of comments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //Page system
        $page = 1;
        if ($request['page']) {
            $page = $request['page'];
        };
        $skip = 0;
        if ($page > 1) {
            $skip = ($page - 1) * 10;
        }
        $comments = [];
        if($request['user_id']){
            $user = User::find($request['user_id']);
            if (!$user)
            return response([
                'message' => 'error user not found'
            ]);
            $comments =$user->comments->where("parent_id" , "=" , null)->skip($skip)->take(10);
        }else if($request['post_id']){
            $post = Post::find($request['post_id']);
            if (!$post)
            return response([
                'message' => 'error post not found'
            ]);
            $comments =$post->comments->where("parent_id" , "=" , null)->skip($skip)->take(10);
            foreach ($comments as $comment) {
                 $comment->childComments;
            }
        }else{
            return response([
                "message"=>"insert user_id or post_id as query"
            ],424);
        }

        return $comments;
    }

 /**
     * Display the specified comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find the the resource
        $comment = Comment::find($id);

        // if the resource not found
        if (!$comment)
            return response([
                'message' => 'error comment not found'
            ]);
            $comment->childComments;

        return $comment;
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
            'content' => 'string',

        ]);
        //find the the resource
        $comment = Comment::find($id);

        // if the resource not found
        if (!$comment)
            return response([
                'message' => 'error comment not found'
            ]);
        // check if the auth user is the same user
        if ($comment->user_id !== auth()->user()->id)
            return response([
                "message" => "Forbidden."
            ], 403);
        //update the resource
        $comment->update($request->all());

        return response(['message' => "updated successfully"], 204);
    }

     /**
     * Remove the specified post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find the the comment
        $comment = Comment::find($id);
        // if the comment not found
        if (!$comment)
            return response([
                'message' => 'error comment not found'
            ], 404);

        //if the comment writer not the auth user
        if ($comment->user_id !== auth()->user()->id)
            return response([
                "message" => "Forbidden."
            ], 403);

        //delete all comment's children
        $comentChildren =  $comment->childComments;
        foreach ($comentChildren as $childcomment) {
            $childcomment->delete();
        }

        //delete the post
        Comment::destroy($id);
        return response(['message' => "deleted successfully"], 204);
    }

}
