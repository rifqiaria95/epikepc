<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Pembaruan status</title></head>
<body style="font-family:Arial,sans-serif;background:#f8fafc;padding:24px;color:#202C38;">
<table width="600" style="margin:0 auto;background:#fff;padding:32px;">
    <tr><td>
        <h1 style="color:#253C74;">EPIKEPC</h1>
        <p>Halo {{ $candidateName }},</p>
        <p>Ada pembaruan untuk lamaran {{ $reference }} ({{ $vacancyTitle }}).</p>
        <p>Status: <strong>{{ $publicStatus }}</strong></p>
        @if ($publicMessage)
            <p>{{ $publicMessage }}</p>
        @endif
    </td></tr>
</table>
</body>
</html>
