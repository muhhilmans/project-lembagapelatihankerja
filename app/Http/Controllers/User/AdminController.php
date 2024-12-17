<?php

namespace App\Http\Controllers\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataRoles = Role::whereNotIn('name', ['pembantu', 'majikan', 'superadmin'])
            ->when(Auth::user()->roles->pluck('name')->contains('owner'), function ($query) {
                $query->where('name', '!=', 'superadmin');
            })->get();

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                if (Auth::user()->roles->pluck('name')->contains('superadmin')) {
                    $query->whereIn('name', ['owner', 'admin']);
                } elseif (Auth::user()->roles->pluck('name')->contains('owner')) {
                    $query->where('name', 'admin');
                }
            })->get();

        return view('cms.user.admin',  compact(['users', 'dataRoles']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $auth = Auth::user();
        if ($auth->roles->first()->name == 'superadmin') {
            $user->assignRole($request->role);
        } elseif ($auth->roles->first()->name == 'owner') {
            $user->assignRole('admin');
        }

        return redirect()->route('users-admin.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            // 'password' => ['sometimes', 'nullable', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        // $user->password = $data['password'];

        $auth = Auth::user();
        if ($auth->roles->first()?->name == 'superadmin') {
            $role = Role::findById($request->role);

            $user->syncRoles($role);
        } elseif ($auth->roles->first()?->name == 'owner') {
            $user->syncRoles('admin');
        }

        $user->save();

        return redirect()->route('users-admin.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
