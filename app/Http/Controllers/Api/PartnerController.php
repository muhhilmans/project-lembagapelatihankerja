<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Application;
use App\Models\Profession;
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

        $searchName    = $request->input('name') ?? $request->input('search');
        $religion      = $request->input('religion');
        $isInval       = $request->input('is_inval');
        $isStay        = $request->input('is_stay');
        $professionIds = $request->input('profession_ids') ?? $request->input('professions');
        $minAge        = $request->input('min_age');
        $maxAge        = $request->input('max_age');
        $minExperience = $request->input('min_experience');
        $maxExperience = $request->input('max_experience');
        $minRating     = $request->input('min_rating');
        $lat           = $request->input('lat');
        $lng           = $request->input('lng');
        $radius        = $request->input('radius'); // km, opsional

        $favoritedIds = $user->favoriteServants()->pluck('servant_detail_id')->toArray();

        $query = User::with(['roles', 'servantDetails.professions', 'appServant', 'servantSkills'])
            ->whereHas('roles', fn($q) => $q->where('name', 'pembantu'))
            ->where('is_active', true)
            ->whereDoesntHave('appServant', function ($q) use ($user) {
                $q->where('employe_id', $user->id)
                    ->whereIn('status', ['interview', 'verify', 'passed', 'choose', 'accepted', 'rejected', 'pending']);
            })
            ->when($searchName, fn($q) => $q->where('name', 'like', "%{$searchName}%"))
            ->whereHas('servantDetails', function ($q) use ($religion, $isInval, $isStay, $minAge, $maxAge, $minExperience, $maxExperience) {
                $q->where('working_status', false);
                $q->when($religion, fn($s) => $s->where('religion', $religion));
                $q->when($isInval !== null, fn($s) => $s->where('is_inval', $isInval));
                $q->when($isStay !== null, fn($s) => $s->where('is_stay', $isStay));
                if ($minAge) $q->whereRaw("TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?", [$minAge]);
                if ($maxAge) $q->whereRaw("TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?", [$maxAge]);
                if ($minExperience !== null) $q->where('experience', '>=', $minExperience);
                if ($maxExperience !== null) $q->where('experience', '<=', $maxExperience);
            })
            ->when($minRating && $minRating > 0, fn($q) => $q->whereIn('id', function ($sub) use ($minRating) {
                $sub->select('reviewee_id')->from('reviews')
                    ->groupBy('reviewee_id')
                    ->havingRaw('AVG(rating) >= ?', [$minRating]);
            }))
            ->when($professionIds, fn($q) => $q->whereHas('servantDetails.professions', function ($sub) use ($professionIds) {
                $ids = is_array($professionIds) ? $professionIds : explode(',', $professionIds);
                $sub->whereIn('professions.id', $ids);
            }));

        // Filter & sort berdasarkan jarak (Haversine)
        $useDistance = $lat && $lng;
        if ($useDistance) {
            $query->join('servant_details AS sd_geo', 'users.id', '=', 'sd_geo.user_id')
                ->select('users.*')
                ->selectRaw(
                    "(6371 * acos(GREATEST(-1, LEAST(1,
                        cos(radians(?)) * cos(radians(sd_geo.latitude))
                        * cos(radians(sd_geo.longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(sd_geo.latitude))
                    )))) AS distance_km",
                    [(float) $lat, (float) $lng, (float) $lat]
                )
                ->whereNotNull('sd_geo.latitude')
                ->whereNotNull('sd_geo.longitude');

            if ($radius) {
                $query->havingRaw("distance_km <= ?", [(float) $radius]);
            }

            $query->orderBy('distance_km');
        } else {
            $query->latest();
        }

        $partners = $query->paginate(10);

        $professionsList = Profession::select('id', 'name')->get();

        $datas = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->roles->first()->name,
                'access_token' => $user->access_token,
            ],
            'professions' => $professionsList,
            'mitra' => [
                'data' => $partners->map(function ($partner) use ($favoritedIds, $useDistance) {
                    return [
                        'id'                => $partner->id,
                        'is_favorited'      => in_array($partner->id, $favoritedIds),
                        'distance_km'       => $useDistance ? round((float) $partner->distance_km, 2) : null,
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
        $partner = User::with(['roles', 'servantDetails.professions', 'servantSkills'])->find($id);

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mitra tidak ditemukan',
            ], 404);
        }

        $favoritedIds = $user->favoriteServants()->pluck('servant_detail_id')->toArray();
        $isFavorited  = in_array($partner->id, $favoritedIds);

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
                'is_favorited'      => $isFavorited,
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
