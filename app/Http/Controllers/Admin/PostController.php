<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Post;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|max:255|min:5',
                'content' => 'nullable|max:65000',
                'category_id' => 'nullable|exists:categories,id'
            ]
        );
        $data = $request->all();

        $newPost = new Post();
        $newPost->fill($data);

        $slug = $this->getSlug($newPost->title);

        $newPost->slug = $slug;
        $newPost->save();

        return redirect()->route('admin.posts.index')->with('status', 'Post successfully created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate(
            [
                'title' => 'required|max:255|min:5',
                'content' => 'nullable|max:65000',
                'category_id' => 'nullable|exists:categories,id'
            ]
        );
        $data = $request->all();

        if ($post->title != $data['title']) {
            $data['slug'] = $this->getSlug($data['title']);
        }

        $post->update($data);
        return redirect()->route('admin.posts.show', compact('post'))->with('status', 'Post successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('status', 'post succesfully deleted!');
    }

    protected function getSlug($title) {

        $slug = Str::slug($title, '-');

        $checkPost = Post::where('slug', $slug)->first();

        $counter = 1;

        while($checkPost) {
            $slug = Str::slug($title . '-' . $counter, '-');
            $counter++;
            $checkPost = Post::where('slug', $slug)->first();
        }

        return $slug;

    }
}
