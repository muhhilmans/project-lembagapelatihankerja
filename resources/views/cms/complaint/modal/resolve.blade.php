{{-- Modal Selesaikan Pengaduan --}}
<div class="modal fade" id="resolveModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="resolveModalLabel{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('complaints.change', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="resolved">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="resolveModalLabel{{ $data->id }}">
                        <i class="fas fa-check-circle mr-2"></i> Selesaikan Pengaduan
                    </h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- Ringkasan Pengaduan --}}
                    <div class="alert alert-light border mb-3">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Pengadu</small><br>
                                <strong>{{ $data->reporter->name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Terlapor</small><br>
                                <strong>{{ $data->reportedUser->name ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <hr class="my-2">
                        <small class="text-muted">Jenis: </small>
                        <strong>{{ $data->complaintType->name ?? 'N/A' }}</strong>
                    </div>

                    {{-- Text area catatan --}}
                    <div class="form-group">
                        <label for="resolution_notes_{{ $data->id }}">
                            <strong><i class="fas fa-edit mr-1"></i> Catatan Penyelesaian</strong>
                            <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            class="form-control" 
                            id="resolution_notes_{{ $data->id }}" 
                            name="resolution_notes" 
                            rows="5" 
                            required
                            minlength="10"
                            placeholder="Jelaskan bagaimana masalah ini diselesaikan...&#10;&#10;Contoh: Telah dilakukan mediasi antara kedua pihak. Majikan setuju untuk membayar gaji yang tertunggak selama 2 bulan."
                        ></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Catatan ini akan dapat dilihat oleh pengadu dan terlapor. Minimal 10 karakter.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-check-circle mr-1"></i> Selesaikan Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
