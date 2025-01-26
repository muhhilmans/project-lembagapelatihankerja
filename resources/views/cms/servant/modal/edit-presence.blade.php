<div class="modal fade" id="editKehadiranModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editKehadiranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKehadiranModalLabel">Tambah Kehadiran Pekerja</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('worker.presence.update', ['app' => $data->application_id, 'salary' => $data->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="application_id" value="{{ $data->id }}" hidden>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="month">Bulan <span class="text-danger">*</span></label>
                                <input type="month" name="month" id="month" class="form-control" value="{{ \Carbon\Carbon::parse($data->month)->format('Y-m') }}" required readonly> 
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="presence">Kehadiran <span class="text-danger">*</span></label>
                                <input type="number" name="presence" id="presence" class="form-control" max="{{ now()->daysInMonth }}" value="{{ $data->presence }}" required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label for="voucher">Kode Voucher</label>
                            <input type="text" name="voucher" id="voucher" class="form-control" value="{{ $data->voucher->code ?? '' }}" readonly>
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