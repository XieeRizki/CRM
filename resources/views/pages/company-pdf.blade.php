<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Rumah Sakit - {{ $company->company_name }}</title>
    <style>
        @page {
            margin: 40px 40px 70px 40px;
        }
        body {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
        }
        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 8px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }
        table.letterhead-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1e3a8a;
            margin-bottom: 20px;
        }
        table.letterhead-table td {
            padding: 0 0 10px 0;
            vertical-align: middle;
        }
        .letterhead-logo-cell {
            width: 55px;
        }
        .letterhead-logo-cell img {
            max-width: 55px;
            max-height: 55px;
        }
        .letterhead-text-cell {
            padding-left: 12px;
        }
        .letterhead-text-cell p {
            margin: 0;
            font-size: 10px;
            color: #4b5563;
            font-style: italic;
        }
        .letterhead-meta-cell {
            text-align: right;
            font-size: 9px;
            color: #4b5563;
        }
        .doc-title {
            text-align: center;
            margin: 0 0 20px 0;
        }
        .doc-title h1 {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 10px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        .subject-box {
            display: table;
            width: 100%;
            border: 1px solid #1e3a8a;
            background-color: #f8fafc;
            padding: 10px 14px;
            margin-bottom: 22px;
        }
        .subject-logo {
            display: table-cell;
            width: 55px;
            vertical-align: middle;
        }
        .subject-logo img {
            max-width: 50px;
            max-height: 50px;
        }
        .subject-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }
        .subject-info h3 {
            margin: 0;
            font-size: 14px;
            color: #1e3a8a;
        }
        .subject-info p {
            margin: 2px 0 0 0;
            font-size: 9.5px;
            color: #4b5563;
        }
        .section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            background-color: #1e3a8a;
            padding: 5px 10px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }
        table.info-table td {
            padding: 6px 10px;
            vertical-align: top;
            border-bottom: 1px solid #e5e7eb;
        }
        table.info-table tr:last-child td {
            border-bottom: none;
        }
        table.info-table td.label {
            width: 160px;
            font-weight: bold;
            color: #374151;
            background-color: #f9fafb;
        }
        table.info-table td.colon {
            width: 12px;
            background-color: #f9fafb;
        }
        table.pic-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }
        table.pic-table th {
            background-color: #1e3a8a;
            color: #fff;
            padding: 7px 10px;
            text-align: left;
            font-size: 9.5px;
            text-transform: uppercase;
        }
        table.pic-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table.pic-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
        }
        .badge-active {
            background-color: #15803d;
        }
        .badge-inactive {
            background-color: #b91c1c;
        }
        .signature-block {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        .signature-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            font-size: 10px;
            color: #374151;
        }
        .signature-space {
            height: 55px;
        }
        .confidential {
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            font-style: italic;
            margin-top: 25px;
        }
    </style>
</head>
<body>

    <footer>
        Dokumen ini dihasilkan secara otomatis oleh Sistem CRM Inotal pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.<br>
        Dokumen ini bersifat rahasia dan hanya digunakan untuk kepentingan internal.
    </footer>

    <table class="letterhead-table">
        <tr>
            <td class="letterhead-logo-cell">
                @if($inotalLogoPath)
                    <img src="{{ $inotalLogoPath }}" alt="Inotal">
                @endif
            </td>
            
            <td class="letterhead-meta-cell">
                No. Dokumen: {{ $documentNumber }}<br>
                Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>

    <div class="doc-title">
        <h1>Laporan Informasi Rumah Sakit</h1>
        <p>Dokumen Profil Mitra Fasilitas Kesehatan</p>
    </div>

    <div class="subject-box">
        <div class="subject-logo">
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="Logo RS">
            @endif
        </div>
        <div class="subject-info">
            <h3>{{ $company->company_name }}</h3>
            <p>{{ $company->companyType->type_name ?? '-' }} &middot; Tier {{ $company->tier ?? '-' }}</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">I. Informasi Umum</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Rumah Sakit</td>
                <td class="colon">:</td>
                <td>{{ $company->company_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tipe Fasilitas</td>
                <td class="colon">:</td>
                <td>{{ $company->companyType->type_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tier</td>
                <td class="colon">:</td>
                <td>{{ $company->tier ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status Kerja Sama</td>
                <td class="colon">:</td>
                <td>
                    <span class="badge {{ $company->status == 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($company->status ?? 'inactive') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">Deskripsi</td>
                <td class="colon">:</td>
                <td>{{ $company->description ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">II. Alamat</div>
        <table class="info-table">
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td class="colon">:</td>
                <td>{{ $company->address ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Provinsi</td>
                <td class="colon">:</td>
                <td>{{ $company->province->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kabupaten/Kota</td>
                <td class="colon">:</td>
                <td>{{ $company->regency->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kecamatan</td>
                <td class="colon">:</td>
                <td>{{ $company->district->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Desa/Kelurahan</td>
                <td class="colon">:</td>
                <td>{{ $company->village->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">III. Kontak & Media Resmi</div>
        <table class="info-table">
            <tr>
                <td class="label">Telepon</td>
                <td class="colon">:</td>
                <td>{{ $company->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="colon">:</td>
                <td>{{ $company->email ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Website</td>
                <td class="colon">:</td>
                <td>{{ $company->website ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">LinkedIn</td>
                <td class="colon">:</td>
                <td>{{ $company->linkedin ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Instagram</td>
                <td class="colon">:</td>
                <td>{{ $company->instagram ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">IV. Penanggung Jawab (Person In Charge)</div>
        @if($pics->count() > 0)
            <table class="pic-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Telepon</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pics as $pic)
                        <tr>
                            <td>{{ $pic->pic_name ?? '-' }}</td>
                            <td>{{ $pic->position ?? '-' }}</td>
                            <td>{{ $pic->phone ?? '-' }}</td>
                            <td>{{ $pic->email ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="info-table">
                <tr><td>Belum ada data penanggung jawab yang tercatat.</td></tr>
            </table>
        @endif
    </div>

    <div class="signature-block">
        <div class="signature-cell">
            <div>Dibuat oleh,</div>
            <div class="signature-space"></div>
            <div>( Tim Inotal )</div>
        </div>
        <div class="signature-cell">
            <div>Diketahui oleh,</div>
            <div class="signature-space"></div>
            <div>( Penanggung Jawab RS )</div>
        </div>
    </div>

    <div class="confidential">
        &mdash; Dokumen ini bersifat rahasia dan tidak untuk disebarluaskan tanpa izin &mdash;
    </div>

</body>
</html>