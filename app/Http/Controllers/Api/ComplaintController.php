<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Pengaduan;
use App\Models\Urgency;
use App\Notifications\GeneralNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    use ApiResponse;

    public function complaintTypes()
    {
        $types = Urgency::where('is_active', true)
            ->get(['id', 'name', 'description', 'default_urgency']);

        return $this->successResponse($types, 'Daftar tipe pengaduan.');
    }

    public function index(Request $request)
    {
        try {
            $user  = auth()->user();
            $isAdmin = $user->hasRole('admin') || $user->hasRole('superadmin');

            $query = Pengaduan::with([
                'complaintType:id,name,default_urgency',
                'reporter:id,name,email',
                'reportedUser:id,name,email',
                'application.vacancy:id,title,user_id',
                'resolvedBy:id,name',
            ]);

            if ($isAdmin) {
                $search = $request->input('search');
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('reporter', fn($s) => $s->where('name', 'like', "%{$search}%"))
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                }

                $status = $request->input('status');
                if ($status) {
                    $query->where('status', $status);
                }
            } else {
                $query->where(function ($q) use ($user) {
                    $q->where('reporter_id', $user->id)
                      ->orWhere('reported_user_id', $user->id);
                });

                $status = $request->input('status');
                if ($status) {
                    $query->where('status', $status);
                }
            }

            $query->orderByRaw("CASE urgency_level
                        WHEN 'CRITICAL' THEN 1 WHEN 'HIGH' THEN 2
                        WHEN 'MEDIUM' THEN 3 WHEN 'LOW' THEN 4 ELSE 5 END ASC")
                  ->orderByRaw("CASE status
                        WHEN 'open' THEN 1 WHEN 'investigating' THEN 2
                        WHEN 'resolved' THEN 3 ELSE 4 END ASC")
                  ->latest();

            $complaints = $query->paginate(10);

            return $this->successResponse([
                'data' => $complaints->items(),
                'pagination' => [
                    'current_page' => $complaints->currentPage(),
                    'per_page'     => $complaints->perPage(),
                    'total'        => $complaints->total(),
                    'last_page'    => $complaints->lastPage(),
                ],
            ], 'Data pengaduan berhasil dimuat.');
        } catch (\Throwable $th) {
            Log::error("Error get complaints: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memuat data pengaduan.', [], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'complaint_type_id' => ['required', 'exists:urgencies,id'],
            'description'       => ['required', 'string', 'min:20'],
            'contract_id'       => ['required', 'exists:applications,id'],
            'reported_user_id'  => ['required', 'exists:users,id'],
        ], [
            'complaint_type_id.required' => 'Tipe pengaduan wajib dipilih.',
            'complaint_type_id.exists'   => 'Tipe pengaduan tidak valid.',
            'description.required'       => 'Deskripsi pengaduan wajib diisi.',
            'description.min'            => 'Deskripsi pengaduan minimal 20 karakter.',
            'contract_id.required'       => 'Kontrak wajib dipilih.',
            'contract_id.exists'         => 'Kontrak tidak ditemukan.',
            'reported_user_id.required'  => 'Terlapor wajib dipilih.',
            'reported_user_id.exists'    => 'Terlapor tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = auth()->user();

        // Verifikasi kontrak benar-benar milik user dan masih aktif
        $application = Application::with('vacancy:id,user_id')
            ->where('id', $request->contract_id)
            ->where(function ($q) use ($user) {
                $q->where('servant_id', $user->id)
                  ->orWhere('employe_id', $user->id);
            })
            ->whereIn('status', ['accepted', 'contract'])
            ->first();

        if (!$application) {
            return $this->errorResponse(
                'Kontrak tidak ditemukan atau Anda tidak memiliki akses ke kontrak ini.', [], 403
            );
        }

        // Verifikasi terlapor adalah pihak lain dalam kontrak yang sama
        $isServant     = (string) $application->servant_id === (string) $user->id;
        $validReported = $isServant
            ? ($application->employe_id ?? $application->vacancy?->user_id)
            : $application->servant_id;

        if ((string) $request->reported_user_id !== (string) $validReported) {
            return $this->errorResponse('Terlapor tidak valid untuk kontrak ini.', [], 403);
        }

        try {
            DB::beginTransaction();

            $urgency   = Urgency::find($request->complaint_type_id);
            $complaint = Pengaduan::create([
                'contract_id'       => $request->contract_id,
                'complaint_type_id' => $request->complaint_type_id,
                'urgency_level'     => $urgency->default_urgency ?? 'LOW',
                'reporter_id'       => $user->id,
                'reported_user_id'  => $request->reported_user_id,
                'description'       => $request->description,
                'status'            => 'open',
            ]);

            DB::commit();

            // Notifikasi ke admin tentang pengaduan baru
            try {
                $admins = \App\Models\User::role(['admin', 'superadmin'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new GeneralNotification(
                        title: 'Pengaduan Baru Masuk',
                        body: "{$user->name} mengajukan pengaduan baru dengan urgensi {$complaint->urgency_level}.",
                        type: 'complaint_new',
                        data: ['complaint_id' => $complaint->id]
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal kirim notif complaint baru: {$e->getMessage()}");
            }

            return $this->successResponse(
                $complaint->load(['complaintType:id,name,default_urgency', 'reporter:id,name', 'reportedUser:id,name']),
                'Pengaduan berhasil dikirimkan.',
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error store complaint: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat menyimpan pengaduan.', [], 500);
        }
    }

    public function show($id)
    {
        try {
            $user    = auth()->user();
            $isAdmin = $user->hasRole('admin') || $user->hasRole('superadmin');

            $query = Pengaduan::with([
                'complaintType',
                'reporter:id,name,email',
                'reportedUser:id,name,email',
                'application.vacancy:id,title',
                'resolvedBy:id,name',
            ]);

            if ($isAdmin) {
                $complaint = $query->find($id);
            } else {
                $complaint = $query->where(function ($q) use ($user) {
                    $q->where('reporter_id', $user->id)
                      ->orWhere('reported_user_id', $user->id);
                })->find($id);
            }

            if (!$complaint) {
                return $this->errorResponse('Data pengaduan tidak ditemukan.', [], 404);
            }

            return $this->successResponse($complaint, 'Detail pengaduan.');
        } catch (\Throwable $th) {
            Log::error("Error show complaint: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memuat detail pengaduan.', [], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $rules = [
            'status' => ['required', 'in:open,investigating,resolved'],
        ];

        if ($request->input('status') === 'resolved') {
            $rules['resolution_notes'] = ['required', 'string', 'min:10'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'status.required'            => 'Status wajib diisi.',
            'status.in'                  => 'Status tidak valid. Gunakan: open, investigating, atau resolved.',
            'resolution_notes.required'  => 'Catatan penyelesaian wajib diisi.',
            'resolution_notes.min'       => 'Catatan penyelesaian minimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $complaint = Pengaduan::find($id);

        if (!$complaint) {
            return $this->errorResponse('Data pengaduan tidak ditemukan.', [], 404);
        }

        try {
            DB::beginTransaction();

            $updateData = ['status' => $request->status];

            if ($request->status === 'resolved') {
                $updateData['resolved_at']       = now();
                $updateData['resolution_notes']  = $request->resolution_notes;
                $updateData['resolved_by']       = auth()->id();
            } else {
                $updateData['resolved_at']      = null;
                $updateData['resolution_notes'] = null;
                $updateData['resolved_by']      = null;
            }

            $complaint->update($updateData);

            DB::commit();

            // Notifikasi ke reporter bahwa pengaduannya diproses/diselesaikan
            try {
                $reporter = $complaint->reporter;
                if ($reporter) {
                    $messages = [
                        'investigating' => 'Pengaduanmu sedang dalam proses investigasi oleh admin.',
                        'resolved'      => 'Pengaduanmu telah diselesaikan. Lihat catatan penyelesaian untuk detail.',
                        'open'          => 'Status pengaduanmu telah diubah kembali ke terbuka.',
                    ];
                    $reporter->notify(new GeneralNotification(
                        title: 'Status Pengaduan Diperbarui',
                        body: $messages[$request->status] ?? 'Status pengaduanmu telah diperbarui.',
                        type: 'complaint_status_changed',
                        data: ['complaint_id' => $complaint->id, 'status' => $request->status]
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal kirim notif status complaint: {$e->getMessage()}");
            }

            return $this->successResponse(
                $complaint->fresh(['complaintType', 'reporter:id,name', 'reportedUser:id,name', 'resolvedBy:id,name']),
                'Status pengaduan berhasil diubah.'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error changeStatus complaint: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat mengubah status pengaduan.', [], 500);
        }
    }

    public function resolve(Request $request, $id)
    {
        $request->merge(['status' => 'resolved']);
        return $this->changeStatus($request, $id);
    }
}
