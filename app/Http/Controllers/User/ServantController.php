<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Models\ServantDetail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ServantSkill;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

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
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
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
                Alert::success('Berhasil!', 'Pembantu berhasil ditambahkan!');
                return redirect()->route('users-servant.index');
            } else {
                return back()->with('toast_error', 'Pembantu gagal ditambahkan!');
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
        $user = User::findOrFail($id);

        $professions = Profession::all();

        return view('cms.user.partials.servant.edit-servant', compact(['user', 'professions']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id),],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id),],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:255'],
            'religion' => ['required', 'string', 'max:255'],
            'marital_status' => ['required', 'string', 'max:255'],
            'children' => ['required', 'integer'],
            'profession_id' => ['required', 'exists:professions,id'],
            'last_education' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'emergency_number' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'rt' => ['required', 'string', 'max:255'],
            'rw' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'regency' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'village' => ['required', 'string', 'max:255'],
            'working_status' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'photo' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'identity_card' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'family_card' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $user) {
                $filesToUpdate = ['photo', 'identity_card', 'family_card'];
    
                foreach ($filesToUpdate as $fileKey) {
                    if (isset($data[$fileKey])) {
                        $oldFile = $user->servantDetails->$fileKey;
    
                        if ($oldFile && Storage::exists("public/img/$fileKey/$oldFile")) {
                            Storage::delete("public/img/$fileKey/$oldFile");
                        }
    
                        $newFile = $data[$fileKey];
                        $newFileName = "{$fileKey}_{$user->username}." . $newFile->getClientOriginalExtension();
                        Storage::putFileAs("public/img/$fileKey", $newFile, $newFileName);
                        $data[$fileKey] = $newFileName;
                    } else {
                        $data[$fileKey] = $user->servantDetails->$fileKey;
                    }
                }
    
                $user->update([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                ]);
    
                $user->servantDetails()->update([
                    'place_of_birth' => $data['place_of_birth'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'religion' => $data['religion'],
                    'marital_status' => $data['marital_status'],
                    'children' => $data['children'],
                    'profession_id' => $data['profession_id'],
                    'last_education' => $data['last_education'],
                    'phone' => $data['phone'],
                    'emergency_number' => $data['emergency_number'],
                    'address' => $data['address'],
                    'rt' => $data['rt'],
                    'rw' => $data['rw'],
                    'province' => $data['province'],
                    'regency' => $data['regency'],
                    'district' => $data['district'],
                    'village' => $data['village'],
                    'working_status' => $data['working_status'],
                    'experience' => $data['experience'],
                    'description' => $data['description'],
                    'photo' => $data['photo'],
                    'identity_card' => $data['identity_card'],
                    'family_card' => $data['family_card'],
                ]);
            });
    
            Alert::success('Berhasil!', 'Pembantu berhasil diperbarui!');
            return redirect()->route('users-servant.show', $user->id);
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->user_id);

        if ($user->applyJobs->count() > 0) {
            return redirect()->route('users-servant.index')->with('toast_error', 'Pembantu memiliki lamaran pekerjaan!');
        }

        $user->delete();

        Alert::success('Berhasil!', 'Pembantu berhasil dihapus!');
        return redirect()->route('users-servant.index');
    }

    public function changeStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $user->is_active = ($user->is_active == 1 ? 0 : 1);
        $user->save();

        $statusMessage = $user->is_active == 1 ? 'Diaktifkan' : 'Dinonaktifkan';

        Alert::success('Berhasil!', 'Pembantu berhasil ' . $statusMessage);
        return redirect()->route('users-servant.index');
    }

    public function storeSkill(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'skill' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $user) {
                ServantSkill::create([
                    'user_id' => $user->id,
                    'skill' => $data['skill'],
                    'level' => $data['level'],
                ]);
            });

            Alert::success('Berhasil', 'Keahlian berhasil ditambahkan!');
            return redirect()->route('users-servant.show', $user->id);
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function updateSkill(Request $request, string $id, string $skill_id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $skill = ServantSkill::findOrFail($skill_id);
        
        $validator = Validator::make($request->all(), [
            'skill' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $skill) {
                $skill->update([
                    'skill' => $data['skill'],
                    'level' => $data['level'],
                ]);
            });

            Alert::success('Berhasil', 'Keahlian berhasil diperbarui!');
            return redirect()->route('users-servant.show', $user->id);
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function destroySkill(string $id, string $skill_id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $skill = ServantSkill::findOrFail($skill_id);

        $skill->delete();

        Alert::success('Berhasil', 'Keahlian berhasil dihapus!');
        return redirect()->route('users-servant.show', $user->id);
    }
}
