<div class="modal fade" id="invalModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="invalModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invalModalLabel">Ubah Status Inval</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah anda yakin <b>{{ $data->servantDetails->is_inval ? 'Tidak Bersedia' : 'Bersedia' }}</b> inval?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <form action="{{ route('profile.change-inval', ['user' => $data->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="data_id" name="data_id" value="{{ $data->id }}" />
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </form>
            </div>
        </div>
    </div>
</div>