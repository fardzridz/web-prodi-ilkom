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
Route::get('/alumni', [PublicController::class, 'alumni'])->name('alumni');
Route::get('/kontak', [PublicController::class, 'contact'])->name('contact');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/beranda', [HomeSectionController::class, 'index'])->name('beranda');
    Route::get('/profil', [ProgramProfileController::class, 'index'])->name('profil');
    Route::get('/dosen', [LecturerController::class, 'index'])->name('dosen.index');
    Route::get('/dosen/create', [LecturerController::class, 'create'])->name('dosen.create');
    Route::get('/dosen/{lecturer}/edit', [LecturerController::class, 'edit'])->name('dosen.edit');
    Route::get('/kegiatan', [ActivityController::class, 'index'])->name('kegiatan.index');
    Route::get('/kegiatan/create', [ActivityController::class, 'create'])->name('kegiatan.create');
    Route::get('/kegiatan/{activity}/edit', [ActivityController::class, 'edit'])->name('kegiatan.edit');
    Route::get('/dokumen', [DocumentController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/create', [DocumentController::class, 'create'])->name('dokumen.create');
    Route::get('/dokumen/{document}/edit', [DocumentController::class, 'edit'])->name('dokumen.edit');
    Route::get('/kategori-dokumen', [DocumentCategoryController::class, 'index'])->name('kategori-dokumen');
    Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/create', [AlumniController::class, 'create'])->name('alumni.create');
    Route::get('/alumni/{alumni}/edit', [AlumniController::class, 'edit'])->name('alumni.edit');
    Route::get('/jurnal', [SiteSettingController::class, 'journal'])->name('jurnal');
    Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');
    Route::get('/pengaturan', [SiteSettingController::class, 'index'])->name('pengaturan');
    Route::get('/akun-admin', [AccountController::class, 'index'])->name('akun-admin');
});
