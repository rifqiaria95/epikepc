<?php

namespace App\Enums\Career;

enum ReferralSource: string
{
    case CompanyWebsite = 'COMPANY_WEBSITE';
    case LinkedIn = 'LINKEDIN';
    case JobPortal = 'JOB_PORTAL';
    case EmployeeReferral = 'EMPLOYEE_REFERRAL';
    case SocialMedia = 'SOCIAL_MEDIA';
    case Other = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::CompanyWebsite => 'Website perusahaan',
            self::LinkedIn => 'LinkedIn',
            self::JobPortal => 'Job portal',
            self::EmployeeReferral => 'Referral karyawan',
            self::SocialMedia => 'Media sosial',
            self::Other => 'Lainnya',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
