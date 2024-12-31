<div class="modal fade" id="editBankModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Akun Rekening</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('update-bank', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_bank"
                                    name="is_bank"
                                    {{ old('is_bank', $data->servant->servantDetails->is_bank ?? 0) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_bank">
                                    Memiliki Rekening
                                </label>
                            </div>

                            <div id="bank-details"
                                class="{{ old('is_bank', $data->servant->servantDetails->is_bank) == 1 ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name"
                                        value="{{ old('bank_name', $data->servant->servantDetails->bank_name) }}"
                                        placeholder="Isi dengan nama bank...">
                                </div>

                                <div class="form-group">
                                    <label for="account_number">Nomor Rekening <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="account_number" name="account_number"
                                        value="{{ old('account_number', $data->servant->servantDetails->account_number) }}"
                                        placeholder="Isi dengan nomor rekening...">
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_bpjs"
                                    name="is_bpjs"
                                    {{ old('is_bpjs', $data->servant->servantDetails->is_bpjs ?? 0) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_bpjs">
                                    Memiliki BPJS
                                </label>
                            </div>

                            <div id="bpjs-details"
                                class="{{ old('is_bpjs', $data->servant->servantDetails->is_bpjs) == 1 ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="type_bpjs">Jenis BPJS <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="type_bpjs" name="type_bpjs"
                                        value="{{ old('type_bpjs', $data->servant->servantDetails->type_bpjs) }}"
                                        placeholder="Isi dengan jenis BPJS...">
                                </div>

                                <div class="form-group">
                                    <label for="number_bpjs">Nomor BPJS <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="number_bpjs" name="number_bpjs"
                                        value="{{ old('number_bpjs', $data->servant->servantDetails->number_bpjs) }}"
                                        placeholder="Isi dengan nomor BPJS...">
                                </div>
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

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isBankCheckbox = document.getElementById('is_bank');
            const bankDetails = document.getElementById('bank-details');
            isBankCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    bankDetails.classList.remove('d-none');
                } else {
                    bankDetails.classList.add('d-none');
                }
            });

            const isBpjsCheckbox = document.getElementById('is_bpjs');
            const bpjsDetails = document.getElementById('bpjs-details');
            isBpjsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    bpjsDetails.classList.remove('d-none');
                } else {
                    bpjsDetails.classList.add('d-none');
                }
            });
        });
    </script>
@endpush
