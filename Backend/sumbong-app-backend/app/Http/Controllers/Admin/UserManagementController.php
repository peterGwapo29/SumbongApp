<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        $perPage = max(5, min(100, (int) $request->get('per_page', 10)));

        // Apply filters only when a non-empty value is provided
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->string('user_type'));
        }

        if ($request->filled('verified')) {
            $query->where('verified', $request->boolean('verified'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show($id)
    {
        $user = User::with(['role', 'requests', 'assignments'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }
}

