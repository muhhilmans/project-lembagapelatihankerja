<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Models\ServantDetail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class ServantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })->get();

        $professions = Profession::all();

        return view('cms.user.servant', compact(['users', 'professions']));
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
            'profession_id' => ['required', 'exists:professions,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
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

                $store->assignRole('pembantu');

                ServantDetail::create([
                    'user_id' => $store->id,
                    'profession_id' => $request->profession_id
                ]);
            });
            if ($store) {
                return redirect()->route('users-servant.index')->with('success', 'Pembantu berhasil ditambahkan!');
            } else {
                return back()->with('error', 'Pembantu gagal ditambahkan!');
            }
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return view('cms.user.partials.servant.detail-servant', compact('user'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->user_id);

        $user->delete();

        return redirect()->route('users-servant.index')->with('success', 'Pembantu berhasil dihapus!');
    }

    public function changeStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $user->is_active = ($user->is_active == 1 ? 0 : 1);
        $user->save();

        $statusMessage = $user->is_active == 1 ? 'Diaktifkan' : 'Dinonaktifkan';

        return redirect()->route('users-servant.index')->with('success', 'Pembantu Berhasil ' . $statusMessage);
    }
}
