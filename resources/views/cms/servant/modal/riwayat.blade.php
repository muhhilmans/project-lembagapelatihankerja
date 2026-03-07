<div class="modal fade" id="riwayatModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="riwayatLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="riwayatLabel-{{ $data->id }}"><i class="fas fa-history mr-2"></i> Riwayat Pekerja Sebelumnya</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                @php
                    $historyArts = collect();
                    if ($data->vacancy_id) {
                        $historyArts = \App\Models\Application::with('servant')
                            ->where('vacancy_id', $data->vacancy_id)
                            ->orderBy('work_start_date', 'asc')
                            ->get();
                    }
                @endphp

                <div class="table-responsive">
                    <table class="table text-dark mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0">Nama Pembantu</th>
                                <th class="border-top-0">Periode Bekerja</th>
                                <th class="border-top-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historyArts as $history)
                                @php
                                    $histStart = $history->work_start_date ? \Carbon\Carbon::parse($history->work_start_date) : null;
                                    $histEnd = $history->work_end_date ? \Carbon\Carbon::parse($history->work_end_date) : null;
                                    
                                    $periodeStr = ($histStart ? $histStart->format('d M y') : '-') . ' - ' . ($histEnd ? $histEnd->format('d M y') : 'Sekarang');
                                    
                                    $histStatus = 'Aktif';
                                    if ($history->status == 'reject' || $history->status == 'laidoff') {
                                        $histStatus = 'Diganti';
                                    } else if ($histEnd && now()->gt($histEnd)) {
                                        $histStatus = 'Selesai';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $history->servant ? $history->servant->name : '-' }}</td>
                                    <td>{{ $periodeStr }}</td>
                                    <td>
                                        <span class="badge badge-{{ $histStatus == 'Aktif' ? 'success' : ($histStatus == 'Diganti' ? 'danger' : 'secondary') }} px-2 py-1" style="border-radius:12px">
                                            {{ $histStatus }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada history ART untuk lowongan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
