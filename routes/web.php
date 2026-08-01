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
use App\Http\Controllers\Admin\ProgramProfileController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/profil', [PublicController::class, 'profile'])->name('profile');
Route::get('/visi-misi', [PublicController::class, 'visionMission'])->name('vision-mission');
Route::get('/dosen', [PublicController::class, 'lecturers'])->name('lecturers');
Route::get('/kegiatan', [PublicController::class, 'activities'])->name('activities.index');
Route::get('/kegiatan/{slug}', [PublicController::class, 'activityDetail'])->name('activities.show');
Route::get('/jurnal', [PublicController::class, 'journalRedirect'])->name('journal');
Route::get('/dokumen', [PublicController::class, 'documents'])->name('documents');
Route::get('/dokumen/{document}/download', [PublicController::class, 'documentDownload'])->name('documents.download');
Route::get('/alumni', [PublicController::class, 'alumni'])->name('alumni');
Route::get('/kontak', [PublicController::class, 'contact'])->name('contact');
Route::post('/kontak', [PublicController::class, 'contactStore'])->middleware('throttle:5,1')->name('contact.store');

// Route::prefix('admin')->name('admin.')->group(function (): void {
Route::prefix('komi-panel')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');

    Route::middleware('auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/beranda', [HomeSectionController::class, 'index'])->name('beranda');
        Route::put('/beranda', [HomeSectionController::class, 'update'])->name('beranda.update');
        Route::get('/profil', [ProgramProfileController::class, 'index'])->name('profil');
        Route::put('/profil', [ProgramProfileController::class, 'update'])->name('profil.update');
        Route::patch('/dosen/{lecturer}/status', [LecturerController::class, 'toggleStatus'])->name('dosen.status');
        Route::resource('dosen', LecturerController::class)
            ->except(['show'])
            ->parameters(['dosen' => 'lecturer']);
        Route::resource('kegiatan', ActivityController::class)
            ->except(['show'])
            ->parameters(['kegiatan' => 'activity']);
        Route::get('/dokumen/{document}/download', [DocumentController::class, 'download'])->name('dokumen.download');
        Route::patch('/dokumen/{document}/status', [DocumentController::class, 'toggleStatus'])->name('dokumen.status');
        Route::resource('dokumen', DocumentController::class)
            ->except(['show'])
            ->parameters(['dokumen' => 'document']);
        Route::resource('kategori-dokumen', DocumentCategoryController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['kategori-dokumen' => 'documentCategory']);
        Route::patch('/alumni/{alumni}/status', [AlumniController::class, 'toggleStatus'])->name('alumni.status');
        Route::resource('alumni', AlumniController::class)
            ->except(['show'])
            ->parameters(['alumni' => 'alumni']);
        Route::get('/jurnal', [SiteSettingController::class, 'journal'])->name('jurnal');
        Route::put('/jurnal', [SiteSettingController::class, 'updateJournal'])->name('jurnal.update');
        Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');
        Route::put('/kontak', [ContactController::class, 'update'])->name('kontak.update');
        Route::get('/pengaturan', [SiteSettingController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan', [SiteSettingController::class, 'update'])->name('pengaturan.update');
        Route::get('/akun-admin', [AccountController::class, 'index'])->name('akun-admin');
        Route::put('/akun-admin', [AccountController::class, 'update'])->name('akun-admin.update');
    });
});
