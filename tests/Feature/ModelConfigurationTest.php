<?php

use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Contact;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\HomeSection;
use App\Models\Lecturer;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('core models expose only their documented mass assignable attributes', function () {
    $models = [
        User::class => ['name', 'email', 'password', 'role'],
        SiteSetting::class => [
            'site_name', 'university_name', 'faculty_name', 'logo', 'favicon',
            'journal_url', 'footer_text', 'footer_academic_links',
        ],
        HomeSection::class => [
            'hero_title', 'hero_subtitle', 'hero_slides', 'cta_text', 'cta_link',
            'welcome_title', 'welcome_description',
        ],
        ProgramProfile::class => [
            'history', 'description', 'vision', 'mission', 'goals', 'accreditation', 'advantages',
        ],
        Lecturer::class => [
            'name', 'nidn', 'position', 'expertise', 'education', 'email', 'photo',
            'bio', 'status', 'sort_order',
        ],
        Activity::class => [
            'user_id', 'title', 'slug', 'excerpt', 'content', 'image', 'activity_date',
            'location', 'category', 'status', 'published_at',
        ],
        DocumentCategory::class => ['name', 'slug'],
        Document::class => [
            'document_category_id', 'title', 'slug', 'description', 'file', 'file_type',
            'file_size', 'status', 'uploaded_at',
        ],
        Alumni::class => [
            'name', 'batch_year', 'graduation_year', 'job_position', 'company',
            'testimonial', 'photo', 'status',
        ],
        Contact::class => [
            'address', 'email', 'phone', 'instagram', 'youtube', 'facebook', 'map_embed',
        ],
    ];

    foreach ($models as $modelClass => $fillable) {
        expect((new $modelClass)->getFillable())->toBe($fillable);
    }
});

test('json dates and numeric model attributes use the expected casts', function () {
    expect((new SiteSetting)->getCasts())->toMatchArray([
        'footer_academic_links' => 'array',
    ])->and((new HomeSection)->getCasts())->toMatchArray([
        'hero_slides' => 'array',
    ])->and((new Lecturer)->getCasts())->toMatchArray([
        'sort_order' => 'integer',
    ])->and((new Activity)->getCasts())->toMatchArray([
        'activity_date' => 'date',
        'published_at' => 'datetime',
    ])->and((new Document)->getCasts())->toMatchArray([
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ])->and((new Alumni)->getCasts())->toMatchArray([
        'batch_year' => 'integer',
        'graduation_year' => 'integer',
    ]);
});

test('status models publish the values accepted by the documented workflow', function () {
    expect(User::ROLE_ADMIN)->toBe('admin')
        ->and(Lecturer::STATUSES)->toBe(['active', 'inactive'])
        ->and(Activity::STATUSES)->toBe(['draft', 'scheduled', 'published'])
        ->and(Document::STATUSES)->toBe(['draft', 'published'])
        ->and(Alumni::STATUSES)->toBe(['active', 'inactive']);
});

test('user and activity define both sides of their relationship', function () {
    $activities = (new User)->activities();
    $user = (new Activity)->user();

    expect($activities)->toBeInstanceOf(HasMany::class)
        ->and($activities->getRelated())->toBeInstanceOf(Activity::class)
        ->and($activities->getForeignKeyName())->toBe('user_id')
        ->and($user)->toBeInstanceOf(BelongsTo::class)
        ->and($user->getRelated())->toBeInstanceOf(User::class)
        ->and($user->getForeignKeyName())->toBe('user_id')
        ->and($user->getOwnerKeyName())->toBe('id');
});

test('document category and document define both sides of their relationship', function () {
    $documents = (new DocumentCategory)->documents();
    $documentCategory = (new Document)->documentCategory();

    expect($documents)->toBeInstanceOf(HasMany::class)
        ->and($documents->getRelated())->toBeInstanceOf(Document::class)
        ->and($documents->getForeignKeyName())->toBe('document_category_id')
        ->and($documentCategory)->toBeInstanceOf(BelongsTo::class)
        ->and($documentCategory->getRelated())->toBeInstanceOf(DocumentCategory::class)
        ->and($documentCategory->getForeignKeyName())->toBe('document_category_id')
        ->and($documentCategory->getOwnerKeyName())->toBe('id');
});

test('alumni model maps to the documented alumni table', function () {
    expect((new Alumni)->getTable())->toBe('alumni');
});
