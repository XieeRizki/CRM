@php
    $rpf = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $tgl = fn ($d) => $d ? $d->format('d/m/Y H:i') : '-';

    // Angka -> kata, contoh: 5100000 -> "lima juta seratus ribu rupiah"
    $terbilang = function ($angka) {
        $n = (int) round(abs((float) $angka));
        if ($n === 0) {
            return 'nol rupiah';
        }

        $s = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        $t = function ($x) use (&$t, $s) {
            if ($x < 12)            return $s[$x];
            if ($x < 20)            return $t($x - 10) . ' belas';
            if ($x < 100)           return $t(intdiv($x, 10)) . ' puluh' . ($x % 10 ? ' ' . $t($x % 10) : '');
            if ($x < 200)           return 'seratus' . ($x - 100 ? ' ' . $t($x - 100) : '');
            if ($x < 1000)          return $t(intdiv($x, 100)) . ' ratus' . ($x % 100 ? ' ' . $t($x % 100) : '');
            if ($x < 2000)          return 'seribu' . ($x - 1000 ? ' ' . $t($x - 1000) : '');
            if ($x < 1000000)       return $t(intdiv($x, 1000)) . ' ribu' . ($x % 1000 ? ' ' . $t($x % 1000) : '');
            if ($x < 1000000000)    return $t(intdiv($x, 1000000)) . ' juta' . ($x % 1000000 ? ' ' . $t($x % 1000000) : '');
            if ($x < 1000000000000) return $t(intdiv($x, 1000000000)) . ' miliar' . ($x % 1000000000 ? ' ' . $t($x % 1000000000) : '');
            return $t(intdiv($x, 1000000000000)) . ' triliun' . ($x % 1000000000000 ? ' ' . $t($x % 1000000000000) : '');
        };

        return trim($t($n)) . ' rupiah';
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pengajuan RAB</title>
    <style>
        @page { margin: 28px 34px; }
        body { margin: 0; font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 15px; text-align: center; margin: 0 0 4px; }
        .sub { text-align: center; color: #555; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        .kv td { padding: 2px 0; vertical-align: top; }
        .kv td.k { width: 150px; color: #444; }
        .sec { font-weight: bold; margin: 14px 0 6px; font-size: 13px; }
        .items th, .items td { border: 1px solid #444; padding: 5px 6px; }
        .items th { background: #e8e8f5; text-align: center; }
        .r { text-align: right; }
        .c { text-align: center; }
        .total td { font-weight: bold; background: #f3f3f3; }
        .sign td { width: 50%; text-align: center; vertical-align: top; padding-top: 6px; }
    </style>
</head>
<body>
@foreach ($rabs as $rab)
    <div @if (!$loop->last) style="page-break-after: always;" @endif>

        <h1>SURAT PENGAJUAN RENCANA ANGGARAN BIAYA (RAB)</h1>
        <div class="sub">Nomor: {{ $rab->kode_rab }} &nbsp;|&nbsp; ID Transaksi: {{ $rab->kode_transaksi }}</div>

        <div class="sec">Data Pemohon</div>
        <table class="kv">
            <tr><td class="k">Nama</td><td>: {{ $rab->nama_pemohon }}</td></tr>
            <tr><td class="k">Jabatan</td><td>: {{ $rab->jabatan }}</td></tr>
            <tr><td class="k">Telepon</td><td>: {{ $rab->telepon }}</td></tr>
            <tr><td class="k">Email</td><td>: {{ $rab->email }}</td></tr>
            <tr><td class="k">Tanggal Pengajuan</td><td>: {{ $tgl($rab->tanggal_pengajuan) }}</td></tr>
            <tr><td class="k">Status</td><td>: {{ $rab->status }}</td></tr>
        </table>

        <div class="sec">Detail Perjalanan</div>
        <table class="kv">
            <tr><td class="k">Lokasi Tujuan</td><td>: {{ $rab->lokasi_tujuan }}</td></tr>
            <tr><td class="k">Keperluan</td><td>: {{ $rab->keperluan }}</td></tr>
            <tr><td class="k">Tanggal Berangkat</td><td>: {{ $tgl($rab->tanggal_berangkat) }}</td></tr>
            <tr><td class="k">Tanggal Pulang</td><td>: {{ $tgl($rab->tanggal_pulang) }}</td></tr>
            <tr><td class="k">Durasi</td><td>: {{ $rab->jumlah_hari }} hari</td></tr>
        </table>

        <div class="sec">Rincian Biaya</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th>Item Biaya</th>
                    <th style="width:12%">Unit</th>
                    <th style="width:9%">Kali/hari</th>
                    <th style="width:8%">Durasi</th>
                    <th style="width:8%">Qty</th>
                    <th style="width:14%">Harga</th>
                    <th style="width:15%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rab->items as $it)
                    <tr>
                        <td class="c">{{ $loop->iteration }}</td>
                        <td>{{ $it->nama_item }}@if ($it->keterangan)<br><span style="color:#666;font-size:10px">{{ $it->keterangan }}</span>@endif</td>
                        <td class="c">{{ $it->jumlah_unit }} {{ $it->satuan_unit }}</td>
                        <td class="c">{{ $it->kali_per_hari }}</td>
                        <td class="c">{{ $it->durasi_hari }}</td>
                        <td class="c">{{ $it->qty }}</td>
                        <td class="r">{{ $rpf($it->harga) }}</td>
                        <td class="r">{{ $rpf($it->jumlah) }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="7" class="r">Total Anggaran</td>
                    <td class="r">{{ $rpf($rab->total_anggaran) }}</td>
                </tr>
            </tbody>
        </table>
        <p style="margin:6px 0 0"><i>Terbilang: {{ ucfirst($terbilang($rab->total_anggaran)) }}</i></p>

        <div class="sec">Pembayaran</div>
        <table class="kv">
            <tr><td class="k">Metode</td><td>: {{ $rab->metode_pembayaran }}</td></tr>
            @if ($rab->metode_pembayaran === 'Split')
                <tr><td class="k">Cash</td><td>: {{ $rpf($rab->nominal_cash) }}</td></tr>
                <tr><td class="k">Transfer</td><td>: {{ $rpf($rab->nominal_transfer) }}</td></tr>
            @endif
            @if ($rab->metode_pembayaran === 'Cash')
                <tr><td class="k">Penerima Cash</td><td>: {{ $rab->penerima_cash }}</td></tr>
            @else
                <tr><td class="k">Bank / E-Wallet</td><td>: {{ $rab->bank_nama }}</td></tr>
                <tr><td class="k">No. Rekening</td><td>: {{ $rab->no_rekening }}</td></tr>
                <tr><td class="k">Atas Nama</td><td>: {{ $rab->nama_pemilik_rekening }}</td></tr>
            @endif
        </table>

        <table class="sign" style="margin-top:28px">
            <tr><td>Pemohon,</td><td>Disetujui oleh,</td></tr>
            <tr><td style="height:64px"></td><td></td></tr>
            <tr>
                <td><b>{{ $rab->nama_pemohon }}</b><br>{{ $rab->jabatan }}</td>
                <td>( ............................................ )</td>
            </tr>
        </table>
    </div>
@endforeach
</body>
</html>