<div class="modal fade" id="agreeModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Apakah Anda Yakin</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vacancies.change', ['vacancy' => $d->vacancy_id, 'user' => $d->servant_id]) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="contract" hidden>
                    <input type="text" name="notes" value="" hidden>

                    Apakah anda yakin untuk memverifikasi <b>{{ $d->servant->name }}</b> sehingga dapat bekerja di <b>{{ $d->vacancy->user->name }}</b> ini?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </div>
            </form>
        </div>
    </div>
</div>
