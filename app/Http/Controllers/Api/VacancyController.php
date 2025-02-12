<?php

namespace App\Http\Controllers\Api;

use App\Models\Vacancy;
use App\Models\Profession;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyResource;

class VacancyController extends Controller
{
    public function index()
    {
        $professions = Profession::all();
        $user = auth()->user();
        $vacancies = Vacancy::where('user_id', $user->id)->get();

        if ($vacancies->count < 1) {
            return response()->json([
                'success'   => 'success',
                'message'   => 'Belum ada lowongan!',
            ], 200);
        }

        $datas = [
            'professions' => $professions,
            'vacancies' => $vacancies
        ];

        return new VacancyResource(200, 'Data semua lowongan', $datas);
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

        return new VacancyResource(200, 'Detail lowongan', $datas);
    }
}
