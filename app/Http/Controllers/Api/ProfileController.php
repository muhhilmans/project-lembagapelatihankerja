<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profileMajikan($id)
    {
        $data = User::with(['employeDetails', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->find($id);

        if (!$data) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data majikan tidak ditemukan!',
            ], 404);
        }

        return response()->json([
            'success'   => 'success',
            'message'   => 'Data profile majikan',
            'data'      => $data
        ]);
    }

    public function updateMajikan(Request $request, $id)
    {
        $user = User::with('employeDetails')->find($id);

        if (!$user) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data majikan tidak ditemukan!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id),],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id),],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'identity_card' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

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

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Profil gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data profil majikan diperbarui',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki profil',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function profilePembantu($id)
    {
        $data = User::with(['servantDetails.profession', 'servantSkills', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })->find($id);

        if (!$data) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data pembantu tidak ditemukan!',
            ], 404);
        }

        return response()->json([
            'success'   => 'success',
            'message'   => 'Data profile pembantu',
            'data'      => $data->makeHidden(['access_token'])
        ]);
    }

    public function updatePembantu(Request $request, $id)
    {
        $user = User::with('servantDetails')->find($id);

        if (!$user) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data pembantu tidak ditemukan!',
            ], 404);
        }

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
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $data['is_bank'] = $request->has('is_bank') ? $request->boolean('is_bank') : false;
        $data['is_bpjs'] = $request->has('is_bpjs') ? $request->boolean('is_bpjs') : false;

        try {
            DB::beginTransaction();

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

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Profil gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data profil pembantu diperbarui',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki profil',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
