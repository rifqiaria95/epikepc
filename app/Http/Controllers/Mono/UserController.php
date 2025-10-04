<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Services\FileStorageService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }
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

        $roles = Cache::remember('roles_list', 3600, function() {
            return Role::select(['id', 'name'])->get();
        });

        return response()->json([
            'user'            => $user,
            'roles'           => $roles,
            'userRole'        => $user->roles->pluck('id')->first()
        ]);
    }

    public function create()
    {
        $roles = Cache::remember('roles_list', 3600, function() {
            return Role::select(['id', 'name'])->get();
        });

        return response()->json([
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'active' => ['required', 'in:0,1'],
            'role' => ['required', 'exists:roles,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048']
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->active = (int) $validated['active'];
        $user->password = bcrypt($validated['password']);
        $user->save();

        // Upload avatar ke object storage jika ada
        if ($request->hasFile('avatar')) {
            $uploadResult = $this->fileStorageService->uploadImage(
                $request->file('avatar'),
                'users/avatars'
            );

            if (!$uploadResult['success']) {
                throw new \Exception('Gagal upload avatar: ' . $uploadResult['error']);
            }

            $user->update(['avatar' => $uploadResult['path']]);
        }

        $role = Role::findById($validated['role']);
        if ($role) {
            $user->syncRoles($role);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data user berhasil disimpan',
            'user_id' => $user->id
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

        // Upload avatar baru ke object storage jika ada
        if ($request->hasFile('avatar')) {
            $uploadResult = $this->fileStorageService->uploadImage(
                $request->file('avatar'),
                'users/avatars'
            );

            if (!$uploadResult['success']) {
                throw new \Exception('Gagal upload avatar: ' . $uploadResult['error']);
            }

            // Hapus avatar lama jika ada
            if ($user->avatar) {
                $this->fileStorageService->deleteFile($user->avatar);
            }

            $user->update(['avatar' => $uploadResult['path']]);
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
