<?php

namespace App\Http\Controllers;

use App\Models\Application;
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
use RealRashid\SweetAlert\Facades\Alert;

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
            'is_bank' => ['sometimes', 'boolean'],
            'bank_name' => ['required_if:is_bank,1', 'string', 'max:255'],
            'account_number' => ['required_if:is_bank,1', 'string', 'max:255'],
            'is_bpjs' => ['sometimes', 'boolean'],
            'type_bpjs' => ['required_if:is_bpjs,1', 'string', 'max:255'],
            'number_bpjs' => ['required_if:is_bpjs,1', 'string', 'max:255'],
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
            return redirect()->route('profile', $user->id)->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();
        
        $data['is_bank'] = $request->has('is_bank') ? $request->boolean('is_bank') : false;
        $data['is_bpjs'] = $request->has('is_bpjs') ? $request->boolean('is_bpjs') : false;

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
                    'is_bank' => $data['is_bank'],
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'is_bpjs' => $data['is_bpjs'],
                    'type_bpjs' => $data['type_bpjs'],
                    'number_bpjs' => $data['number_bpjs'],
                    'experience' => $data['experience'],
                    'description' => $data['description'],
                    'photo' => $data['photo'],
                    'identity_card' => $data['identity_card'],
                    'family_card' => $data['family_card'],
                ]);
            });
    
            Alert::success('Berhasil', 'Profile berhasil diperbarui!');
            return redirect()->route('profile', $user->id);
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
            return redirect()->route('profile', $user->id)->with('toast_error', $validator->messages()->all()[0])->withInput();
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
            return redirect()->route('profile', $user->id);
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
            return redirect()->route('profile', $user->id)->with('toast_error', $validator->messages()->all()[0])->withInput();
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
            return redirect()->route('profile', $user->id);
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

        return redirect()->route('profile', $user->id)->with('toast_success', 'Keahlian berhasil dihapus!');
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
            'identity_card' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('profile', $user->id)->with('toast_error', $validator->messages()->all()[0])->withInput();
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
            'bank_name' => "-",
            'account_number' => "-",
            'identity_card' => $data['identity_card'],
        ]);

        Alert::success('Berhasil', 'Profile berhasil diperbarui!');
        return redirect()->route('profile', $user->id);
    }

    public function updateBank(Request $request, string $id)
{
    $oldData = Application::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'is_bank' => ['sometimes', 'boolean'],
        'bank_name' => ['required_if:is_bank,1', 'nullable', 'string', 'max:255'],
        'account_number' => ['required_if:is_bank,1', 'nullable', 'string', 'max:255'],
        'is_bpjs' => ['sometimes', 'boolean'],
        'type_bpjs' => ['required_if:is_bpjs,1', 'nullable', 'string', 'max:255'],
        'number_bpjs' => ['required_if:is_bpjs,1', 'nullable', 'string', 'max:255'],
    ]);

    if ($validator->fails()) {
        return redirect()->route('worker-all')->with('toast_error', $validator->messages()->all()[0])->withInput();
    }

    $data = $validator->validated();

    try {
        DB::transaction(function () use ($data, $oldData) {
            $update = User::where('id', $oldData->servant_id)->first();

            $update->servantDetails()->update([
                'is_bank' => $data['is_bank'] ?? 0,
                'bank_name' => $data['bank_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'is_bpjs' => $data['is_bpjs'] ?? 0,
                'type_bpjs' => $data['type_bpjs'] ?? null,
                'number_bpjs' => $data['number_bpjs'] ?? null,
            ]);
        });

        Alert::success('Berhasil', 'Akun rekening berhasil diperbarui!');
        return redirect()->route('worker-all');
    } catch (\Throwable $th) {
        $data = [
            'message' => $th->getMessage(),
            'status' => 400
        ];

        return view('cms.error', compact('data'));
    }
}

}
