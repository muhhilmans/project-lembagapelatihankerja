<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $users = auth()->user();
            $datas = Vacancy::where('user_id', $users->id)->get();
        } else {
            $datas = Vacancy::all();

            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->get();
        }

        return view('cms.vacancy.index', compact(['datas', 'users']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'closing_date' => ['required', 'date'],
            'user_id' => ['required', 'exists:users,id'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                Vacancy::create([
                    'title' => $data['title'],
                    'closing_date' => $data['closing_date'],
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'benefits' => $data['benefits'],
                    'status' => true
                ]);
            });

            return redirect()->route('vacancies.index')->with('success', 'Lowongan berhasil ditambahkan!');
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

        return view('cms.vacancy.partial.detail', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dataUpdate = Vacancy::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'closing_date' => ['required', 'date'],
            'user_id' => ['required', 'exists:users,id'],
            'description' => ['required'],
            'requirements' => ['required'],
            'benefits' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $dataUpdate) {
                $dataUpdate->update([
                    'title' => $data['title'],
                    'closing_date' => $data['closing_date'],
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'requirements' => $data['requirements'],
                    'benefits' => $data['benefits'],
                ]);
            });

            return redirect()->route('vacancies.index')->with('success', 'Lowongan berhasil diperbarui!');
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
        //
    }
}
