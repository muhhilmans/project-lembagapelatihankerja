<div class="modal fade" id="updateSkillModal-{{ $dataSkill->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ubah Keahlian</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST"
                    action="{{ route('profile-servant.update-skill', [$data->id, $dataSkill->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="skill">Keahlian <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="skill" name="skill"
                            value="{{ $dataSkill->skill }}" required>
                    </div>
                    <div class="form-group">
                        <label for="level">Tingkat <span class="text-danger">*</span></label>
                        <select class="custom-select" id="level" name="level" required>
                            <option selected>Pilih Tingkat Keahlian...</option>
                            <option value="pemula" class="text-capitalize"
                                {{ $dataSkill->level == 'pemula' ? 'selected' : '' }}>pemula</option>
                            <option value="kompeten" class="text-capitalize"
                                {{ $dataSkill->level == 'kompeten' ? 'selected' : '' }}>kompeten</option>
                            <option value="mahir" class="text-capitalize"
                                {{ $dataSkill->level == 'mahir' ? 'selected' : '' }}>mahir</option>
                            <option value="ahli" class="text-capitalize"
                                {{ $dataSkill->level == 'ahli' ? 'selected' : '' }}>ahli</option>
                            <option value="master" class="text-capitalize"
                                {{ $dataSkill->level == 'master' ? 'selected' : '' }}>master</option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="submit">Simpan</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                </form>

                <form method="POST" action="{{ route('profile-servant.destroy-skill', [$data->id, $dataSkill->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
