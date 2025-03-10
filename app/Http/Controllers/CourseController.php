<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['company', 'curriculumType', 'author', 'status'])->get();
        return response()->json($courses, 200);
    }

    public function show($id)
    {
        try {
            $course = Course::with(['company', 'curriculumType', 'author', 'status'])->findOrFail($id);
            return response()->json($course, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'curriculum_type_id' => 'required|integer|exists:curriculum_types,id',
                'author_id' => 'required|integer|exists:users,id',
                'status_id' => 'required|integer|exists:status,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric',
                'duration' => 'nullable|integer',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('courses', 'public');
                $validatedData['image'] = $imagePath;
            }

            $course = Course::create($validatedData);

            return response()->json(['message' => 'Course created successfully', 'data' => $course], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validatedData = $request->validate([
                'company_id' => 'integer|exists:companies,id',
                'curriculum_type_id' => 'integer|exists:curriculum_types,id',
                'author_id' => 'integer|exists:users,id',
                'status_id' => 'integer|exists:status,id',
                'title' => 'string|max:255',
                'description' => 'nullable|string',
                'price' => 'numeric',
                'duration' => 'integer',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                if ($course->image) {
                    Storage::disk('public')->delete($course->image);
                }
                $imagePath = $request->file('image')->store('courses', 'public');
                $validatedData['image'] = $imagePath;
            }

            $course->update($validatedData);

            return response()->json(['message' => 'Course updated successfully', 'data' => $course], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            
            // Hapus gambar jika ada
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }

            $course->delete();

            return response()->json(['message' => 'Course deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    }
}
