<div class="modal fade" id="createSkillModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Keahlian</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('profile-servant.store-skill', $data->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="skill">Keahlian <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="skill" name="skill" required>
                    </div>
                    <div class="form-group">
                        <label for="level">Tingkat <span class="text-danger">*</span></label>
                        <select class="custom-select" id="level" name="level" required>
                            <option selected>Pilih Tingkat Keahlian...</option>
                            <option value="pemula" class="text-capitalize">pemula</option>
                            <option value="kompeten" class="text-capitalize">kompeten</option>
                            <option value="mahir" class="text-capitalize">mahir</option>
                            <option value="ahli" class="text-capitalize">ahli</option>
                            <option value="master" class="text-capitalize">master</option>
                        </select>
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
