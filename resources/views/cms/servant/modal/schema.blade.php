<div class="modal fade" id="editSchemaModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModal">Edit Pengaturan Gaji</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('worker.change-schema', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="schema_salary">Pengaturan Gaji <span class="text-danger">*</span></label>
                                <select class="form-control" id="schema_salary" name="schema_salary" required>
                                    <option selected disabled>Pilih Pengaturan Gaji...</option>
                                    @foreach ($schemas as $item)
                                        <option value="{{ $item->id }}" {{ $data->schema_salary == $item->id ? 'selected' : '' }} class="text-wrap">
                                            Client ({{ $item->adds_client }}, {{ $item->bpjs_client == 1 ? 'dengan BPJS' : 'Tidak dengan BPJS' }}) | Mitra ({{ $item->adds_mitra }}, {{ $item->bpjs_mitra == 1 ? 'dengan BPJS' : 'Tidak dengan BPJS' }})
                                        </option>    
                                    @endforeach
                                </select>
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
