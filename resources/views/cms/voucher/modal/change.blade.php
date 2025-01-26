<div class="modal fade" id="changeModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="changeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeModalLabel">Ubah Status Voucher</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah anda yakin untuk <b>{{ $data->is_active ? 'menonaktifkan' : 'mengaktifkan' }}</b> voucher dengan kode <b>{{ $data->code }}</b> ini?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <form action="{{ route('vouchers.change', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="data_id" name="data_id" value="{{ $data->id }}" />
                    <button class="btn btn-info" type="submit">Yakin</button>
                </form>
            </div>
        </div>
    </div>
</div>
