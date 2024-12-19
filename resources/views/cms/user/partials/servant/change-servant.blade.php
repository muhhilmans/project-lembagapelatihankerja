<div class="modal fade" id="changeModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ubah Status Pembantu</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah anda yakin untuk <b>{{ $user->is_active ? 'nonaktifkan' : 'aktifkan' }} {{ $user->name }}</b> ini?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <form action="{{ route('users-servant.change', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}" />
                    <button class="btn btn-warning" type="submit">Yakin</button>
                </form>
            </div>
        </div>
    </div>
</div>