<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('activities', function (Blueprint $table): void {
                $table->index(['status', 'activity_date', 'id'], 'activities_status_date_id_idx');
                $table->index(['status', 'category'], 'activities_status_category_idx');
                if (DB::getDriverName() === 'mysql') {
                    $table->fullText(['title', 'location'], 'activities_title_location_fulltext');
                }
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('lecturers', function (Blueprint $table): void {
                $table->index(['status', 'expertise'], 'lecturers_status_expertise_idx');
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('alumni', function (Blueprint $table): void {
                $table->index(['status', 'job_position'], 'alumni_status_job_idx');
                $table->unique(['name', 'batch_year'], 'alumni_name_batch_unique');
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('documents', function (Blueprint $table): void {
                $table->index(['document_category_id', 'status'], 'documents_cat_status_idx');
                $table->index(['status', 'uploaded_at', 'id'], 'documents_status_uploaded_idx');
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('messages', function (Blueprint $table): void {
                $table->index('email', 'messages_email_idx');
                $table->index('subject', 'messages_subject_idx');
                $table->index(['created_at', 'id'], 'messages_created_idx');
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('document_categories', function (Blueprint $table): void {
                $table->index('name', 'doccat_name_idx');
            });
        } catch (Throwable $e) {
        }
    }

    public function down(): void
    {
        // Activities
        try {
            Schema::table('activities', function (Blueprint $table): void {
                try {
                    $table->dropIndex('activities_status_date_id_idx');
                } catch (Throwable $e) {
                }
                try {
                    $table->dropIndex('activities_status_category_idx');
                } catch (Throwable $e) {
                }
                if (DB::getDriverName() === 'mysql') {
                    try {
                        $table->dropFullText('activities_title_location_fulltext');
                    } catch (Throwable $e) {
                    }
                }
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('lecturers', function (Blueprint $table): void {
                try {
                    $table->dropIndex('lecturers_status_expertise_idx');
                } catch (Throwable $e) {
                }
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('alumni', function (Blueprint $table): void {
                try {
                    $table->dropIndex('alumni_status_job_idx');
                } catch (Throwable $e) {
                }
                try {
                    $table->dropUnique('alumni_name_batch_unique');
                } catch (Throwable $e) {
                }
            });
        } catch (Throwable $e) {
        }

        // Documents — FK handling: composite index is used by FK, must drop FK first
        try {
            Schema::table('documents', function (Blueprint $table): void {
                try {
                    $table->dropForeign(['document_category_id']);
                } catch (Throwable $e) {
                }
                try {
                    $table->dropIndex('documents_cat_status_idx');
                } catch (Throwable $e) {
                }
                try {
                    $table->dropIndex('documents_status_uploaded_idx');
                } catch (Throwable $e) {
                }
                // Recreate FK with base index for future
                try {
                    $table->foreign('document_category_id')->references('id')->on('document_categories')->restrictOnDelete();
                } catch (Throwable $e) {
                }
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('messages', function (Blueprint $table): void {
                try {
                    $table->dropIndex('messages_email_idx');
                } catch (Throwable $e) {
                }
                try {
                    $table->dropIndex('messages_subject_idx');
                } catch (Throwable $e) {
                }
                try {
                    $table->dropIndex('messages_created_idx');
                } catch (Throwable $e) {
                }
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('document_categories', function (Blueprint $table): void {
                try {
                    $table->dropIndex('doccat_name_idx');
                } catch (Throwable $e) {
                }
            });
        } catch (Throwable $e) {
        }
    }
};
