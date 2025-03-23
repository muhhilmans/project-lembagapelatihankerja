<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\ApplyJob;
use App\Models\Profession;
use App\Models\Application;
use App\Models\RecomServant;
use App\Models\Salary;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller
{
    public function displayImage($path, $imageName)
    {
        $path = storage_path('app/public/img/' . $path . '/' . $imageName);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header('Content-Type', $type);

        return $response;
    }

    public function displayFile($path, $fileName)
    {
        $path = storage_path('app/public/' . $path . '/' . $fileName);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $extension = File::extension($path);

        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => File::mimeType($path),
        };

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }

    public function pdfProfession(string $id)
    {
        $profession = Profession::findOrFail($id);

        if (!$profession->file_draft) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        $filePath = storage_path('app/public/professions/' . $profession->file_draft);

        if (!file_exists($filePath)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        try {
            chmod($filePath, 0644);

            $pdfContent = file_get_contents($filePath);

            if ($pdfContent === false) {
                throw new \Exception('Gagal membaca isi file PDF.');
            }

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses file PDF.',
                'message' => $e->getMessage()
            ], 500);
        }
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
            })->where('is_active', true)->get();
            return view('cms.servant.partial.detail', compact(['data', 'employes']));
        }
    }

    public function allVacancy()
    {
        $datas = Vacancy::where('closing_date', '>=', now())->where('status', true)->get();

        $professions = Profession::all();

        return view('cms.seek-vacancy.index', compact(['datas', 'professions']));
    }

    public function showVacancy(string $id)
    {
        $data = Vacancy::findOrFail($id);

        if (auth()->user()->roles->first()->name == 'majikan') {
            return view('cms.seek-vacancy.partial.detail', compact(['data']));
        } else {
            $servants = User::whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })
                ->where('is_active', true)
                ->whereHas('servantDetails', function ($query) {
                    $query->where('working_status', false);
                })
                ->whereDoesntHave('recomServants')
                ->get();

            $professions = Profession::all();

            return view('cms.seek-vacancy.partial.detail', compact(['data', 'servants', 'professions']));
        }
    }

    public function allApplicant()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::where('employe_id', auth()->user()->id)
                ->where('status', 'accepted')
                ->get();
        } else {
            $datas = Application::all();
        }

        return view('cms.applicant.all', compact('datas'));
    }

    public function hireApplicant()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::where('employe_id', auth()->user()->id)
                ->whereNotNull('employe_id')
                ->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])
                ->get();
            return view('cms.applicant.hire', compact('datas'));
        } else {
            $datas = Application::whereNotNull('employe_id')->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])->get();

            $schemas = Salary::all();
            return view('cms.applicant.hire', compact(['datas', 'schemas']));
        }
    }

    public function indieApplicant()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::whereHas('vacancy.user', function ($query) {
                $query->where('id', auth()->user()->id);
            })
                ->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])
                ->whereNotNull('vacancy_id')
                ->get();

            return view('cms.applicant.independent', compact('datas'));
        } else {
            $datas = Application::whereNotNull('vacancy_id')->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])->get();

            $schemas = Salary::all();

            return view('cms.applicant.independent', compact(['datas', 'schemas']));
        }
    }

    public function hireApplication()
    {
        if (auth()->user()->roles->first()->name == 'pembantu') {
            $datas = Application::where('servant_id', auth()->user()->id)
                ->whereNotNull('employe_id')
                ->get();
        } else {
            $datas = Application::whereNotNull('employe_id')->get();
        }

        return view('cms.application.hire', compact('datas'));
    }

    public function indieApplication()
    {
        if (auth()->user()->roles->first()->name == 'pembantu') {
            $datas = Application::where('servant_id', auth()->user()->id)
                ->whereNotNull('vacancy_id')
                ->get();
        } else {
            $datas = Application::whereNotNull('vacancy_id')->get();
        }

        return view('cms.application.independent', compact('datas'));
    }

    public function storeRecom(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'servant_id' => ['required', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $vacancy = Vacancy::findOrFail($id);

        try {
            DB::transaction(function () use ($data, $vacancy) {
                RecomServant::create([
                    'servant_id' => $data['servant_id'],
                    'vacancy_id' => $vacancy->id,
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengirimkan rekomendasi!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }
}
