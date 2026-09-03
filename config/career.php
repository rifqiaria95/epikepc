<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Document upload
    |--------------------------------------------------------------------------
    */
    'documents' => [
        'disk' => env('CAREER_DOCUMENT_DISK', 'local'),
        'directory' => env('CAREER_DOCUMENT_DIRECTORY', 'career/documents'),
        'max_cv_kilobytes' => (int) env('CAREER_MAX_CV_KB', 5120),
        'allowed_cv_mimes' => ['pdf', 'doc', 'docx'],
        'allowed_cv_mime_types' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'allowed_certificate_mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
        'blocked_extensions' => [
            'svg', 'html', 'htm', 'js', 'exe', 'bat', 'cmd', 'sh', 'php',
            'zip', 'rar', '7z', 'tar', 'gz', 'docm', 'xlsm', 'pptm',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tokens
    |--------------------------------------------------------------------------
    */
    'tokens' => [
        'verification_ttl_hours' => (int) env('CAREER_VERIFICATION_TTL_HOURS', 48),
        'status_access_ttl_days' => (int) env('CAREER_STATUS_ACCESS_TTL_DAYS', 90),
        'withdrawal_ttl_hours' => (int) env('CAREER_WITHDRAWAL_TTL_HOURS', 72),
        'resend_cooldown_seconds' => (int) env('CAREER_RESEND_COOLDOWN_SECONDS', 60),
        'max_resend_per_hour' => (int) env('CAREER_MAX_RESEND_PER_HOUR', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limiting & abuse
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'apply_per_ip' => env('CAREER_APPLY_RATE_IP', '10,60'),
        'apply_per_email' => env('CAREER_APPLY_RATE_EMAIL', '5,60'),
        'verify_per_ip' => env('CAREER_VERIFY_RATE_IP', '30,60'),
        'status_per_ip' => env('CAREER_STATUS_RATE_IP', '30,60'),
        'captcha_threshold' => (int) env('CAREER_CAPTCHA_THRESHOLD', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention & privacy
    |--------------------------------------------------------------------------
    */
    'privacy' => [
        'notice_url' => env('CAREER_PRIVACY_NOTICE_URL', '/privacy'),
        'retention_months' => (int) env('CAREER_RETENTION_MONTHS', 24),
        'consent_version' => env('CAREER_CONSENT_VERSION', '2026-09-01'),
    ],

    'defaults' => [
        'salary_currency' => env('CAREER_DEFAULT_CURRENCY', 'IDR'),
        'headcount' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Public status mapping (internal => public label)
    |--------------------------------------------------------------------------
    */
    'public_statuses' => [
        'PENDING_VERIFICATION' => 'Menunggu verifikasi email',
        'SUBMITTED' => 'Lamaran diterima',
        'SCREENING' => 'Dalam peninjauan',
        'SHORTLISTED' => 'Lolos seleksi awal',
        'INTERVIEW' => 'Proses wawancara',
        'OFFERED' => 'Penawaran kerja',
        'HIRED' => 'Diterima',
        'REJECTED' => 'Tidak dilanjutkan',
        'WITHDRAWN' => 'Ditarik kandidat',
        'EXPIRED' => 'Kadaluarsa',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'recruiter_role' => env('CAREER_RECRUITER_ROLE', 'superadmin'),
        'recruiter_emails' => array_filter(array_map('trim', explode(',', (string) env('CAREER_RECRUITER_EMAILS', '')))),
        'queue' => env('CAREER_MAIL_QUEUE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature toggles
    |--------------------------------------------------------------------------
    */
    'features' => [
        'allow_withdrawal' => (bool) env('CAREER_ALLOW_WITHDRAWAL', true),
        'honeypot' => (bool) env('CAREER_HONEYPOT', true),
        'malware_scanner' => env('CAREER_MALWARE_SCANNER', 'null'), // null|clamav
    ],

    'pagination' => [
        'public_per_page' => (int) env('CAREER_PUBLIC_PER_PAGE', 9),
        'cms_per_page' => (int) env('CAREER_CMS_PER_PAGE', 25),
    ],
];
