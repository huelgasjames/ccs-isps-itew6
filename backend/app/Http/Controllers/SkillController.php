<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::with('student.user')->get();
        return response()->json($skills);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'skill_name' => 'required|string|max:100',
        ]);

        $skill = Skill::create($request->all());
        return response()->json($skill->load('student.user'), 201);
    }

    public function show(Skill $skill)
    {
        return response()->json($skill->load('student.user'));
    }

    public function update(Request $request, Skill $skill)
    {
        $request->validate([
            'skill_name' => 'required|string|max:100',
        ]);

        $skill->update($request->all());
        return response()->json($skill->load('student.user'));
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json(null, 204);
    }
}
