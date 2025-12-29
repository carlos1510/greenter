<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::where('user_id', JWTAuth::user()->id)->get();

        return response()->json($companies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'razon_social' => 'required|string',
            'ruc' => [
                'required',
                'string',
                'size:11',
                'regex:/^(10|20)\d{9}$/',
                new \App\Rules\UniqueRucRule(),
            ],
            'direccion' => 'required|string',
            'logo' => 'nullable|image',
            'sol_user' => 'required|string',
            'sol_pass' => 'required|string',
            'cert' => 'required|file|mimes:pem,txt',
            'cliente_id' => 'nullable|string',
            'cliente_secret' => 'nullable|string',
            'production' => 'nullable|boolean',
        ]);

        $data['cert_path'] = $request->file('cert')->store('certs');
        $data['user_id'] = JWTAuth::user()->id;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        $company = Company::create($data);
        
        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($company)
    {
        $company = Company::where('ruc', $company)
            ->where('user_id', JWTAuth::user()->id)
            ->firstOrFail();

        return response()->json($company, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $company)
    {
        $company = Company::where('ruc', $company)
            ->where('user_id', JWTAuth::user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'razon_social' => 'nullable|string|min:5',
            'ruc' => [
                'nullable',
                'string',
                'size:11',
                'regex:/^(10|20)\d{9}$/',
                new \App\Rules\UniqueRucRule($company->id),
            ],
            'direccion' => 'nullable|string|min:5',
            'logo' => 'nullable|image',
            'sol_user' => 'nullable|string|min:5',
            'sol_pass' => 'nullable|string|min:5',
            'cert' => 'nullable|file|mimes:pem,txt',
            'cliente_id' => 'nullable|string',
            'cliente_secret' => 'nullable|string',
            'production' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        if ($request->hasFile('cert')) {
            $data['cert_path'] = $request->file('cert')->store('certs');
        }

        $company->update($data);
        
        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($company)
    {
        $company = Company::where('ruc', $company)
            ->where('user_id', JWTAuth::user()->id)
            ->firstOrFail();
        
        $company->delete();

        return response()->json([
            'message' => 'Company deleted successfully'
        ], 200);
    }
}
