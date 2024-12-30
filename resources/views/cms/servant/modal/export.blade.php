<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data</h5></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('worker.download') }}">
                @csrf
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="select_data">Pilih data yang akan diunduh <span class="text-danger">*</span></label>
                        <select class="form-control" id="select_data" name="select_data" required>
                            <option selected disabled>Pilih Data...</option>
                            <option value="all">Semua data</option>
                            <option value="not_have_bank">Belum memiliki rekening</option>
                            <option value="not_have_bpjs">Belum memiliki BPJS</option>
                            <option value="not_have_account">Belum memiliki rekening dan BPJS</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Unduh</button>
                </div>
            </form>
        </div>
    </div>
</div>
