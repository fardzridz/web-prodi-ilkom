<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\ContactMessageRequest;
use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Concerns\SanitizesHtml;
use App\Models\Contact;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\HomeSection;
use App\Models\Lecturer;
use App\Models\Message;
use App\Models\Page;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicController extends Controller
{
    use SanitizesHtml;

    public function __construct()
    {
        try {
            $site = Cache::rememberForever('site_setting', fn () => SiteSetting::query()->first()?->toArray());
            $site = is_array($site) ? new SiteSetting($site) : $this->defaultSite();
        } catch (\Throwable) {
            $site = $this->defaultSite();
        }

        view()->share('site', $site);

        try {
            $contact = Cache::rememberForever('contact_info', fn () => Contact::query()->first()?->toArray());
            $contact = is_array($contact) ? new Contact($contact) : $this->defaultContact();
        } catch (\Throwable) {
            $contact = $this->defaultContact();
        }

        view()->share('contactInfo', $contact);
    }

    private function defaultSite(): SiteSetting
    {
        return new SiteSetting([
            'site_name' => 'Program Studi Ilmu Komputer',
            'university_name' => 'Universitas PGRI Wiranegara',
            'faculty_name' => 'Fakultas Teknologi dan Sains',
            'footer_text' => '© '.date('Y').' Program Studi Ilmu Komputer.',
            'journal_url' => 'https://ejurnal.uniwara.ac.id',
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

    public function home(): View
    {
        $homeSection = HomeSection::query()
            ->select([
                'hero_title',
                'hero_subtitle',
                'hero_slides',
                'advantages',
                'cta_text',
                'cta_link',
                'welcome_title',
                'welcome_description',
            ])
            ->first() ?? $this->defaultHomeSection();

        return view('public.home', [
            'homeSection' => $homeSection,
            'heroSlides' => $this->heroSlidesForPublic($homeSection),
            'heroCtaUrl' => $this->resolvePublicLink($homeSection->cta_link, route('profile')),
            'advantageSection' => $this->advantageSectionData($homeSection),
            'activities' => $this->activitiesData(3),
            'alumni' => $this->alumniData(4),
            'programProfile' => ProgramProfile::query()->first() ?? new ProgramProfile,
        ]);
    }

    /**
     * @return array{heading: string, items: array<int, array{order: int, title: string, description: string, image_url: string, image_alt: string}>}
     */
    private function advantageSectionData(HomeSection $homeSection): array
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
                        ? asset('storage/'.$item['image'])
                        : asset('assets/images/hero/hero-1.jpeg'),
                    'image_alt' => $item['title'],
                ])
                ->all(),
        ];
    }

    public function profile(): View
    {
        return view('public.profile', [
            'programProfile' => ProgramProfile::query()->first() ?? new ProgramProfile,
        ]);
    }

    public function visionMission(): RedirectResponse
    {
        return redirect('/profil#visi-misi-page');
    }

    public function privacyPolicy(): View
    {
        return view('public.kebijakan-privasi', [
            'page' => Page::query()->where('slug', 'kebijakan-privasi')->first()
                ?? new Page(['title' => 'Kebijakan Privasi']),
        ]);
    }

    public function accessibility(): View
    {
        return view('public.aksesibilitas', [
            'page' => Page::query()->where('slug', 'aksesibilitas')->first()
                ?? new Page(['title' => 'Aksesibilitas']),
        ]);
    }

    public function lecturers(): View
    {
        return view('public.lecturers', [
            'lecturers' => $this->lecturersData(),
            'expertises' => Lecturer::query()
                ->where('status', Lecturer::STATUS_ACTIVE)
                ->whereNotNull('expertise')
                ->where('expertise', '!=', '')
                ->distinct()
                ->orderBy('expertise')
                ->pluck('expertise'),
        ]);
    }

    public function activities(): View
    {
        return view('public.activities.index', [
            'activities' => $this->activitiesData(),
            'categories' => Activity::query()
                ->where('status', Activity::STATUS_PUBLISHED)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }

    public function activityDetail(string $slug): View
    {
        return view('public.activities.show', [
            'activity' => $this->findActivity($slug),
            'otherActivities' => $this->otherActivitiesData($slug),
        ]);
    }

    public function journalRedirect(): RedirectResponse
    {
        $site = view()->shared('site');
        $journalUrl = $site?->journal_url ?? 'https://ejurnal.uniwara.ac.id';

        return redirect()->away(trim((string) $journalUrl) !== '' ? $journalUrl : 'https://ejurnal.uniwara.ac.id');
    }

    public function documents(): View
    {
        return view('public.documents', [
            'documents' => $this->documentsData(),
            'documentCategories' => DocumentCategory::query()
                ->whereHas('documents', fn ($q) => $q->where('status', Document::STATUS_PUBLISHED))
                ->orderBy('name')
                ->pluck('name'),
        ]);
    }

    public function documentDownload(Document $document): StreamedResponse
    {
        abort_unless($document->status === Document::STATUS_PUBLISHED, 404);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('local');

        abort_unless($storage->exists($document->file), 404);

        return $storage->download(
            $document->file,
            $document->slug.'.'.$document->file_type,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }

    public function documentView(Document $document): StreamedResponse
    {
        abort_unless($document->status === Document::STATUS_PUBLISHED, 404);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('local');

        abort_unless($storage->exists($document->file), 404);

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $type = strtolower($document->file_type);
        $mime = $mimeTypes[$type] ?? $storage->mimeType($document->file);

        $headers = [
            'Content-Type' => $mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$document->slug.'.'.$document->file_type.'"',
            'X-Content-Type-Options' => 'nosniff',
        ];

        return $storage->response($document->file, $document->slug.'.'.$document->file_type, $headers);
    }

    public function alumni(): View
    {
        return view('public.alumni', [
            'alumni' => $this->alumniData(),
            'jobPositions' => Alumni::query()
                ->where('status', Alumni::STATUS_ACTIVE)
                ->whereNotNull('job_position')
                ->where('job_position', '!=', '')
                ->distinct()
                ->orderBy('job_position')
                ->pluck('job_position'),
        ]);
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function contactStore(ContactMessageRequest $request): RedirectResponse
    {
        Message::query()->create($request->validated());

        return redirect()
            ->route('contact')
            ->with('success', 'Pesan Anda berhasil terkirim. Terima kasih atas masukannya.');
    }

    private function defaultHomeSection(): HomeSection
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

    /**
     * @return list<array{url: string, alt: string, delay_class: string}>
     */
    private function heroSlidesForPublic(HomeSection $homeSection): array
    {
        $slides = Collection::make($homeSection->hero_slides ?? [])
            ->filter(fn (mixed $slide): bool => is_array($slide) && filled($slide['path'] ?? null))
            ->values()
            ->take(5)
            ->map(fn (array $slide, int $index): array => [
                'url' => asset('storage/'.$slide['path']),
                'alt' => (string) ($slide['alt'] ?? ''),
                'delay_class' => 'hero-slide-delay-'.$index,
            ])
            ->all();

        if ($slides === []) {
            $slides = Collection::make([
                'assets/images/hero/hero-1.jpeg',
                'assets/images/hero/hero-2.jpeg',
                'assets/images/hero/hero-3.jpeg',
                'assets/images/hero/hero-4.jpeg',
            ])->values()->map(fn (string $path, int $index): array => [
                'url' => asset($path),
                'alt' => '',
                'delay_class' => 'hero-slide-delay-'.$index,
            ])->all();
        }

        if (count($slides) === 1) {
            $slides[0]['delay_class'] = 'hero-slide-static';
        }

        return $slides;
    }

    private function resolvePublicLink(?string $link, string $fallback): string
    {
        $link = trim((string) $link);

        if ($link === '') {
            return $fallback;
        }

        // Relative app path only (block protocol-relative //evil.com).
        if (str_starts_with($link, '/') && ! str_starts_with($link, '//')) {
            return url($link);
        }

        // Absolute https only — blocks http:, javascript:, data:, etc.
        if (filter_var($link, FILTER_VALIDATE_URL) !== false) {
            $scheme = strtolower((string) parse_url($link, PHP_URL_SCHEME));

            if ($scheme === 'https') {
                return $link;
            }
        }

        return $fallback;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function activitiesData(int $limit = 0): array
    {
        $query = Activity::query()
            ->where('status', Activity::STATUS_PUBLISHED)
            ->orderByDesc('activity_date')
            ->orderByDesc('id');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(fn (Activity $activity): array => $this->mapActivity($activity))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseActivityContent(?string $content): array
    {
        if (blank($content)) {
            return [];
        }

        if (str_starts_with(ltrim((string) $content), '<')) {
            return [
                ['type' => 'html', 'html' => $this->sanitizeHtml((string) $content, ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'span', 'blockquote', 'pre', 'code', 'img', 'figure', 'figcaption'])],
            ];
        }

        return [
            ['type' => 'paragraph', 'text' => $content],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapActivity(Activity $activity): array
    {
        return [
            'title' => $activity->title,
            'slug' => $activity->slug,
            'excerpt' => $activity->excerpt ?? '',
            'date' => $activity->activity_date?->format('Y-m-d') ?? '',
            'date_label' => $activity->activity_date?->translatedFormat('d F Y') ?? '',
            'location' => $activity->location ?? '',
            'category' => $activity->category ?? '',
            'image' => $activity->image ? asset('storage/'.$activity->image) : asset('assets/images/hero/hero-1.jpeg'),
            'content_blocks' => $this->parseActivityContent($activity->content),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function findActivity(string $slug): array
    {
        $activity = Activity::query()
            ->where('slug', $slug)
            ->where('status', Activity::STATUS_PUBLISHED)
            ->first();

        abort_unless($activity, 404);

        return $this->mapActivity($activity);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function otherActivitiesData(string $excludeSlug): array
    {
        return Activity::query()
            ->where('status', Activity::STATUS_PUBLISHED)
            ->where('slug', '!=', $excludeSlug)
            ->orderByDesc('activity_date')
            ->orderByDesc('id')
            ->limit(4)
            ->get()
            ->map(fn (Activity $activity): array => $this->mapActivity($activity))
            ->all();
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function lecturersData(): array
    {
        return Lecturer::query()
            ->where('status', Lecturer::STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->orderBy('id')
            ->get()
            ->map(fn (Lecturer $lecturer): array => [
                'sort_order' => $lecturer->sort_order,
                'name' => $lecturer->name,
                'nidn' => $lecturer->nidn,
                'position' => $lecturer->position ?? '',
                'expertise' => $lecturer->expertise ?? '',
                'education' => $lecturer->education ?? '',
                'email' => $lecturer->email ?? '',
                'image' => $lecturer->photo ? asset('storage/'.$lecturer->photo) : 'assets/images/hero/hero-1.jpeg',
                'description' => $lecturer->bio ?? '',
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function alumniData(int $limit = 0): array
    {
        $query = Alumni::query()
            ->where('status', Alumni::STATUS_ACTIVE)
            ->orderByDesc('batch_year')
            ->orderBy('name')
            ->orderBy('id');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(fn (Alumni $alumni): array => [
                'name' => $alumni->name,
                'role' => trim(implode(' ', array_filter([$alumni->job_position, 'di', $alumni->company]))),
                'batch_year' => (string) ($alumni->batch_year ?? ''),
                'graduation_year' => (string) ($alumni->graduation_year ?? ''),
                'job_position' => $alumni->job_position ?? '',
                'company' => $alumni->company ?? '',
                'quote' => $alumni->testimonial ?? '',
                'image' => $alumni->photo ? asset('storage/'.$alumni->photo) : 'assets/images/hero/hero-1.jpeg',
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function documentsData(): array
    {
        return Document::query()
            ->with('documentCategory')
            ->where('status', Document::STATUS_PUBLISHED)
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Document $document): array => [
                'id' => $document->id,
                'title' => $document->title,
                'category' => $document->documentCategory?->name ?? '',
                'description' => $document->description ?? '',
                'file_type' => $document->fileTypeLabel(),
                'file_size' => $document->formattedFileSize(),
                'updated_at' => $document->uploaded_at?->format('Y-m-d') ?? '',
                'updated_label' => $document->uploaded_at?->translatedFormat('d F Y') ?? '',
                'file' => $document->file,
            ])
            ->all();
    }
}
