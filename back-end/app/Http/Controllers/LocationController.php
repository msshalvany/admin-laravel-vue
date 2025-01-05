<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::all();

        return response()->json([
            'status' => true,
            'message' => 'Location retrieved successfully',
            'data' => $locations,
        ], 200);
    }

    public function show($id)
    {
        $locations = Location::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Location found successfully',
            'data' => $locations,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $locations = Location::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Location created successfully',
            'data' => $locations
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $locations = Location::findOrFail($id);
        $locations->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Location updated successfully',
            'data' => $locations
        ], 200);
    }

    public function destroy($id)
    {
        $locations = Location::findOrFail($id);
        $locations->delete();

        return response()->json([
            'status' => true,
            'message' => 'Location deleted successfully'
        ], 204);
    }
}
