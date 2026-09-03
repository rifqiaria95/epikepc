<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CareerPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_career_dashboard',
            'view_vacancies',
            'create_vacancies',
            'edit_vacancies',
            'publish_vacancies',
            'close_vacancies',
            'archive_vacancies',
            'view_applications',
            'review_applications',
            'assign_applications',
            'change_application_status',
            'reject_applications',
            'view_candidates',
            'view_candidate_documents',
            'download_candidate_documents',
            'create_application_notes',
            'delete_application_notes',
            'manage_career_settings',
        ];

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate(['name' => $name]);
            if (! $superadmin->hasPermissionTo($permission)) {
                $superadmin->givePermissionTo($permission);
            }
        }
    }
}
