<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmployeDetail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->get();

        return view('cms.user.employe', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$store) {
                $store = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                $store->assignRole('majikan');

                EmployeDetail::create([
                    'user_id' => $store->id,
                    'phone' => $request->phone,
                    'address' => $request->address
                ]);
            });
            if ($store) {
                return redirect()->route('users-employe.index')->with('success', 'Majikan berhasil ditambahkan!');
            } else {
                return back()->with('error', 'Users Majikan gagal ditambahkan!');
            }
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('error', compact('data'));
        }
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
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id),],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id),],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $user->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ]);

        $user->employeDetails()->update([
            'phone' => $data['phone'],
            'address' => $data['address']
        ]);

        return redirect()->route('users-employe.index')->with('success', 'Majikan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->user_id);

        $user->delete();

        return redirect()->route('users-employe.index')->with('success', 'Majikan berhasil dihapus!');
    }
}
