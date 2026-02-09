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
            $datas = Pengaduan::with('complaintType')->get();
        } else {
            // Show complaints where user is reporter or reported
            $datas = Pengaduan::with('complaintType')
                ->where('reporter_id', $user->id)
                ->orWhere('reported_user_id', $user->id)
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
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:open,investigating,resolved'],
            // 'file' removed as per new schema
            // 'notes' logic removed as no column for it
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $update = Pengaduan::findOrFail($id);

        try {
            $update->update([
                'status' => $data['status'],
                // Set resolved_at if resolved?
                'resolved_at' => $data['status'] == 'resolved' ? now() : null,
            ]);

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
            'complaint_type_id' => ['required', 'exists:urgencies,id'], // Jenis pengaduan/urgensi
            'message' => ['required'], // Deskripsi masalah
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
                'urgency_level' => $urgencyLevel,
                'reporter_id' => $user->id,
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
