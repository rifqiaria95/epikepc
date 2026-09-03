<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 32)->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department', 120);
            $table->string('location_city', 120);
            $table->string('location_province', 120);
            $table->string('employment_type', 32);
            $table->string('work_arrangement', 32);
            $table->string('experience_level', 32);
            $table->text('summary');
            $table->longText('description');
            $table->longText('responsibilities');
            $table->longText('qualifications');
            $table->longText('preferred_qualifications')->nullable();
            $table->string('minimum_education', 80)->nullable();
            $table->unsignedSmallInteger('minimum_experience_years')->nullable();
            $table->unsignedSmallInteger('headcount')->default(1);
            $table->boolean('requires_site_travel')->default(false);
            $table->boolean('allows_salary_expectation')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->string('status', 32)->default('DRAFT');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at', 'closes_at'], 'job_vacancies_public_lookup_idx');
            $table->index(['department', 'status']);
            $table->index(['location_city', 'location_province']);
            $table->index(['employment_type', 'work_arrangement']);
        });

        Schema::create('job_vacancy_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_vacancy_id')->constrained('job_vacancies')->cascadeOnDelete();
            $table->string('question');
            $table->string('help_text', 500)->nullable();
            $table->string('type', 32);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['job_vacancy_id', 'sort_order']);
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name', 160);
            $table->string('email', 190);
            $table->string('normalized_email', 190)->unique();
            $table->string('phone', 40);
            $table->string('normalized_phone', 40)->nullable()->index();
            $table->string('domicile_city', 120);
            $table->string('domicile_province', 120);
            $table->string('highest_education', 80);
            $table->string('education_major', 160)->nullable();
            $table->string('institution_name', 190)->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->decimal('total_experience_years', 4, 1)->default(0);
            $table->string('current_or_last_company', 190)->nullable();
            $table->string('current_or_last_title', 190)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number', 32)->unique();
            $table->foreignUuid('job_vacancy_id')->constrained('job_vacancies')->restrictOnDelete();
            $table->foreignUuid('candidate_id')->constrained('candidates')->restrictOnDelete();
            $table->string('status', 40)->default('PENDING_VERIFICATION');
            $table->string('email_verification_status', 32)->default('PENDING');
            $table->text('cover_letter')->nullable();
            $table->unsignedBigInteger('latest_salary_amount')->nullable();
            $table->unsignedBigInteger('expected_salary_amount')->nullable();
            $table->string('salary_currency', 8)->default('IDR');
            $table->string('availability_type', 40);
            $table->date('available_from')->nullable();
            $table->boolean('willing_to_relocate')->nullable();
            $table->boolean('willing_to_travel_to_site')->nullable();
            $table->string('referral_source', 40)->nullable();
            $table->string('referral_detail', 255)->nullable();
            $table->foreignId('assigned_recruiter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('consent_version', 40)->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->boolean('accuracy_declared')->default(false);
            $table->json('question_snapshot')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['job_vacancy_id', 'candidate_id'], 'job_applications_vacancy_candidate_unique');
            $table->index(['status', 'submitted_at']);
            $table->index(['email_verification_status', 'created_at']);
            $table->index(['assigned_recruiter_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('job_application_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->uuid('job_vacancy_question_id');
            $table->string('question_text');
            $table->string('question_type', 32);
            $table->json('question_options')->nullable();
            $table->text('answer_text')->nullable();
            $table->json('answer_json')->nullable();
            $table->timestamps();

            $table->unique(['job_application_id', 'job_vacancy_question_id'], 'job_application_answers_unique');
            $table->foreign('job_vacancy_question_id', 'jaa_question_fk')
                ->references('id')
                ->on('job_vacancy_questions')
                ->restrictOnDelete();
        });

        Schema::create('job_application_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->string('document_type', 32);
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('disk', 40);
            $table->string('path');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->string('checksum', 64);
            $table->string('scan_status', 32)->default('PENDING');
            $table->timestamp('uploaded_at');
            $table->timestamps();

            $table->index(['job_application_id', 'document_type']);
            $table->index('scan_status');
        });

        Schema::create('job_application_status_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_application_id')->constrained('job_applications')->restrictOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->string('reason_code', 80)->nullable();
            $table->text('public_message')->nullable();
            $table->text('internal_note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['job_application_id', 'created_at']);
        });

        Schema::create('job_application_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->text('note');
            $table->boolean('is_pinned')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_application_id', 'is_pinned']);
        });

        Schema::create('career_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->string('purpose', 40);
            $table->string('token_hash', 128);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedSmallInteger('use_count')->default(0);
            $table->string('created_ip', 45)->nullable();
            $table->timestamps();

            $table->unique(['purpose', 'token_hash']);
            $table->index(['job_application_id', 'purpose', 'expires_at']);
        });

        Schema::create('career_document_access_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_application_document_id')->constrained('job_application_documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 40);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['job_application_document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_document_access_logs');
        Schema::dropIfExists('career_access_tokens');
        Schema::dropIfExists('job_application_notes');
        Schema::dropIfExists('job_application_status_histories');
        Schema::dropIfExists('job_application_documents');
        Schema::dropIfExists('job_application_answers');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_vacancy_questions');
        Schema::dropIfExists('job_vacancies');
    }
};
