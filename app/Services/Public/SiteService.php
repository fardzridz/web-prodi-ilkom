<?php

namespace App\Services\Public;

use App\Models\Contact;
use App\Models\HomeSection;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SiteService
{
    public const DEFAULT_JOURNAL_URL = 'https://ejurnal.uniwara.ac.id';

    /**
     * Columns hydrated into the `site_setting` cache entry. This service is the
     * single writer of that key — reading a narrower set elsewhere would poison
     * the cache for every consumer.
     *
     * @var list<string>
     */
    private const SITE_COLUMNS = [
        'site_name',
        'university_name',
        'faculty_name',
        'logo',
        'favicon',
        'journal_url',
        'registration_url',
        'footer_text',
        'footer_academic_links',
    ];

    /**
     * @var list<string>
     */
    private const CONTACT_COLUMNS = [
        'address',
        'email',
        'phone',
        'instagram',
        'youtube',
        'facebook',
        'map_embed',
    ];

    private ?SiteSetting $site = null;

    private ?Contact $contact = null;

    public function __construct(private readonly ImageService $images) {}

    /**
     * Resolve site-wide settings, memoized per request and cached across requests.
     */
    public function getSiteSetting(): SiteSetting
    {
        if ($this->site instanceof SiteSetting) {
            return $this->site;
        }

        try {
            $cached = Cache::rememberForever('site_setting', fn () => SiteSetting::query()
                ->select(self::SITE_COLUMNS)
                ->first()?->toArray());

            return $this->site = is_array($cached)
                ? new SiteSetting($cached)
                : $this->defaultSite();
        } catch (\Throwable $exception) {
            report($exception);

            return $this->site = $this->defaultSite();
        }
    }

    /**
     * Resolve contact information, memoized per request and cached across requests.
     */
    public function getContact(): Contact
    {
        if ($this->contact instanceof Contact) {
            return $this->contact;
        }

        try {
            $cached = Cache::rememberForever('contact_info', fn () => Contact::query()
                ->select(self::CONTACT_COLUMNS)
                ->first()?->toArray());

            return $this->contact = is_array($cached)
                ? new Contact($cached)
                : $this->defaultContact();
        } catch (\Throwable $exception) {
            report($exception);

            return $this->contact = $this->defaultContact();
        }
    }

    /**
     * Resolve the outbound e-journal URL, falling back to the faculty default.
     */
    public function journalUrl(): string
    {
        $url = trim((string) $this->getSiteSetting()->journal_url);

        return $url !== '' ? $url : self::DEFAULT_JOURNAL_URL;
    }

    public function getHomeSection(): HomeSection
    {
        return HomeSection::query()
            ->select([
                'hero_title',
                'hero_subtitle',
                'hero_slides',
                'advantages',
                'cta_text',
                'cta_link',
                'welcome_title',
                'welcome_description',
                'welcome_image',
            ])
            ->first() ?? $this->defaultHomeSection();
    }

    public function getProgramProfile(): ProgramProfile
    {
        $cached = Cache::rememberForever('program_profile', fn () => ProgramProfile::query()
            ->select(['history', 'description', 'vision', 'mission', 'goals', 'accreditation', 'advantages', 'history_image', 'description_image', 'goals_image', 'advantages_image'])
            ->first()?->toArray());

        if (is_array($cached)) {
            return new ProgramProfile($cached);
        }

        return ProgramProfile::query()->first() ?? new ProgramProfile;
    }

    public function defaultHomeSection(): HomeSection
    {
        return new HomeSection([
            'hero_title' => "Logika diasah.\nKreativitas dikembangkan.\nMasa depan diciptakan.",
            'hero_subtitle' => 'Dunia digital terus berubah, membawa tantangan dan peluang baru di setiap langkahnya. Di Program Studi Ilmu Komputer, mahasiswa belajar membangun solusi teknologi yang berguna bagi masyarakat.',
            'hero_slides' => [],
            'cta_text' => 'Lihat Profil',
            'cta_link' => '/profil',
            'welcome_title' => 'Tentang Ilmu Komputer',
            'welcome_description' => 'Program Studi Ilmu Komputer Universitas PGRI Wiranegara menyiapkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, mampu bekerja sama, serta siap berkontribusi di tingkat nasional maupun internasional.',
        ]);
    }

    private function defaultSite(): SiteSetting
    {
        return new SiteSetting([
            'site_name' => 'Program Studi Ilmu Komputer',
            'university_name' => 'Universitas PGRI Wiranegara',
            'faculty_name' => 'Fakultas Teknologi dan Sains',
            'footer_text' => '© '.date('Y').' Program Studi Ilmu Komputer.',
            'journal_url' => self::DEFAULT_JOURNAL_URL,
            'registration_url' => 'https://admisi.uniwara.ac.id',
        ]);
    }

    private function defaultContact(): Contact
    {
        return new Contact([
            'address' => 'Jl. Ki Hajar Dewantara No. 27-29, Pasuruan, Jawa Timur',
            'email' => 'univ.pgriwiranegara@gmail.com',
            'phone' => '0821-4155-4377',
            'instagram' => 'https://instagram.com/uniwara',
            'youtube' => 'https://youtube.com/@uniwara',
            'facebook' => 'https://facebook.com/uniwara',
        ]);
    }

    /**
     * @return list<array{url: string, alt: string, delay_class: string}>
     */
    public function heroSlidesForPublic(HomeSection $homeSection): array
    {
        $slides = Collection::make($homeSection->hero_slides ?? [])
            ->filter(fn (mixed $slide): bool => is_array($slide) && filled($slide['path'] ?? null))
            ->values()
            ->take(5)
            ->map(fn (array $slide, int $index): array => [
                'url' => asset('storage/'.$slide['path']),
                'alt' => filled($slide['alt'] ?? null) ? (string) $slide['alt'] : self::defaultHeroAlt($index),
                'delay_class' => 'hero-slide-delay-'.$index,
            ])
            ->all();

        if ($slides === []) {
            $slides = Collection::make([
                'assets/images/hero/hero-1.webp',
                'assets/images/hero/hero-2.webp',
                'assets/images/hero/hero-3.webp',
                'assets/images/hero/hero-4.webp',
            ])->values()->map(fn (string $path, int $index): array => [
                'url' => asset($path),
                'alt' => self::defaultHeroAlt($index),
                'delay_class' => 'hero-slide-delay-'.$index,
            ])->all();
        }

        if (count($slides) === 1) {
            $slides[0]['delay_class'] = 'hero-slide-static';
        }

        return $slides;
    }

    /**
     * Descriptive fallback alt so hero images still carry keyword context for SEO.
     */
    private static function defaultHeroAlt(int $index): string
    {
        $alts = [
            'Mahasiswa S1 Ilmu Komputer UNIWARA Pasuruan praktikum di laboratorium komputer',
            'Kegiatan perkuliahan Program Studi S1 Ilmu Komputer UNIWARA Pasuruan',
            'Mahasiswa Ilmu Komputer UNIWARA mengerjakan proyek rekayasa perangkat lunak',
            'Suasana kampus Universitas PGRI Wiranegara Pasuruan',
        ];

        return $alts[$index % count($alts)];
    }

    /**
     * @return array{heading: string, items: array<int, array{order: int, title: string, description: string, image_url: string, image_srcset: string|null, image_alt: string}>}
     */
    public function advantageSectionData(HomeSection $homeSection): array
    {
        $raw = $homeSection->advantages;

        return [
            'heading' => HomeSection::advantageHeading($raw),
            'items' => collect(HomeSection::advantageItems($raw))
                ->map(fn (array $item): array => [
                    'order' => $item['order'],
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'image_url' => $item['image'] !== null
                        ? $this->images->url($item['image'])
                        : asset('assets/images/hero/hero-1.webp'),
                    'image_srcset' => $item['image'] !== null ? $this->images->srcSet($item['image']) : null,
                    'image_alt' => $item['title'],
                ])
                ->all(),
        ];
    }

    public function resolvePublicLink(?string $link, string $fallback): string
    {
        $link = trim((string) $link);

        if ($link === '') {
            return $fallback;
        }

        if (str_starts_with($link, '/') && ! str_starts_with($link, '//')) {
            return url($link);
        }

        if (filter_var($link, FILTER_VALIDATE_URL) !== false) {
            $scheme = strtolower((string) parse_url($link, PHP_URL_SCHEME));

            if ($scheme === 'https') {
                return $link;
            }
        }

        return $fallback;
    }

    public function ctaText(HomeSection $homeSection, string $fallback = 'Jelajahi Prodi'): string
    {
        $text = trim((string) $homeSection->cta_text);

        return $text !== '' ? $text : $fallback;
    }
}
