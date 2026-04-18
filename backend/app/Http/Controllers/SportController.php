<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use Illuminate\Http\Request;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::with('student.user')->get();
        return response()->json($sports);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'sport_name' => 'required|string|max:100',
        ]);

        $sport = Sport::create($request->all());
        return response()->json($sport->load('student.user'), 201);
    }

    public function show(Sport $sport)
    {
        return response()->json($sport->load('student.user'));
    }

    public function update(Request $request, Sport $sport)
    {
        $request->validate([
            'sport_name' => 'required|string|max:100',
        ]);

        $sport->update($request->all());
        return response()->json($sport->load('student.user'));
    }

    public function destroy(Sport $sport)
    {
        $sport->delete();
        return response()->json(null, 204);
    }
}
