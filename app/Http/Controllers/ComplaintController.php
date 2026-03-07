<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\Urgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $activeContracts = [];
        $urgencies = Urgency::where('is_active', true)->get();

        if ($user->hasRole('admin') || $user->hasRole('superadmin')) { 
            $datas = Pengaduan::with(['complaintType', 'reporter.roles', 'reportedUser.roles', 'application.vacancy', 'resolvedBy'])
                ->orderByRaw("FIELD(status, 'open', 'investigating', 'resolved')")
                ->orderByRaw("FIELD(urgency_level, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW')")
                ->get();
        } else {
            // Show complaints where user is reporter or reported
            $datas = Pengaduan::with(['complaintType', 'reporter', 'reportedUser', 'resolvedBy'])
                ->where('reporter_id', $user->id)
                ->orWhere('reported_user_id', $user->id)
                ->orderByRaw("FIELD(status, 'open', 'investigating', 'resolved')")
                ->orderByRaw("FIELD(urgency_level, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW')")
                ->get();

            // Fetch active contracts for dropdown
            // Assuming 'accepted' status means active contract. ADD 'contract' if that is a valid status too.
            // Based on DashboardController, 'accepted' seems to be the main active status.
            $activeContracts = \App\Models\Application::where(function($q) use ($user) {
                    $q->where('servant_id', $user->id)
                      ->orWhere('employe_id', $user->id);
                })
                ->whereIn('status', ['accepted', 'contract']) // Include 'contract' if used
                ->with(['servant', 'employe', 'vacancy'])
                ->get();
        }

        return view('cms.complaint.index', compact('datas', 'activeContracts', 'urgencies'));
    }

    public function changeStatus(Request $request, string $id)
    {
        $rules = [
            'status' => ['required', 'in:open,investigating,resolved'],
        ];

        // Wajib isi catatan penyelesaian saat menyelesaikan pengaduan
        if ($request->input('status') === 'resolved') {
            $rules['resolution_notes'] = ['required', 'string', 'min:10'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'resolution_notes.required' => 'Catatan penyelesaian wajib diisi.',
            'resolution_notes.min' => 'Catatan penyelesaian minimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->first())->withInput();
        }

        $data = $validator->validated();

        $update = Pengaduan::findOrFail($id);

        try {
            $updateData = [
                'status' => $data['status'],
            ];

            if ($data['status'] === 'resolved') {
                $updateData['resolved_at'] = now();
                $updateData['resolution_notes'] = $data['resolution_notes'];
                $updateData['resolved_by'] = auth()->id();
            } else {
                $updateData['resolved_at'] = null;
                $updateData['resolution_notes'] = null;
                $updateData['resolved_by'] = null;
            }

            $update->update($updateData);

            Alert::success('Berhasil', 'Status pengaduan berhasil diubah!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return redirect()->back()->with('toast_error', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input pengaduan
        $validator = Validator::make($request->all(), [
            'complaint_type_id' => ['required', 'exists:urgencies,id'],
            'message' => ['required', 'string'],
            'contract_id' => ['required', 'exists:applications,id'],
            'reported_user_id' => ['required', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();
        $user = auth()->user();

        // Get urgency level from the selected complaint type
        $urgency = Urgency::find($data['complaint_type_id']);
        $urgencyLevel = $urgency ? $urgency->default_urgency : 'LOW';

        try {
            Pengaduan::create([
                'complaint_type_id' => $data['complaint_type_id'],
                'contract_id' => $data['contract_id'],
                'urgency_level' => $urgencyLevel,
                'reporter_id' => $user->id,
                'reported_user_id' => $data['reported_user_id'],
                'description' => $data['message'],
                'status' => 'open',
            ]);

            Alert::success('Berhasil', 'Berhasil mengirimkan pengaduan!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return redirect()->back()->with('toast_error', $data);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
