<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FacultyResource;
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
        // Get faculties
        $faculties = Faculty::when(request()->search, function($query) {
            $query->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        // Append query string to pagination links
        $faculties->appends(['search' => request()->search]);

        // Return with Api Resource
        return new FacultyResource(true, 'List Data Faculties', $faculties);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'initial'  => 'required',
            'desc'     => 'required',
            'domain'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create faculty
        $faculty = Faculty::create([
            'name'     => $request->name,
            'initial'  => $request->initial,
            'desc'     => $request->desc,
            'domain'   => $request->domain
        ]);

        // Return success with Api Resource
        return new FacultyResource(true, 'Data Faculty Successfully Created!', $faculty);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $faculty = Faculty::whereId($id)->first();

        if ($faculty) {
            // Return success with Api Resource
            return new FacultyResource(true, 'Detail Data Faculty!', $faculty);
        }

        // Return failed with Api Resource
        return new FacultyResource(false, 'Data Faculty Not Found!', null);
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
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'initial'  => 'required',
            'desc'     => 'required',
            'domain'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $faculty = Faculty::find($id);

        if (!$faculty) {
            return new FacultyResource(false, 'Data Faculty Not Found!', null);
        }

        // Update faculty
        $faculty->update([
            'name'     => $request->name,
            'initial'  => $request->initial,
            'desc'     => $request->desc,
            'domain'   => $request->domain
        ]);

        // Return success with Api Resource
        return new FacultyResource(true, 'Data Faculty Successfully Updated!', $faculty);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faculty = Faculty::find($id);

        if ($faculty && $faculty->delete()) {
            // Return success with Api Resource
            return new FacultyResource(true, 'Data Faculty Successfully Deleted!', null);
        }

        // Return failed with Api Resource
        return new FacultyResource(false, 'Data Faculty Deletion Failed!', null);
    }
}
