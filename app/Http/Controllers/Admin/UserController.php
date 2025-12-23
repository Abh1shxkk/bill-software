<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.users.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telephone' => $validated['telephone'] ?? null,
                'address' => $validated['address'] ?? null,
                'role' => 'user',
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Save permissions
            if (!empty($validated['permissions'])) {
                $this->syncPermissions($user, $validated['permissions']);
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user->load('permissions');
        $permissions = Permission::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.users.show', compact('user', 'permissions'));
    }

    public function edit(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot edit admin user.');
        }

        $user->load('permissions');
        $permissions = Permission::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        // Get user's current permissions as array
        $userPermissions = $user->userPermissions->keyBy('permission_id');

        return view('admin.users.edit', compact('user', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot edit admin user.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'password' => 'nullable|string|min:6|confirmed',
            'telephone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'full_name' => $validated['full_name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Sync permissions
            $this->syncPermissions($user, $validated['permissions'] ?? []);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin user.',
            ], 403);
        }

        if ($user->user_id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account.',
            ], 403);
        }

        try {
            $user->userPermissions()->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change admin status.',
            ], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'User activated.' : 'User deactivated.',
            'is_active' => $user->is_active,
        ]);
    }

    public function permissions(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Admin has all permissions by default.');
        }

        $user->load('permissions');
        $permissions = Permission::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $userPermissions = $user->userPermissions->keyBy('permission_id');

        return view('admin.users.permissions', compact('user', 'permissions', 'userPermissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot modify admin permissions.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $this->syncPermissions($user, $validated['permissions'] ?? []);
            DB::commit();

            return redirect()->route('admin.users.permissions', $user)
                ->with('success', 'Permissions updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }

    /**
     * Sync user permissions.
     */
    private function syncPermissions(User $user, array $permissions): void
    {
        // Delete existing permissions
        $user->userPermissions()->delete();

        // Insert new permissions
        foreach ($permissions as $permissionId => $actions) {
            if (is_array($actions) && (
                !empty($actions['view']) ||
                !empty($actions['create']) ||
                !empty($actions['edit']) ||
                !empty($actions['delete'])
            )) {
                UserPermission::create([
                    'user_id' => $user->user_id,
                    'permission_id' => $permissionId,
                    'can_view' => !empty($actions['view']),
                    'can_create' => !empty($actions['create']),
                    'can_edit' => !empty($actions['edit']),
                    'can_delete' => !empty($actions['delete']),
                ]);
            }
        }
    }

    public function multipleDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No users selected.',
            ], 400);
        }

        // Filter out admin users and current user
        $users = User::whereIn('user_id', $ids)
            ->where('role', '!=', 'admin')
            ->where('user_id', '!=', auth()->id())
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid users to delete.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                $user->userPermissions()->delete();
                $user->delete();
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($users) . ' user(s) deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete users: ' . $e->getMessage(),
            ], 500);
        }
    }
}
