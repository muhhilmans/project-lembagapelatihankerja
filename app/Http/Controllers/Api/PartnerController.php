<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function allPartner()
    {
        $user = auth()->user();
        $partners = User::with(['roles', 'servantDetails', 'appServant'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })->where('is_active', true)->whereHas('servantDetails', function ($query) {
                $query->where('working_status', false);
            })->whereDoesntHave('appServant', function ($query) use ($user) {
                $query->where('employe_id', $user->id)
                    ->whereIn('status', ['interview', 'verify', 'passed', 'choose', 'accepted', 'rejected', 'pending']);
            })->paginate(10);

        $datas = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->roles->first()->name,
                'access_token' => $user->access_token,
            ],
            'mitra' => [
                'data' => $partners->map(function ($partner) {
                    return [
                        'id'                => $partner->id,
                        'name'              => $partner->name,
                        'username'          => $partner->username,
                        'email'             => $partner->email,
                        'email_verified_at' => $partner->email_verified_at,
                        'is_active'         => $partner->is_active,
                        'created_at'        => $partner->created_at,
                        'updated_at'        => $partner->updated_at,
                        'servant_details'   => [
                            'user_id'          => $partner->servantDetails->user_id ?? null,
                            'gender'           => $partner->servantDetails->gender ?? 'not_filled',
                            'place_of_birth'   => $partner->servantDetails->place_of_birth ?? '-',
                            'date_of_birth'    => $partner->servantDetails->date_of_birth,
                            'religion'         => $partner->servantDetails->religion ?? '-',
                            'marital_status'   => $partner->servantDetails->marital_status ?? 'not_filled',
                            'children'         => $partner->servantDetails->children ?? 0,
                            'last_education'   => $partner->servantDetails->last_education ?? 'not_filled',
                            'phone'            => $partner->servantDetails->phone ?? '-',
                            'emergency_number' => $partner->servantDetails->emergency_number ?? '-',
                            'address'          => $partner->servantDetails->address ?? '-',
                            'rt'               => $partner->servantDetails->rt,
                            'rw'               => $partner->servantDetails->rw,
                            'village'          => $partner->servantDetails->village,
                            'district'         => $partner->servantDetails->district,
                            'regency'          => $partner->servantDetails->regency,
                            'province'         => $partner->servantDetails->province,
                            'is_bank'          => $partner->servantDetails->is_bank ?? 0,
                            'bank_name'        => $partner->servantDetails->bank_name ?? '-',
                            'account_number'   => $partner->servantDetails->account_number ?? '-',
                            'is_bpjs'          => $partner->servantDetails->is_bpjs ?? 0,
                            'type_bpjs'        => $partner->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                            'number_bpjs'      => $partner->servantDetails->number_bpjs ?? '-',
                            'photo'            => $partner->servantDetails->photo,
                            'identity_card'    => $partner->servantDetails->identity_card,
                            'family_card'      => $partner->servantDetails->family_card,
                            'working_status'   => $partner->servantDetails->working_status ?? 0,
                            'experience'       => $partner->servantDetails->experience ?? '-',
                            'description'      => $partner->servantDetails->description ?? '-',
                            'created_at'       => $partner->servantDetails->created_at,
                            'updated_at'       => $partner->servantDetails->updated_at,
                            'is_inval'         => $partner->servantDetails->is_inval ?? 0,
                            'is_stay'          => $partner->servantDetails->is_stay ?? 0,
                            'profession'       => $partner->servantDetails->profession->name ?? null,
                        ],
                        'servant_skills' => $partner->servantSkills->map(function ($skill) {
                            return [
                                'id' => $skill->id,
                                'user_id' => $skill->user_id,
                                'skill' => $skill->skill,
                                'keahlian' => $skill->level
                            ];
                        }),
                    ];
                }),
                'pagination' => [
                    'current_page' => $partners->currentPage(),
                    'per_page' => $partners->perPage(),
                    'total' => $partners->total(),
                    'last_page' => $partners->lastPage(),
                    'next_page_url' => $partners->nextPageUrl(),
                    'prev_page_url' => $partners->previousPageUrl(),
                ],
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Data semua mitra',
            'datas' => $datas
        ]);
    }

    public function showPartner($id)
    {
        $user = auth()->user();
        $partner = User::with(['roles', 'servantDetails'])->find($id);

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mitra tidak ditemukan',
            ], 404);
        }

        $datas = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->roles->first()->name,
                'access_token' => $user->access_token,
            ],
            'partner' => [
                'id'                => $partner->id,
                'name'              => $partner->name,
                'username'          => $partner->username,
                'email'             => $partner->email,
                'email_verified_at' => $partner->email_verified_at,
                'is_active'         => $partner->is_active,
                'created_at'        => $partner->created_at,
                'updated_at'        => $partner->updated_at,
                'servant_details'   => [
                    'user_id'          => $partner->servantDetails->user_id ?? null,
                    'gender'           => $partner->servantDetails->gender ?? 'not_filled',
                    'place_of_birth'   => $partner->servantDetails->place_of_birth ?? '-',
                    'date_of_birth'    => $partner->servantDetails->date_of_birth,
                    'religion'         => $partner->servantDetails->religion ?? '-',
                    'marital_status'   => $partner->servantDetails->marital_status ?? 'not_filled',
                    'children'         => $partner->servantDetails->children ?? 0,
                    'last_education'   => $partner->servantDetails->last_education ?? 'not_filled',
                    'phone'            => $partner->servantDetails->phone ?? '-',
                    'emergency_number' => $partner->servantDetails->emergency_number ?? '-',
                    'address'          => $partner->servantDetails->address ?? '-',
                    'rt'               => $partner->servantDetails->rt,
                    'rw'               => $partner->servantDetails->rw,
                    'village'          => $partner->servantDetails->village,
                    'district'         => $partner->servantDetails->district,
                    'regency'          => $partner->servantDetails->regency,
                    'province'         => $partner->servantDetails->province,
                    'is_bank'          => $partner->servantDetails->is_bank ?? 0,
                    'bank_name'        => $partner->servantDetails->bank_name ?? '-',
                    'account_number'   => $partner->servantDetails->account_number ?? '-',
                    'is_bpjs'          => $partner->servantDetails->is_bpjs ?? 0,
                    'type_bpjs'        => $partner->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                    'number_bpjs'      => $partner->servantDetails->number_bpjs ?? '-',
                    'photo'            => $partner->servantDetails->photo,
                    'identity_card'    => $partner->servantDetails->identity_card,
                    'family_card'      => $partner->servantDetails->family_card,
                    'working_status'   => $partner->servantDetails->working_status ?? 0,
                    'experience'       => $partner->servantDetails->experience ?? '-',
                    'description'      => $partner->servantDetails->description ?? '-',
                    'created_at'       => $partner->servantDetails->created_at,
                    'updated_at'       => $partner->servantDetails->updated_at,
                    'is_inval'         => $partner->servantDetails->is_inval ?? 0,
                    'is_stay'          => $partner->servantDetails->is_stay ?? 0,
                    'profession'       => $partner->servantDetails->profession->name ?? null,
                ],
                'servant_skills' => $partner->servantSkills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'user_id' => $skill->user_id,
                        'skill' => $skill->skill,
                        'keahlian' => $skill->level
                    ];
                }),
            ]
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Data detail mitra',
            'datas' => $datas
        ]);
    }

    public function hirePartner(Request $request, $id)
    {
        $partner = User::find($id);

        if (!$partner) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data partner tidak ditemukan!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'employe_id' => ['required', 'exists:users,id'],
        ]);

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $store = Application::create([
                'servant_id' => $partner->id,
                'employe_id' => $data['employe_id'],
                'status' => 'pending',
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Hire mitra gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Hire mitra berhasil disimpan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki lowongan',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }
}
