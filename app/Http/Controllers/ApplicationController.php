<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\ServantDetail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
// use App\Models\Notification;
// use App\Events\NotificationDispatched;
use Illuminate\Support\Facades\Validator;
use App\Models\Urgency; // Added for new methods
use App\Models\Salary; // Added for new methods
use App\Models\Scheme; // Added for new methods
use App\Models\Garansi; // Added for new methods

class ApplicationController extends Controller
{
    public function hireApplicant()
    {
        $urgencies = Urgency::where('is_active', true)->get();
        $garansiOptions = Garansi::where('is_active', true)->get();
        $schemeOptions = Scheme::where('is_active', true)->get();

        if (auth()->user()->roles->first()->name == 'majikan') {
            $hireData = Application::where('employe_id', auth()->user()->id)
                ->whereNotNull('servant_id')
                ->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])
                ->get();
            $indieData = Application::where('employe_id', auth()->user()->id)
                ->whereNotNull('servant_id')
                ->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])
                ->get(); // Assuming indieData is similar or needs to be fetched
            $type = 'hire'; // Or determine based on context
            $schemas = Salary::all(); // Assuming Salary model exists

            return view('cms.applicant.index', compact('hireData', 'indieData', 'type', 'schemas', 'urgencies', 'schemeOptions', 'garansiOptions'));
        } else {
            // Default behavior if not 'majikan' or if the above condition is not met
            // This part was not fully provided in the snippet, so I'm making an assumption
            // based on the return view. You might need to adjust this logic.
            $hireData = Application::whereNotNull('employe_id')->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])->get();
            $indieData = Application::whereNotNull('employe_id')->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])->get();
            $type = 'hire';
            $schemas = Salary::all();
            return view('cms.applicant.index', compact('hireData', 'indieData', 'type', 'schemas', 'urgencies', 'schemeOptions', 'garansiOptions'));
        }
    }

    public function hireServant(Request $request, string $id)
    {
        $servant = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employe_id' => ['required', 'exists:users,id'],
            // 'interview_date' => ['required', 'date'],
            // 'notes' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $existing = Application::where('servant_id', $servant->id)
            ->where('employe_id', $data['employe_id'])
            ->first();

        if ($existing) {
            Alert::warning('Peringatan', 'Anda sudah mempekerjakan pembantu ini sebelumnya.');
            return redirect()->back();
        }

        try {
            $application = null;
            DB::transaction(function () use ($data, $servant, &$application) {
                $application = Application::create([
                    'servant_id' => $servant->id,
                    'employe_id' => $data['employe_id'],
                    'status' => 'pending',
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


    public function changeStatusHire(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'interview_link' => ['nullable', 'url'],
            'interview_date' => ['sometimes', 'date'],
            'work_end_date' => ['sometimes', 'date'],
            'salary' => ['nullable', 'numeric'],
            'schema_salary' => ['sometimes', 'exists:salaries,id'],
            'end_reason' => ['nullable', 'string', 'in:selesai_kontrak,diberhentikan,diganti,mengundurkan_diri'],
        ]);


        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $update = Application::findOrFail($id);

        try {
            DB::transaction(function () use ($update, $data) {
                if ($data['status'] == 'schedule') {
                    $update->update([
                        'status' => $data['status'],
                        'notes_interview' => $data['notes'],
                        'interview_date' => $data['interview_date'],
                    ]);
                } elseif ($data['status'] == 'interview') {
                    $update->update([
                        'status' => $data['status'],
                        'link_interview' => $data['interview_link'],
                        'notes_interview' => $data['notes'],
                    ]);
                } elseif ($data['status'] == 'passed') {
                    $update->update([
                        'status' => $data['status'],
                        'salary' => $data['salary'],
                        'schema_salary' => $data['schema_salary'],
                    ]);
                } elseif ($data['status'] == 'verify') {
                    $update->update([
                        'status' => $data['status'],
                        'notes_verify' => $data['notes'],
                    ]);
                } elseif ($data['status'] == 'laidoff') {
                    $update->update([
                        'status' => $data['status'],
                        'work_end_date' => $data['work_end_date'],
                        'end_reason' => $data['end_reason'] ?? null,
                    ]);
                } elseif ($data['status'] == 'accepted') {
                    $update->update([
                        'status' => $data['status'],
                    ]);
                } else {
                    $update->update([
                        'status' => $data['status'],
                        'notes_rejected' => $data['notes'],
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

            // The original code returned 'cms.error'. The snippet provided a different view.
            // I'm assuming the intent was to pass these variables to the error view if it's a specific error page.
            // If the intent was to render a different page on error, the logic needs to be clearer.
            // For now, I'm keeping the original error view but adding the variables if they were meant for it.
            // If the intention was to render 'cms.applicant.hire' on error, the structure of the catch block needs to be completely re-evaluated.
            $urgencies = Urgency::where('is_active', true)->get();
            $garansiOptions = Garansi::where('is_active', true)->get();
            $schemas = Salary::all(); // Assuming Salary model exists
            $datas = Application::whereNotNull('employe_id')->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])->get(); // Assuming datas is needed for this view

            return view('cms.error', compact('data', 'datas', 'urgencies', 'garansiOptions', 'schemas'));
        }
    }

    public function hireContract(Request $request, string $id)
    {
        $request->validate([
            'work_start_date' => 'required|date',
            'file_contract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $application = Application::findOrFail($id);
            $employe = User::findOrFail($application->employe_id);
            $servant = User::findOrFail($application->servant_id);

            $employeName = str_replace(' ', '_', $employe->name);
            $servantName = str_replace(' ', '_', $servant->name);

            $directory = "contracts/hire_{$employeName}";
            $fileName = "contract_{$servantName}." . $request->file('file_contract')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            // Buat direktori jika belum ada
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            // Hapus kontrak lama jika ada
            if ($application->file_contract && Storage::exists($application->file_contract)) {
                Storage::delete($application->file_contract);
            }

            // Simpan file kontrak baru
            $path = $request->file('file_contract')->storeAs($storagePath, $fileName);

            DB::transaction(function () use ($application, $servant, $employe, $path, $request) {
                $application->update([
                    'status' => 'accepted',
                    'work_start_date' => $request->input('work_start_date'),
                    'file_contract' => str_replace('public/', '', $path),
                ]);

                $servantDetail = ServantDetail::where('user_id', $servant->id)->first();
                if ($servantDetail) {
                    $servantDetail->update(['working_status' => true]);
                }

                Application::where('servant_id', $servant->id)
                    ->where('id', '!=', $application->id)
                    ->update([
                        'status' => 'rejected',
                        'notes_rejected' => 'Telah diterima oleh ' . $employe->name,
                    ]);
            });

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

        // Prevent duplicate applications
        $existingApply = Application::where('servant_id', $request->servant_id)
            ->where('vacancy_id', $request->vacancy_id)
            ->first();

        if ($existingApply) {
            Alert::warning('Peringatan', 'Anda sudah melamar lowongan ini sebelumnya.');
            return redirect()->back();
        }

        // Cek apakah pekerja terikat kontrak aktif (salary_type = contract dengan status = accepted)
        $hasActiveContract = Application::where('servant_id', $request->servant_id)
            ->where('status', 'accepted')
            ->where('salary_type', 'contract')
            ->exists();

        if ($hasActiveContract) {
            Alert::warning('Peringatan', 'Anda sedang terikat kontrak dan tidak dapat melamar lowongan baru.');
            return redirect()->back();
        }

        $data = $validator->validated();

        try {
            $application = null;
            DB::transaction(function () use ($data, &$application) {
                $application = Application::create([
                    'servant_id' => $data['servant_id'],
                    'vacancy_id' => $data['vacancy_id'],
                ]);
            });

            $servant = User::find($data['servant_id']);
            $vacancy = Vacancy::find($data['vacancy_id']);

            // Kirim notifikasi ke Admin
            // $adminIds = User::role(['superadmin', 'admin'])->pluck('id');
            // foreach ($adminIds as $adminId) {
            //     $msgAdmin = "Pelamar {$servant->name} telah melamar pada lowongan: {$vacancy->title}.";
            //     // Notification::create([
            //     //     'user_id' => $adminId,
            //     //     'type' => 'success',
            //     //     'message' => $msgAdmin,
            //     //     'link' => route('applicant.index'),
            //     // ]);
            //     // NotificationDispatched::dispatch($msgAdmin, $adminId, 'success', route('applicant.index'));
            // }

            // Kirim notifikasi ke Servant (Konfirmasi)
            // $msgServant = "Lamaran Anda untuk lowongan '{$vacancy->title}' telah berhasil dikirim.";
            // Notification::create([
            //     'user_id' => $servant->id,
            //     'type' => 'success',
            //     'message' => $msgServant,
            //     'link' => route('worker-all'),
            // ]);
            // NotificationDispatched::dispatch($msgServant, $servant->id, 'success', route('worker-all'));

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

        $existing = Application::where('vacancy_id', $vacancy->id)
            ->where('servant_id', $user->id)
            ->first();

        if ($existing) {
            Alert::warning('Peringatan', 'Pelamar ini sudah direkomendasikan untuk lowongan ini.');
            return redirect()->back();
        }

        try {
            DB::transaction(function () use ($data, $vacancy, $user) {
                Application::create([
                    'vacancy_id' => $vacancy->id,
                    'servant_id' => $user->id,
                    'status' => $data['status'],
                    'notes_interview' => $data['notes'],
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
            'interview_link' => ['nullable', 'url'],
            'interview_date' => ['sometimes', 'date'],
            'work_end_date' => ['sometimes', 'date'],
            'salary' => ['nullable', 'numeric'],
            'schema_salary' => ['sometimes', 'exists:salaries,id'],
            'end_reason' => ['nullable', 'string', 'in:selesai_kontrak,diberhentikan,diganti,mengundurkan_diri'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $update = Application::where('vacancy_id', $vacancy->id)->where('servant_id', $user->id)->first();

        try {
            DB::transaction(function () use ($update, $data, $vacancy) {
                if ($data['status'] == 'schedule') {
                    $update->update([
                        'status' => $data['status'],
                        'notes_interview' => $data['notes'],
                        'interview_date' => $data['interview_date'],
                    ]);
                } elseif ($data['status'] == 'interview') {
                    $update->update([
                        'status' => $data['status'],
                        'link_interview' => $data['interview_link'],
                        'notes_interview' => $data['notes'],
                    ]);
                } elseif ($data['status'] == 'passed') {
                    $update->update([
                        'status' => $data['status'],
                        'salary' => $data['salary'],
                        'schema_salary' => $data['schema_salary'],
                    ]);
                } elseif ($data['status'] == 'verify') {
                    $update->update([
                        'status' => $data['status'],
                        'notes_verify' => $data['notes'],
                    ]);
                } elseif ($data['status'] == 'laidoff') {
                    $update->update([
                        'status' => $data['status'],
                        'work_end_date' => $data['work_end_date'],
                        'end_reason' => $data['end_reason'] ?? null,
                    ]);
                } elseif ($data['status'] == 'accepted') {
                    $update->update([
                        'status' => $data['status'],
                    ]);
                } else {
                    $update->update([
                        'status' => $data['status'],
                        'notes_rejected' => $data['notes'],
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
            'work_start_date' => 'required|date',
            'file_contract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $applyJob = Application::where('vacancy_id', $vacancy->id)
                ->where('servant_id', $user->id)
                ->first();

            $vacancyName = str_replace(' ', '_', $vacancy->name);
            $userName = str_replace(' ', '_', $user->name);

            $directory = "contracts/vacancy_{$vacancyName}";
            $fileName = "contract_{$userName}." . $request->file('file_contract')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            if ($applyJob->file_contract && Storage::exists($applyJob->file_contract)) {
                Storage::delete($applyJob->file_contract);
            }

            $path = $request->file('file_contract')->storeAs($storagePath, $fileName);

            $status = 'accepted';

            $applyJob->update([
                'status' => $status,
                'work_start_date' => $request->input('work_start_date'),
                'file_contract' => str_replace('public/', '', $path),
            ]);

            if ($status == 'accepted') {
                DB::transaction(function () use ($vacancy, $user) {
                    $acceptedCount = Application::where('vacancy_id', $vacancy->id)
                        ->where('status', 'accepted')
                        ->count();

                    $updateUser = ServantDetail::where('user_id', $user->id)->first();
                    if ($updateUser) {
                        $updateUser->update([
                            'working_status' => true,
                        ]);
                    }

                    Application::where('servant_id', $user->id)->where('status', '!=', 'accepted')->update([
                        'status' => 'rejected',
                        'notes_rejected' => 'Telah diterima oleh ' . $vacancy->user->name,
                    ]);

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

            $filePath = $data->file_contract;

            if ($filePath && storage_path('app/public/' . $filePath)) {
                return response()->download(storage_path('app/public/' . $filePath));
            }

            return redirect()->back()->with('toast_error', 'File kontrak tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indieApplicant()
    {
        $urgencies = Urgency::where('is_active', true)->get();
        $garansiOptions = Garansi::where('is_active', true)->get();

        // This part of the snippet was incomplete and syntactically incorrect.
        // Assuming the intent was to fetch data and pass it to a view.
        // The original code had a conditional 'if (auth()->user()->roles->first()->name == 'majikan') { ... }'
        // but it was not followed by any logic.
        // I'm providing a basic structure that returns the view with the requested variables.
        $datas = Application::whereNull('employe_id')->whereNotIn('status', ['accepted', 'review', 'rejected', 'laidoff'])->get(); // Example data fetch
        return view('cms.applicant.all', compact('datas', 'urgencies', 'garansiOptions'));
    }

    public function updateSalary(Request $request, $id)
    {
        // \Illuminate\Support\Facades\Log::info('--- Update Salary Start ---');
        // \Illuminate\Support\Facades\Log::info('ID: ' . $id);
        // \Illuminate\Support\Facades\Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'salary_type' => ['required', 'in:contract,fee'],
            // Contract Validation
            'contract_salary' => ['required_if:salary_type,contract', 'numeric', 'min:0', 'nullable'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'contract_start_date' => ['required_if:salary_type,contract', 'date', 'nullable'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'garansi_id' => ['nullable', 'exists:garansis,id'],
            'garansi_price' => ['nullable', 'numeric', 'min:0'],
            'warranty_duration' => ['nullable', 'string'],

            // Fee Validation
            'is_infal' => ['sometimes', 'boolean'],

            // Fee - Regular
            'fee_salary_regular' => ['nullable', 'numeric', 'min:0'],
            'fee_frequency_regular' => ['nullable', 'string'],
            'fee_end_date_regular' => ['nullable', 'date'],

            // Fee - Infal
            'infal_frequency' => ['nullable', 'string'],
            'fee_salary_infal' => ['nullable', 'numeric', 'min:0'],
            'infal_start_date' => ['nullable', 'date'],
            'infal_end_date' => ['nullable', 'date'],
            'infal_time_in' => ['nullable', 'string'],
            'infal_time_out' => ['nullable', 'string'],
            'infal_hourly_rate' => ['nullable', 'numeric', 'min:0'],

            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'scheme_id' => ['nullable', 'exists:schemes,id'],
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Validation Failed:', $validator->messages()->all());
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        try {
            $application = Application::findOrFail($id);
            $data = $request->except(['_token', '_method']);

            // Common updates
            $updateData = [
                'salary_type' => $data['salary_type'],
                'deduction_amount' => $data['deduction_amount'] ?? 0,
                'scheme_id' => $data['scheme_id'] ?? null,
            ];

            if ($data['salary_type'] == 'contract') {
                $updateData['salary'] = $data['contract_salary'];
                $updateData['admin_fee'] = $data['admin_fee'];
                $updateData['work_start_date'] = $data['contract_start_date'];
                $updateData['work_end_date'] = $data['contract_end_date'];
                $updateData['work_end_date'] = $data['contract_end_date'];

                if (isset($data['garansi_id'])) {
                    $updateData['garansi_id'] = $data['garansi_id'];
                    $updateData['garansi_price'] = $data['garansi_price'] ?? null;

                    // fetch garansi details to fill warranty_duration just in case for backward compatibility
                    $garansi = Garansi::find($data['garansi_id']);
                    if ($garansi) {
                        $updateData['warranty_duration'] = $garansi->name;
                    }
                } else {
                    $updateData['garansi_id'] = null;
                    $updateData['garansi_price'] = null;
                    $updateData['warranty_duration'] = $data['warranty_duration'] ?? null;
                }

                // Reset Fee fields
                $updateData['is_infal'] = false;
                $updateData['infal_frequency'] = null;
                $updateData['infal_time_in'] = null;
                $updateData['infal_time_out'] = null;
                $updateData['infal_hourly_rate'] = null;

            } else { // Fee
                $isInfal = $request->has('is_infal');
                $updateData['is_infal'] = $isInfal;

                // Reset Contract fields
                $updateData['admin_fee'] = null;
                $updateData['warranty_duration'] = null;

                if ($isInfal) {
                    $updateData['salary'] = $data['fee_salary_infal'];
                    $updateData['infal_frequency'] = $data['infal_frequency'];

                    if ($data['infal_frequency'] == 'hourly') {
                         // For hourly, we might still want a date, assuming start_date is used as "Work Date"
                         $updateData['work_start_date'] = $data['infal_start_date'] ?? null;
                         $updateData['work_end_date'] = null; // No end date or same as start?

                         $updateData['infal_time_in'] = $data['infal_time_in'];
                         $updateData['infal_time_out'] = $data['infal_time_out'];
                         $updateData['infal_hourly_rate'] = $data['infal_hourly_rate'];
                    } else {
                        $updateData['work_start_date'] = $data['infal_start_date'];
                        $updateData['work_end_date'] = $data['infal_end_date'];

                        $updateData['infal_time_in'] = null;
                        $updateData['infal_time_out'] = null;
                        $updateData['infal_hourly_rate'] = null;
                    }

                } else {
                    $updateData['salary'] = $data['fee_salary_regular'];
                    $updateData['infal_frequency'] = $data['fee_frequency_regular'] ?? null;
                    $updateData['infal_time_in'] = null;
                    $updateData['infal_time_out'] = null;
                    $updateData['infal_hourly_rate'] = null;

                    // Start date might be existing or not set for regular fee yet? assuming update doesn't enforce start date for regular unless specified
                    // Logic: End date pembantu automatically H+7 from Employer End Date
                    if (!empty($data['fee_end_date_regular'])) {
                        $employerEndDate = \Carbon\Carbon::parse($data['fee_end_date_regular']);
                        $updateData['work_end_date'] = $employerEndDate->addDays(7)->format('Y-m-d');
                    } else {
                        $updateData['work_end_date'] = null;
                    }
                }
            }

            $application->update($updateData);

            if ($application->status == 'interview') {
                $application->update(['status' => 'passed']);
            }

            Alert::success('Berhasil', 'Pengaturan gaji berhasil diperbarui!');
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
