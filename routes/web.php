<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AlumniController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProgramProfileController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Public\ActivityController as PublicActivityController;
use App\Http\Controllers\Public\AlumniController as PublicAlumniController;
use App\Http\Controllers\Public\ContactController as PublicContactController;
use App\Http\Controllers\Public\DocumentController as PublicDocumentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LecturerController as PublicLecturerController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware('public.security')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::get('/visi-misi', [ProfileController::class, 'visionMission'])->name('vision-mission');
    Route::get('/dosen', [PublicLecturerController::class, 'index'])->name('lecturers');
    Route::get('/kegiatan', [PublicActivityController::class, 'index'])->name('activities.index');
    Route::get('/kegiatan/{slug}', [PublicActivityController::class, 'show'])->name('activities.show');
    Route::get('/jurnal', [PublicPageController::class, 'journalRedirect'])->name('journal');
    Route::get('/dokumen', [PublicDocumentController::class, 'index'])->name('documents');
    Route::get('/dokumen/{document}/download', [PublicDocumentController::class, 'download'])
        ->middleware('throttle:30,1')
        ->name('documents.download');
    Route::get('/dokumen/{document}/view', [PublicDocumentController::class, 'view'])
        ->middleware('throttle:30,1')
        ->name('documents.view');
    Route::get('/alumni', [PublicAlumniController::class, 'index'])->name('alumni');
    Route::get('/kontak', [PublicContactController::class, 'index'])->name('contact');
    Route::get('/kebijakan-privasi', [PublicPageController::class, 'privacyPolicy'])->name('public.privacy-policy');
    Route::get('/aksesibilitas', [PublicPageController::class, 'accessibility'])->name('public.accessibility');
    Route::post('/kontak', [PublicContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
});

// Route::prefix('admin')->name('admin.')->group(function (): void {
Route::prefix('komi-panel')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/beranda', [HomeSectionController::class, 'index'])->name('beranda');
        Route::put('/beranda', [HomeSectionController::class, 'update'])->middleware('throttle:60,1')->name('beranda.update');
        Route::get('/profil', [ProgramProfileController::class, 'index'])->name('profil');
        Route::put('/profil', [ProgramProfileController::class, 'update'])
            ->middleware('throttle:30,1')
            ->name('profil.update');
        Route::patch('/dosen/{lecturer}/status', [LecturerController::class, 'toggleStatus'])->middleware('throttle:60,1')->name('dosen.status');
        Route::resource('dosen', LecturerController::class)
            ->except(['show'])
            ->parameters(['dosen' => 'lecturer'])
            ->middlewareFor(['store', 'update', 'destroy'], 'throttle:60,1');
        Route::resource('kegiatan', ActivityController::class)
            ->except(['show'])
            ->parameters(['kegiatan' => 'activity'])
            ->middlewareFor(['store', 'update', 'destroy'], 'throttle:60,1');
        Route::get('/dokumen/{document}/download', [DocumentController::class, 'download'])->middleware('throttle:60,1')->name('dokumen.download');
        Route::patch('/dokumen/{document}/status', [DocumentController::class, 'toggleStatus'])->middleware('throttle:60,1')->name('dokumen.status');
        Route::resource('dokumen', DocumentController::class)
            ->except(['show'])
            ->parameters(['dokumen' => 'document'])
            ->middlewareFor(['store', 'update', 'destroy'], 'throttle:60,1');
        Route::resource('kategori-dokumen', DocumentCategoryController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['kategori-dokumen' => 'documentCategory'])
            ->middlewareFor(['store', 'update', 'destroy'], 'throttle:60,1');
        Route::patch('/alumni/{alumni}/status', [AlumniController::class, 'toggleStatus'])->middleware('throttle:60,1')->name('alumni.status');
        Route::resource('alumni', AlumniController::class)
            ->except(['show'])
            ->parameters(['alumni' => 'alumni'])
            ->middlewareFor(['store', 'update', 'destroy'], 'throttle:60,1');
        Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');
        Route::put('/kontak', [ContactController::class, 'update'])->middleware('throttle:60,1')->name('kontak.update');
        Route::get('/pengaturan', [SiteSettingController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan', [SiteSettingController::class, 'update'])->middleware('throttle:60,1')->name('pengaturan.update');
        Route::get('/akun-admin', [AccountController::class, 'index'])->name('akun-admin');
        Route::put('/akun-admin', [AccountController::class, 'update'])->middleware('throttle:60,1')->name('akun-admin.update');
        Route::get('/halaman', [PageController::class, 'index'])->name('halaman');
        Route::get('/halaman/{slug}/edit', [PageController::class, 'edit'])->name('halaman.edit');
        Route::put('/halaman/{slug}', [PageController::class, 'update'])->middleware('throttle:60,1')->name('halaman.update');
    });
});
