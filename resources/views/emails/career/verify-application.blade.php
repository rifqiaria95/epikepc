<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Verifikasi lamaran</title></head>
<body style="font-family:Arial,sans-serif;background:#f8fafc;padding:24px;color:#202C38;">
<table width="600" style="margin:0 auto;background:#fff;padding:32px;">
    <tr><td>
        <h1 style="color:#253C74;">EPIKEPC</h1>
        <p>Halo {{ $candidateName }},</p>
        <p>Kami menerima lamaran Anda untuk posisi <strong>{{ $vacancyTitle }}</strong>. Klik tombol berikut untuk memverifikasi email dan mengirim lamaran.</p>
        <p><a href="{{ $url }}" style="display:inline-block;background:#253C74;color:#fff;padding:12px 20px;text-decoration:none;">Verifikasi email</a></p>
        <p>Atau salin tautan ini: {{ $url }}</p>
        <p>Tautan berlaku {{ $hours }} jam dan hanya dapat digunakan sekali.</p>
    </td></tr>
</table>
</body>
</html>
