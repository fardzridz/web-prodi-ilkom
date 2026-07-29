<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk Pengelola - Program Studi Ilmu Komputer</title>
    <link rel="stylesheet" href="{{ asset('css/app/app.css') }}?v={{ filemtime(public_path('css/app/app.css')) }}">
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

        <section class="content-panel login-card" aria-labelledby="login-heading">
            <div class="login-heading">
                <p class="eyebrow">Situs Prodi</p>
                <h1 id="login-heading">Masuk Pengelola</h1>
                <p>Gunakan akun pengelola untuk memperbarui konten Program Studi Ilmu Komputer.</p>
            </div>

            @if (session('status'))
            <div class="login-alert login-alert-success" role="status">
                {{ session('status') }}
            </div>
            @endif

            <form class="login-form" action="{{ route('admin.login.store') }}" method="post" novalidate>
                @csrf
                <label>
                    <span>Alamat surel</span>
                    <input
                        class="@error('email') is-invalid @enderror"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        inputmode="email"
                        required
                        autofocus
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                    <small id="email-error" class="field-error">{{ $message }}</small>
                    @enderror
                </label>
                <label>
                    <span>Kata sandi</span>
                    <input
                        class="@error('password') is-invalid @enderror"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    @error('password')
                    <small id="password-error" class="field-error">{{ $message }}</small>
                    @enderror
                </label>
                <label class="checkbox-row">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Ingat perangkat ini</span>
                </label>
                <button class="button button-login" type="submit">Masuk</button>
                <a class="button button-outline" href="{{ route('home') }}">Kembali ke situs</a>
            </form>
        </section>
    </main>
</body>

</html>