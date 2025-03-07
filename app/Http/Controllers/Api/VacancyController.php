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
use Illuminate\Support\Facades\Validator;

class VacancyController extends Controller
{
    public function index()
    {
        $professions = Profession::all();
        $user = auth()->user();
        $vacancies = Vacancy::where('user_id', $user->id)->get();

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

        if ($detail->user_id != $user->id) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized access. You do not have permission to view this vacancy.'
            ], 401);
        }

        $recoms = RecomServant::where('vacancy_id', $id)
            ->whereHas('servant.servantDetails', function ($query) {
                $query->where('working_status', false);
            })->get();
        $applications = Application::where('vacancy_id', $id)->where('status', '!=', 'accepted')->get();

        $datas = [
            'detail' => $detail,
            'recoms' => $recoms,
            'applications' => $applications,
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
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat menambahkan lowongan',
                'error'   => $th->getMessage()
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
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat memperbaiki lowongan',
                'error'   => $th->getMessage()
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
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat menghapus lowongan',
                'error'   => $th->getMessage()
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
                    'next_page_url' => $vacancies->nextPageUrl(),
                    'prev_page_url' => $vacancies->previousPageUrl(),
                ]
            ]
        ];

        return new VacancyResource('success', 'Data semua lowongan', $datas);
    }

    public function showVacancy($id)
    {
        $detail = Vacancy::with('profession')->find($id);

        if (!$detail) {
            return response()->json([
                'success'   => 'failed',
                'message'   => 'Data lowongan tidak ditemukan!',
            ], 404);
        }

        return new VacancyResource('success', 'Data detail lowongan', $detail);
    }
}
