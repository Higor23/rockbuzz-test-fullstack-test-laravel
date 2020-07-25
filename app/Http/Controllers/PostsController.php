<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Post $post)
    {
        $this->request = $request;
        $this->repository = $post;
        $this->middleware('auth');
    }

    public function list()
    {
        $postsOn = Post::all()->where('published', '=', 1);
        $postsOff = Post::all()->where('published', '=', 0);


        return view('posts.index', ['postsOn' => $postsOn, 'postsOff' => $postsOff]);
    }


    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', ['tags' => $tags]);
    }

    public function store(Request $request)
    {

        $data = $request->only('title', 'body', 'image',
        'published', 'author', 'created_at', 'slug');

        if ($request->hasFile('image') && $request->image->isValid()){
            $imagePath = $request->image->store('media');

            $data['image'] = $imagePath;
        }
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'published' => 'required',
            'slug' => 'required',
        ]);
        $this->repository->create($data);
        return redirect('posts');
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $tags = Tag::all();

        return view('posts.edit', ['post' => $post, 'tags' => $tags]);
    }

    public function update(Request $request, $id)
    {

        if(!$post = $this->repository->find($id))
            return redirect()->back();

        $data = $request->all();

        if ($request->hasFile('image') && $request->image->isValid()){

            if ($post->image && Storage::exists($post->image)){
                Storage::delete($post->image);
            }
            $imagePath = $request->image->store('media');

            $data['image'] = $imagePath;
        }

        $post->update($data);
        return redirect('posts');
    }

    public function delete($id)
    {
        $post = Post::find($id);

        if ($post->image && Storage::exists($post->image)){
            Storage::delete($post->image);
        }

        $post->delete();

        return redirect('posts');
    }
}
