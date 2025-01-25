<div class="modal fade" id="createKehadiranModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="createKehadiranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createKehadiranModalLabel">Tambah Kehadiran Pekerja</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('worker.presence.store', $data->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-left">
                    <input type="text" name="application_id" value="{{ $data->id }}" hidden>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="month">Bulan <span class="text-danger">*</span></label>
                                <input type="month" name="month" id="month" class="form-control" value="{{ date('Y-m') }}" required readonly> 
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="presence">Kehadiran <span class="text-danger">*</span></label>
                                <input type="number" name="presence" id="presence" class="form-control" max="{{ now()->daysInMonth }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>