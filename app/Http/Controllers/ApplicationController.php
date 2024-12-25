<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Exception;

class ApplicationController extends Controller
{
    public function hireServant(Request $request, string $id)
    {
        $servant = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employe_id' => ['required', 'exists:users,id'],
            'interview_date' => ['required', 'date'],
            'notes' => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $servant) {
                Application::create([
                    'servant_id' => $servant->id,
                    'employe_id' => $data['employe_id'],
                    'status' => 'interview',
                    'interview_date' => $data['interview_date'],
                    'notes' => $data['notes'],
                ]);
            });

            Alert::success('Berhasil', 'Pembantu berhasil dipekerjakan!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function hireContract(Request $request, string $id)
    {
        $request->validate([
            'file_contract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $data = Application::findOrFail($id);

            $directory = "contracts/{$data->employe->name}";
            $fileName = "contract_hire_{$data->servant->name}." . $request->file('file_contract')->getClientOriginalExtension();

            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            if ($data->file_contract && Storage::exists($data->file_contract)) {
                Storage::delete($data->file_contract);
            }

            $path = $request->file('file_contract')->storeAs($directory, $fileName);

            $data->update([
                'status' => 'accepted',
                'file_contract' => $path,
            ]);

            Alert::success('Berhasil', 'File kontrak berhasil diunggah.');
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('toast_error', 'Gagal mengunggah file kontrak: ' . $e->getMessage());
        }
    }

    public function hireReject(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'interview_date' => ['sometimes', 'date'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $update = Application::findOrFail($id);

        try {
            DB::transaction(function () use ($update, $data) {
                $update->update([
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                ]);
            });

            Alert::success('Berhasil', 'Pelamar berhasil ditolak!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function applyJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'servant_id' => ['required', 'exists:users,id'],
            'vacancy_id' => ['required', 'exists:vacancies,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                Application::create([
                    'servant_id' => $data['servant_id'],
                    'vacancy_id' => $data['vacancy_id'],
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengirimkan lamaran!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function applyRecom(Request $request, Vacancy $vacancy, User $user)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'interview_date' => ['sometimes', 'date'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $vacancy, $user) {
                Application::create([
                    'vacancy_id' => $vacancy->id,
                    'servant_id' => $user->id,
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'interview_date' => $data['interview_date'],
                ]);
            });

            Alert::success('Berhasil', 'Berhasil menambahkan pelamar!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function changeStatus(Request $request, Vacancy $vacancy, User $user)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'interview_date' => ['sometimes', 'date'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $update = Application::where('vacancy_id', $vacancy->id)->where('servant_id', $user->id)->first();

        try {
            DB::transaction(function () use ($update, $data, $vacancy) {
                if ($data['status'] == 'interview') {
                    $update->update([
                        'status' => $data['status'],
                        'notes' => $data['notes'],
                        'interview_date' => $data['interview_date'],
                    ]);
                } else {
                    $update->update([
                        'status' => $data['status'],
                        'notes' => $data['notes'],
                    ]);
                }
            });

            Alert::success('Berhasil', 'Berhasil memproses pelamar!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function uploadContract(Request $request, Vacancy $vacancy, User $user)
    {
        $request->validate([
            'file_contract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $applyJob = Application::where('vacancy_id', $vacancy->id)
                ->where('servant_id', $user->id)
                ->first();

            $directory = "contracts/vacancy_{$vacancy->name}";
            $fileName = "contract_{$user->name}." . $request->file('file_contract')->getClientOriginalExtension();

            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            if ($applyJob->file_contract && Storage::exists($applyJob->file_contract)) {
                Storage::delete($applyJob->file_contract);
            }

            $path = $request->file('file_contract')->storeAs($directory, $fileName);

            $status = 'accepted';

            $applyJob->update([
                'status' => $status,
                'file_contract' => $path,
            ]);

            if ($status == 'accepted') {
                DB::transaction(function () use ($vacancy) {
                    $acceptedCount = Application::where('vacancy_id', $vacancy->id)
                        ->where('status', 'accepted')
                        ->count();
            
                    if ($acceptedCount >= $vacancy->limit) {
                        Application::where('vacancy_id', $vacancy->id)
                            ->whereIn('status', ['pending', 'interview'])
                            ->update([
                                'status' => 'rejected'
                            ]);
            
                        $vacancy->update(['status' => false]);
                    }
                });
            }

            Alert::success('Berhasil', 'File kontrak berhasil diunggah.');
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('toast_error', 'Gagal mengunggah file kontrak: ' . $e->getMessage());
        }
    }

    public function downloadContract($applicationId)
    {
        try {
            $data = Application::findOrFail($applicationId);

            if ($data->file_contract && Storage::exists($data->file_contract)) {

                Alert::success('Berhasil', 'File kontrak berhasil diunduh.');
                return Storage::download($data->file_contract);
            }

            return redirect()->back()->with('toast_error', 'File kontrak tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
