<!-- Edit Salary Type Modal -->
<div class="modal fade" id="editSalaryTypeModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editSalaryTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSalaryTypeModalLabel">Ubah Jenis Penggajian (Ikatan Kerja)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.update-salary-type', $data->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Ubah jenis penggajian untuk pembantu <strong>{{ $data->servant->name ?? '...' }}</strong>.</p>
                    
                    <div class="form-group border-bottom pb-3">
                        <label class="font-weight-bold">Jenis Penggajian Saat Ini</label>
                        <div>
                            @if ($data->salary_type == 'contract')
                                <span class="badge badge-primary p-2">Kontrak (Agensi)</span>
                            @elseif ($data->salary_type == 'fee' && $data->is_infal)
                                <span class="badge badge-warning p-2">Infal</span>
                            @elseif ($data->salary_type == 'fee')
                                <span class="badge badge-info p-2">Fee Langsung</span>
                            @else
                                <span class="badge badge-secondary p-2">Belum Diatur (-)</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="salary_type_{{ $data->id }}">Ubah Menjadi <span class="text-danger">*</span></label>
                        <select class="form-control" name="salary_type" id="salary_type_{{ $data->id }}" required>
                            <option value="">-- Pilih Jenis Penggajian --</option>
                            <option value="contract" {{ $data->salary_type == 'contract' ? 'selected' : '' }}>Kontrak (Agensi)</option>
                            <option value="fee_langsung" {{ ($data->salary_type == 'fee' && !$data->is_infal) ? 'selected' : '' }}>Fee Langsung</option>
                            <option value="infal" {{ ($data->salary_type == 'fee' && $data->is_infal) ? 'selected' : '' }}>Infal</option>
                        </select>
                        <small class="form-text text-muted">
                            Catatan: Merubah jenis penggajian dapat mempengaruhi akses pembantu untuk melamar pekerjaan lain. Pekerja kontrak tidak bisa melamar kerja ganda.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
