<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('student.user')->get();
        return response()->json($certificates);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'certificate_name' => 'required|string|max:255',
            'date_received' => 'nullable|date',
        ]);

        $certificate = Certificate::create($request->all());
        return response()->json($certificate->load('student.user'), 201);
    }

    public function show(Certificate $certificate)
    {
        return response()->json($certificate->load('student.user'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $request->validate([
            'certificate_name' => 'required|string|max:255',
            'date_received' => 'nullable|date',
        ]);

        $certificate->update($request->all());
        return response()->json($certificate->load('student.user'));
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return response()->json(null, 204);
    }
}
