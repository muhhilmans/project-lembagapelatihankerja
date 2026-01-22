<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Notifikasi Realtime</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="font-family: sans-serif; padding: 20px; background: #f4f6f8;">
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
        <h1 style="margin-top: 0;">📡 Radar Notifikasi</h1>
        <p>Sedang menyamar sebagai User ID:</p>
        <code style="background: #eee; padding: 5px; border-radius: 4px; display: block; word-break: break-all;">{{ $userId }}</code>

        <br>
        <p>Status Koneksi: <strong id="status" style="color: orange">Menghubungkan...</strong></p>

        <hr>
        <h3>Log Pesan Masuk:</h3>
        <ul id="log" style="background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 6px; height: 300px; overflow-y: auto; font-family: monospace; list-style: none;">
            </ul>
    </div>

    <script type="module">

        setTimeout(() => {
            const userId = "{{ $userId }}";

            console.log("Mulai mendengarkan channel: notifications." + userId);

            // Subscribe ke Private Channel
            window.Echo.private(`notifications.${userId}`)
                .listen('.notification.dispatched', (e) => {
                    console.log("NOTIFIKASI DITERIMA:", e);

                    const li = document.createElement('li');
                    li.style.marginBottom = "10px";
                    li.style.borderBottom = "1px solid #333";
                    li.style.paddingBottom = "5px";

                    let color = '#00ff00'; // Success (Hijau)
                    if(e.type === 'error') color = '#ff4444'; // Merah
                    if(e.type === 'info') color = '#44ccff'; // Biru

                    li.innerHTML = `
                        <span style="color: ${color}">[${e.type ? e.type.toUpperCase() : 'INFO'}]</span>
                        ${e.message}
                        <br><small style="color: #777">Target: ${e.targetUserId}</small>
                    `;

                    const log = document.getElementById('log');
                    log.prepend(li);

                    // Mainkan suara 'ting' (opsional)
                    // new Audio('https://www.soundjay.com/buttons/beep-01a.mp3').play().catch(e=>{});
                })
                .error((error) => {
                    console.error("Gagal connect:", error);
                    document.getElementById('status').innerText = "Gagal Terhubung (Cek Console)";
                    document.getElementById('status').style.color = "red";
                });

            // Update status visual jika koneksi berhasil
            window.Echo.connector.pusher.connection.bind('connected', () => {
                 document.getElementById('status').innerText = "Terhubung & Siap Menerima Sinyal";
                 document.getElementById('status').style.color = "green";
            });

        }, 1000);
    </script>
</body>
</html>
