<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeManagementController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::latest()->paginate(20);

        return view('admin.service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('admin.service-types.create');
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

        ServiceType::create($validated);

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type created successfully');
    }

    public function edit($id)
    {
        $serviceType = ServiceType::findOrFail($id);

        return view('admin.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $serviceType->update($validated);

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type updated successfully');
    }

    public function destroy($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceType->delete();

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type deleted successfully');
    }
}

