<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceTypeResource;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();

        return ServiceTypeResource::collection($serviceTypes);
    }

    public function show($id)
    {
        $serviceType = ServiceType::findOrFail($id);

        return new ServiceTypeResource($serviceType);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $serviceType = ServiceType::create($validated);

        return new ServiceTypeResource($serviceType);
    }

    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'department' => 'sometimes|string|max:255',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $serviceType->update($validated);

        return new ServiceTypeResource($serviceType);
    }

    public function destroy($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceType->delete();

        return response()->json(['message' => 'Service type deleted successfully']);
    }
}

