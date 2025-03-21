<div class="modal fade" id="deleteModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModal">Hapus Pengaturan Gaji</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah anda yakin untuk menghapus <b>Pengaturan Gaji</b> ini?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <form action="{{ route('salaries.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="data_id" name="data_id" value="{{ $data->id }}" />
                    <button class="btn btn-danger" type="submit">Yakin</button>
                </form>
            </div>
        </div>
    </div>
</div>
