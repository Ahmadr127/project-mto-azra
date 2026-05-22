<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku MCU - {{ $mcuRegistration->patient->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #2c3e50; }
        .header p { margin: 2px 0; font-size: 11px; color: #7f8c8d; }
        
        .section-title { font-size: 14px; font-weight: bold; background-color: #ecf0f1; padding: 5px 10px; margin-top: 15px; margin-bottom: 10px; border-left: 4px solid #2980b9; }
        
        .info-table { w-full; margin-bottom: 15px; width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px; vertical-align: top; }
        .info-table .label { width: 120px; font-weight: bold; color: #555; }
        .info-table .colon { width: 10px; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th { background-color: #bdc3c7; padding: 6px; text-align: left; border: 1px solid #95a5a6; font-size: 11px; }
        .data-table td { padding: 6px; border: 1px solid #95a5a6; font-size: 11px; }
        .text-center { text-align: center; }
        
        .footer { position: fixed; bottom: 0px; width: 100%; text-align: center; font-size: 10px; color: #7f8c8d; border-top: 1px solid #ddd; padding-top: 5px; }
        
        .signature-box { float: right; width: 200px; text-align: center; margin-top: 30px; }
        .signature-line { margin-top: 60px; border-bottom: 1px solid #333; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HASIL PEMERIKSAAN MEDICAL CHECK-UP</h1>
        <p>KLINIK UTAMA MTO-RS | Jl. Kesehatan No. 123, Kota Medis</p>
        <p>Telp: (021) 1234567 | Email: info@mto-rs.com</p>
    </div>

    <!-- Identitas -->
    <table class="info-table">
        <tr>
            <td class="label">No. Rekam Medis</td><td class="colon">:</td><td>{{ $mcuRegistration->patient->patient_code }}</td>
            <td class="label">Tanggal MCU</td><td class="colon">:</td><td>{{ date('d M Y', strtotime($mcuRegistration->registration_date)) }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pasien</td><td class="colon">:</td><td><strong>{{ $mcuRegistration->patient->name }}</strong></td>
            <td class="label">Paket MCU</td><td class="colon">:</td><td>{{ $mcuRegistration->package->name }}</td>
        </tr>
        <tr>
            <td class="label">Kelamin / Umur</td><td class="colon">:</td><td>{{ $mcuRegistration->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }} / {{ $mcuRegistration->patient->age }} Thn</td>
            <td class="label">Perusahaan</td><td class="colon">:</td><td>{{ $mcuRegistration->patient->company ?? '-' }}</td>
        </tr>
    </table>

    <!-- Hasil Anamnesis -->
    @if($mcuRegistration->examAnamneses->count() > 0)
    @php
        $anmExams = $mcuRegistration->examAnamneses->keyBy(function($exam) {
            return trim($exam->anamnesis->code);
        });
        
        $getAnmVal = function($code, $default = '-') use ($anmExams) {
            $exam = $anmExams->get($code);
            return $exam ? ($exam->result ?: $default) : $default;
        };
    @endphp
    <div class="section-title">Anamnesis & Riwayat Kesehatan</div>
    
    <!-- Keluhan & Koreksi Dokter -->
    <table class="data-table" style="margin-bottom: 10px;">
        <tr>
            <td width="30%" style="font-weight: bold; background-color: #f8f9fa; font-size: 10px; padding: 4px 6px;">Keluhan saat Bekerja (Dalam Pekerjaan)</td>
            <td width="70%" style="font-size: 10px; padding: 4px 6px;">{{ $getAnmVal('ANM-KP-001', 'Tidak ada keluhan') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f8f9fa; font-size: 10px; padding: 4px 6px;">Keluhan di Luar Pekerjaan</td>
            <td style="font-size: 10px; padding: 4px 6px;">{{ $getAnmVal('ANM-KP-002', 'Tidak ada keluhan') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f8f9fa; color: #0d5c4e; font-size: 10px; padding: 4px 6px;">Koreksi / Catatan Dokter Pemeriksa</td>
            <td style="font-weight: bold; color: #0d5c4e; font-size: 10px; padding: 4px 6px;">{{ $getAnmVal('ANM-KD-001', 'Tidak ada') }}</td>
        </tr>
    </table>

    <table width="100%" style="margin-bottom: 10px; border-collapse: collapse;">
        <tr>
            <!-- Kolom Kiri: Riwayat Penyakit Keluarga -->
            <td width="48%" valign="top" style="padding: 0; padding-right: 10px;">
                <div style="font-weight: bold; font-size: 10px; margin-bottom: 4px; color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 2px;">
                    1. RIWAYAT PENYAKIT KELUARGA
                </div>
                <table class="data-table" style="font-size: 9px; margin-bottom: 0;">
                    <tr><td width="60%" style="padding: 3px 5px;">Hipertensi (Darah Tinggi)</td><td width="40%" class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RK-002') !== 'Tidak' && $getAnmVal('ANM-RK-002') !== '-' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RK-002', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Kencing Manis (Diabetes)</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RK-003') !== 'Tidak' && $getAnmVal('ANM-RK-003') !== '-' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RK-003', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Jantung & Pembuluh Darah</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RK-004') !== 'Tidak' && $getAnmVal('ANM-RK-004') !== '-' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RK-004', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Kanker</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RK-005') !== 'Tidak' && $getAnmVal('ANM-RK-005') !== '-' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RK-005', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Asthma</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RK-006') !== 'Tidak' && $getAnmVal('ANM-RK-006') !== '-' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RK-006', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Penyakit Lainnya</td><td style="padding: 3px 5px;">{{ $getAnmVal('ANM-RK-007', '-') }}</td></tr>
                    @if($getAnmVal('ANM-RK-008') !== '-' || $getAnmVal('ANM-RK-009') !== '-')
                    <tr><td style="padding: 3px 5px;">Lainnya (Ayah / Ibu)</td><td style="padding: 3px 5px;">A: {{ $getAnmVal('ANM-RK-008', '-') }} / I: {{ $getAnmVal('ANM-RK-009', '-') }}</td></tr>
                    @endif
                    <tr><td style="padding: 3px 5px;">Meninggal Dunia?</td><td style="padding: 3px 5px;">{{ $getAnmVal('ANM-RK-010', 'Tidak') }}</td></tr>
                    @if($getAnmVal('ANM-RK-011') !== '-' || $getAnmVal('ANM-RK-012') !== '-')
                    <tr style="font-size: 8px; color: #555;"><td style="padding: 2px 4px;">Penyebab Meninggal</td><td style="padding: 2px 4px;">A: {{ $getAnmVal('ANM-RK-011', '-') }} / I: {{ $getAnmVal('ANM-RK-012', '-') }}</td></tr>
                    @endif
                </table>
            </td>
            
            <!-- Kolom Kanan: Riwayat Penyakit Pribadi -->
            <td width="48%" valign="top" style="padding: 0; padding-left: 10px;">
                <div style="font-weight: bold; font-size: 10px; margin-bottom: 4px; color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 2px;">
                    2. RIWAYAT PENYAKIT PRIBADI
                </div>
                <table class="data-table" style="font-size: 9px; margin-bottom: 0;">
                    <tr><td width="60%" style="padding: 3px 5px;">Hipertensi (Darah Tinggi)</td><td width="40%" class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RP-002') === 'Ya' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RP-002', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Kencing Manis (Diabetes)</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RP-003') === 'Ya' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RP-003', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Jantung & Pembuluh Darah</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RP-004') === 'Ya' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RP-004', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Kanker</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RP-005') === 'Ya' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RP-005', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Asthma</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RP-006') === 'Ya' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RP-006', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Penyakit Paru-paru</td><td class="text-center" style="padding: 3px 5px; {{ $getAnmVal('ANM-RP-011') === 'Ya' ? 'font-weight:bold; color:#b25e00;' : '' }}">{{ $getAnmVal('ANM-RP-011', 'Tidak') }}</td></tr>
                    <tr><td style="padding: 3px 5px;">Ambeien / Alergi</td><td style="padding: 3px 5px;">Amb: {{ $getAnmVal('ANM-RP-008', 'Tidak') }} / Alerg: {{ $getAnmVal('ANM-RP-009', 'Tidak') }}</td></tr>
                    @if($getAnmVal('ANM-RP-010') !== '-')
                    <tr style="font-size: 8px; color: #555;"><td style="padding: 2px 4px;">Alergi Lainnya</td><td style="padding: 2px 4px;">{{ $getAnmVal('ANM-RP-010', '-') }}</td></tr>
                    @endif
                    @if($mcuRegistration->patient->gender == 'P')
                    <tr><td style="padding: 3px 5px;">Riwayat Haid</td><td style="padding: 3px 5px;">{{ $getAnmVal('ANM-RP-012', '-') }} {{ $getAnmVal('ANM-RP-013') !== '-' ? '(' . $getAnmVal('ANM-RP-013') . ')' : '' }}</td></tr>
                    @endif
                    <tr><td style="padding: 3px 5px;">Penyakit Lain / Tambahan</td><td style="padding: 3px 5px;">{{ $getAnmVal('ANM-RP-007', '-') }} / {{ $getAnmVal('ANM-RP-014', '-') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="data-table" style="font-size: 9px; margin-bottom: 15px;">
        <thead>
            <tr style="background-color: #ecf0f1;">
                <th width="50%" style="padding: 4px 6px;">3. RIWAYAT MEDIS LAINNYA</th>
                <th width="50%" style="padding: 4px 6px;">4. KEBIASAAN & GAYA HIDUP</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td valign="top" style="padding: 5px 8px;">
                    <div style="margin-bottom: 3px;"><strong>Riwayat Rawat Inap RS:</strong></div>
                    <ul style="margin: 0; padding-left: 12px; font-weight: bold; color: #333;">
                        @if($getAnmVal('ANM-RI-001') !== '-' || $getAnmVal('ANM-RI-002') !== '-' || $getAnmVal('ANM-RI-003') !== '-')
                            @if($getAnmVal('ANM-RI-001') !== '-') <li>{{ $getAnmVal('ANM-RI-001') }}</li> @endif
                            @if($getAnmVal('ANM-RI-002') !== '-') <li>{{ $getAnmVal('ANM-RI-002') }}</li> @endif
                            @if($getAnmVal('ANM-RI-003') !== '-') <li>{{ $getAnmVal('ANM-RI-003') }}</li> @endif
                        @else
                            <li style="list-style-type: none; padding-left: 0; font-weight: normal; color: #7f8c8d; font-style: italic;">Tidak ada riwayat rawat inap</li>
                        @endif
                    </ul>
                    <div style="margin-top: 5px; border-top: 1px dashed #ddd; padding-top: 4px;">
                        <strong>Riwayat Operasi:</strong> {{ $getAnmVal('ANM-OP-001', 'Tidak ada') }}
                    </div>
                    <div style="margin-top: 3px;">
                        <strong>Penggunaan Obat Rutin:</strong> {{ $getAnmVal('ANM-OB-001', 'Tidak ada') }}
                    </div>
                </td>
                <td valign="top" style="padding: 5px 8px;">
                    <div><strong>Kebiasaan Merokok:</strong> {{ $getAnmVal('ANM-KB-001', '-') }}</div>
                    <div style="margin-top: 4px;"><strong>Olahraga Mingguan:</strong> {{ $getAnmVal('ANM-KB-002', '-') }}</div>
                    <div style="margin-top: 4px;"><strong>Pola Makan & Minum:</strong> {{ $getAnmVal('ANM-KB-003', '-') }}</div>
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Hasil Fisik -->
    @if($mcuRegistration->physicalExamResult)
    @php $phys = $mcuRegistration->physicalExamResult; @endphp
    <div class="section-title">Pemeriksaan Fisik</div>
    <table class="data-table">
        <tr>
            <td width="20%" style="font-weight:bold;">Tinggi Badan</td><td width="30%">{{ $phys->vital_signs['tinggi_badan'] ?? '-' }} cm</td>
            <td width="20%" style="font-weight:bold;">Tensi Darah</td><td width="30%">{{ $phys->vital_signs['tensi_avg_sys'] ?? '-' }}/{{ $phys->vital_signs['tensi_avg_dia'] ?? '-' }} mmHg</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Berat Badan</td><td>{{ $phys->vital_signs['berat_badan'] ?? '-' }} kg</td>
            <td style="font-weight:bold;">Nadi</td><td>{{ $phys->vital_signs['nadi'] ?? '-' }} x/mnt</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">BMI</td><td>{{ $phys->vital_signs['bmi'] ?? '-' }} ({{ $phys->vital_signs['bmi_kesimpulan'] ?? '-' }})</td>
            <td style="font-weight:bold;">Buta Warna</td><td>{{ $phys->eye['buta_warna'] ?? '-' }}</td>
        </tr>
    </table>
    @endif

    <!-- Hasil Lab -->
    @if($mcuRegistration->examLabs->count() > 0)
    @php
        $categoryOrder = [
            'Hematologi Lengkap',
            'Urine',
            'Urine Sedimen',
            'Fungsi Liver',
            'Lipid Profil',
            'Fungsi Ginjal',
            'Glukosa Darah',
            'Imunologi',
            'Lainnya'
        ];
        $groupedExams = $mcuRegistration->examLabs->groupBy(function($exam) {
            return $exam->lab->category ?? 'Lainnya';
        });
    @endphp
    <div class="section-title">Hasil Laboratorium</div>
    
    @foreach($categoryOrder as $categoryName)
        @php
            $exams = $groupedExams->get($categoryName);
        @endphp
        @if($exams && $exams->count() > 0)
            @php
                $normalExams = $exams->filter(function($exam) {
                    return !str_ends_with($exam->lab->code, '-KES');
                })->sortBy('lab.display_order');

                $summaryExam = $exams->first(function($exam) {
                    return str_ends_with($exam->lab->code, '-KES');
                });
            @endphp
            
            <div style="font-weight: bold; font-size: 11px; margin-top: 12px; margin-bottom: 4px; color: #2c3e50; border-bottom: 1px solid #ecf0f1; padding-bottom: 2px;">
                {{ strtoupper($categoryName) }}
            </div>
            
            @if($normalExams->count() > 0)
            <table class="data-table" style="margin-bottom: 5px;">
                <thead>
                    <tr>
                        <th width="40%">Pemeriksaan</th>
                        <th width="20%" class="text-center">Hasil</th>
                        <th width="25%" class="text-center">Nilai Rujukan</th>
                        <th width="15%">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($normalExams as $examLab)
                    <tr>
                        <td>{{ $examLab->lab->name }}</td>
                        <td class="text-center" style="{{ $examLab->is_abnormal ? 'color:red; font-weight:bold;' : '' }}">
                            {{ $examLab->result_value ?? '-' }}
                        </td>
                        <td class="text-center" style="font-family: monospace;">{{ $examLab->normal_value ?? $examLab->lab->normal_value }}</td>
                        <td>{{ $examLab->lab->unit }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            @if($summaryExam)
            <div style="background-color: #f2faf9; padding: 6px; border: 1px solid #d1ede8; border-radius: 3px; font-size: 10px; margin-bottom: 10px; color: #0d5c4e;">
                <strong style="text-transform: uppercase; font-size: 9px; color: #0f766e;">Rekapitulasi / Kesimpulan:</strong> 
                <span style="font-weight: bold;">{{ $summaryExam->result_value ?: 'Dalam Batas Normal' }}</span>
            </div>
            @endif
        @endif
    @endforeach
    @endif

    <!-- Hasil Penunjang Medis Non-Lab -->
    @if($mcuRegistration->examRadiologies->count() > 0)
    <div class="section-title">Penunjang Medis Non-Lab</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="35%">Jenis Pemeriksaan</th>
                <th width="20%" class="text-center">Status</th>
                <th width="45%">Hasil / Kesimpulan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mcuRegistration->examRadiologies->sortBy('radiology.display_order') as $examRad)
            <tr>
                <td valign="top"><strong>{{ $examRad->radiology->name }}</strong></td>
                <td valign="top" class="text-center" style="font-weight: bold; color: {{ $examRad->is_normal ? '#0d5c4e' : '#b25e00' }};">
                    {{ $examRad->is_normal ? 'Normal' : 'Abnormal' }}
                </td>
                <td valign="top">{!! nl2br(e($examRad->findings ?? '-')) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Hasil Konsultasi & Tindakan Dokter -->
    @if($mcuRegistration->examMedicalActions->count() > 0)
    <div class="section-title">Konsultasi & Tindakan Medis Dokter</div>
    <table class="data-table" style="font-size: 10px;">
        <thead>
            <tr style="background-color: #ecf0f1;">
                <th width="35%" style="padding: 5px 8px;">Jenis Pemeriksaan / Konsultasi</th>
                <th width="65%" style="padding: 5px 8px;">Hasil / Tindakan Klinis / Catatan Dokter</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mcuRegistration->examMedicalActions as $exam)
            <tr>
                <td valign="top" style="padding: 5px 8px;">
                    <strong>{{ $exam->medicalAction->name }}</strong><br>
                    <span style="font-size: 8px; color: #7f8c8d;">Kode: {{ $exam->medicalAction->code }}</span>
                </td>
                <td valign="top" style="font-weight: bold; color: #2c3e50; font-size: 10px; padding: 5px 8px;">
                    {!! nl2br(e($exam->result ?: 'Dalam Batas Normal')) !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Kesimpulan -->
    <div class="section-title">Kesimpulan Akhir & Saran Medis</div>
    <table class="info-table" style="border: 1px solid #bdc3c7; padding: 10px; background-color: #f9f9f9;">
        <tr>
            <td style="font-weight:bold; width: 100px;">Kesimpulan</td>
            <td style="width: 10px;">:</td>
            <td>{!! nl2br(e($mcuRegistration->conclusion ?? '-')) !!}</td>
        </tr>
        <tr><td colspan="3"><hr style="border-top:1px dashed #ccc; margin:5px 0;"></td></tr>
        <tr>
            <td style="font-weight:bold;">Saran / Anjuran</td>
            <td>:</td>
            <td>{!! nl2br(e($mcuRegistration->recommendation ?? '-')) !!}</td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="clearfix">
        <div class="signature-box">
            <p style="margin: 0;">Dokter Pemeriksa,</p>
            <div class="signature-line"></div>
            <p style="margin-top: 5px; font-weight:bold;">dr. {{ $mcuRegistration->resumeDoctor->name ?? '.......................' }}</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini diterbitkan secara elektronik oleh Sistem Informasi MCU MTO-RS dan sah tanpa stempel basah.
        <br>
        Dicetak pada: {{ date('d M Y H:i') }}
    </div>
</body>
</html>
