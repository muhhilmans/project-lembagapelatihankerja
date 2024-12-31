<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\ApplyJob;
use App\Models\Profession;
use App\Models\Application;
use App\Models\RecomServant;
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

    public function allWorker()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::whereIn('status', ['accepted', 'review'])
                ->where(function ($query) {
                    $query->where('employe_id', auth()->user()->id)
                        ->orWhereHas('vacancy.user', function ($q) {
                            $q->where('id', auth()->user()->id);
                        });
                })->get();
        } else {
            $datas = Application::whereIn('status', ['accepted', 'review'])->get();
        }

        return view('cms.servant.worker', compact('datas'));
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'select_data' => 'required|string',
        ]);

        $filter = $request->input('select_data');
        $query = Application::where('status', 'accepted');

        if ($filter === 'not_have_bank') {
            $query->whereHas('servant.servantDetails', function ($q) {
                $q->where('is_bank', 0);
            });
        } elseif ($filter === 'not_have_bpjs') {
            $query->whereHas('servant.servantDetails', function ($q) {
                $q->where('is_bpjs', 0);
            });
        } elseif ($filter === 'not_have_account') {
            $query->whereHas('servant.servantDetails', function ($q) {
                $q->where('is_bank', 0)->where('is_bpjs', 0);
            });
        }

        $datas = $query->get();

        if ($filter === 'not_have_bank') {
            $pdf = Pdf::loadView('cms.servant.pdf.export-bank', compact('datas'))
                ->setPaper('a4', 'potrait');
            return $pdf->download('data_pekerja_tidak_memiliki_rekening_' . date('d-M-Y') . '.pdf');
        } elseif ($filter === 'not_have_bpjs') {
            $pdf = Pdf::loadView('cms.servant.pdf.export-bpjs', compact('datas'))
                ->setPaper('a4', 'potrait');
            return $pdf->download('data_pekerja_tidak_memiliki_bpjs' . date('d-M-Y') . '.pdf');
        } elseif ($filter === 'not_have_account') {
            $pdf = Pdf::loadView('cms.servant.pdf.export', compact('datas'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('data_pekerja_tidak_memiliki_rekening_dan_bpjs' . date('d-M-Y') . '.pdf');
        } else {
            $pdf = Pdf::loadView('cms.servant.pdf.export', compact('datas'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('data_pekerja_' . date('d-M-Y') . '.pdf');
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
            })->get();
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
                ->get();
            return view('cms.applicant.hire', compact('datas'));
        } else {
            $datas = Application::whereNotNull('employe_id')->get();
            return view('cms.applicant.hire', compact('datas'));
        }
    }

    public function indieApplicant()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::whereHas('vacancy.user', function ($query) {
                $query->where('id', auth()->user()->id);
            })
                ->whereNotNull('vacancy_id')
                ->get();
        } else {
            $datas = Application::whereNotNull('vacancy_id')->get();
        }

        return view('cms.applicant.independent', compact('datas'));
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
