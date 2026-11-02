<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Permission;
use App\Models\Professional;
use App\Models\ProfessionalType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'dashboard'],
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users'],
            ['name' => 'View Roles', 'slug' => 'roles.view', 'group' => 'roles'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'group' => 'roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'group' => 'roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'group' => 'roles'],
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'group' => 'permissions'],
            ['name' => 'Assign Permissions', 'slug' => 'permissions.assign', 'group' => 'permissions'],
            ['name' => 'View Psychologists', 'slug' => 'psychologists.view', 'group' => 'psychologists'],
            ['name' => 'Verify Psychologists', 'slug' => 'psychologists.verify', 'group' => 'psychologists'],
            ['name' => 'Suspend Psychologists', 'slug' => 'psychologists.suspend', 'group' => 'psychologists'],
            ['name' => 'View Patients', 'slug' => 'patients.view', 'group' => 'patients'],
            ['name' => 'View Specializations', 'slug' => 'specializations.view', 'group' => 'specializations'],
            ['name' => 'Create Specializations', 'slug' => 'specializations.create', 'group' => 'specializations'],
            ['name' => 'Edit Specializations', 'slug' => 'specializations.edit', 'group' => 'specializations'],
            ['name' => 'Delete Specializations', 'slug' => 'specializations.delete', 'group' => 'specializations'],
            ['name' => 'View Logs', 'slug' => 'logs.view', 'group' => 'logs'],
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'group' => 'settings'],
            ['name' => 'View Reports', 'slug' => 'reports.view', 'group' => 'reports'],
            ['name' => 'Manage Reports', 'slug' => 'reports.manage', 'group' => 'reports'],
            ['name' => 'View Plans', 'slug' => 'plans.view', 'group' => 'plans'],
            ['name' => 'Create Plans', 'slug' => 'plans.create', 'group' => 'plans'],
            ['name' => 'Edit Plans', 'slug' => 'plans.edit', 'group' => 'plans'],
            ['name' => 'Delete Plans', 'slug' => 'plans.delete', 'group' => 'plans'],
            ['name' => 'View Subscriptions', 'slug' => 'subscriptions.view', 'group' => 'subscriptions'],
            ['name' => 'Adjust Subscription Exchanges', 'slug' => 'subscriptions.adjust', 'group' => 'subscriptions'],
            ['name' => 'View CMS Categories', 'slug' => 'cms.categories.view', 'group' => 'cms'],
            ['name' => 'Create CMS Categories', 'slug' => 'cms.categories.create', 'group' => 'cms'],
            ['name' => 'Edit CMS Categories', 'slug' => 'cms.categories.edit', 'group' => 'cms'],
            ['name' => 'Delete CMS Categories', 'slug' => 'cms.categories.delete', 'group' => 'cms'],
            ['name' => 'View CMS Pages', 'slug' => 'cms.pages.view', 'group' => 'cms'],
            ['name' => 'Create CMS Pages', 'slug' => 'cms.pages.create', 'group' => 'cms'],
            ['name' => 'Edit CMS Pages', 'slug' => 'cms.pages.edit', 'group' => 'cms'],
            ['name' => 'Delete CMS Pages', 'slug' => 'cms.pages.delete', 'group' => 'cms'],
            ['name' => 'View Home Sliders', 'slug' => 'cms.sliders.view', 'group' => 'cms'],
            ['name' => 'Create Home Sliders', 'slug' => 'cms.sliders.create', 'group' => 'cms'],
            ['name' => 'Edit Home Sliders', 'slug' => 'cms.sliders.edit', 'group' => 'cms'],
            ['name' => 'Delete Home Sliders', 'slug' => 'cms.sliders.delete', 'group' => 'cms'],
            ['name' => 'View Testimonials', 'slug' => 'cms.testimonials.view', 'group' => 'cms'],
            ['name' => 'Create Testimonials', 'slug' => 'cms.testimonials.create', 'group' => 'cms'],
            ['name' => 'Edit Testimonials', 'slug' => 'cms.testimonials.edit', 'group' => 'cms'],
            ['name' => 'Delete Testimonials', 'slug' => 'cms.testimonials.delete', 'group' => 'cms'],
            ['name' => 'View Team Members', 'slug' => 'cms.team.view', 'group' => 'cms'],
            ['name' => 'Create Team Members', 'slug' => 'cms.team.create', 'group' => 'cms'],
            ['name' => 'Edit Team Members', 'slug' => 'cms.team.edit', 'group' => 'cms'],
            ['name' => 'Delete Team Members', 'slug' => 'cms.team.delete', 'group' => 'cms'],
            ['name' => 'View FAQs', 'slug' => 'cms.faqs.view', 'group' => 'cms'],
            ['name' => 'Create FAQs', 'slug' => 'cms.faqs.create', 'group' => 'cms'],
            ['name' => 'Edit FAQs', 'slug' => 'cms.faqs.edit', 'group' => 'cms'],
            ['name' => 'Delete FAQs', 'slug' => 'cms.faqs.delete', 'group' => 'cms'],
            ['name' => 'View Blog Categories', 'slug' => 'cms.blog.categories.view', 'group' => 'cms'],
            ['name' => 'Create Blog Categories', 'slug' => 'cms.blog.categories.create', 'group' => 'cms'],
            ['name' => 'Edit Blog Categories', 'slug' => 'cms.blog.categories.edit', 'group' => 'cms'],
            ['name' => 'Delete Blog Categories', 'slug' => 'cms.blog.categories.delete', 'group' => 'cms'],
            ['name' => 'View Blog Posts', 'slug' => 'cms.blog.posts.view', 'group' => 'cms'],
            ['name' => 'Create Blog Posts', 'slug' => 'cms.blog.posts.create', 'group' => 'cms'],
            ['name' => 'Edit Blog Posts', 'slug' => 'cms.blog.posts.edit', 'group' => 'cms'],
            ['name' => 'Delete Blog Posts', 'slug' => 'cms.blog.posts.delete', 'group' => 'cms'],
            ['name' => 'View CMS Header & Footer', 'slug' => 'cms.site.view', 'group' => 'cms'],
            ['name' => 'Edit CMS Header & Footer', 'slug' => 'cms.site.edit', 'group' => 'cms'],
            ['name' => 'View CMS Menus', 'slug' => 'cms.menus.view', 'group' => 'cms'],
            ['name' => 'Create CMS Menus', 'slug' => 'cms.menus.create', 'group' => 'cms'],
            ['name' => 'Edit CMS Menus', 'slug' => 'cms.menus.edit', 'group' => 'cms'],
            ['name' => 'Delete CMS Menus', 'slug' => 'cms.menus.delete', 'group' => 'cms'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'superadmin',
                'description' => 'Full system access with elevated dashboard',
                'is_full_access' => true,
                'is_active' => false,
                'is_system' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full administrative access',
                'is_full_access' => true,
                'is_active' => false,
                'is_system' => true,
            ],
            [
                'name' => 'Psychologist',
                'slug' => 'psychologist',
                'description' => 'Psychology consultation access',
                'is_full_access' => false,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Legal Advisor',
                'slug' => 'legal_advisor',
                'description' => 'Legal consultation access',
                'is_full_access' => false,
                'is_active' => false,
                'is_system' => true,
            ],
            [
                'name' => 'patient',
                'slug' => 'patient',
                'description' => 'Patient access',
                'is_full_access' => false,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'support',
                'slug' => 'support',
                'description' => 'Support staff access',
                'is_full_access' => false,
                'is_active' => false,
                'is_system' => false,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'User access',
                'is_full_access' => false,
                'is_active' => false,
                'is_system' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::query()->updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $allPermissionIds = Permission::query()->pluck('id');

        Role::query()->whereIn('slug', ['superadmin', 'admin'])->each(function (Role $role) use ($allPermissionIds) {
            $role->permissions()->sync($allPermissionIds);
        });

        $psychologist = Role::query()->where('slug', 'psychologist')->first();
        $psychologist?->syncPermissions(['dashboard.view']);

        $legalAdvisor = Role::query()->where('slug', 'legal_advisor')->first();
        $legalAdvisor?->syncPermissions(['dashboard.view']);

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => 'admin@123',
                'is_active' => true,
                'locale' => 'fr_CH',
                'role' => 'superadmin',
                'phone' => '0781234567',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'admin@123',
                'is_active' => true,
                'locale' => 'fr_CH',
                'role' => 'admin',
                'phone' => '0781234567',
            ],

            [
                'name' => 'Admin User',
                'email' => 'p_admin@example.com',
                'password' => 'admin@123',
                'is_active' => true,
                'locale' => 'fr_CH',
                'role' => 'admin',
                'phone' => '078123423423',
            ],

            // [
            //     'name' => 'Psychologist User',
            //     'email' => 'psychologist@example.com',
            //     'password' => 'admin@123',
            //     'is_active' => true,
            //     'locale' => 'fr_CH',
            //     'role' => 'psychologist',
            //     'phone' => '0781234567',
            //     'email_verified_at' => now(),
            // ],
            // [
            //     'name' => 'Legal Advisor',
            //     'email' => 'legal@example.com',
            //     'password' => 'admin@123',
            //     'is_active' => true,
            //     'locale' => 'fr_CH',
            //     'role' => 'legal_advisor',
            //     'phone' => '0781234567',
            // ],
            [
                'name' => 'Patient User',
                'email' => 'patient@example.com',
                'password' => 'admin@123',
                'is_active' => true,
                'locale' => 'fr_CH',
                'role' => 'patient',
                'phone' => '0781234567',
                'email_verified_at' => now(),
            ],
            // [
            //     'name' => 'Support User',
            //     'email' => 'support@example.com',
            //     'password' => 'admin@123',
            //     'is_active' => true,
            //     'locale' => 'fr_CH',
            //     'role' => 'support',
            // ],
            // [
            //     'name' => 'User User',
            //     'email' => 'user@example.com',
            //     'password' => 'admin@123',
            //     'is_active' => true,
            //     'locale' => 'fr_CH',
            //     'role' => 'user',
            // ],
        ];

        foreach ($users as $userData) {
            $roleSlug = $userData['role'];
            unset($userData['role']);

            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->syncRbacRoles([$roleSlug]);
            $this->ensureRoleProfile($user, $roleSlug);
        }
    }

    protected function ensureRoleProfile(User $user, string $roleSlug): void
    {
        if ($roleSlug === 'patient') {
            // Legacy source: Psychologist::query()->value('id');
            $psychologistId = Professional::query()
                ->whereHas('professionalType', fn ($query) => $query->where('slug', 'psychologist'))
                ->value('id');

            Patient::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => '1990-01-15',
                    'gender' => 'other',
                    'country' => 'CH',
                    'psychologist_id' => $psychologistId,
                ]
            );

            return;
        }

        if ($roleSlug === 'psychologist') {
            $nameParts = explode(' ', trim($user->name), 2);
            $type = ProfessionalType::query()->firstOrCreate(
                ['slug' => 'psychologist'],
                [
                    'name' => 'Psychologist',
                    'status' => true,
                    'sort_order' => 1,
                ]
            );

            Professional::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'professional_type_id' => $type->id,
                    'first_name' => $nameParts[0] ?: 'Psychologist',
                    'last_name' => $nameParts[1] ?? null,
                    'slug' => Str::slug($user->name).'-'.$user->id,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'license_number' => 'PSY-SEED-'.$user->id,
                    'specialization' => 'General Psychology',
                    'experience_years' => 5,
                    'bio' => 'Seeded psychologist profile for local development.',
                    // 'languages' => ['fr_CH', 'en'],
                    'languages' => \App\Support\Locales::codes(),
                    'is_verified' => true,
                    'verification_status' => Professional::VERIFICATION_VERIFIED,
                    'is_accepting_patients' => true,
                    'max_active_patients' => 40,
                    'status' => true,
                ]
            );

            /*
             * Legacy psychologists profile disabled.
             * Psychologist::query()->updateOrCreate(...);
             */
        }
    }
}
