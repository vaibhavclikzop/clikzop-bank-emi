<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    use AuthorizesRequests;

    public function getRoles(Request $request)
    {
        try {
            $this->authorize('viewAny', User::class);
            $user = Role::with('permissions')->get();

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getUsers(Request $request)
    {
        try {
            $this->authorize('viewAny', User::class);
            $user = User::all();

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function saveUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authorize('create', User::class);
            if ($request->id) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                    'name' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first(),
                        'data' => [],
                    ], 401);
                }
                $user = User::where('id', $request->id)->first();

                if (! $user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User not found',
                    ]);
                }

                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'parent_id' => $request->parent_id,
                    'tenant_id' => null,
                    'role' => $request->role,
                    'state' => $request->state,
                    'district' => $request->district,
                    'city' => $request->city,
                    'address' => $request->address,
                    'pincode' => $request->pincode,
                    'active' => $request->active,
                ]);
            } else {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email|unique:users,email',

                    'name' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first(),
                        'data' => [],
                    ], 401);
                }

                $role = Role::where('name', $request->role)->first();

                if (! $role) {

                    return response()->json([
                        'status' => false,
                        'message' => 'Role Not found',
                        'data' => [],
                    ], 401);
                }

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'parent_id' => $request->parent_id,

                    'tenant_id' => null,
                    'state' => $request->state,
                    'district' => $request->district,
                    'city' => $request->city,
                    'address' => $request->address,
                    'pincode' => $request->pincode,
                ]);

                $user->roles()->sync([$role->id]);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save Successfully',
                'data' => $user,

            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getRoleBaseManager(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 401);
        }

        try {

            $authUser = auth()->user();

            $mapping = [

                'super_admin' => [
                    'coordinator' => ['super_admin'],
                    'staff' => ['super_admin', 'coordinator'],
                ],

                'tenant' => [
                    'coordinator' => ['dsa_admin'],
                    'staff' => ['coordinator'],
                ],

            ];

            $authUser = auth()->user();

            $type = $authUser->tenant_id == null
                ? 'super_admin'
                : 'tenant';

            $roles = $mapping[$type][$request->role] ?? [];

            $query = User::query();

            $query->whereIn('role', $roles);

            if ($type == 'super_admin') {
                $query->whereNull('tenant_id');
            } else {
                $query->where('tenant_id', $authUser->tenant_id);
            }

            $users = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $users,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getAllPermissions(Request $request)
    {
        $data = Permission::get();

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => $data,

        ], 200);
    }

    public function getCompanyHierarchy(Request $request)
    {

        $arr = [];
        $sno = 0;
        $users = DB::table('users as a')->join('roles as b', 'a.role', 'b.name')->select('a.*', 'b.name as designation')->get();
        foreach ($users as $key => $value) {

            if ($value->id == 1) {
                $value->parent_id = 1;
            }
            $user = DB::table('users as a')
                ->join('roles as b', 'a.role', 'b.name')
                ->select('a.*', 'b.name as designation')
                ->where('a.id', $value->parent_id)->get();

            $name = ['name' => $value->name, 'role' => $value->designation];
            $arr[$sno][] = $name;
            foreach ($user as $key1 => $value1) {

                $arr[$sno][] = $value1->name;
            }
            $arr[$sno][] = '';
            $sno++;
        }
        $data = $arr;

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => $data,

        ], 200);
    }

    public function saveRolePermission(Request $request)
    {
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'roles' => 'required|array',
            'roles.*.role_id' => 'required|exists:roles,id',
            'roles.*.permission_ids' => 'required|array',
            'roles.*.permission_ids.*' => 'exists:permissions,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'message' => $validator->errors()->first(),
                'data' => [],
            ]);
        }
        DB::beginTransaction();
        try {
            foreach ($data['roles'] as $item) {
                $role = Role::find($item['role_id']);
                if ($role) {
                    $role->permissions()->sync($item['permission_ids']);
                }
            }
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Role permissions saved successfully',
                'data' => [],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
                'data' => [],
            ]);
        }
    }
}
