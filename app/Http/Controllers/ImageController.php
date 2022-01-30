<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

use App\Models\Post;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ImageIn;

class ImageController extends Controller
{
    /**
     * Display a listing of post images.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //find the the resource
        $post = Post::find($id);

        // if the resource not found
        if (!$post)
            return response([
                'message' => 'error post not found'
            ]);
        return $post->Images;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "post_id" => 'required',
            'pic' => 'required|image|nullable|max:1999',
        ]);
        //find the the post
        $post = Post::find($request['post_id']);

        // if the post not found
        if (!$post)
            return response([
                'message' => 'error post not found'
            ]);
        // check if the auth user is the same user
        if ($post->user_id !== auth()->user()->id)
            return response([
                "message" => "Forbidden."
            ], 403);
        $imageCount = count($post->images);
        if ($imageCount >= 4) {
            return response([
                'message' => 'you can not add more than 4 images in a post'
            ]);
        }
        // resize and uploud Image
        $imagename = $this->resize($request);
        Image::create([
            'name' => $imagename,
            'post_id' => $request['post_id'],
        ]);
        return response(['message' => "created successfully"], 201);
    }

    /**
     * resize then store an image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return resized_Image
     */
    public function resize(Request $request)
    {
        // نسبة التصغير 80 بالمية
        $resize_percent = .8;
        // تحقق من وجود صورة
        $request->validate([
            'pic' => 'required|image|nullable|max:1999',
        ]);
        $image = $request->file('pic');
        // Get filename with the extension
        $filenameWithExt = $image->getClientOriginalName();
        // Get just filename
        $filename = pathinfo($filenameWithExt, PATHINFO_DIRNAME);
        // Get just ext
        $extension = $image->getClientOriginalExtension();
        // Filename to store
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
        $image_resize = ImageIn::make($image->getRealPath());
        // determine orginal Image dimensions
        $org_width = $image_resize->width();
        $org_height = $image_resize->height();
        //change image dimensions size
        $width = $org_width * $resize_percent;
        $height = $org_height * $resize_percent;
        // image resize
        $image_resize->resize($width, $height);
        // Upload Image

        if (!file_exists("storage/image/")) {
            mkdir("storage/image/", 666, true);
        }
        $image_resize->save(public_path('storage/image/' . $fileNameToStore));
        return $fileNameToStore;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find the the resource
        $image = Image::find($id);
        //return $name->name;
        // if the resource not found
        if (!$image)
            return response([
                'message' => 'error image not found'
            ]);
        return asset('storage/image/' . $image->name);
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
        //find the the resource
        $image = Image::find($id);

        // if the resource not found
        if (!$image)
            return response([
                'message' => 'error image not found'
            ]);
        $post = $image->post;
        // check if the auth user is the same user
        if ($post->user_id !== auth()->user()->id)
            return response([
                "message" => "Forbidden."
            ], 403);
        //delete the old image
        Storage::delete('public/image/' . $image->name);

        // resize and uploud Image
        $imagename = $this->resize($request);

        //save the new name in DB
        $image->update(["name" => $imagename]);

        return response(['message' => "updated successfully"], 204);
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
        $image = Image::find($id);

        // if the resource not found
        if (!$image)
            return response([
                'message' => 'error image not found'
            ]);
        $post = $image->post;
        // check if the auth user is the same user
        if ($post->user_id !== auth()->user()->id)
            return response([
                "message" => "Forbidden."
            ], 403);
        //delete the image
        Storage::delete('public/image/' . $image->name);

        // delete the image in DB
        Image::destroy($id);
        return response(['message' => "deleted successfully"], 204);
    }
}
