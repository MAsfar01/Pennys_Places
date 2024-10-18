<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function addPost(Request $request){
        $validatePost = Validator::make(
            $request->all(),
            [
                'name' => 'required'
            ]
            );
        $post = Post::create([
            'name' => $request->name,
            'location' => $request->location,
            'type' => $request->type,
            'image' => $request->image,
            'notes' => $request->notes,
        ]);
    }
}
