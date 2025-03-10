<?php

namespace App\Http\Controllers;

use App\Models\Kalender;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index()
    {
        $kalenders = Kalender::with('course')->get();
        return response()->json($kalenders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_course' => 'required|exists:course,id',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $kalender = Kalender::create($request->all());

        return response()->json(['message' => 'Kalender created successfully', 'kalender' => $kalender]);
    }

    public function show($id)
    {
        $kalender = Kalender::with('course')->findOrFail($id);
        return response()->json($kalender);
    }

    public function update(Request $request, $id)
    {
        $kalender = Kalender::findOrFail($id);

        $request->validate([
            'id_course' => 'required|exists:course,id',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $kalender->update($request->all());

        return response()->json(['message' => 'Kalender updated successfully', 'kalender' => $kalender]);
    }

    public function destroy($id)
    {
        $kalender = Kalender::findOrFail($id);
        $kalender->delete();

        return response()->json(['message' => 'Kalender deleted successfully']);
    }
}

