<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Contact;
use App\Models\Document;
use App\Models\HomeSection;
use App\Models\Lecturer;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activityCounts = collect(Cache::remember('dashboard:counts:activity', 300, fn (): array => $this->countsByStatus(Activity::class)->all()));
        $lecturerCounts = collect(Cache::remember('dashboard:counts:lecturer', 300, fn (): array => $this->countsByStatus(Lecturer::class)->all()));
        $documentCounts = collect(Cache::remember('dashboard:counts:document', 300, fn (): array => $this->countsByStatus(Document::class)->all()));
        $alumniCounts = collect(Cache::remember('dashboard:counts:alumni', 300, fn (): array => $this->countsByStatus(Alumni::class)->all()));

        $summaryCards = [
            [
                'key' => 'activities',
                'label' => 'Kegiatan',
                'icon' => 'fa-calendar-days',
                'count' => $activityCounts->sum(),
                'detail' => sprintf(
                    '%d terbit, %d terjadwal',
                    $activityCounts->get(Activity::STATUS_PUBLISHED, 0),
                    $activityCounts->get(Activity::STATUS_SCHEDULED, 0),
                ),
            ],
            [
                'key' => 'lecturers',
                'label' => 'Dosen',
                'icon' => 'fa-chalkboard-user',
                'count' => $lecturerCounts->sum(),
                'detail' => sprintf(
                    '%d aktif, %d nonaktif',
                    $lecturerCounts->get(Lecturer::STATUS_ACTIVE, 0),
                    $lecturerCounts->get(Lecturer::STATUS_INACTIVE, 0),
                ),
            ],
            [
                'key' => 'documents',
                'label' => 'Dokumen',
                'icon' => 'fa-file-lines',
                'count' => $documentCounts->sum(),
                'detail' => sprintf(
                    '%d terbit, %d draf',
                    $documentCounts->get(Document::STATUS_PUBLISHED, 0),
                    $documentCounts->get(Document::STATUS_DRAFT, 0),
                ),
            ],
            [
                'key' => 'alumni',
                'label' => 'Alumni',
                'icon' => 'fa-user-graduate',
                'count' => $alumniCounts->sum(),
                'detail' => sprintf(
                    '%d aktif, %d nonaktif',
                    $alumniCounts->get(Alumni::STATUS_ACTIVE, 0),
                    $alumniCounts->get(Alumni::STATUS_INACTIVE, 0),
                ),
            ],
        ];

        $statusCards = [
            [
                'key' => 'draft',
                'label' => 'Draf',
                'count' => $activityCounts->get(Activity::STATUS_DRAFT, 0)
                    + $documentCounts->get(Document::STATUS_DRAFT, 0),
                'tone' => 'draft',
            ],
            [
                'key' => 'scheduled',
                'label' => 'Terjadwal',
                'count' => $activityCounts->get(Activity::STATUS_SCHEDULED, 0),
                'tone' => 'scheduled',
            ],
            [
                'key' => 'published',
                'label' => 'Terbit',
                'count' => $activityCounts->get(Activity::STATUS_PUBLISHED, 0)
                    + $documentCounts->get(Document::STATUS_PUBLISHED, 0),
                'tone' => 'published',
            ],
            [
                'key' => 'active',
                'label' => 'Aktif',
                'count' => $lecturerCounts->get(Lecturer::STATUS_ACTIVE, 0)
                    + $alumniCounts->get(Alumni::STATUS_ACTIVE, 0),
                'tone' => 'active',
            ],
        ];

        return view('admin.dashboard', [
            'summaryCards' => $summaryCards,
            'statusCards' => $statusCards,
            'latestContent' => collect(Cache::remember('dashboard:latest_content', 300, fn (): array => $this->latestContent()->all())),
            'publicReadiness' => Cache::remember('dashboard:readiness', 600, fn (): array => $this->publicReadiness()),
            'chartActivityMonthly' => $this->chartActivityMonthly(),
            'chartStatusDistribution' => $this->chartStatusDistribution($activityCounts, $lecturerCounts, $documentCounts, $alumniCounts),
            'chartCombinedMonthly' => $this->chartCombinedMonthly(),
        ]);
    }

    /**
     * @param  class-string<Model>  $model
     * @return Collection<string, int>
     */
    private function countsByStatus(string $model): Collection
    {
        return $model::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total): int => (int) $total);
    }

    /**
     * @return Collection<int, array{title: string, type: string, status: string, status_label: string, tone: string, updated_at: string}>
     */
    private function latestContent(): Collection
    {
        $content = Activity::query()
            ->latest('updated_at')
            ->limit(5)
            ->get(['title', 'status', 'updated_at'])
            ->map(fn (Activity $activity): array => $this->contentItem(
                $activity->title,
                'Kegiatan',
                $activity->status,
                $activity->updated_at,
            ));

        $content = $content->concat(
            Document::query()
                ->latest('updated_at')
                ->limit(5)
                ->get(['title', 'status', 'updated_at'])
                ->map(fn (Document $document): array => $this->contentItem(
                    $document->title,
                    'Dokumen',
                    $document->status,
                    $document->updated_at,
                )),
        );

        $content = $content->concat(
            Lecturer::query()
                ->latest('updated_at')
                ->limit(5)
                ->get(['name', 'status', 'updated_at'])
                ->map(fn (Lecturer $lecturer): array => $this->contentItem(
                    $lecturer->name,
                    'Dosen',
                    $lecturer->status,
                    $lecturer->updated_at,
                )),
        );

        return $content->concat(
            Alumni::query()
                ->latest('updated_at')
                ->limit(5)
                ->get(['name', 'status', 'updated_at'])
                ->map(fn (Alumni $alumni): array => $this->contentItem(
                    $alumni->name,
                    'Alumni',
                    $alumni->status,
                    $alumni->updated_at,
                )),
        )
            ->sortByDesc('updated_at')
            ->take(6)
            ->values();
    }

    /**
     * @return array{title: string, type: string, status: string, status_label: string, tone: string, updated_at: string}
     */
    private function contentItem(
        string $title,
        string $type,
        string $status,
        CarbonInterface $updatedAt,
    ): array {
        $presentation = match ($status) {
            Activity::STATUS_SCHEDULED => ['label' => 'Terjadwal', 'tone' => 'scheduled'],
            Activity::STATUS_PUBLISHED => ['label' => 'Terbit', 'tone' => 'published'],
            Lecturer::STATUS_ACTIVE => ['label' => 'Aktif', 'tone' => 'active'],
            Lecturer::STATUS_INACTIVE => ['label' => 'Nonaktif', 'tone' => 'inactive'],
            default => ['label' => 'Draf', 'tone' => 'draft'],
        };

        return [
            'title' => $title,
            'type' => $type,
            'status' => $status,
            'status_label' => $presentation['label'],
            'tone' => $presentation['tone'],
            'updated_at' => $updatedAt->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array{label: string, status: string, tone: string}>
     */
    private function publicReadiness(): array
    {
        return [
            $this->readinessItem('Tampilan utama beranda', HomeSection::query()->exists()),
            $this->readinessItem('Profil prodi', ProgramProfile::query()->exists()),
            $this->readinessItem(
                'Kontak dan peta',
                Contact::query()->whereNotNull('map_embed')->where('map_embed', '<>', '')->exists(),
            ),
            $this->readinessItem(
                'E-jurnal',
                SiteSetting::query()->whereNotNull('journal_url')->where('journal_url', '<>', '')->exists(),
                'Aktif',
            ),
        ];
    }

    /**
     * @return array{label: string, status: string, tone: string}
     */
    private function readinessItem(string $label, bool $ready, string $readyLabel = 'Siap'): array
    {
        return [
            'label' => $label,
            'status' => $ready ? $readyLabel : 'Perlu isi',
            'tone' => $ready ? 'ready' : 'attention',
        ];
    }

    /**
     * @return array{labels: array<int, string>, counts: array<int, int>}
     */
    private function chartActivityMonthly(): array
    {
        return Cache::remember('dashboard:chart_activity_monthly', 300, function (): array {
            $now = Carbon::now();
            $months = collect(range(5, 0))->map(fn (int $i) => $now->copy()->subMonths($i));
            $labels = $months->map(fn (Carbon $d) => $d->locale('id')->translatedFormat('M'))->toArray();
            $from = $now->copy()->subMonths(5)->startOfMonth();
            $rows = Activity::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
                ->where('created_at', '>=', $from)
                ->groupBy('ym')
                ->pluck('c', 'ym');
            $counts = $months->map(fn (Carbon $d) => (int) ($rows[$d->format('Y-m')] ?? 0))->toArray();

            return compact('labels', 'counts');
        });
    }

    /**
     * @return array{series: array<int, int>, labels: array<int, string>}
     */
    private function chartStatusDistribution(
        Collection $activityCounts,
        Collection $lecturerCounts,
        Collection $documentCounts,
        Collection $alumniCounts,
    ): array {
        return [
            'series' => [
                (int) $activityCounts->sum() + (int) $documentCounts->sum(),
                (int) $lecturerCounts->sum(),
                (int) $alumniCounts->sum(),
                (int) $activityCounts->get('draft', 0) + (int) $documentCounts->get('draft', 0),
            ],
            'labels' => ['Konten', 'Dosen', 'Alumni', 'Draf'],
        ];
    }

    /**
     * @return array{series: array<int, array{name: string, data: array<int, int>}>, labels: array<int, string>}
     */
    private function chartCombinedMonthly(): array
    {
        return Cache::remember('dashboard:chart_combined_monthly', 300, function (): array {
            $now = Carbon::now();
            $months = collect(range(5, 0))->map(fn (int $i) => $now->copy()->subMonths($i));
            $labels = $months->map(fn (Carbon $d) => $d->locale('id')->translatedFormat('M'))->toArray();
            $from = $now->copy()->subMonths(5)->startOfMonth();
            $activityRows = Activity::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
                ->where('created_at', '>=', $from)->groupBy('ym')->pluck('c', 'ym');
            $alumniRows = Alumni::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
                ->where('created_at', '>=', $from)->groupBy('ym')->pluck('c', 'ym');
            $activityData = $months->map(fn (Carbon $d) => (int) ($activityRows[$d->format('Y-m')] ?? 0))->toArray();
            $alumniData = $months->map(fn (Carbon $d) => (int) ($alumniRows[$d->format('Y-m')] ?? 0))->toArray();

            return [
                'series' => [
                    ['name' => 'Kegiatan', 'data' => $activityData],
                    ['name' => 'Alumni', 'data' => $alumniData],
                ],
                'labels' => $labels,
            ];
        });
    }
}
