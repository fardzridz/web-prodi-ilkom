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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicController extends Controller
{
    use SanitizesHtml;

    public function __construct()
    {
        view()->share('site', SiteSetting::query()->first() ?? new SiteSetting([
            'site_name' => 'Program Studi Ilmu Komputer',
            'university_name' => 'Universitas PGRI Wiranegara',
            'faculty_name' => 'Fakultas Teknologi dan Sains',
            'footer_text' => '© '.date('Y').' Program Studi Ilmu Komputer.',
            'journal_url' => 'https://ejurnal.uniwara.ac.id',
        ]));

        view()->share('contactInfo', Contact::query()->first() ?? new Contact([
            'address' => 'Jl. Ki Hajar Dewantara No. 27-29, Pasuruan, Jawa Timur',
            'email' => 'univ.pgriwiranegara@gmail.com',
            'phone' => '0821-4155-4377',
            'instagram' => 'https://instagram.com/uniwara',
            'youtube' => 'https://youtube.com/@uniwara',
            'facebook' => 'https://facebook.com/uniwara',
        ]));
    }

    public function home(): View
    {
        $homeSection = HomeSection::query()
            ->select([
                'hero_title',
                'hero_subtitle',
                'hero_slides',
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
            'activities' => $this->activitiesData(3),
            'alumni' => $this->alumniData(4),
        ]);
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
        ]);
    }

    public function activities(): View
    {
        return view('public.activities.index', [
            'activities' => $this->activitiesData(),
        ]);
    }

    public function activityDetail(string $slug): View
    {
        return view('public.activities.show', [
            'activity' => $this->findActivity($slug),
        ]);
    }

    public function journalRedirect(): RedirectResponse
    {
        $journalUrl = SiteSetting::query()->value('journal_url');

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

        return $storage->response(
            $document->file,
            $document->slug.'.'.$document->file_type,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }

    public function alumni(): View
    {
        return view('public.alumni', [
            'alumni' => $this->alumniData(),
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
                ['type' => 'html', 'html' => $this->sanitizeHtml((string) $content, ['p', 'br', 'strong', 'b', 'em', 'i', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'a', 'span'])],
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
                'icon' => 'fa-solid fa-user-graduate',
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
                'icon' => 'fa-solid fa-briefcase',
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
                'file_icon' => match (strtoupper($document->file_type)) {
                    'PDF' => 'fa-file',
                    'DOCX', 'DOC' => 'fa-file-word',
                    'XLSX', 'XLS', 'CSV' => 'fa-file-excel',
                    'PPTX', 'PPT' => 'fa-file-powerpoint',
                    'ZIP', 'RAR' => 'fa-file-zipper',
                    default => 'fa-file',
                },
                'file_size' => $document->formattedFileSize(),
                'updated_at' => $document->uploaded_at?->format('Y-m-d') ?? '',
                'updated_label' => $document->uploaded_at?->translatedFormat('d F Y') ?? '',
                'file' => $document->file,
                'icon' => match (strtoupper($document->file_type)) {
                    'PDF' => 'fa-file-pdf',
                    'DOCX', 'DOC' => 'fa-file-word',
                    'XLSX', 'XLS', 'CSV' => 'fa-file-excel',
                    'PPTX', 'PPT' => 'fa-file-powerpoint',
                    'ZIP', 'RAR' => 'fa-file-zipper',
                    default => 'fa-file-lines',
                },
            ])
            ->all();
    }
}
