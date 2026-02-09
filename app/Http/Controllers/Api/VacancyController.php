<?php

namespace App\Http\Controllers\Api;

use App\Models\Vacancy;
use App\Models\Profession;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class VacancyController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();

        $query = Vacancy::with(['professions:id,name', 'user:id,name'])
            ->where('user_id', $user->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        $vacancies = $query->latest()->paginate(10);

        if ($vacancies->isEmpty()) {
            return $this->successResponse([], 'Belum ada lowongan!');
        }

        $professions = Profession::select('id', 'name')->get();

        $datas = [
            'professions' => $professions,
            'vacancies' => [
                'data' => $vacancies->map(fn($v) => $this->formatVacancy($v)),
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

        return $this->successResponse($datas, 'Data semua lowongan');
    }

    public function show(string $id)
    {
        $user = auth()->user();

        $detail = Vacancy::with(['professions:id,name', 'user:id,name'])->find($id);

        if (!$detail) {
            return $this->errorResponse('Data lowongan tidak ditemukan!', [], 404);
        }

        if ($detail->user_id !== $user->id) {
            return $this->errorResponse('Anda tidak memiliki izin melihat lowongan ini.', [], 403);
        }

        $applications = Application::with(['servant.servantDetails'])
            ->where('vacancy_id', $id)
            ->where('status', '!=', 'accepted')
            ->get();

        $appliedServantIds = $applications->pluck('servant_id')->toArray();

        $recoms = RecomServant::with(['servant.servantDetails'])
            ->where('vacancy_id', $id)
            ->whereHas('servant.servantDetails', function ($query) {
                $query->where('working_status', false);
            })
            ->whereNotIn('servant_id', $appliedServantIds)
            ->get();

        $datas = [
            'detail' => $this->formatVacancy($detail),
            'pelamar' => $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'status' => $app->status,
                    'servant' => $this->formatUserDetail($app->servant),
                    'salary' => $app->salary,
                    'link_interview' => $app->link_interview,
                    'interview_date' => $app->interview_date,
                    'notes_interview' => $app->notes_interview,
                    'notes_verify' => $app->notes_verify,
                    'notes_accepted' => $app->notes_accepted,
                    'notes_rejected' => $app->notes_rejected,
                    'work_start_date' => $app->work_start_date,
                    'work_end_date' => $app->work_end_date,
                    'file_contract' => $app->file_contract,
                ];
            }),
            'rekomendasi' => $recoms->map(function ($recom) {
                return [
                    'id' => $recom->id,
                    'vacancy_id' => $recom->vacancy_id,
                    'servant' => $this->formatUserDetail($recom->servant),
                ];
            }),
        ];

        return $this->successResponse($datas, 'Detail lowongan');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'profession_ids' => ['required', 'array'],
            'profession_ids.*' => ['exists:professions,id'],
            'closing_date' => ['required', 'date'],
            'limit' => ['required', 'integer'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($data, $request) {
            try {
                $vacancy = Vacancy::create([
                    'title' => $data['title'],
                    'closing_date' => $data['closing_date'],
                    'user_id' => auth()->id(),
                    'limit' => $data['limit'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'benefits' => $data['benefits'] ?? null,
                    'status' => true
                ]);

                $vacancy->professions()->sync($data['profession_ids']);
                
                $vacancy->load('professions');

                return $this->successResponse($this->formatVacancy($vacancy), 'Lowongan berhasil ditambahkan', 201);

            } catch (\Throwable $th) {
                Log::error("Store Vacancy Error: " . $th->getMessage());
                return $this->errorResponse('Terjadi kesalahan saat menambahkan lowongan', [], 500);
            }
        });
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'profession_ids' => ['required', 'array'],
            'profession_ids.*' => ['exists:professions,id'],
            'closing_date' => ['required', 'date'],
            'limit' => ['required', 'integer'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $vacancy = Vacancy::find($id);

        if (!$vacancy) {
            return $this->errorResponse('Data lowongan tidak ditemukan!', [], 404);
        }

        if ($vacancy->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', [], 403);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($vacancy, $data, $request) {
            try {
                $status = ($vacancy->limit <= $data['limit']);

                $vacancy->update([
                    'title' => $data['title'],
                    'closing_date' => $data['closing_date'],
                    'limit' => $data['limit'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'benefits' => $data['benefits'] ?? $vacancy->benefits,
                    'status' => $status
                ]);

                $vacancy->professions()->sync($data['profession_ids']);

                $vacancy->load('professions');

                return $this->successResponse($this->formatVacancy($vacancy), 'Lowongan berhasil diperbarui');

            } catch (\Throwable $th) {
                Log::error("Update Vacancy Error: " . $th->getMessage());
                return $this->errorResponse('Terjadi kesalahan saat memperbaiki lowongan', [], 500);
            }
        });
    }

    public function destroy($id)
    {
        $vacancy = Vacancy::find($id);

        if (!$vacancy) {
            return $this->errorResponse('Data lowongan tidak ditemukan!', [], 404);
        }

        if ($vacancy->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', [], 403);
        }

        if ($vacancy->applications()->exists()) {
            return $this->errorResponse('Lowongan masih digunakan oleh pelamar', [], 422);
        }

        try {
            $vacancy->professions()->detach();
            $vacancy->delete();
            return $this->successResponse(true, 'Lowongan berhasil dihapus!');
        } catch (\Throwable $th) {
            Log::error("Delete Vacancy Error: " . $th->getMessage());
            return $this->errorResponse('Terjadi kesalahan saat menghapus lowongan', [], 500);
        }
    }

    public function seekVacancy()
    {
        $vacancies = Vacancy::with(['user:id,name', 'professions:id,name'])
            ->where('status', true)
            ->paginate(5);

        if ($vacancies->isEmpty()) {
            return $this->successResponse([], 'Tidak ada lowongan!');
        }

        $professions = Profession::select('id', 'name')->get();

        $datas = [
            'professions' => $professions,
            'vacancies' => [
                'data' => $vacancies->map(fn($v) => $this->formatVacancy($v)),
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

        return $this->successResponse($datas, 'Data semua lowongan');
    }

    public function showVacancy($id)
    {
        $detail = Vacancy::with(['professions:id,name', 'user:id,name'])->find($id);

        if (!$detail) {
            return $this->errorResponse('Data lowongan tidak ditemukan!', [], 404);
        }

        $application = Application::where('vacancy_id', $id)
            ->where('servant_id', auth()->id())
            ->first();

        $statusLamaran = $application
            ? ['status' => 'Sudah melamar', 'applied_at' => $application->created_at]
            : ['status' => 'Belum melamar', 'applied_at' => null];

        $data = [
            'detail' => $this->formatVacancy($detail),
            'status_lamaran' => $statusLamaran
        ];

        return $this->successResponse($data, 'Data detail lowongan');
    }

    private function formatVacancy($vacancy)
    {
        return [
            'id' => $vacancy->id,
            'title' => $vacancy->title,
            'description' => $vacancy->description,
            'requirements' => $vacancy->requirements,
            'benefits' => $vacancy->benefits,
            'closing_date' => $vacancy->closing_date,
            'limit' => $vacancy->limit,
            'status' => $vacancy->status,
            'user' => $vacancy->user->name ?? 'Unknown',
            'is_favorited' => $vacancy->is_favorited,
            'professions' => $vacancy->professions?->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                ];
            }) ?? [],
            'profession' => $vacancy->professions->first()->name ?? 'Umum',
        ];
    }

    private function formatUserDetail($userModel)
    {
        $detail = $userModel->servantDetails->where('user_id', $userModel->id)->first();

        return [
            'id' => $userModel->id,
            'name' => $userModel->name,
            'email' => $userModel->email,
            'detail' => $detail ? $detail->makeHidden(['id', 'servant_id', 'created_at', 'updated_at']) : null,
        ];
    }

    public function toggleFavorite(Vacancy $vacancy)
    {
        $user = auth()->user();

        if (!$vacancy) {
            return response()->json(['message' => 'Lowongan tidak ditemukan'], 404);
        }

        $changes = $user->favoriteVacancies()->toggle($vacancy->id);

        $message = count($changes['attached']) > 0 ? 'Berhasil ditambahkan ke favorit' : 'Dihapus dari favorit';

        return response()->json([
            'message' => $message,
            'is_favorited' => count($changes['attached']) > 0
        ]);
    }

    public function myFavorites()
    {
        try {
            $favorites = auth()->user()->favoriteVacancies()->latest()->get();
            return $this->successResponse($favorites, 'Berhasil mengambil data favorit');
        } catch (\Throwable $th) {
            return $this->errorResponse('Terjadi kesalahan saat mengambil data favorit', [], 500);
        }
    }
}