<div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ubah Profesi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('professions.update', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group text-left">
                        <label for="name">Nama Profesi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $data->name }}" required>
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
