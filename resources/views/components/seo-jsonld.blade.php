@php
    $org = config('seo.organization');
    $provider = config('seo.provider');
    $program = config('seo.program');
    $canonicalUrl = $canonical ?? url()->current();
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollegeOrUniversity',
    'name' => $provider['name'] ?? 'Universitas PGRI Wiranegara',
    'url' => $provider['url'] ?? 'https://uniwara.ac.id',
    'logo' => $org['logo'] ?? asset('assets/images/logo/logo.webp'),
    'sameAs' => $org['sameAs'] ?? ['https://uniwara.ac.id'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'EducationalOccupationalProgram',
    'name' => $program['name'] ?? 'S1 Ilmu Komputer',
    'alternateName' => $org['alternateName'] ?? 'S1 Ilmu Komputer UNIWARA',
    'description' => $seoDesc ?? config('seo.description'),
    'provider' => [
        '@type' => $provider['type'] ?? 'CollegeOrUniversity',
        'name' => $provider['name'] ?? 'Universitas PGRI Wiranegara',
        'sameAs' => $provider['url'] ?? 'https://uniwara.ac.id',
    ],
    'url' => $canonicalUrl,
    'credentialCategory' => $program['credentialCategory'] ?? 'Bachelor Degree',
    'timeToComplete' => $program['timeToComplete'] ?? 'P8Y',
    'inLanguage' => $program['inLanguage'] ?? 'id-ID',
    'programType' => $program['programType'] ?? 'Ilmu Komputer',
    'offers' => [
        '@type' => 'Offer',
        'category' => 'Biaya Kuliah',
        'availability' => 'https://schema.org/InStock',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
