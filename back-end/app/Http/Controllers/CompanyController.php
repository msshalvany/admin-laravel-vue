<?php

// app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::all();

        return response()->json([
            'status' => true,
            'message' => 'Companies retrieved successfully',
            'data' => $companies,
        ], 200);
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Company found successfully',
            'data' => $company
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::findOrFail($id);
        $company->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Company updated successfully',
            'data' => $company
        ], 200);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Company deleted successfully'
        ], 204);
    }

    public function getTrucks($id)
    {
        $company = Company::find($id);
        return response()->json([
            'status' => true,
            'data' => $company->trucks,
        ]);
    }
}
