<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Company::paginate(500), 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
                'type' => 'required|in:partner,client', 
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('companies', 'public');
                $validatedData['image'] = Storage::url($path);
            }

            $company = Company::create($validatedData);
            return response()->json($company, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function show(Company $company)
    {
        return response()->json($company, 200);
    }

    public function update(Request $request, Company $company)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
                'type' => 'sometimes|required|in:partner,client', 
            ]);

            if ($request->hasFile('image')) {
                if ($company->image) {
                    $oldImagePath = str_replace('/storage/', 'public/', $company->image);
                    Storage::delete($oldImagePath);
                }

                
                $path = $request->file('image')->store('companies', 'public');
                $validatedData['image'] = Storage::url($path);
            } elseif ($request->has('image') && $request->image === null) {
              
                if ($company->image) {
                    $oldImagePath = str_replace('/storage/', 'public/', $company->image);
                    Storage::delete($oldImagePath);
                }
                $validatedData['image'] = null;
            }

            $company->update($validatedData);

            return response()->json($company, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy(Company $company)
    {
        if ($company->image) {
            $imagePath = str_replace('/storage/', 'public/', $company->image);
            Storage::delete($imagePath);
        }
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully'], 200);
    }
}
