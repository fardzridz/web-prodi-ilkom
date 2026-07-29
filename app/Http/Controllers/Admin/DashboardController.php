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
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activityCounts = $this->countsByStatus(Activity::class);
        $lecturerCounts = $this->countsByStatus(Lecturer::class);
        $documentCounts = $this->countsByStatus(Document::class);
        $alumniCounts = $this->countsByStatus(Alumni::class);

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
            'latestContent' => $this->latestContent(),
            'publicReadiness' => $this->publicReadiness(),
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
     * @return Collection<int, array{title: string, type: string, status: string, status_label: string, tone: string, updated_at: CarbonInterface}>
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
     * @return array{title: string, type: string, status: string, status_label: string, tone: string, updated_at: CarbonInterface}
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
            'updated_at' => $updatedAt,
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
}
