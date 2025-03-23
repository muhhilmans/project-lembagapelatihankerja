<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\ServantSkill;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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

            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki profil',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
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
            'data'    => [
                'id'                => $data->id,
                'name'              => $data->name,
                'username'          => $data->username,
                'email'             => $data->email,
                'email_verified_at' => $data->email_verified_at,
                'is_active'         => $data->is_active,
                'created_at'        => $data->created_at,
                'updated_at'        => $data->updated_at,
                'servant_details'   => [
                    'user_id'          => $data->servantDetails->user_id ?? null,
                    'gender'           => $data->servantDetails->gender ?? 'not_filled',
                    'place_of_birth'   => $data->servantDetails->place_of_birth ?? '-',
                    'date_of_birth'    => $data->servantDetails->date_of_birth,
                    'religion'         => $data->servantDetails->religion ?? '-',
                    'marital_status'   => $data->servantDetails->marital_status ?? 'not_filled',
                    'children'         => $data->servantDetails->children ?? 0,
                    'last_education'   => $data->servantDetails->last_education ?? 'not_filled',
                    'phone'            => $data->servantDetails->phone ?? '-',
                    'emergency_number' => $data->servantDetails->emergency_number ?? '-',
                    'address'          => $data->servantDetails->address ?? '-',
                    'rt'               => $data->servantDetails->rt,
                    'rw'               => $data->servantDetails->rw,
                    'village'          => $data->servantDetails->village,
                    'district'         => $data->servantDetails->district,
                    'regency'          => $data->servantDetails->regency,
                    'province'         => $data->servantDetails->province,
                    'is_bank'          => $data->servantDetails->is_bank ?? 0,
                    'bank_name'        => $data->servantDetails->bank_name ?? '-',
                    'account_number'   => $data->servantDetails->account_number ?? '-',
                    'is_bpjs'          => $data->servantDetails->is_bpjs ?? 0,
                    'type_bpjs'        => $data->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                    'number_bpjs'      => $data->servantDetails->number_bpjs ?? '-',
                    'photo'            => $data->servantDetails->photo,
                    'identity_card'    => $data->servantDetails->identity_card,
                    'family_card'      => $data->servantDetails->family_card,
                    'working_status'   => $data->servantDetails->working_status ?? 0,
                    'experience'       => $data->servantDetails->experience ?? '-',
                    'description'      => $data->servantDetails->description ?? '-',
                    'created_at'       => $data->servantDetails->created_at,
                    'updated_at'       => $data->servantDetails->updated_at,
                    'is_inval'         => $data->servantDetails->is_inval ?? 0,
                    'is_stay'          => $data->servantDetails->is_stay ?? 0,
                    'profession'       => $data->servantDetails->profession->name ?? null,
                ],
                'servant_skills' => $data->servantSkills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'user_id' => $skill->user_id,
                        'skill' => $skill->skill,
                        'keahlian' => $skill->level
                    ];
                }),
                'roles' => $data->roles->pluck('name')->toArray(),
            ]
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

            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki profil',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function storeSkill(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data pembantu tidak ditemukan!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'skill' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
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

            $store = ServantSkill::create([
                'user_id' => $user->id,
                'skill' => $data['skill'],
                'level' => $data['level'],
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data keahlian berhasil ditambahkan!',
                'data'    => $store
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat menambahkan keahlian.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function updateSkill(Request $request, string $id, string $skill_id)
    {
        $user = User::with('servantSkills')->find($id);

        if (!$user) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pembantu tidak ditemukan!',
            ], 404);
        }

        $skill = $user->servantSkills()->find($skill_id);

        if (!$skill) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data keahlian tidak ditemukan atau tidak dimiliki oleh pengguna ini!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'skill' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
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

            $skill->update([
                'skill' => $data['skill'],
                'level' => $data['level'],
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data keahlian berhasil diperbarui!',
                'data'    => $skill
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbarui keahlian.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function destroySkill(string $id, string $skill_id)
    {
        $user = User::with('servantSkills')->find($id);

        if (!$user) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pembantu tidak ditemukan!',
            ], 404);
        }

        $skill = $user->servantSkills()->find($skill_id);

        if (!$skill) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data keahlian tidak ditemukan atau tidak dimiliki oleh pengguna ini!',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $delete = $skill->delete();

            if (!$delete) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Keahlian gagal dihapus. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data keahlian berhasil dihapus!',
                'data'    => $delete
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat menghapus keahlian.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function changeInval(string $id)
    {
        $user = User::with('servantDetails')->find($id);

        if (!$user) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pengguna tidak ditemukan.'
            ], 404);
        }

        if (!$user->servantDetails) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data servant tidak ditemukan.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $user->servantDetails->is_inval = $user->servantDetails->is_inval == 1 ? 0 : 1;
            $user->servantDetails->save();

            DB::commit();

            return response()->json([
                'success' => 'success',
                'message' => 'Status inval berhasil diperbarui!',
                'data' => [
                    'is_inval' => $user->servantDetails->is_inval,
                    'status' => $user->servantDetails->is_inval == 1 ? 'Bersedia' : 'Tidak Bersedia'
                ]
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat memperbarui status inval.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function changeStay(string $id)
    {
        $user = User::with('servantDetails')->find($id);

        if (!$user) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pengguna tidak ditemukan.'
            ], 404);
        }

        if (!$user->servantDetails) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data servant tidak ditemukan.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $user->servantDetails->is_stay = $user->servantDetails->is_stay == 1 ? 0 : 1;
            $user->servantDetails->save();

            DB::commit();

            return response()->json([
                'success' => 'success',
                'message' => 'Status pulang-pergi berhasil diperbarui!',
                'data' => [
                    'is_stay' => $user->servantDetails->is_stay,
                    'status' => $user->servantDetails->is_stay == 1 ? 'Bersedia' : 'Tidak Bersedia'
                ]
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat memperbarui status pulang-pergi.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }
}
