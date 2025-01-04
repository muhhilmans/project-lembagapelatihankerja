@extends('auth.layout.main', ['title' => 'Syarat dan Ketentuan'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('select-register') }}" class="btn btn-secondary mb-3 shadow"><i
                class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <h3 class="card-title">Syarat dan Ketentuan Pendaftaran Mitra ART di Aplikasi Sipembantu.com</h3>
                    <div class="card-body text-left">
                        <ol class="font-weight-bold text-justify">
                            <li>Persyaratan Umum:
                                <ol type="a" class="font-weight-normal">
                                    <li>Calon Mitra ART adalah warga negara Indonesia dengan usia minimal 18
                                        tahun.</li>
                                    <li>Memiliki KTP atau identitas resmi lainnya yang masih berlaku.</li>
                                    <li>Bersedia mengikuti proses pendaftaran dan wawancara yang dilakukan melalui aplikasi
                                        atau video call.</li>
                                    <li>Memiliki kemampuan dan pengalaman kerja di bidang asisten rumah tangga
                                        (ART) atau babysitter (jika ada).</li>
                                </ol>
                            </li>
                            <li>Proses Pendaftaran:
                                <ol type="a" class="font-weight-normal">
                                    <li>Calon Mitra ART dapat mendaftar secara online melalui link pendaftaran yang
                                        disediakan oleh Aplikasi Sipembantu.</li>
                                    <li>Pendaftaran akan dibantu oleh Tim Sipembantu untuk mempermudah proses.</li>
                                    <li>Calon Mitra ART wajib menyertakan dokumen pendukung seperti:
                                        <ul type="disc">
                                            <li>KTP atau identitas resmi lainnya.</li>
                                            <li>Pas foto terbaru.</li>
                                            <li>Surat keterangan pengalaman kerja (jika ada).</li>
                                        </ul>
                                    </li>
                                </ol>
                            </li>
                            <li>Fasilitas yang Diterima Mitra ART:
                                <ol type="a" class="font-weight-normal">
                                    <li>Buku rekening tabungan/ATM Bank yang dibuatkan secara gratis.</li>
                                    <li>Kartu BPJS Ketenagakerjaan untuk perlindungan kecelakaan kerja dan
                                        perlindungan kematian.</li>
                                    <li>Gaji bulanan yang ditransfer langsung ke rekening Mitra ART setiap tanggal 1.</li>
                                    <li>Proses antar ke rumah majikan yang difasilitasi oleh pihak Aplikasi
                                        Sipembantu.</li>
                                </ol>
                            </li>
                            <li>Hak dan Kewajiban Mitra ART:
                                <ol type="a">
                                    <li>Hak Mitra ART:
                                        <ul type="disc" class="font-weight-normal">
                                            <li>Gaji bersih diterima setiap bulan setelah dikurangi biaya jasa aplikasi
                                                sebesar 2,5%.</li>
                                            <li>Tidak terikat kontrak dengan majikan atau Aplikasi Sipembantu.</li>
                                            <li>Bebas untuk keluar atau mencari majikan baru jika merasa tidak cocok dengan
                                                majikan saat ini.</li>
                                        </ul>
                                    </li>
                                    <li>Kewajiban Mitra ART:
                                        <ul type="disc" class="font-weight-normal">
                                            <li>Mematuhi semua peraturan yang telah disepakati dengan Aplikasi
                                                Sipembantu dan majikan.</li>
                                            <li>Mengembalikan ongkos antar kepada majikan jika berhenti bekerja
                                                lebih awal.</li>
                                            <li>Hadir ke kantor Aplikasi Sipembantu untuk verifikasi ulang sebelum diantar
                                                ke rumah majikan</li>
                                        </ul>
                                    </li>
                                </ol>
                            </li>
                            <li>Biaya Jasa Aplikasi:
                                <ol type="a" class="font-weight-normal">
                                    <li>Pihak Aplikasi Sipembantu membebankan biaya jasa sebesar 2,5% dari gaji
                                        bulanan Mitra ART.</li>
                                    <li>Contoh perhitungan: Jika gaji Rp. 2.500.000, maka gaji bersih yang diterima Mitra
                                        ART adalah Rp. 2.437.500.</li>
                                </ol>
                            </li>
                            <li>Kebijakan Legalitas dan Keamanan:
                                <ol type="a" class="font-weight-normal">
                                    <li>Identitas asli calon Mitra ART tidak akan ditahan oleh pihak Aplikasi
                                        Sipembantu.</li>
                                    <li>Pihak majikan diperbolehkan menahan identitas asli Mitra ART (seperti KTP) sebagai
                                        jaminan selama masa kerja.</li>
                                    <li>Aplikasi Sipembantu dikelola oleh PT Purwa Sentosa Gemilang yang memiliki
                                        izin resmi dan menjamin legalitas.</li>
                                </ol>
                            </li>
                            <li>Sanksi dan Pelanggaran:
                                <ol type="a" class="font-weight-normal">
                                    <li>Jika Mitra ART melanggar peraturan yang disepakati, Aplikasi Sipembantu
                                        berhak memberikan sanksi sesuai ketentuan.</li>
                                    <li>Apabila terjadi tindak kejahatan oleh Mitra ART, pihak majikan dapat
                                        melaporkannya ke pihak berwajib.</li>
                                </ol>
                            </li>
                            <li>Ketentuan Tambahan:
                                <ol type="a" class="font-weight-normal">
                                    <li>Pihak Mitra ART wajib menghadiri wawancara dan proses verifikasi dengan
                                        tepat waktu.</li>
                                    <li>Segala aktivitas pendaftaran dan kerja sama akan diawasi oleh pihak Aplikasi
                                        Sipembantu untuk menjamin kepercayaan dan keamanan semua pihak.</li>
                                </ol>
                            </li>
                        </ol>
                    </div>
                    <div class="card-footer text-right">
                        <div class="form-check text-left">
                            <input class="form-check-input" type="checkbox" value="" id="checkTerm" required>
                            <label class="form-check-label" for="checkTerm">
                                Dengan mencentang ini, calon Mitra ART dianggap telah membaca,memahami, dan menyetujui semua
                                syarat dan ketentuan ini.
                            </label>
                        </div>

                        <a href="{{ route('register-servant') }}" class="btn btn-primary mt-3 shadow disabled"
                            id="nextButton">Selanjutnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        document.getElementById('checkTerm').addEventListener('change', function() {
            const nextButton = document.getElementById('nextButton');
            const isDisabled = !this.checked;

            nextButton.classList.toggle('disabled', isDisabled);
            nextButton.setAttribute('tabindex', isDisabled ? '-1' : '0');
            nextButton.setAttribute('aria-disabled', isDisabled.toString());
        });
    </script>
@endpush
