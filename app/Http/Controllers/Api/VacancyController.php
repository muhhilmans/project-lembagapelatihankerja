<?php

namespace App\Http\Controllers\Api;

use App\Models\Vacancy;
use App\Models\Profession;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VacancyController extends Controller
{
    public function index()
    {
        $professions = Profession::all();
        $user = auth()->user();
        $vacancies = Vacancy::where('user_id', $user->id)->paginate(10);

        if ($vacancies->isEmpty()) {
            return response()->json([
                'success'   => 'success',
                'message'   => 'Belum ada lowongan!',
            ], 200);
        }

        $datas = [
            'professions' => $professions->map(function ($profession) {
                return [
                    'id' => $profession->id,
                    'name' => $profession->name,
                ];
            }),
            'vacancies' => [
                'data' => $vacancies->map(function ($vacancy) {
                    return [
                        'id' => $vacancy->id,
                        'title' => $vacancy->title,
                        'description' => $vacancy->description,
                        'requirements' => $vacancy->requirements,
                        'benefits' => $vacancy->benefits,
                        'closing_date' => $vacancy->closing_date,
                        'limit' => $vacancy->limit,
                        'status' => $vacancy->status,
                        'user' => $vacancy->user->name,
                        'profession' => $vacancy->profession->name,
                    ];
                }),
                'pagination' => [
                    'current_page' => $vacancies->currentPage(),
                    'per_page' => $vacancies->perPage(),
                    'total' => $vacancies->total(),
                    'last_page' => $vacancies->lastPage(),
                    'current_page_url' => $vacancies->url($vacancies->currentPage()),
                    'next_page_url' => $vacancies->nextPageUrl(),
                    'prev_page_url' => $vacancies->previousPageUrl(),
                ]
            ]
        ];

        return new VacancyResource('success', 'Data semua lowongan', $datas);
    }

    public function show(string $id)
    {
        $user = auth()->user();
        $detail = Vacancy::find($id);

        if (!$detail) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data lowongan tidak ditemukan!',
            ], 404);
        }

        if ($detail->user_id !== $user->id) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized access. You do not have permission to view this vacancy.'
            ], 401);
        }

        $applications = Application::with('servant')->where('vacancy_id', $id)->where('status', '!=', 'accepted')->get();
        $appliedServantIds = $applications->pluck('servant_id')->toArray();
        $recoms = RecomServant::with('servant.servantDetails')->where('vacancy_id', $id)
            ->whereHas('servant.servantDetails', function ($query) {
                $query->where('working_status', false);
            })
            ->whereNotIn('servant_id', $appliedServantIds)
            ->get();

        $datas = [
            'detail' => [
                'id' => $detail->id,
                'title' => $detail->title,
                'description' => $detail->description,
                'requirements' => $detail->requirements,
                'benefits' => $detail->benefits,
                'closing_date' => $detail->closing_date,
                'limit' => $detail->limit,
                'status' => $detail->status,
                'client' => $detail->user->name,
                'profession' => $detail->profession->name,
            ],
            'pelamar' => $applications->map(function ($application) {
                return [
                    'id' => $application->id,
                    'status' => $application->status,
                    'servant' => [
                        'id' => $application->servant->id,
                        'name' => $application->servant->name,
                        'email' => $application->servant->email,
                        'detail' => $application->servant->servantDetails->where('user_id', $application->servant->id)->first()->makeHidden(['id', 'servant_id', 'created_at', 'updated_at']),
                    ],
                    'salary' => $application->salary,
                    'link_interview' => $application->link_interview,
                    'interview_date' => $application->interview_date,
                    'notes_interview' => $application->notes_interview,
                    'notes_verify' => $application->notes_verify,
                    'notes_accepted' => $application->notes_accepted,
                    'notes_rejected' => $application->notes_rejected,
                    'work_start_date' => $application->work_start_date,
                    'work_end_date' => $application->work_end_date,
                    'file_contract' => $application->file_contract,
                ];
            }),
            'rekomendasi' => $recoms->map(function ($recom) {
                return [
                    'id' => $recom->id,
                    'vacancy_id' => $recom->vacancy_id,
                    'servant' => [
                        'id' => $recom->servant->id,
                        'name' => $recom->servant->name,
                        'email' => $recom->servant->email,
                        'detail' => $recom->servant->servantDetails->where('user_id', $recom->servant->id)->first()->makeHidden(['id', 'servant_id', 'created_at', 'updated_at']),
                    ],
                ];
            }),
        ];

        return new VacancyResource('success', 'Detail lowongan', $datas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'profession_id' => ['required', 'exists:professions,id'],
            'closing_date' => ['required', 'date'],
            'limit' => ['required', 'integer'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $user = auth()->user();

        try {
            DB::beginTransaction();

            $store = Vacancy::create([
                'title'         => $data['title'],
                'profession_id' => $data['profession_id'],
                'closing_date'  => $data['closing_date'],
                'user_id'       => $user->id,
                'limit'         => $data['limit'],
                'description'   => $data['description'],
                'requirements'  => $data['requirements'],
                'benefits'      => $data['benefits'] ?? null,
                'status'        => true
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Lowongan gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return new VacancyResource('success', 'Lowongan berhasil ditambahkan', $store);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat menambahkan lowongan',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'profession_id' => ['required', 'exists:professions,id'],
            'closing_date' => ['required', 'date'],
            'limit' => ['required', 'integer'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $update = Vacancy::find($id);

        if (!$update) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data lowongan tidak ditemukan!',
            ], 404);
        }

        $data = $validator->validated();
        $user = auth()->user();

        try {
            DB::beginTransaction();

            if ($update->limit <= $data['limit']) {
                $status = true;
            } else {
                $status = false;
            }

            $update->update([
                'title' => $data['title'],
                'closing_date' => $data['closing_date'],
                'profession_id' => $data['profession_id'],
                'user_id' => $user->id,
                'limit' => $data['limit'],
                'description' => $data['description'],
                'requirements' => $data['requirements'],
                'benefits' => $data['benefits'],
                'status' => $status
            ]);

            if (!$update) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Lowongan gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return new VacancyResource('success', 'Lowongan berhasil diperbarui', $update);
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

    public function destroy($id)
    {
        $data = Vacancy::find($id);

        if (!$data) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data lowongan tidak ditemukan!',
            ], 404);
        }

        if ($data->applications->count() > 0) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Lowongan masih digunakan oleh pelamar'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $delete = $data->delete();

            if (!$delete) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Lowongan gagal dihapus. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return new VacancyResource('success', 'Lowongan berhasil dihapus!', true);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat menghapus lowongan',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function seekVacancy()
    {
        $vacancies = Vacancy::with('user')
            ->where('closing_date', '>=', now())
            ->where('status', true)
            ->paginate(5);

        $professions = Profession::all();

        if ($vacancies->isEmpty()) {
            return response()->json([
                'success'   => 'success',
                'message'   => 'Tidak ada lowongan!',
            ], 200);
        }

        $datas = [
            'professions' => $professions->map(function ($profession) {
                return [
                    'id' => $profession->id,
                    'name' => $profession->name,
                ];
            }),
            'vacancies' => [
                'data' => $vacancies->map(function ($vacancy) {
                    return [
                        'id' => $vacancy->id,
                        'title' => $vacancy->title,
                        'description' => $vacancy->description,
                        'requirements' => $vacancy->requirements,
                        'benefits' => $vacancy->benefits,
                        'closing_date' => $vacancy->closing_date,
                        'limit' => $vacancy->limit,
                        'status' => $vacancy->status,
                        'user' => $vacancy->user->name,
                        'profession' => $vacancy->profession->name,
                    ];
                }),
                'pagination' => [
                    'current_page' => $vacancies->currentPage(),
                    'per_page' => $vacancies->perPage(),
                    'total' => $vacancies->total(),
                    'last_page' => $vacancies->lastPage(),
                    'current_page_url' => $vacancies->url($vacancies->currentPage()),
                    'next_page_url' => $vacancies->nextPageUrl(),
                    'prev_page_url' => $vacancies->previousPageUrl(),
                ]
            ]
        ];

        return new VacancyResource('success', 'Data semua lowongan', $datas);
    }

    public function showVacancy($id)
    {
        $detail = Vacancy::with(['profession', 'user'])->find($id);

        if (!$detail) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data lowongan tidak ditemukan!',
            ], 404);
        }

        $application = Application::where('vacancy_id', $id)
            ->where('servant_id', auth()->id())
            ->first();

        $statusLamaran = $application
            ? ['status' => 'Sudah melamar', 'applied_at' => $application->created_at]
            : ['status' => 'Belum melamar', 'applied_at' => null];

        $data = [
            'detail' => [
                'id' => $detail->id,
                'title' => $detail->title,
                'description' => $detail->description,
                'requirements' => $detail->requirements,
                'benefits' => $detail->benefits,
                'closing_date' => $detail->closing_date,
                'limit' => $detail->limit,
                'status' => $detail->status,
                'client' => $detail->user->name,
                'profession' => $detail->profession->name,
            ],
            'status_lamaran' => $statusLamaran
        ];

        return new VacancyResource('success', 'Data detail lowongan', $data);
    }
}
