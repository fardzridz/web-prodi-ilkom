<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 | Sesi Habis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            background-color: #ececec;
            background-image: url("{{ asset('assets/images/error-bg.gif') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Righteous', cursive;
            color: #29557b;
            -webkit-font-smoothing: antialiased;
        }
        .error { width: 100%; height: 100%; height: 100dvh; display: flex; flex-direction: column; justify-content: space-between; align-items: center; text-align: center; }
        .error__top { width: 100%; padding-top: 20vh; padding-top: 20dvh; }
        .error__bottom { width: 100%; padding-bottom: 14vh; padding-bottom: 14dvh; }
        .error__title { font-size: 10em; line-height: 1; color: #29557b; }
        .error__subtitle { font-size: 2em; margin-top: 0.25em; color: #29557b; }
        .error__description { font-size: 1em; opacity: 0.5; color: #343638; margin-top: 1em; }
        .pushable { display: inline-block; margin-top: 2.5em; background: #961a26; border-radius: 12px; border: none; padding: 0; cursor: pointer; outline-offset: 4px; text-decoration: none; }
        .front { display: block; padding: 12px 42px; border-radius: 12px; font-size: 1.25rem; background: #e91b2f; color: white; transform: translateY(-6px); transition: transform 0.1s ease; }
        .pushable:active .front { transform: translateY(-2px); }
        @media (max-width: 640px) {
            body { background-size: auto 115%; background-position: center 88%; }
            .error__title { font-size: 6em; }
            .error__subtitle { font-size: 1.5em; }
            .error__top { padding-top: 12dvh; }
            .error__bottom { padding-bottom: 10dvh; }
        }
    </style>
</head>
<body>
    <div class="error">
        <div class="error__top">
            <div class="error__title">419</div>
        </div>
        <div class="error__bottom">
            <div class="error__subtitle">Waduh!</div>
            <div class="error__description">Sesi kamu sudah habis. Silakan muat ulang halaman dan coba lagi.</div>
            <a class="pushable" href="{{ route('home') }}">
                <span class="front">Beranda</span>
            </a>
        </div>
    </div>
</body>
</html>
