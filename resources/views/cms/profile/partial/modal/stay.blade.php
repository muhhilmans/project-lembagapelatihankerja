<div class="modal fade" id="stayModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="stayModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stayModalLabel">Ubah Status Pulang Pergi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah anda yakin <b>{{ $data->servantDetails->is_stay ? 'Tidak Bersedia' : 'Bersedia' }}</b> pulang pergi?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <form action="{{ route('profile.change-stay', ['user' => $data->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="data_id" name="data_id" value="{{ $data->id }}" />
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </form>
            </div>
        </div>
    </div>
</div>