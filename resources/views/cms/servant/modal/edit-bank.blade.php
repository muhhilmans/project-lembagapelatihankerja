<div class="modal fade" id="editBankModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Data Keuangan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('update-bank', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="row">
                        <!-- Kolom Rekening Bank -->
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-university mr-2"></i>Data Rekening Bank</h6>
                            
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="is_bank_{{ $data->id }}" name="is_bank" value="1"
                                    {{ old('is_bank', $data->servant->servantDetails->is_bank ?? 0) == 1 ? 'checked' : '' }}
                                    onchange="document.getElementById('bank-details-{{ $data->id }}').classList.toggle('d-none', !this.checked)">
                                <label class="custom-control-label" for="is_bank_{{ $data->id }}">Memiliki Rekening Bank</label>
                            </div>

                            <div id="bank-details-{{ $data->id }}" class="{{ old('is_bank', $data->servant->servantDetails->is_bank ?? 0) == 1 ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="bank_name_{{ $data->id }}">Nama Bank <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="bank_name_{{ $data->id }}" name="bank_name"
                                        value="{{ old('bank_name', $data->servant->servantDetails->bank_name) }}"
                                        placeholder="Contoh: BCA, Mandiri, BRI...">
                                </div>

                                <div class="form-group">
                                    <label for="account_number_{{ $data->id }}">Nomor Rekening <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="account_number_{{ $data->id }}" name="account_number"
                                        value="{{ old('account_number', $data->servant->servantDetails->account_number) }}"
                                        placeholder="Masukkan nomor rekening...">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom BPJS -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-notes-medical mr-2"></i>Data BPJS</h6>
                            
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="is_bpjs_{{ $data->id }}" name="is_bpjs" value="1"
                                    {{ old('is_bpjs', $data->servant->servantDetails->is_bpjs ?? 0) == 1 ? 'checked' : '' }}
                                    onchange="document.getElementById('bpjs-details-{{ $data->id }}').classList.toggle('d-none', !this.checked)">
                                <label class="custom-control-label" for="is_bpjs_{{ $data->id }}">Memiliki BPJS</label>
                            </div>

                            <div id="bpjs-details-{{ $data->id }}" class="{{ old('is_bpjs', $data->servant->servantDetails->is_bpjs ?? 0) == 1 ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="type_bpjs_{{ $data->id }}">Jenis BPJS <span class="text-danger">*</span></label>
                                    <select class="form-control" id="type_bpjs_{{ $data->id }}" name="type_bpjs">
                                        <option value="" disabled selected>Pilih Jenis BPJS</option>
                                        <option value="Kesehatan" {{ old('type_bpjs', $data->servant->servantDetails->type_bpjs) == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                        <option value="Ketenagakerjaan" {{ old('type_bpjs', $data->servant->servantDetails->type_bpjs) == 'Ketenagakerjaan' ? 'selected' : '' }}>Ketenagakerjaan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="number_bpjs_{{ $data->id }}">Nomor BPJS <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="number_bpjs_{{ $data->id }}" name="number_bpjs"
                                        value="{{ old('number_bpjs', $data->servant->servantDetails->number_bpjs) }}"
                                        placeholder="Masukkan nomor kartu BPJS...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

