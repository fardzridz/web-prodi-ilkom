{{--
    Ikon situs lengkap (RealFaviconGenerator) untuk browser, iOS, dan Android.
    Favicon unggahan pengelola tetap diprioritaskan bila tersedia.
--}}
@if(($site ?? null)?->favicon)
    <link rel="icon" href="{{ asset('storage/'.$site->favicon) }}">
@else
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
@endif
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<meta name="apple-mobile-web-app-title" content="Ilmu Komputer UNIWARA">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<meta name="theme-color" content="#1B365D">
