@extends('cms.layouts.main', ['title' => 'Master Garansi'])

@push('custom-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .garansi-card { border-left: 4px solid #f6c23e; transition: all 0.2s; }
        .garansi-card:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .badge-unit { font-size: 0.7rem; padding: 2px 6px; }
    </style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-gray-800"><i class="bi bi-shield-check me-2"></i>Master Garansi</h4>
            <button class="btn btn-warning btn-sm shadow text-dark font-weight-bold" data-toggle="modal" data-target="#garansiModal" onclick="resetGaransiForm()">
                <i class="bi bi-plus-lg mr-1"></i> Tambah Garansi
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Table of Saved Garansi --}}
        @if($garansis->count() > 0)
            @foreach($garansis as $garansi)
                <div class="card garansi-card shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold text-warning text-darken-2 mb-2">
                                    <i class="bi bi-shield mr-1"></i>{{ $garansi->name }}
                                    @if($garansi->is_active)
                                        <span class="badge badge-success badge-unit ml-1">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary badge-unit ml-1">Nonaktif</span>
                                    @endif
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <small class="text-muted d-block">Maksimal Penukaran/Perpanjangan:</small>
                                        <span class="badge badge-light border"><strong>{{ $garansi->max_replacements }} kali</strong></span>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <small class="text-muted d-block">Harga Default Garansi:</small>
                                        <span class="badge badge-light border"><strong>Rp {{ number_format($garansi->price, 0, ',', '.') }}</strong></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted"><i class="bi bi-clock mr-1"></i>Dibuat: {{ $garansi->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            <div class="d-flex flex-column ml-3">
                                <button class="btn btn-outline-info btn-sm mb-1" title="Edit"
                                    onclick="editGaransi({{ json_encode($garansi) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('garansis.destroy', $garansi->id) }}" method="POST" onsubmit="return confirm('Yakin hapus garansi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada opsi garansi. Klik <strong>Tambah Garansi</strong> untuk membuat yang baru.</p>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Add/Edit Garansi --}}
<div class="modal fade" id="garansiModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold" id="garansiModalTitle">
                    <i class="bi bi-plus-circle mr-1"></i> Tambah Garansi Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('garansis.store') }}" method="POST" id="garansiForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Garansi / Durasi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="garansiName" class="form-control" placeholder="Contoh: 1 Bulan, 3 Bulan, atau Garansi Premium" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-3">
                            <label class="font-weight-bold">Maks. Penukaran <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="max_replacements" id="garansiMaxReplacements" class="form-control" placeholder="0" required min="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">kali</span>
                                </div>
                            </div>
                            <small class="text-muted">Berapa kali pengguna bisa menukar/perpanjang.</small>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label class="font-weight-bold">Harga Default (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" id="garansiPriceVisual" class="form-control" placeholder="0" required onkeyup="formatGaransiPrice(this)">
                                <input type="hidden" name="price" id="garansiPrice">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="garansiIsActive" name="is_active" value="1" checked>
                            <label class="custom-control-label font-weight-bold" for="garansiIsActive">Status Aktif</label>
                        </div>
                        <small class="text-muted d-block mt-1">Hanya garansi aktif yang bisa dipilih di modal penggajian.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold px-4">
                        <i class="bi bi-check-all mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script>
    function resetGaransiForm() {
        document.getElementById('garansiModalTitle').innerHTML = '<i class="bi bi-plus-circle mr-1"></i> Tambah Garansi Baru';
        document.getElementById('garansiForm').action = '{{ route("garansis.store") }}';
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('garansiName').value = '';
        document.getElementById('garansiMaxReplacements').value = '';
        document.getElementById('garansiPrice').value = '';
        document.getElementById('garansiPriceVisual').value = '';
        document.getElementById('garansiIsActive').checked = true;
    }

    function editGaransi(garansi) {
        document.getElementById('garansiModalTitle').innerHTML = '<i class="bi bi-pencil-square mr-1"></i> Edit Garansi: ' + garansi.name;
        document.getElementById('garansiForm').action = '{{ url("garansis") }}/' + garansi.id + '/update';
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('garansiName').value = garansi.name;
        document.getElementById('garansiMaxReplacements').value = garansi.max_replacements;
        document.getElementById('garansiPrice').value = garansi.price;
        document.getElementById('garansiPriceVisual').value = formatRupiahString(garansi.price.toString());
        document.getElementById('garansiIsActive').checked = garansi.is_active ? true : false;
        
        $('#garansiModal').modal('show');
    }

    function formatGaransiPrice(element) {
        var val = element.value.replace(/[^0-9]/g, '');
        document.getElementById('garansiPrice').value = val;
        element.value = formatRupiahString(val);
    }

    function formatRupiahString(angka) {
        if (!angka) return '';
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }
</script>
@endpush
