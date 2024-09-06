<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get faculties with optional search functionality
        $faculties = Faculty::when(request()->search, function($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(10);

        // Append query string to pagination links
        $faculties->appends(['search' => request()->search]);

        // Return the list of faculties with pagination
        return response()->json([
            'success' => true,
            'message' => 'List of Faculties',
            'data'    => $faculties
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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'initial'  => 'required|string',
            'desc'     => 'required|string',
            'domain'   => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Create a new faculty
        $faculty = Faculty::create([
            'name'     => $request->name,
            'initial'  => $request->initial,
            'desc'     => $request->desc,
            'domain'   => $request->domain
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Faculty created successfully!',
            'data'    => $faculty,
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
        // Find the faculty by ID
        $faculty = Faculty::find($id);

        if (!$faculty) {
            return response()->json([
                'success' => false,
                'message' => 'Faculty not found!',
            ], 404);
        }

        // Return the faculty data
        return response()->json([
            'success' => true,
            'message' => 'Faculty details',
            'data'    => $faculty,
        ]);
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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'initial'  => 'required|string',
            'desc'     => 'required|string',
            'domain'   => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Find the faculty by ID
        $faculty = Faculty::find($id);

        if (!$faculty) {
            return response()->json([
                'success' => false,
                'message' => 'Faculty not found!',
            ], 404);
        }

        // Update faculty details
        $faculty->update([
            'name'     => $request->name,
            'initial'  => $request->initial,
            'desc'     => $request->desc,
            'domain'   => $request->domain
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Faculty updated successfully!',
            'data'    => $faculty,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the faculty by ID
        $faculty = Faculty::find($id);

        if (!$faculty) {
            return response()->json([
                'success' => false,
                'message' => 'Faculty not found!',
            ], 404);
        }

        // Delete the faculty
        $faculty->delete();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Faculty deleted successfully!',
        ]);
    }


    public function all(){
        $faculty = Faculty::all();
        return response()->json(['data' => $faculty]);
    }
}
