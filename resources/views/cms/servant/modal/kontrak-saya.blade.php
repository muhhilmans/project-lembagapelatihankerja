<div class="modal fade" id="kontrakSayaModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="kontrakSayaLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="kontrakSayaLabel-{{ $data->id }}"><i class="fas fa-file-contract mr-2"></i> Kontrak Saya</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                @php
                    $startKontrak = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
                    $endKontrak = $data->work_end_date ? \Carbon\Carbon::parse($data->work_end_date) : ($startKontrak ? $startKontrak->copy()->addMonths(12) : null);
                    $durasiKontrak = $startKontrak && $endKontrak ? round($startKontrak->floatDiffInMonths($endKontrak)) : 12;
                    
                    $sisaKontrak = 0;
                    if ($endKontrak && now()->lessThan($endKontrak)) {
                        $sisaKontrak = round(now()->floatDiffInMonths($endKontrak));
                    }
                    
                    $statusKontrakHtml = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Aktif</span>';
                    if ($endKontrak && now()->gt($endKontrak)) {
                        $statusKontrakHtml = '<span class="badge badge-danger px-2 py-1" style="border-radius:12px">Habis</span>';
                    }

                    $isContract = $data->salary_type == 'contract';
                    $garansiDuration = 3; 
                    $garansiEnd = $startKontrak ? $startKontrak->copy()->addMonths($garansiDuration) : null;
                    $sisaGaransi = 0;
                    if ($garansiEnd && now()->lessThan($garansiEnd)) {
                        $sisaGaransi = round(now()->floatDiffInMonths($garansiEnd));
                    }
                    $sisaPergantian = 3; 
                    
                    $historyArts = collect();
                    if ($data->vacancy_id) {
                        $historyArts = \App\Models\Application::with('servant')
                            ->where('vacancy_id', $data->vacancy_id)
                            ->orderBy('work_start_date', 'asc')
                            ->get();
                    }
                @endphp

                <div class="row">
                    <div class="col-lg-12 mb-4 mb-lg-0">
                        {{-- Status Kontrak --}}
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Status Kontrak</h6>
                            <p class="mb-1 text-dark">Durasi: {{ $durasiKontrak }} Bulan</p>
                            <p class="mb-1 text-dark">Sisa Kontrak: {{ $sisaKontrak }} Bulan</p>
                            <p class="mb-1 text-dark">Status: {!! $statusKontrakHtml !!}</p>
                            
                            @if($isContract)
                                <div class="mt-4">
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Garansi</h6>
                                    <p class="mb-1 text-dark">Sisa Garansi: {{ $sisaGaransi }} Bulan</p>
                                    <p class="mb-3 text-dark">Sisa Pergantian: {{ $sisaPergantian }}x</p>
                                    
                                    <div class="d-flex" style="gap: 10px;">
                                        <button type="button" class="btn btn-primary btn-sm" style="border-radius:6px;"><i class="fas fa-sync-alt mr-1"></i> Perpanjang</button>
                                        <button type="button" class="btn btn-success btn-sm" style="border-radius:6px;" data-dismiss="modal" data-toggle="modal" data-target="#reviewModal-{{ $data->id }}"><i class="fas fa-exchange-alt mr-1"></i> Tukar</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
