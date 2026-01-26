<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Application;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    use ApiResponse;

    public function allPartner(Request $request)
    {
        $user = auth()->user();
        // 1. Ambil semua parameter filter titah Yang Mulia
        $searchName  = $request->input('name') ?? $request->input('search');
        $religion    = $request->input('religion');
        $isInval     = $request->input('is_inval');
        $isStay      = $request->input('is_stay');
        $professions = $request->input('professions');

        $favoritedIds = $user->favoriteServants()->pluck('servant_detail_id')->toArray();

        $partners = User::with(['roles', 'servantDetails.professions', 'appServant']) 
            // Filter Role Pembantu & Aktif
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })
            ->where('is_active', true)

            // Filter: TIDAK sedang dalam proses lamaran dengan user ini
            ->whereDoesntHave('appServant', function ($query) use ($user) {
                $query->where('employe_id', $user->id)
                    ->whereIn('status', ['interview', 'verify', 'passed', 'choose', 'accepted', 'rejected', 'pending']);
            })

            // Filter Nama (Search)
            ->when($searchName, function ($q) use ($searchName) {
                $q->where('name', 'like', "%{$searchName}%");
            })

            // Filter Detail Servant (Agama, Inval, Stay)
            ->whereHas('servantDetails', function ($query) use ($religion, $isInval, $isStay) {
                $query->where('working_status', false);

                $query->when($religion, function ($sub) use ($religion) {
                    $sub->where('religion', $religion);
                });

                $query->when($isInval, function ($sub) {
                    $sub->where('is_inval', 1);
                });

                $query->when($isStay, function ($sub) {
                    $sub->where('is_stay', 1);
                });
            })

            // Filter Professions (Many-to-Many via ServantDetail)
            ->when($professions, function ($query) use ($professions) {
                $query->whereHas('servantDetails.professions', function ($subQuery) use ($professions) {
                    $ids = is_array($professions) ? $professions : explode(',', $professions);
                    
                    $subQuery->whereIn('professions.id', $ids);
                    
                    // $subQuery->whereIn('professions.name', $ids);
                });
            })
            ->latest()
            ->paginate(10);

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
                'data' => $partners->map(function ($partner) use ($favoritedIds) {
                    return [
                        'id'                => $partner->id,
                        'is_favorited'      => in_array($partner->id, $favoritedIds),
                        'name'              => $partner->name,
                        'username'          => $partner->username,
                        'email'             => $partner->email,
                        'rating'            => $partner->average_rating,
                        'reviews_count'     => $partner->review_count,
                        'email_verified_at' => $partner->email_verified_at,
                        'is_active'         => $partner->is_active,
                        'created_at'        => $partner->created_at,
                        'updated_at'        => $partner->updated_at,
                        'servant_details'   => [
                            'id'               => $partner->servantDetails->id,
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
                            'professions'       => $partner->servantDetails->professions?->map(function ($p) {
                                return [
                                    'id' => $p->id,
                                    'name' => $p->name,
                                    'file_draft' => $p->file_draft
                                ];
                            }),
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
                    'current_page_url' => $partners->url($partners->currentPage()),
                    'next_page_url' => $partners->nextPageUrl(),
                    'prev_page_url' => $partners->previousPageUrl(),
                ],
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Data semua mitra.',
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
                    'professions'       => $partner->servantDetails->professions?->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'file_draft' => $p->file_draft
                        ];
                    }),
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
            'message' => 'Data detail mitra.',
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

            try {
                $employer = User::find($data['employe_id']);
                $employerName = $employer ? $employer->name : 'Seseorang';

                \App\Events\NotificationDispatched::dispatch(
                    "Tawaran Pekerjaan: {$employerName} ingin merekrut Anda secara langsung.", // Pesan
                    $partner->id,
                    'info'
                );
            } catch (\Exception $e) {
                Log::error("Gagal kirim notif hirePartner: " . $e->getMessage());
            }

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

    public function toggleFavoriteServant(User $servant)
    {
        $majikan = auth()->user();

        if ($majikan->id === $servant->id) {
            return response()->json(['message' => 'Tidak valid'], 400);
        }

        $changes = $majikan->favoriteServants()->toggle($servant->id);

        $message = count($changes['attached']) > 0
            ? 'Berhasil ditambahkan ke favorit'
            : 'Dihapus dari favorit';

        return response()->json([
            'message' => $message,
            'is_favorited' => count($changes['attached']) > 0
        ]);
    }

    public function myFavoriteServants()
    {
        try {
            $user = auth()->user();

            $favorites = $user->favoriteServants()
                ->latest('favorite_servants.created_at')
                ->get();

            return $this->successResponse($favorites, 'Daftar mitra favorit Anda');
        } catch (\Throwable $th) {
            return $this->errorResponse('Gagal mengambil data favorit', $th->getMessage());
        }
    }
}
