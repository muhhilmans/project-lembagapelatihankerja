<div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModal">Ubah Pengaturan Gaji</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('salaries.update', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="adds_client">Tambahan Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adds_client" name="adds_client" value="{{ $data->adds_client }}" required>
                            <small id="adds_client" class="form-text text-muted">Ex. 7,5% = 0.075</small>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="hidden" name="bpjs_client" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="adds_mitra">Potongan Mitra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adds_mitra" name="adds_mitra" value="{{ $data->adds_mitra }}" required>
                            <small id="adds_mitra" class="form-text text-muted">Ex. 2,5% = 0.025</small>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="hidden" name="bpjs_mitra" value="0">
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
