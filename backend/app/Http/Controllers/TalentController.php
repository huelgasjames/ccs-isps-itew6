<?php

namespace App\Http\Controllers;

use App\Models\Talent;
use Illuminate\Http\Request;

class TalentController extends Controller
{
    public function index()
    {
        $talents = Talent::with('student.user')->get();
        return response()->json($talents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'talent_name' => 'required|string|max:100',
        ]);

        $talent = Talent::create($request->all());
        return response()->json($talent->load('student.user'), 201);
    }

    public function show(Talent $talent)
    {
        return response()->json($talent->load('student.user'));
    }

    public function update(Request $request, Talent $talent)
    {
        $request->validate([
            'talent_name' => 'required|string|max:100',
        ]);

        $talent->update($request->all());
        return response()->json($talent->load('student.user'));
    }

    public function destroy(Talent $talent)
    {
        $talent->delete();
        return response()->json(null, 204);
    }
}
