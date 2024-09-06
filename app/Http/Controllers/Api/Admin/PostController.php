<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        // Get posts with pagination
        $posts = Post::latest()->paginate(10);
        // Return posts as JSON
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:posts,slug',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'faculty_id' => 'required|exists:faculties,id',
            'content' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Handle image upload
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        // Create post
        $post = Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id,
            'faculty_id' => $request->faculty_id,
            'content' => $request->content,
            'image' => $imageName,
        ]);

        // Return created post as JSON
        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if ($post) {
            // Return post as JSON
            return response()->json($post);
        }

        // Return not found response
        return response()->json(['message' => 'Post not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|unique:posts,slug,'.$id,
            'category_id' => 'sometimes|required|exists:categories,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'faculty_id' => 'sometimes|required|exists:faculties,id',
            'content' => 'sometimes|required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $post->image = $imageName;
        }

        // Update post
        $post->update([
            'title' => $request->input('title', $post->title),
            'slug' => $request->input('slug', $post->slug),
            'category_id' => $request->input('category_id', $post->category_id),
            'user_id' => $request->input('user_id', $post->user_id),
            'faculty_id' => $request->input('faculty_id', $post->faculty_id),
            'content' => $request->input('content', $post->content),
            'image' => $post->image,
        ]);

        // Return updated post as JSON
        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post) {
            $post->delete();
            // Return success message
            return response()->json(['message' => 'Post deleted successfully'], 200);
        }

        // Return not found response
        return response()->json(['message' => 'Post not found'], 404);
    }
}
