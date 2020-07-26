<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Str;


class PostDetailController extends Controller
{
    public function detail($slug)
    {

        $post = Post::where('slug', '=', $slug)->firstOrFail();



        return view('posts.postDetail', ['post' => $post]);
    }
}
