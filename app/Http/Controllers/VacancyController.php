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

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $professions = Profession::all();

        if (auth()->user()->roles->first()->name == 'majikan') {
            $users = auth()->user();
            $datas = Vacancy::where('user_id', $users->id)->get();
        } else {
            $datas = Vacancy::all();

            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->get();
        }

        return view('cms.vacancy.index', compact(['datas', 'users', 'professions']));
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

        return view('cms.vacancy.partial.detail', compact(['data', 'recoms', 'applications']));
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

        if ($data->applications->count() > 0) {
            return redirect()->route('vacancies.index')->with('toast_error', 'Lowongan masih digunakan oleh pelamar!');
        }

        $data->delete();

        return redirect()->route('vacancies.index')->with('toast_success', 'Lowongan berhasil dihapus!');
    }
}
