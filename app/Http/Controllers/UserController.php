<?php

namespace App\Http\Controllers;

use App\Models\OrganizationUnit;
use App\Models\Role;
use App\Models\User;
use App\Support\SearchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'organizationUnit']);

        // Global search (backward compat) - case-insensitive
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'username', $search, 'or');
                SearchHelper::whereLike($q, 'email', $search, 'or');
            });
        }

        // Per-column filters - case-insensitive
        if ($request->filled('filter_name')) {
            SearchHelper::whereLike($query, 'name', $request->filter_name);
        }
        if ($request->filled('filter_nik')) {
            SearchHelper::whereLike($query, 'nik', $request->filter_nik);
        }
        if ($request->filled('filter_username')) {
            SearchHelper::whereLike($query, 'username', $request->filter_username);
        }
        if ($request->filled('filter_email')) {
            SearchHelper::whereLike($query, 'email', $request->filter_email);
        }
        if ($request->filled('filter_role')) {
            $query->whereHas('role', function ($q) use ($request) {
                SearchHelper::whereLike($q, 'name', $request->filter_role, 'and');
                SearchHelper::whereLike($q, 'display_name', $request->filter_role, 'or');
            });
        }
        if ($request->filled('filter_organization')) {
            $query->whereHas('organizationUnit', function ($q) use ($request) {
                SearchHelper::whereLike($q, 'name', $request->filter_organization);
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
