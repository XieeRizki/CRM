<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap RAB</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9px; color: #111; }
        h1 { font-size: 15px; margin: 0 0 2px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #555; padding: 4px 5px; word-wrap: break-word; }
        th { background: #e8e8f5; }
        .r { text-align: right; }
        .c { text-align: center; }
        tfoot td { font-weight: bold; background: #f3f3f3; }
    </style>
</head>
<body>
    <h1>REKAP RAB (Rencana Anggaran Biaya)</h1>
    <div class="meta">
        Dicetak: {{ $dicetak->format('d/m/Y H:i') }}
        &nbsp;|&nbsp; {{ $keterangan }}
        &nbsp;|&nbsp; Total data: {{ $rabs->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:8%">ID Transaksi</th>
                <th style="width:12%">ID RAB</th>
                <th style="width:12%">Pemohon</th>
                <th style="width:12%">Tujuan</th>
                <th style="width:17%">Keperluan</th>
                <th style="width:9%">Berangkat</th>
                <th style="width:9%">Pulang</th>
                <th style="width:10%">Jumlah</th>
                <th style="width:7%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rabs as $r)
                <tr>
                    <td class="c">{{ $loop->iteration }}</td>
                    <td>{{ $r->kode_transaksi }}</td>
                    <td>{{ $r->kode_rab }}</td>
                    <td>{{ $r->nama_pemohon }}</td>
                    <td>{{ $r->lokasi_tujuan }}</td>
                    <td>{{ $r->keperluan }}</td>
                    <td class="c">{{ optional($r->tanggal_berangkat)->format('d/m/Y') }}</td>
                    <td class="c">{{ optional($r->tanggal_pulang)->format('d/m/Y') }}</td>
                    <td class="r">Rp {{ number_format((float) $r->total_anggaran, 0, ',', '.') }}</td>
                    <td class="c">{{ $r->status }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="r">Total</td>
                <td class="r">Rp {{ number_format((float) $rabs->sum('total_anggaran'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>