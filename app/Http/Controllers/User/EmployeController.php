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
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
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
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'identity_card' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, &$store) {
                $filesToUpdate = ['identity_card'];

                foreach ($filesToUpdate as $fileKey) {
                    if (isset($data[$fileKey]) && $data[$fileKey]->isValid()) {
                        $newFile = $data[$fileKey];
                        $newFileName = "{$fileKey}_{$data['username']}." . $newFile->getClientOriginalExtension();
                        Storage::putFileAs("public/img/$fileKey", $newFile, $newFileName);
                        $data[$fileKey] = $newFileName;
                    }
                }

                $store = User::create([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                $store->assignRole('majikan');

                EmployeDetail::create([
                    'user_id' => $store->id,
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'identity_card' => $data['identity_card'],
                ]);
            });
            if ($store) {
                Alert::success('Berhasil!', 'Majikan berhasil ditambahkan!');
                return redirect()->route('users-employe.index')->with('success', 'Majikan berhasil ditambahkan!');
            } else {
                return back()->with('toast_error', 'Users Majikan gagal ditambahkan!');
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
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'identity_card' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $filesToUpdate = ['identity_card'];

        foreach ($filesToUpdate as $fileKey) {
            if (isset($data[$fileKey])) {
                $oldFile = $user->employeDetails->$fileKey;

                if ($oldFile && Storage::exists("public/img/$fileKey/$oldFile")) {
                    Storage::delete("public/img/$fileKey/$oldFile");
                }

                $newFile = $data[$fileKey];
                $newFileName = "{$fileKey}_{$user->username}." . $newFile->getClientOriginalExtension();
                Storage::putFileAs("public/img/$fileKey", $newFile, $newFileName);
                $data[$fileKey] = $newFileName;
            } else {
                $data[$fileKey] = $user->employeDetails->$fileKey;
            }
        }

        $user->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ]);

        $user->employeDetails()->update([
            'phone' => $data['phone'],
            'address' => $data['address'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'identity_card' => $data['identity_card'],
        ]);

        Alert::success('Berhasil!', 'Majikan berhasil diperbarui!');
        return redirect()->route('users-employe.index');
    }

    public function show(string $id)
    {
        $data = User::find($id);

        return view('cms.user.partials.employe.detail', compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->user_id);

        if ($user->vacancies->count() > 0) {
            return redirect()->route('users-employe.index')->with('toast_error', 'Majikan memiliki lowongan pekerjaan!');
        }

        $user->delete();

        Alert::success('Berhasil!', 'Majikan berhasil dihapus!');
        return redirect()->route('users-employe.index');
    }
}
