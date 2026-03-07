<div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excelModalLabel">Export Data Excel</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('worker.export-excel') }}">
                @csrf
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="filter_data">Pilih data yang akan diunduh <span class="text-danger">*</span></label>
                        <select class="form-control" id="filter_data" name="filter_data" required>
                            <option selected disabled value="">Pilih Data...</option>
                            <option value="all">Semua Data</option>
                            <option value="contract">Kontrak</option>
                            <option value="fee_bulanan">Fee - Bulanan</option>
                            <option value="fee_mingguan">Fee - Mingguan</option>
                            <option value="fee_harian">Fee - Harian</option>
                            <option value="fee_jam">Fee - Jam</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit"><i class="fas fa-file-excel mr-1"></i> Unduh Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>
