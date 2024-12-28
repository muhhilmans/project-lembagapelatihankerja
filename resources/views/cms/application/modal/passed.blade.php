<div class="modal fade" id="passedModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Majikan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vacancies.change', ['vacancy' => $data->vacancy_id, 'user' => $data->servant_id]) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="choose" hidden>
                    <input type="text" name="notes" value="" hidden>

                    Apakah anda yakin untuk memilih bekerja di <b>{{ $data->vacancy->user->name }}</b> ini?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </div>
            </form>
        </div>
    </div>
</div>
