<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index(){
        $categories = Category::when(request()->search, function($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(10);

        // Append query string to pagination links
        $categories->appends(['search' => request()->search]);

        // Return the list of categories with pagination
        return response()->json([
            'success' => true,
            'message' => 'List of Categories',
            'data'    => $categories
        ]);
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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Generate slug from name if not provided
        $slug = $request->slug ? $request->slug : Str::slug($request->name);

        // Ensure the slug is unique
        $slug = $this->generateUniqueSlug($slug);

        // Create category
        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        // Return created category as JSON
        return response()->json([
            'success'=> true,
            'message'=> 'Category Create Successfully',
        'data'=> $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);

        if ($category) {
            // Return category as JSON
            return response()->json($category);
        }

        // Return not found response
        return response()->json(['message' => 'Category not found'], 404);
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
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|unique:categories,slug,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Generate slug from name if not provided
        $slug = $request->input('slug') ? $request->input('slug') : Str::slug($request->input('name', $category->name));

        // Ensure the slug is unique
        $slug = $this->generateUniqueSlug($slug, $id);

        // Update category
        $category->update([
            'name' => $request->input('name', $category->name),
            'slug' => $slug,
        ]);

        // Return updated category as JSON
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            // Return success message
            return response()->json(['message' => 'Category deleted successfully'], 200);
        }

        // Return not found response
        return response()->json(['message' => 'Category not found'], 404);
    }

    /**
     * Generate a unique slug by appending a number if necessary.
     *
     * @param  string  $slug
     * @param  int|null  $excludeId
     * @return string
     */
    private function generateUniqueSlug($slug, $excludeId = null)
    {
        $originalSlug = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
