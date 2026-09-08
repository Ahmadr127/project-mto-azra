<?php

namespace App\Http\Controllers;

use App\Models\OrganizationUnit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'organizationUnit']);

        // Global search (backward compat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Per-column filters
        if ($request->filled('filter_name')) {
            $query->where('name', 'like', "%{$request->filter_name}%");
        }
        if ($request->filled('filter_nik')) {
            $query->where('nik', 'like', "%{$request->filter_nik}%");
        }
        if ($request->filled('filter_username')) {
            $query->where('username', 'like', "%{$request->filter_username}%");
        }
        if ($request->filled('filter_email')) {
            $query->where('email', 'like', "%{$request->filter_email}%");
        }
        if ($request->filled('filter_role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->filter_role}%")
                    ->orWhere('display_name', 'like', "%{$request->filter_role}%");
            });
        }
        if ($request->filled('filter_organization')) {
            $query->whereHas('organizationUnit', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->filter_organization}%");
            });
        }

        // Date range filter (fallback for legacy table-filter)
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Per-column date filter (alias)
        if ($request->filled('filter_created_at')) {
            $query->whereDate('created_at', $request->filter_created_at);
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $users = $query->latest()->paginate($perPage)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $organizationUnits = OrganizationUnit::active()->orderBy('name')->get();

        return view('users.create', compact('roles', 'organizationUnits'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'organization_unit_id' => 'nullable|exists:organization_units,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'organization_unit_id' => $request->organization_unit_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $organizationUnits = OrganizationUnit::active()->orderBy('name')->get();

        return view('users.edit', compact('user', 'roles', 'organizationUnits'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50|unique:users,nik,'.$user->id,
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'organization_unit_id' => 'nullable|exists:organization_units,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'nik' => $request->nik,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'organization_unit_id' => $request->organization_unit_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Mencegah user menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
