<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\Office;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;

use Spatie\Permission\Models\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;

use App\Actions\SendEmailNotificationAction;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::all();
        $roles = Role::all();

        return Inertia::render('Admin/Users/Index', [
            'roles' => $roles,
            'offices' => $offices
        ]);
    }

    /**
     * Fetch a list of users
     */
    public function fetch(Request $request)
    {
        $options = $request->options;
        
        $users = User::query();
        $users = $users->where('role', '!=', 1);

        if ($request->keyword != "") {
            $users = $users->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->office_id != "") {
            $users = $users->where('office_id', $request->office_id);
        }

        if ($request->role != "") {
            $users = $users->where('role', $request->role);
        }

        if ($request->status != "") {
            $users = $users->where('status', $request->status);
        }

        if ($request->type == "1") {
            $users = $users->where('role', User::ROLE_CLIENT);
        } else {
            $users = $users->where('role', '!=', User::ROLE_CLIENT);
        }

        $users = $users->paginate($options['rowsPerPage'], ['*'], 'page', $options['page']);

        return response()->json([
            'items' => $users,
            'message' => 'Users successfully fetched!'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $offices = Office::all();
        $roles = Role::all();

        return Inertia::render('Admin/Users/Create', [
            'offices' => $offices,
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $password = Str::random(8);
        
        $user = User::create(array_merge($request->all(), [
            'name' => $request->first_name . ' ' . $request->last_name,
            'password' => Hash::make($password),
            'image_path' => 'https://placehold.co/500x500'
        ]));

        $role = Role::findOrFail($request->role);
        $user->assignRole($role);
        $user->syncPermissions($role->permissions);

        if ($user) {
            
            Notification::create([
                'type' => User::class,
                'resource_id' => $user->id,
                'description' => 'Admin has created your account.'
            ]);

            $data = [
                'user' => ['name' => $user->name, 'email' => $user->email],
                'notification' => auth()->user()->name . ' has created an account for you. To access your account, please use this temporary password: ' . $password,
                'link' => route('staff.login')
            ];
    
            // Send email notification
            $action = new SendEmailNotificationAction;
            $action->execute($data);

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'type' => User::class,
                'action' => 'You have created a user account (#'. $user->id.').'
            ]);

            return response()->json([
                'message' => 'User successfully created!']);
        } else {
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $admin = User::withTrashed()->findOrFail($id);

        return Inertia::render('Admin/Users/Id', [
            'item' => $admin
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $offices = Office::all();
        $roles = Role::all();
        $admin = User::findOrFail($id);

        return Inertia::render('Admin/Users/Edit', [
            'item' => $admin,
            'roles' => $roles,
            'offices' => $offices
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $officeId = $request->role == User::ROLE_CLIENT ? 1 : $request->office_id;
        $admin->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'name' => $request->first_name . ' ' . $request->last_name,
            'office_id' => $officeId,
            'role' => $request->role
        ]);

        $role = Role::findOrFail($request->role);
        $admin->syncRoles($role);
        $admin->syncPermissions($role->permissions);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have updated a user account (#'. $admin->id.').'
        ]);

        return response()->json([
            'message' => 'User #' . $id . ' was successfully updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Activate / Deactivate the specified resource from storage.
     * 
     */
    public function toggleActivate($id)
    {
        $user = User::findOrFail($id);

        $user->status = !$user->status;
        $user->save();

        $status = $user->status ? 'activated' : 'deactivated';

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have ' . $status .' User #' . $id . '.'
        ]);

        return response()->json([
            'message' => 'User #' . $id . ' was successfully ' . $status . '.'
        ]);
    }
}
