@extends('cms.layouts.main', ['title' => 'Master Skema Biaya'])

@push('custom-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .scheme-card { border-left: 4px solid #4e73df; transition: all 0.2s; }
        .scheme-card:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .badge-unit { font-size: 0.7rem; padding: 2px 6px; }
        .input-group-text { font-size: 0.8rem; background-color: #f8f9fa; min-width: 45px; justify-content: center; }
        .section-header { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .preview-box { background: #212529; color: #39FF14; padding: 12px; border-radius: 8px; font-family: 'Courier New', Courier, monospace; font-size: 0.8rem; word-break: break-all; }
    </style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-gray-800"><i class="bi bi-database-fill-gear me-2"></i>Master Skema Biaya</h4>
            <button class="btn btn-primary btn-sm shadow" data-toggle="modal" data-target="#schemeModal" onclick="resetSchemeForm()">
                <i class="bi bi-plus-lg mr-1"></i> Tambah Skema
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Table of Saved Schemes --}}
        @if($schemes->count() > 0)
            @foreach($schemes as $scheme)
                @php
                    $formatVal = function($f) {
                        if ($f['unit'] === 'Rp') return 'Rp ' . number_format((float)$f['value'], 0, ',', '.');
                        return $f['value'] . '%';
                    };
                @endphp
                <div class="card scheme-card shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="bi bi-file-earmark-text mr-1"></i>{{ $scheme->name }}
                                    @if($scheme->is_active)
                                        <span class="badge badge-success badge-unit ml-1">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary badge-unit ml-1">Nonaktif</span>
                                    @endif
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1"><strong>Sisi Klien:</strong></small>
                                        @foreach($scheme->client_data ?? [] as $item)
                                            <span class="badge badge-light border mr-1 mb-1" style="font-size: 0.78rem;">
                                                {{ $item['label'] }}: <strong>{{ $formatVal($item) }}</strong>
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1"><strong>Sisi Mitra:</strong></small>
                                        @foreach($scheme->mitra_data ?? [] as $item)
                                            <span class="badge badge-light border mr-1 mb-1" style="font-size: 0.78rem;">
                                                {{ $item['label'] }}: <strong>{{ $formatVal($item) }}</strong>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted"><i class="bi bi-clock mr-1"></i>Dibuat: {{ $scheme->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            <div class="d-flex flex-column ml-3">
                                <button class="btn btn-outline-warning btn-sm mb-1" title="Edit"
                                    onclick="editScheme({{ json_encode($scheme) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('schemes.destroy', $scheme->id) }}" method="POST" onsubmit="return confirm('Yakin hapus skema ini?')">
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
                    <p class="text-muted mt-3 mb-0">Belum ada skema biaya. Klik <strong>Tambah Skema</strong> untuk membuat yang baru.</p>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Add/Edit Scheme --}}
<div class="modal fade" id="schemeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" x-data="schemeHandler()">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="schemeModalTitle">
                    <i class="bi bi-plus-circle mr-1"></i> Tambah Skema Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form :action="formAction" method="POST" id="schemeForm">
                @csrf
                <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Skema Pembayaran</label>
                        <input type="text" name="scheme_name" class="form-control" placeholder="Contoh: Skema Standar 2026" x-model="schemeName" required>
                    </div>

                    <div class="row g-4">
                        {{-- Client Side --}}
                        <div class="col-md-6">
                            <div class="section-header text-primary">Sisi Klien (Client)</div>
                            <template x-for="(field, index) in clientFields" :key="'c'+index">
                                <div class="mb-2">
                                    <label class="form-label small font-weight-bold" x-text="field.label"></label>
                                    <div class="input-group input-group-sm">
                                        <template x-if="field.unit === 'Rp'">
                                            <span class="input-group-text">Rp</span>
                                        </template>
                                        <input type="number" step="any" :name="'client['+index+'][value]'" class="form-control" x-model="field.value">
                                        <template x-if="field.unit === '%'">
                                            <span class="input-group-text">%</span>
                                        </template>
                                        <input type="hidden" :name="'client['+index+'][label]'" :value="field.label">
                                        <input type="hidden" :name="'client['+index+'][unit]'" :value="field.unit">
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removeField('client', index)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Mitra Side --}}
                        <div class="col-md-6">
                            <div class="section-header text-success">Sisi Mitra (Partner)</div>
                            <template x-for="(field, index) in mitraFields" :key="'m'+index">
                                <div class="mb-2">
                                    <label class="form-label small font-weight-bold" x-text="field.label"></label>
                                    <div class="input-group input-group-sm">
                                        <template x-if="field.unit === 'Rp'">
                                            <span class="input-group-text">Rp</span>
                                        </template>
                                        <input type="number" step="any" :name="'mitra['+index+'][value]'" class="form-control" x-model="field.value">
                                        <template x-if="field.unit === '%'">
                                            <span class="input-group-text">%</span>
                                        </template>
                                        <input type="hidden" :name="'mitra['+index+'][label]'" :value="field.label">
                                        <input type="hidden" :name="'mitra['+index+'][unit]'" :value="field.unit">
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removeField('mitra', index)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Add New Field --}}
                    <div class="mt-3 p-3 bg-light rounded border">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small font-weight-bold">Nama Kolom Baru</label>
                                <input type="text" x-model="newField.label" class="form-control form-control-sm" placeholder="Misal: PPh 21">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small font-weight-bold">Target</label>
                                <select x-model="newField.target" class="form-select form-control form-control-sm">
                                    <option value="client">Klien</option>
                                    <option value="mitra">Mitra</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small font-weight-bold">Satuan</label>
                                <select x-model="newField.unit" class="form-select form-control form-control-sm">
                                    <option value="Rp">Rupiah (Rp)</option>
                                    <option value="%">Persen (%)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" @click="addNewField()" class="btn btn-dark btn-sm w-100">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div class="mt-3">
                        <label class="font-weight-bold small text-muted">PREVIEW:</label>
                        <div class="preview-box" x-text="generatePreview()"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-all mr-1"></i> <span x-text="isEdit ? 'Perbarui Skema' : 'Simpan Skema'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function schemeHandler() {
        return {
            schemeName: '',
            clientFields: [],
            mitraFields: [],
            newField: { label: '', target: 'client', unit: 'Rp' },
            isEdit: false,
            formAction: '{{ route("schemes.store") }}',

            addNewField() {
                if(this.newField.label === '') return alert('Nama kolom tidak boleh kosong!');
                const data = { label: this.newField.label, unit: this.newField.unit, value: 0 };
                if(this.newField.target === 'client') this.clientFields.push(data);
                else this.mitraFields.push(data);
                this.newField.label = '';
            },
            removeField(type, index) {
                if(type === 'client') this.clientFields.splice(index, 1);
                else this.mitraFields.splice(index, 1);
            },
            formatVal(f) {
                if (f.unit === 'Rp') {
                    const num = parseFloat(f.value) || 0;
                    return 'Rp ' + num.toLocaleString('id-ID');
                }
                return f.value + '%';
            },
            generatePreview() {
                const cText = this.clientFields.map(f => `${f.label.toLowerCase()} ${this.formatVal(f)}`).join(', ');
                const mText = this.mitraFields.map(f => `${f.label.toLowerCase()} ${this.formatVal(f)}`).join(', ');
                return `klien (${cText}) | mitra (${mText})`;
            },
            // Methods called from outside Alpine
            reset() {
                this.schemeName = '';
                this.clientFields = [];
                this.mitraFields = [];
                this.isEdit = false;
                this.formAction = '{{ route("schemes.store") }}';
            },
            loadScheme(scheme) {
                this.schemeName = scheme.name;
                this.clientFields = JSON.parse(JSON.stringify(scheme.client_data || []));
                this.mitraFields = JSON.parse(JSON.stringify(scheme.mitra_data || []));
                this.isEdit = true;
                this.formAction = '{{ url("schemes") }}/' + scheme.id + '/update';
            }
        }
    }

    function resetSchemeForm() {
        document.getElementById('schemeModalTitle').innerHTML = '<i class="bi bi-plus-circle mr-1"></i> Tambah Skema Baru';
        // Wait for Alpine to be ready
        setTimeout(() => {
            const el = document.querySelector('#schemeModal [x-data]');
            if (el && el.__x) el.__x.$data.reset();
            else if (el && el._x_dataStack) el._x_dataStack[0].reset();
        }, 100);
    }

    function editScheme(scheme) {
        document.getElementById('schemeModalTitle').innerHTML = '<i class="bi bi-pencil-square mr-1"></i> Edit Skema: ' + scheme.name;
        // Wait for Alpine to be ready
        setTimeout(() => {
            const el = document.querySelector('#schemeModal [x-data]');
            if (el && el.__x) el.__x.$data.loadScheme(scheme);
            else if (el && el._x_dataStack) el._x_dataStack[0].loadScheme(scheme);
        }, 100);
        $('#schemeModal').modal('show');
    }
</script>
@endpush
