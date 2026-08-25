<?php

namespace App\Services\Public;

use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Concerns\SanitizesHtml;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Lecturer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PublicDataService
{
    use SanitizesHtml;

    public function __construct(private readonly ImageService $images) {}

    /**
     * Escape LIKE wildcards to prevent injection of % and _.
     */
    public function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }

    public function cachedExpertises(): Collection
    {
        $raw = Cache::get('public:expertises');

        if ($raw instanceof Collection) {
            return $raw;
        }

        if (is_array($raw)) {
            return collect($raw);
        }

        if ($raw !== null) {
            Cache::forget('public:expertises');
        }

        $data = Cache::remember('public:expertises', 3600, fn (): array => Lecturer::query()
            ->where('status', Lecturer::STATUS_ACTIVE)
            ->whereNotNull('expertise')
            ->where('expertise', '!=', '')
            ->distinct()
            ->orderBy('expertise')
            ->pluck('expertise')->toArray());

        return collect($data);
    }

    public function lecturersData(?string $search = null, ?string $expertise = null): LengthAwarePaginator
    {
        return Lecturer::query()
            ->select(['id', 'name', 'nidn', 'position', 'expertise', 'education', 'email', 'photo', 'bio', 'sort_order'])
            ->where('status', Lecturer::STATUS_ACTIVE)
            ->when($search !== null, function ($q) use ($search): void {
                $escaped = $this->escapeLike($search);
                $like = '%'.$escaped.'%';
                $q->where(function ($qq) use ($like): void {
                    $qq->where('name', 'like', $like)
                        ->orWhere('nidn', 'like', $like)
                        ->orWhere('expertise', 'like', $like)
                        ->orWhere('position', 'like', $like);
                });
            })
            ->when($expertise !== null, fn ($q) => $q->where('expertise', $expertise))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(12)->withQueryString()
            ->through(fn (Lecturer $lecturer): array => [
                'sort_order' => $lecturer->sort_order,
                'name' => $lecturer->name,
                'nidn' => $lecturer->nidn,
                'position' => $lecturer->position ?? '',
                'expertise' => $lecturer->expertise ?? '',
                'education' => $lecturer->education ?? '',
                'email' => $lecturer->email ?? '',
                'image' => $this->images->url($lecturer->photo),
                'image_srcset' => $this->images->srcSet($lecturer->photo),
                'description' => $lecturer->bio ?? '',
            ]);
    }

    public function cachedCategories(): Collection
    {
        $raw = Cache::get('public:activity_categories');

        if ($raw instanceof Collection) {
            return $raw;
        }

        if (is_array($raw)) {
            return collect($raw);
        }

        if ($raw !== null) {
            Cache::forget('public:activity_categories');
        }

        $data = Cache::remember('public:activity_categories', 3600, fn (): array => Activity::query()
            ->where('status', Activity::STATUS_PUBLISHED)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')->toArray());

        return collect($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function findActivity(string $slug): array
    {
        $activity = Activity::query()
            ->where('slug', $slug)
            ->where('status', Activity::STATUS_PUBLISHED)
            ->first();

        abort_unless($activity, 404);

        return $this->mapActivity($activity, withContent: true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function otherActivitiesData(string $excludeSlug): array
    {
        return Activity::query()
            ->select(['id', 'title', 'slug', 'excerpt', 'activity_date', 'location', 'category', 'image'])
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
     * @return LengthAwarePaginator|array<int, array<string, mixed>>
     */
    public function activitiesData(int $limit = 0, ?string $search = null, ?string $category = null): LengthAwarePaginator|array
    {
        $query = Activity::query()
            ->select(['id', 'title', 'slug', 'excerpt', 'activity_date', 'location', 'category', 'image'])
            ->where('status', Activity::STATUS_PUBLISHED)
            ->when($search !== null, function ($q) use ($search): void {
                $escaped = $this->escapeLike($search);
                $like = '%'.$escaped.'%';
                $q->where(function ($qq) use ($like): void {
                    $qq->where('title', 'like', $like)
                        ->orWhere('location', 'like', $like)
                        ->orWhere('category', 'like', $like);
                });
            })
            ->when($category !== null, fn ($q) => $q->where('category', $category))
            ->orderByDesc('activity_date')
            ->orderByDesc('id');

        if ($limit > 0) {
            return $query->limit($limit)->get()
                ->map(fn (Activity $activity): array => $this->mapActivity($activity))
                ->all();
        }

        return $query->paginate(12)->withQueryString()->through(fn (Activity $activity): array => $this->mapActivity($activity));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseActivityContent(?string $content): array
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
            ['type' => 'paragraph', 'text' => str_replace(['&nbsp;', "\u{00A0}"], ' ', (string) $content)],
        ];
    }

    /**
     * Map an activity to its view payload.
     *
     * `content` is only selected on the detail page, so `content_blocks` is
     * opt-in — card listings never render it and must not pay the parse cost.
     *
     * @return array<string, mixed>
     */
    public function mapActivity(Activity $activity, bool $withContent = false): array
    {
        return [
            'title' => $activity->title,
            'slug' => $activity->slug,
            'excerpt' => $activity->excerpt ?? '',
            'date' => $activity->activity_date?->format('Y-m-d') ?? '',
            'date_label' => $activity->activity_date?->translatedFormat('d F Y') ?? '',
            'location' => $activity->location ?? '',
            'category' => $activity->category ?? '',
            'image' => $this->images->url($activity->image),
            'image_srcset' => $this->images->srcSet($activity->image),
            'content_blocks' => $withContent
                ? $this->parseActivityContent($activity->content)
                : [],
        ];
    }

    /**
     * @return LengthAwarePaginator|array<int, array<string, string>>
     */
    public function alumniData(int $limit = 0, ?string $search = null, ?string $jobPosition = null): LengthAwarePaginator|array
    {
        $query = Alumni::query()
            ->select(['id', 'name', 'batch_year', 'graduation_year', 'job_position', 'company', 'testimonial', 'photo'])
            ->where('status', Alumni::STATUS_ACTIVE)
            ->when($search !== null, function ($q) use ($search): void {
                $escaped = $this->escapeLike($search);
                $like = '%'.$escaped.'%';
                $q->where(function ($qq) use ($like): void {
                    $qq->where('name', 'like', $like)
                        ->orWhere('job_position', 'like', $like)
                        ->orWhere('company', 'like', $like);
                });
            })
            ->when($jobPosition !== null, fn ($q) => $q->where('job_position', $jobPosition))
            ->orderByDesc('batch_year')
            ->orderBy('name')
            ->orderBy('id');

        if ($limit > 0) {
            return $query->limit($limit)->get()
                ->map(fn (Alumni $alumni): array => [
                    'name' => $alumni->name,
                    'role' => trim(implode(' ', array_filter([$alumni->job_position, 'di', $alumni->company]))),
                    'batch_year' => (string) ($alumni->batch_year ?? ''),
                    'graduation_year' => (string) ($alumni->graduation_year ?? ''),
                    'job_position' => $alumni->job_position ?? '',
                    'company' => $alumni->company ?? '',
                    'quote' => $alumni->testimonial ?? '',
                    'image' => $this->images->url($alumni->photo),
                    'image_srcset' => $this->images->srcSet($alumni->photo),
                ])
                ->all();
        }

        return $query->paginate(12)->withQueryString()
            ->through(fn (Alumni $alumni): array => [
                'name' => $alumni->name,
                'role' => trim(implode(' ', array_filter([$alumni->job_position, 'di', $alumni->company]))),
                'batch_year' => (string) ($alumni->batch_year ?? ''),
                'graduation_year' => (string) ($alumni->graduation_year ?? ''),
                'job_position' => $alumni->job_position ?? '',
                'company' => $alumni->company ?? '',
                'quote' => $alumni->testimonial ?? '',
                'image' => $this->images->url($alumni->photo),
                'image_srcset' => $this->images->srcSet($alumni->photo),
            ]);
    }

    public function cachedDocumentCategories(): Collection
    {
        $raw = Cache::get('public:document_categories');

        if ($raw instanceof Collection) {
            return $raw;
        }

        if (is_array($raw)) {
            return collect($raw);
        }

        if ($raw !== null) {
            Cache::forget('public:document_categories');
        }

        $data = Cache::remember('public:document_categories', 3600, fn (): array => DocumentCategory::query()
            ->whereHas('documents', fn ($q) => $q->where('status', Document::STATUS_PUBLISHED))
            ->orderBy('name')
            ->pluck('name')->toArray());

        return collect($data);
    }

    public function cachedJobPositions(): Collection
    {
        $raw = Cache::get('public:job_positions');

        if ($raw instanceof Collection) {
            return $raw;
        }

        if (is_array($raw)) {
            return collect($raw);
        }

        if ($raw !== null) {
            Cache::forget('public:job_positions');
        }

        $data = Cache::remember('public:job_positions', 3600, fn (): array => Alumni::query()
            ->where('status', Alumni::STATUS_ACTIVE)
            ->whereNotNull('job_position')
            ->where('job_position', '!=', '')
            ->distinct()
            ->orderBy('job_position')
            ->pluck('job_position')->toArray());

        return collect($data);
    }

    public function documentsData(?string $search = null, ?string $category = null): LengthAwarePaginator
    {
        return Document::query()
            ->with('documentCategory:id,name')
            ->select(['id', 'document_category_id', 'title', 'description', 'file_type', 'file_size', 'uploaded_at'])
            ->where('status', Document::STATUS_PUBLISHED)
            ->when($search !== null, function ($q) use ($search): void {
                $escaped = $this->escapeLike($search);
                $like = '%'.$escaped.'%';
                $q->where(function ($qq) use ($like): void {
                    $qq->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->when($category !== null, fn ($q) => $q->whereHas('documentCategory', fn ($qq) => $qq->where('name', $category)))
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->paginate(12)->withQueryString()
            ->through(fn (Document $document): array => [
                'id' => $document->id,
                'title' => $document->title,
                'category' => $document->documentCategory?->name ?? '',
                'description' => $document->description ?? '',
                'file_type' => $document->fileTypeLabel(),
                'file_size' => $document->formattedFileSize(),
                'updated_at' => $document->uploaded_at?->format('Y-m-d') ?? '',
                'updated_label' => $document->uploaded_at?->translatedFormat('d F Y') ?? '',
            ]);
    }
}
