<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('student.user')->get();
        return response()->json($organizations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
        ]);

        $organization = Organization::create($request->all());
        return response()->json($organization->load('student.user'), 201);
    }

    public function show(Organization $organization)
    {
        return response()->json($organization->load('student.user'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
        ]);

        $organization->update($request->all());
        return response()->json($organization->load('student.user'));
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return response()->json(null, 204);
    }
}
