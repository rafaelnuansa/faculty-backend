<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get users with optional search functionality
        $users = User::when(request()->search, function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->with('faculty')->latest()->paginate(10);

        // Append query string to pagination links
        $users->appends(['search' => request()->search]);

        // Return the list of users with pagination
        return response()->json([
            'success' => true,
            'message' => 'List of Users',
            'data' => $users
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
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'faculty_id' => 'nullable|uuid|exists:faculties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Create a new user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'faculty_id' => $request->faculty_id,
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'data'    => $user,
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
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
            ], 404);
        }

        // Return the user data
        return response()->json([
            'success' => true,
            'message' => 'User details',
            'data'    => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:6|confirmed',
            'faculty_id' => 'nullable|uuid|exists:faculties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Update user details without changing password if it's not provided
        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'faculty_id' => $request->faculty_id,
            'password'  => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'data'    => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!',
        ]);
    }
}
