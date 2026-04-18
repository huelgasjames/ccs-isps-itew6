<?php

namespace App\Http\Controllers;

use App\Models\StudentSkill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = StudentSkill::with('student.user')->get();
        return response()->json($skills);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required|string|max:100',
            'category' => 'required|in:technical,soft,business,creative,sports,language',
            'proficiency' => 'required|in:beginner,intermediate,advanced,expert,native',
            'years_experience' => 'nullable|integer|min:0',
            'certifications' => 'nullable|string',
        ]);

        $skill = StudentSkill::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'category' => $request->category,
            'proficiency' => $request->proficiency,
            'years_experience' => $request->years_experience ?? 0,
            'certifications' => $request->certifications,
            'last_used' => now(),
        ]);
        return response()->json($skill->load('student.user'), 201);
    }

    public function show(StudentSkill $skill)
    {
        return response()->json($skill->load('student.user'));
    }

    public function update(Request $request, StudentSkill $skill)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:technical,soft,business,creative,sports,language',
            'proficiency' => 'required|in:beginner,intermediate,advanced,expert,native',
            'years_experience' => 'nullable|integer|min:0',
            'certifications' => 'nullable|string',
        ]);

        $skill->update($request->all());
        return response()->json($skill->load('student.user'));
    }

    public function destroy(StudentSkill $skill)
    {
        $skill->delete();
        return response()->json(null, 204);
    }
}
