# Career & Recruitment Module

Modul Career EPIKEPC: lowongan publik, lamaran tanpa akun, verifikasi email, dan CMS rekrutmen.

## Architecture

Public Blade pages call thin frontend controllers. CMS pages follow the existing Mono + DataTables pattern. Domain work lives in focused services:

- `CareerVacancyQuery` / `CareerApplicationQuery` — listing and aggregates
- `CandidateIdentityService` — email-normalized, race-safe find-or-create
- `JobApplicationSubmissionService` — apply orchestration + transaction
- `JobApplicationVerificationService` — verify / resend / status / withdraw
- `JobApplicationTransitionService` — allowed status matrix + immutable history
- `CandidateDocumentService` — private storage, MIME/magic-byte checks
- `CareerTokenService` — hashed single-use / rotatable tokens
- `CareerNotificationService` — queued mail after commit
- `VacancyManagementService` — CMS vacancy lifecycle

Candidates are **not** `users`. They never receive CMS roles.

## Data model

UUID primary keys on `job_vacancies`, `candidates`, `job_applications`, answers, documents, notes, histories, and tokens.

Important uniqueness:

- `job_vacancies.code`, `job_vacancies.slug`
- `candidates.normalized_email`
- `job_applications.reference_number`
- `job_applications (job_vacancy_id, candidate_id)`
- `job_application_answers (job_application_id, job_vacancy_question_id)`

Question snapshots are stored on the application (`question_snapshot` + answer `question_text`) so later CMS edits do not rewrite history.

Vacancies and applications use `restrictOnDelete` so recruitment history is not cascade-deleted.

## Public flow

`GET /careers` → `GET /careers/{slug}` → `GET /careers/{slug}/apply` → `POST .../applications` → email verify → submitted.

After POST the UI only says a verification link was sent (neutral wording). After verify, the reference number is shown and a status-access token is emailed.

## CMS flow

Menu group **Career** (`jenis_menu` 6):

1. Recruitment Overview
2. Vacancies (CRUD, publish/close/archive/duplicate, screening questions)
3. Applications (filter, detail, transition, notes, assign, document download)
4. Candidates

## Status transition matrix

| From | To |
| --- | --- |
| PENDING_VERIFICATION | SUBMITTED, EXPIRED |
| SUBMITTED | SCREENING, WITHDRAWN |
| SCREENING | SHORTLISTED, REJECTED, WITHDRAWN |
| SHORTLISTED | INTERVIEW, REJECTED, WITHDRAWN |
| INTERVIEW | OFFERED, REJECTED, WITHDRAWN |
| OFFERED | HIRED, REJECTED, WITHDRAWN |
| HIRED / REJECTED / WITHDRAWN / EXPIRED | none |

Terminal statuses cannot be reopened without a new privileged transition (not implemented).

## Permission matrix

Assigned to `superadmin` only by the idempotent seeder. Other roles are not granted these implicitly.

- `view_career_dashboard`
- `view_vacancies`, `create_vacancies`, `edit_vacancies`, `publish_vacancies`, `close_vacancies`, `archive_vacancies`
- `view_applications`, `review_applications`, `assign_applications`, `change_application_status`, `reject_applications`
- `view_candidates`, `view_candidate_documents`, `download_candidate_documents`
- `create_application_notes`, `delete_application_notes`
- `manage_career_settings`

Policies wrap document download and transitions. Route middleware is not the only gate.

## Private document storage

- Disk: `local` → `storage/app/private` (not `/storage` public link)
- Generated filename, MIME + magic-byte validation
- Allowed CV: PDF, DOC, DOCX; default 5 MB
- Download only via authorized CMS controller + `Content-Disposition: attachment`
- Access is logged in `career_document_access_logs`

## Malware scanner

`CAREER_MALWARE_SCANNER=null` uses `NullMalwareScanner`. Files stay `PENDING`. The module never labels a file `CLEAN` without a real scanner decision. CMS may download `PENDING` files with permission; `REJECTED` / `FAILED` cannot be downloaded as trusted files.

## Token lifecycle

1. Issue 64-char plaintext token, store `sha256` hash, rotate previous tokens of the same purpose.
2. Email contains plaintext only.
3. Lookup uses hash + `hash_equals`.
4. Email verification is single-use (`consumed_at`).
5. Status tokens can be touched, rotated, and revoked.
6. Expiry: verification 48h, status 90 days, withdrawal 72h (config).

## Mail / queue

Notifications implement `ShouldQueue`. With `QUEUE_CONNECTION=database` run `php artisan queue:work`. `sync` is the documented fallback (used in tests and local without a worker). Dispatch happens in `DB::afterCommit`.

## Environment

See `.env.example` keys prefixed `CAREER_`. Read them only through `config/career.php`.

## Retention and privacy

- Consent version + timestamp stored on each application
- Default retention 24 months (`CAREER_RETENTION_MONTHS`)
- Privacy URL: `CAREER_PRIVACY_NOTICE_URL`
- Candidate deletion/anonymization is a follow-up job: do not hard-delete applications that have history; anonymize PII and detach documents after retention.

## Testing

```bash
php artisan test --filter=Career
php artisan test
```

Feature tests use `RefreshDatabase`, fake storage/notifications, and Pest.

## Deployment

1. `php artisan migrate`
2. `php artisan db:seed --class=CareerPermissionSeeder`
3. `php artisan db:seed --class=CareerMenuSeeder`
4. Optional demo vacancies: `php artisan db:seed --class=CareerDemoSeeder`
5. Ensure `storage/app/private` is writable and **not** web-accessible
6. Start a queue worker if `QUEUE_CONNECTION` is not `sync`
7. Configure mail from-address and optional `CAREER_RECRUITER_EMAILS`

## Rollback

1. Stop public `/careers` traffic (remove routes or unpublish vacancies)
2. `php artisan migrate:rollback --step=1` rolls back `2026_09_03_200000_create_career_recruitment_tables`
3. Permissions/menus are additive; remove the Career menu group and `career_*` permissions only if you intend a full uninstall
4. Delete `storage/app/private/career/documents` after legal review

## Auth findings (out of scope)

Public `/register` still creates CMS `users`. Career isolation does not use that path. A later hardening pass should restrict registration if it is unused in production.
