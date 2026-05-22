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
    <div class="section-title">Hasil Laboratorium</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Pemeriksaan</th>
                <th class="text-center">Hasil</th>
                <th class="text-center">Nilai Rujukan</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mcuRegistration->examLabs as $examLab)
            <tr>
                <td>{{ $examLab->lab->name }}</td>
                <td class="text-center" style="{{ $examLab->is_abnormal ? 'color:red; font-weight:bold;' : '' }}">{{ $examLab->result ?? '-' }}</td>
                <td class="text-center">{{ $examLab->normal_value ?? $examLab->lab->normal_value }}</td>
                <td>{{ $examLab->lab->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Hasil Radiologi -->
    @if($mcuRegistration->examRadiologies->count() > 0)
    <div class="section-title">Hasil Radiologi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="30%">Pemeriksaan</th>
                <th width="70%">Bacaan / Kesimpulan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mcuRegistration->examRadiologies as $examRad)
            <tr>
                <td valign="top">{{ $examRad->radiology->name }}</td>
                <td>{!! nl2br(e($examRad->result ?? '-')) !!}</td>
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
