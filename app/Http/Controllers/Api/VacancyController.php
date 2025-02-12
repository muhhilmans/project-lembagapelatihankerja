<?php

namespace App\Http\Controllers\Api;

use App\Models\Profession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;

class VacancyController extends Controller
{
    public function index()
    {
        $professions = Profession::all();
        $vacancies = Vacancy::all();

        $datas = [
            'professions' => $professions,
            'vacancies' => $vacancies
        ];

        return new VacancyResource(200, 'Data semua lowongan', $datas);
    }
}
