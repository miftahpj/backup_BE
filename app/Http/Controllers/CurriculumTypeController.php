<?php

namespace App\Http\Controllers;

use App\Models\CurriculumType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CurriculumTypeController extends Controller
{
    public function index()
    {
        $curriculumTypes = CurriculumType::with('company')->get();
        return response()->json($curriculumTypes, 200);
    }

    public function show($id)
    {
        try {
            $curriculumType = CurriculumType::with('company')->findOrFail($id);
            return response()->json($curriculumType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Curriculum Type not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:curriculum_types,name',
                'company_id' => 'required|exists:companies,id'
            ]);

            $curriculumType = CurriculumType::create($validatedData);

            return response()->json([
                'message' => 'Curriculum Type created successfully',
                'data' => $curriculumType
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $curriculumType = CurriculumType::findOrFail($id);

            $validatedData = $request->validate([
                'name' => '|string|max:255|unique:curriculum_types,name',
                'company_id' => '|exists:companies,id'
            ]);
            

            $curriculumType->update($validatedData);

            return response()->json([
                'message' => 'Curriculum Type updated successfully',
                'data' => $curriculumType
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Curriculum Type not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $curriculumType = CurriculumType::findOrFail($id);
            $curriculumType->delete();

            return response()->json(['message' => 'Curriculum Type deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Curriculum Type not found'], 404);
        }
    }

    public function getCoursesByCurriculumType($curriculumTypeId)
    {
        $curriculumType = CurriculumType::with('courses')->find($curriculumTypeId);

        if (!$curriculumType) {
            return response()->json(['message' => 'Curriculum Type not found'], 404);
        }

        if ($curriculumType->courses->isEmpty()) {
            return response()->json(['message' => 'No courses found for this Curriculum Type'], 404);
        }

        return response()->json($curriculumType, 200);
    }
}
