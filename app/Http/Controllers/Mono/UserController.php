<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Select hanya field yang diperlukan dan eager load roles
            $users = User::withoutTrashed()
                ->select(['id', 'name', 'email', 'active', 'avatar', 'created_at'])
                ->with(['roles:id,name']);

            return datatables()->of($users)
                ->addColumn('active', function (User $user) {
                    return $user->active
                        ? '<span class="badge bg-label-success">Active</span>'
                        : '<span class="badge bg-label-danger">Inactive</span>';
                })
                ->addColumn('role', function (User $user) {
                    return $user->roles
                        ->map(fn ($role) => '<span class="badge bg-label-primary">' . $role->name . '</span>')
                        ->implode(' ');
                })
                ->addColumn('aksi', function () {
                    return '';
                })
                ->rawColumns(['active', 'role', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/user.index');
    }

    public function edit($id)
    {
        // Optimasi: Select field yang diperlukan dan cache roles
        $user = User::select(['id', 'name', 'email', 'active', 'avatar'])
            ->with(['roles:id,name'])
            ->findOrFail($id);

        $roles = \Cache::remember('roles_list', 3600, function() {
            return Role::select(['id', 'name'])->get();
        });

        return response()->json([
            'user'            => $user,
            'roles'           => $roles,
            'userRole'        => $user->roles->pluck('id')->first()
        ]);
    }

    public function update($id, UpdateUserRequest $request)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'errors' => 'User Tidak Ditemukan'
            ]);
        }

        // Siapkan data untuk update
        $updateData = $request->only(['name', 'email', 'active']);

        // Jika password diisi, hash dan tambahkan ke data update
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        if ($request->has('role')) {
            $role = Role::findById($request->role);
            if ($role) {
                $user->syncRoles($role);
            }
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            $avatarName = time() . '_' . Str::random(10) . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->storeAs('public/avatars', $avatarName);

            $user->update(['avatar' => $avatarName]);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Data user berhasil diubah'
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data User Tidak Ditemukan'
            ]);
        }

        if ($user->avatar) {
            Storage::delete('public/avatars/' . $user->avatar);
        }

        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data User Berhasil Dihapus'
        ]);
    }

    public function profile($id)
    {
        // Optimasi: Eager load dan select field yang diperlukan
        $user = User::with([
                'roles:id,name',
                'user_profile'
            ])
            ->select(['id', 'name', 'email', 'active', 'avatar', 'created_at'])
            ->findOrFail($id);

        $userProfile = UserProfile::where('user_id', $id)->first();
        return view('internal/user.profile', compact('user', 'userProfile'));
    }

}
