{{-- Modal Gaji - Salary Information --}}
<div class="modal fade" id="salaryModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="salaryModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="salaryModalLabel-{{ $data->id }}">
                    <i class="fas fa-money-bill-wave mr-2"></i>Informasi Gaji
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-warning mx-auto d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-money-bill-wave fa-2x text-white"></i>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user mr-2"></i>{{ $data->servant->name ?? 'Pembantu' }}
                        </h6>

                        @if ($data->salary != null)
                            @php
                                $salary = $data->salary;
                                $service = $salary * 0.025;
                                $gaji = $salary - $service;
                            @endphp

                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Gaji Kotor</td>
                                    <td class="font-weight-bold text-right">Rp. {{ number_format($salary, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Potongan Layanan (2.5%)</td>
                                    <td class="text-right text-danger">- Rp. {{ number_format($service, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="font-weight-bold">Gaji Bersih</td>
                                    <td class="font-weight-bold text-right text-success">Rp.
                                        {{ number_format($gaji, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            @hasrole('majikan')
                                <div class="mt-3 text-center">
                                    <button class="btn btn-sm btn-outline-warning" type="button" data-toggle="collapse"
                                        data-target="#editSalaryCollapse-{{ $data->id }}" aria-expanded="false"
                                        aria-controls="editSalaryCollapse-{{ $data->id }}">
                                        Edit Gaji
                                    </button>
                                </div>
                                <div class="collapse mt-3" id="editSalaryCollapse-{{ $data->id }}">
                                    <form action="{{ route('applicant.salary.update', $data->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="salary">Update Gaji (Rp)</label>
                                            <input type="number" class="form-control" name="salary"
                                                value="{{ $data->salary }}" required min="0">
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-block">Simpan Perubahan</button>
                                    </form>
                                </div>
                            @endhasrole
                        @else
                            @hasrole('majikan')
                                <div class="text-center py-2">
                                    <p class="text-muted mb-3">Silakan atur gaji untuk pelamar ini.</p>
                                    <form action="{{ route('applicant.salary.update', $data->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="salary" class="sr-only">Nominal Gaji</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" class="form-control" name="salary"
                                                    placeholder="Masukkan Nominal Gaji" required min="0">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-block mt-3">Simpan Gaji</button>
                                    </form>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Informasi gaji belum tersedia</p>
                                    <small class="text-muted">Gaji akan ditampilkan setelah ditetapkan oleh majikan</small>
                                </div>
                            @endhasrole
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
