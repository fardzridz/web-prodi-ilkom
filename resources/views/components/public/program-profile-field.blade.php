@props([
    'value' => '',
    'delta' => null,
])

@php
    $raw = (string) $value;
    $isDelta = $delta !== null && $delta !== '';

    if (! $isDelta && $raw !== '') {
        $allowedTags = '<p><br><strong><em><u><s>'
            .'<ul><ol><li><a>'
            .'<h1><h2><h3><h4><h5><h6>'
            .'<blockquote><pre><code><hr>'
            .'<table><thead><tbody><tr><th><td>'
            .'<div><span>';
        $safe = strip_tags($raw, $allowedTags);

        $safe = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $safe) ?? $safe;

        $safe = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $safe) ?? $safe;

        $safe = preg_replace_callback(
            '/\s(href|src|xlink:href)\s*=\s*(["\'])(.*?)\2/i',
            function (array $matches): string {
                $url = trim(html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

                if (in_array($scheme, ['javascript', 'data', 'vbscript'], true)) {
                    return '';
                }

                return ' '.$matches[1].'='.$matches[2].$matches[3].$matches[2];
            },
            $safe
        ) ?? $safe;

        $raw = $safe;
    }
@endphp

<div
    {{ $attributes->except(['delta', 'data-quill-delta'])->merge(['class' => 'rich-text']) }}
    @if($isDelta)
        data-quill-delta="{{ base64_encode((string) $delta) }}"
    @endif
>
    @unless($isDelta)
        {!! $raw !!}
    @endunless
</div>
