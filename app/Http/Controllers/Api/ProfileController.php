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
        $data = User::with('employeDetails')->find($id);

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
                    'message' => 'Profile gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data profile majikan diperbarui',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki lowongan',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
