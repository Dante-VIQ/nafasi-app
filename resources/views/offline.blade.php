{{-- resources/views/offline.blade.php --}}
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline — Nafasi</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: #f0f9ff;
            text-align: center;
            padding: 20px;
        }
        .container { max-width: 400px; }
        h1 { font-size: 2rem; color: #1e40af; }
        p { color: #4b5563; margin: 16px 0; }
        .emergency {
            background: #fee2e2;
            border: 2px solid #ef4444;
            border-radius: 12px;
            padding: 16px;
            margin-top: 24px;
        }
        .emergency a {
            color: #dc2626;
            font-weight: bold;
            font-size: 1.5rem;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📡 You're Offline</h1>
        <p>Nafasi needs internet to find help near you.</p>
        <p>Please check your connection and try again.</p>
        <div class="emergency">
            <p>If this is an emergency:</p>
            <a href="tel:999">📞 Call 999</a>
        </div>
    </div>
</body>
</html>