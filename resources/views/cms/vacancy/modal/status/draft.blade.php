<div class="modal fade" id="draftModal-{{ $d->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Perjanjian Kerja {{ $d->servant->servantDetails->profession->name }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-left">
                ini adalah draft perjanjian kerja pada profesi {{ $d->servant->servantDetails->profession->name }}
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
