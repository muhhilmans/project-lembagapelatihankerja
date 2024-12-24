<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\ApplyJob;
use App\Models\Profession;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class UtilityController extends Controller
{
    public function displayImage($path, $imageName)
    {
        $path = storage_path('app/public/images/' . $path . '/' . $imageName);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $respose = Response::make($file, 200);
        $respose->header('Content-Type', $type);

        return $respose;
    }

    public function allServant()
    {
        $datas = User::with(['roles', 'servantDetails'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })->where('is_active', true)->whereHas('servantDetails', function ($query) {
                $query->where('working_status', false);
            })->get();

        $professions = Profession::all();

        return view('cms.servant.index', compact(['datas', 'professions']));
    }

    public function showServant(string $id)
    {
        $data = User::findOrFail($id);

        if (auth()->user()->roles->first()->name == 'majikan') {
            return view('cms.servant.partial.detail', compact('data'));
        } else {
            $employes = User::whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->get();
            return view('cms.servant.partial.detail', compact(['data', 'employes']));
        }
    }

    public function allVacancy()
    {
        $datas = Vacancy::where('closing_date', '>=', now())->get();

        return view('cms.seek-vacancy.index', compact('datas'));
    }

    public function showVacancy(string $id)
    {
        $data = Vacancy::findOrFail($id);
        $dataApplicants = Application::with('servant')->where('vacancy_id', $id)->get();

        return view('cms.seek-vacancy.partial.detail', compact(['data', 'dataApplicants']));
    }

    public function hireApplicant()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::where('employe_id', auth()->user()->id)
            ->whereNotNull('employe_id')
            ->get();
        } else {
            $datas = Application::whereNotNull('employe_id')->get();
        }

        return view('cms.applicant.hire', compact('datas'));
    }

    public function indieApplicant()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::whereHas('vacancy', function ($query) {
                $query->where('employe_id', auth()->user()->id);
            })
            ->whereNotNull('vacancy_id')
            ->get();
        } else {
            $datas = Application::whereNotNull('vacancy_id')->get();
        }

        return view('cms.applicant.independent', compact('datas'));
    }
}
