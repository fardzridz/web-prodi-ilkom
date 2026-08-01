@props([
    'value' => '',
])

@php
    $raw = (string) $value;
    $isDelta = str_starts_with(ltrim($raw), '{');
    $deltaPayload = $isDelta ? base64_encode($raw) : null;
    $passThrough = $attributes->except(['data-quill-delta'])->merge(['class' => 'rich-text-content']);

    if (! $isDelta && $raw !== '') {
        // Match PublicController::sanitizeActivityHtml allowlist for Froala/Quill HTML.
        $allowedTags = '<p><br><strong><b><em><i><ul><ol><li><h2><h3><h4><a><span>';
        $safe = strip_tags($raw, $allowedTags);
        $safe = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $safe) ?? $safe;
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
    {{ $passThrough }}
    @if($isDelta)
        data-quill-delta="{{ $deltaPayload }}"
    @endif
>
    @unless($isDelta)
        {!! $raw !!}
    @endunless
</div>
