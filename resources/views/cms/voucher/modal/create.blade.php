<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Voucher</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vouchers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="code">Kode Voucher <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" required />
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="time_used">Max Penggunaan (Kali) <span class="text-danger">*</span></label>
                                <input type="number" name="time_used" id="time_used" class="form-control" min="1" required />
                            </div>

                            <div class="form-group">
                                <label for="discount">Diskon (Persen) <span class="text-danger">*</span></label>
                                <input type="string" name="discount" id="discount" class="form-control" required />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="people_used">Max Pengguna (Orang)</label>
                                <input type="number" name="people_used" id="people_used" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label for="expired_date">Masa Berlaku</label>
                                <input type="date" name="expired_date" id="expired_date" class="form-control" />
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
