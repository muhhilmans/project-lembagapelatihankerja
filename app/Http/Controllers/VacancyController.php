<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\Profession;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Models\Salary;

class VacancyController extends Controller
{
    public function index()
    {
        $professions = Profession::all();

        if (auth()->user()->roles->first()->name == 'majikan') {
            $users = auth()->user();
            
            // Auto-Archive Logic: Pre-check active vacancies for fulfilled quotas
            $activeVacancies = Vacancy::where('user_id', $users->id)->get();
            foreach ($activeVacancies as $vacancy) {
                if ($vacancy->isLimitReached()) {
                    // Soft delete the vacancy if its quota (limit) is met
                    $vacancy->delete();
                }
            }

            // Re-fetch data after potential auto-archiving
            $datas = Vacancy::where('user_id', $users->id)->get();
            $archives = Vacancy::onlyTrashed()->where('user_id', auth()->id())->get();
        } else {
            $datas = Vacancy::all();

            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->where('is_active', true)->get();
            $archives = Vacancy::onlyTrashed()->get();
        }

        return view('cms.vacancy.index', compact(['datas', 'users', 'professions', 'archives']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'profession_id' => ['required', 'exists:professions,id'],
            'closing_date' => ['required', 'date'],
            'user_id' => ['required', 'exists:users,id'],
            'limit' => ['required', 'integer'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('vacancies.index')->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                Vacancy::create([
                    'title' => $data['title'],
                    'profession_id' => $data['profession_id'],
                    'closing_date' => $data['closing_date'],
                    'user_id' => $data['user_id'],
                    'limit' => $data['limit'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'benefits' => $data['benefits'],
                    'status' => true
                ]);
            });

            Alert::success('Berhasil', 'Lowongan berhasil ditambahkan!');
            return redirect()->route('vacancies.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Vacancy::findOrFail($id);
        $recoms = RecomServant::where('vacancy_id', $id)
        ->whereHas('servant.servantDetails', function ($query) {
            $query->where('working_status', false);
        })->get();
        $applications = Application::where('vacancy_id', $id)->where('status', '!=', 'accepted')->get();

        $schemas = Salary::all();

        return view('cms.vacancy.partial.detail', compact(['data', 'recoms', 'applications', 'schemas']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dataUpdate = Vacancy::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'profession_id' => ['required', 'exists:professions,id'],
            'closing_date' => ['required', 'date'],
            'user_id' => ['required', 'exists:users,id'],
            'limit' => ['required', 'integer'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('vacancies.index')->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $dataUpdate) {
                if ($dataUpdate->limit <= $data['limit']) {
                    $status = true;
                } else {
                    $status = false;
                }

                $dataUpdate->update([
                    'title' => $data['title'],
                    'closing_date' => $data['closing_date'],
                    'profession_id' => $data['profession_id'],
                    'user_id' => $data['user_id'],
                    'limit' => $data['limit'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'benefits' => $data['benefits'],
                    'status' => $status
                ]);
            });

            Alert::success('Berhasil', 'Lowongan berhasil diperbarui!');
            return redirect()->route('vacancies.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Vacancy::findOrFail($id);

        $data->delete();

        return redirect()->route('vacancies.index')->with('toast_success', 'Lowongan berhasil dihapus (diarsipkan)!');
    }

    public function restore($id)
    {
        $vacancy = Vacancy::withTrashed()->findOrFail($id);
        $vacancy->restore();

        return redirect()->route('vacancies.index')->with('toast_success', 'Lowongan berhasil dipulihkan!');
    }
}
