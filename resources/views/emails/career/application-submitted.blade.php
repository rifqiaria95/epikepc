<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Lamaran terkirim</title></head>
<body style="font-family:Arial,sans-serif;background:#f8fafc;padding:24px;color:#202C38;">
<table width="600" style="margin:0 auto;background:#fff;padding:32px;">
    <tr><td>
        <h1 style="color:#253C74;">EPIKEPC</h1>
        <p>Halo {{ $candidateName }},</p>
        <p>Lamaran Anda untuk <strong>{{ $vacancyTitle }}</strong> telah terkirim.</p>
        <p>Nomor referensi: <strong>{{ $reference }}</strong></p>
        <p>Pantau status melalui tautan aman berikut:</p>
        <p><a href="{{ $statusUrl }}" style="display:inline-block;background:#253C74;color:#fff;padding:12px 20px;text-decoration:none;">Lihat status lamaran</a></p>
        <p>Hanya kandidat yang lolos seleksi awal yang akan dihubungi.</p>
    </td></tr>
</table>
</body>
</html>
