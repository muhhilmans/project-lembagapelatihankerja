<div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Ubah Voucher</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vouchers.update', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="code">Kode Voucher <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control"
                            value="{{ $data->code }}" required />
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="time_used">Max Penggunaan (Kali) <span class="text-danger">*</span></label>
                                <input type="number" name="time_used" id="time_used" class="form-control"
                                    min="1" value="{{ $data->time_used }}" required />
                            </div>

                            <div class="form-group">
                                <label for="discount">Diskon (Persen) <span class="text-danger">*</span></label>
                                <input type="string" name="discount" id="discount" class="form-control"
                                    value="{{ $data->discount }}" required />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="people_used">Max Pengguna (Orang)</label>
                                <input type="number" name="people_used" id="people_used" class="form-control"
                                    value="{{ $data->people_used }}" />
                            </div>

                            <div class="form-group">
                                <label for="expired_date">Masa Berlaku</label>
                                <input type="date" name="expired_date" id="expired_date" class="form-control"
                                    value="{{ old('expired_date', $data->expired_date) }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-warning" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
