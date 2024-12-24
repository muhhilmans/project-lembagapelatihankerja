<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profession;
use App\Models\ServantSkill;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile($id)
    {
        $data = User::findOrFail($id);

        return view('cms.profile.index', compact('data'));
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);

        $professions = Profession::all();

        if ($data->roles->first()->name == 'pembantu') {
            return view('cms.profile.partial.edit-servant', compact(['data', 'professions']));
        } else {
            return view('cms.profile.partial.edit-employe', compact('data'));
        }
    }
    
    public function updateServant(Request $request, string $id)
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
            'experience' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'photo' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'identity_card' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'family_card' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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
                    'experience' => $data['experience'],
                    'description' => $data['description'],
                    'photo' => $data['photo'],
                    'identity_card' => $data['identity_card'],
                    'family_card' => $data['family_card'],
                ]);
            });
    
            return redirect()->route('profile', $user->id)->with('success', 'Profile berhasil diperbarui!');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function storeSkill(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'skill' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

            return redirect()->route('profile', $user->id)->with('success', 'Keahlian berhasil ditambahkan!');
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $skill) {
                $skill->update([
                    'skill' => $data['skill'],
                    'level' => $data['level'],
                ]);
            });

            return redirect()->route('profile', $user->id)->with('success', 'Keahlian berhasil diperbarui!');
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

        return redirect()->route('profile', $user->id)->with('success', 'Keahlian berhasil dihapus!');
    }

    public function updateEmploye(Request $request, string $id)
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

        return redirect()->route('profile', $user->id)->with('success', 'Profile berhasil diperbarui!');
    }
}
