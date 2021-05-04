<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();

        return response()->json(['posts'  => $posts,], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[ 
            'title'     => 'required|max:250',
            'body'      => 'required|max:5000',
        ]);

        if($validation->fails()){
            return response()->json([
            'messages'  => $validation->errors(),
            ], 200);
        }
        else
        {
            $post = new Post;
            $post->title = $request->input('title');
            $post->body = $request->input('body');
            $post->slug = Str::of($request->input('title'))->slug('-');
            $post->save();

            if ($request->hasFile('image')) {

                $name = $request->file('image')->getClientOriginalName();
                $post->images()->create([
                    'name' => $name,
                    'url' => '/storage/'.$name.'/'.$request->file('image')->getClientOriginalExtension(),
                ]);
            }
         

            return response()->json([
            'post'  => $post,
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
            if(is_null($post)){
                return response()->json([
                'message'  => "El registro con el id # $post no existe",
                ], 404);
            }

            return response()->json([
            'post'  => $post,
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
    
          $validation = Validator::make($request->all(),[ 
            'title'     => 'required|max:250',
            'body'      => 'required|max:5000',
            ]);

            if($validation->fails()){
                return response()->json([
                'error' => true,
                'messages'  => $validation->errors(),
                ], 200);
            }
            else
            {
                $post->update([
                    'title'     => $request->input('title'),
                    'body'      => $request->input('body'),
                    'slug'      =>  Str::of($request->input('title'))->slug('-'),
                  ]);
                return response()->json([
                'post'  => $post,
                ], 200);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
       
        if(is_null($post)){
            return response()->json([
            'message'  => "Registro  no existe",
            ], 404);
        }

        $post->delete();
        return response()->json([
         'message'  => "Se elimino la entrada $post->name",
        ], 200);
    }
}

