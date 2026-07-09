<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Pengelola - Program Studi Ilmu Komputer</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-shell">
    <main class="login-panel">
        <a class="brand login-brand" href="{{ route('home') }}" aria-label="Kembali ke situs">
            <span class="brand-mark">IK</span>
            <span>
                <strong>Ilmu Komputer</strong>
                <small>Program Studi</small>
            </span>
        </a>

        <section class="content-panel">
            <p class="eyebrow">Admin CMS</p>
            <h1>Masuk Pengelola</h1>
            <p>Gunakan akun pengelola untuk memperbarui konten Program Studi Ilmu Komputer.</p>

            <form class="login-form" action="#" method="post">
                @csrf
                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="admin@prodi.test" autocomplete="email">
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" value="password" autocomplete="current-password">
                </label>
                <label class="checkbox-row">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                <a class="button button-primary" href="{{ route('admin.dashboard') }}">Masuk</a>
                <a class="button button-outline" href="{{ route('home') }}">Kembali ke situs</a>
            </form>
        </section>
    </main>
</body>
</html>
