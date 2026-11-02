<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Professional;
use App\Models\ProfessionalType;
use App\Models\Review;
use App\Models\Role;
use App\Models\Service;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        $types = collect([
            [
                'name' => 'Psychologist',
                'slug' => 'psychologist',
                'icon' => 'bi-heart-pulse',
                'description' => 'Mental health and psychology professionals.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Legal Advisor',
                'slug' => 'legal_advisor',
                'icon' => 'bi-briefcase',
                'description' => 'Legal consultation professionals.',
                'sort_order' => 2,
            ],
        ])->mapWithKeys(function (array $type): array {
            $model = ProfessionalType::query()->updateOrCreate(
                ['slug' => $type['slug']],
                $type + ['status' => true],
            );

            return [$type['slug'] => $model];
        });

        $professionals = [
            // [
            //     'type' => 'psychologist',
            //     'role' => 'psychologist',
            //     'first_name' => 'Amelia',
            //     'last_name' => 'Hartmann',
            //     'email' => 'amelia.hartmann@example.com',
            //     'title' => 'Dr.',
            //     'license_number' => 'PSY-CH-1001',
            //     'specialization' => 'Anxiety & Stress Management',
            //     'experience_years' => 12,
            //     'consultation_fee' => 150,
            //     'bio' => 'Licensed psychologist helping adults manage anxiety, stress, and major life changes through evidence-based care.',
            //     'languages' => ['en', 'fr_CH'],
            //     'city' => 'Geneva',
            //     'country' => 'Switzerland',
            //     'is_featured' => true,
            // ],
            // [
            //     'type' => 'psychologist',
            //     'role' => 'psychologist',
            //     'first_name' => 'Noah',
            //     'last_name' => 'Dubois',
            //     'email' => 'noah.dubois@example.com',
            //     'title' => 'Dr.',
            //     'license_number' => 'PSY-CH-1002',
            //     'specialization' => 'Cognitive Behavioral Therapy',
            //     'experience_years' => 9,
            //     'consultation_fee' => 135,
            //     'bio' => 'CBT specialist focused on practical strategies for emotional well-being, confidence, and healthy daily routines.',
            //     'languages' => ['fr_CH', 'en'],
            //     'city' => 'Lausanne',
            //     'country' => 'Switzerland',
            //     'is_featured' => true,
            // ],
            [
                'type' => 'psychologist',
                'role' => 'psychologist',
                'first_name' => 'Sofia',
                'last_name' => 'Keller',
                'email' => 'sofia.keller@example.com',
                'title' => 'Ms.',
                'license_number' => 'PSY-CH-1003',
                'specialization' => 'Family & Relationship Counseling',
                'experience_years' => 7,
                'consultation_fee' => 120,
                'bio' => 'Relationship counselor supporting individuals, couples, and families with compassionate and confidential guidance.',
                // 'languages' => ['en', 'fr_CH'],
                'languages' => \App\Support\Locales::codes(),
                'city' => 'Zurich',
                'country' => 'Switzerland',
                'is_featured' => false,
            ],
            // [
            //     'type' => 'psychologist',
            //     'role' => 'psychologist',
            //     'first_name' => 'Lucas',
            //     'last_name' => 'Martin',
            //     'email' => 'psychologist@example.com',
            //     'title' => 'Mr.',
            //     'license_number' => 'PSY-CH-1004',
            //     'specialization' => 'Trauma & PTSD',
            //     'experience_years' => 10,
            //     'consultation_fee' => 145,
            //     'bio' => 'Trauma-informed psychologist providing a safe and structured path toward recovery and resilience.',
            //     'languages' => ['fr_CH', 'en'],
            //     'city' => 'Geneva',
            //     'country' => 'Switzerland',
            //     'is_featured' => false,
            // ],
            // [
            //     'type' => 'legal_advisor',
            //     'role' => 'legal_advisor',
            //     'first_name' => 'Emma',
            //     'last_name' => 'Rossi',
            //     'email' => 'emma.rossi@example.com',
            //     'title' => 'Attorney',
            //     'license_number' => 'LAW-CH-2001',
            //     'specialization' => 'Family Law',
            //     'experience_years' => 11,
            //     'consultation_fee' => 180,
            //     'bio' => 'Legal advisor offering clear, practical guidance in family law and sensitive personal matters.',
            //     'languages' => ['en', 'fr_CH'],
            //     'city' => 'Geneva',
            //     'country' => 'Switzerland',
            //     'is_featured' => true,
            // ],
        ];

        foreach ($professionals as $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['first_name'].' '.$data['last_name'],
                    'password' => 'Password@123',
                    'locale' => 'en',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );

            $user->update([
                'name' => $data['first_name'].' '.$data['last_name'],
                'is_active' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            if (Role::query()->where('slug', $data['role'])->exists()) {
                $user->syncRbacRoles([$data['role']]);
            }

            $professional = Professional::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'professional_type_id' => $types[$data['type']]->id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'slug' => str($data['first_name'].' '.$data['last_name'])->slug().'-'.$user->id,
                    'title' => $data['title'],
                    'email' => $data['email'],
                    'license_number' => $data['license_number'],
                    'specialization' => $data['specialization'],
                    'experience_years' => $data['experience_years'],
                    'consultation_fee' => $data['consultation_fee'],
                    'bio' => $data['bio'],
                    'languages' => $data['languages'],
                    'city' => $data['city'],
                    'country' => $data['country'],
                    'online_consultation' => true,
                    'offline_consultation' => true,
                    'is_verified' => true,
                    'verification_status' => Professional::VERIFICATION_VERIFIED,
                    'is_accepting_patients' => true,
                    'max_active_patients' => 40,
                    'is_featured' => $data['is_featured'],
                    'status' => true,
                ],
            );

            /*
             * Legacy psychologists row disabled; professionals is now the
             * single profile source.
             *
             * Psychologist::query()->updateOrCreate(...);
             */

            $this->seedProfileDetails($professional);
        }
    }

    private function seedProfileDetails(Professional $professional): void
    {
        $specializations = match ($professional->license_number) {
            'PSY-CH-1001' => ['Anxiety', 'Stress Management', 'Depression'],
            'PSY-CH-1002' => ['Cognitive Behavioral Therapy', 'OCD', 'Depression'],
            'PSY-CH-1003' => ['Relationship Counseling', 'Family Therapy', 'Adolescent Psychology'],
            'PSY-CH-1004' => ['Trauma & PTSD', 'Anxiety', 'Grief Counseling'],
            default => [$professional->specialization],
        };

        $specializationIds = collect($specializations)
            ->filter()
            ->map(function (string $name): int {
                return Specialization::query()->updateOrCreate(
                    ['slug' => str($name)->slug()],
                    [
                        'name' => $name,
                        'description' => $name.' consultation and therapeutic support.',
                        'status' => true,
                    ]
                )->id;
            });

        $professional->specializations()->sync($specializationIds);

        Education::query()->updateOrCreate(
            [
                'professional_id' => $professional->id,
                'degree' => 'M.Sc. Clinical Psychology',
            ],
            [
                'institution' => 'University of Geneva',
                'field_of_study' => 'Clinical Psychology',
                'year' => now()->year - $professional->experience_years - 2,
                'description' => 'Advanced clinical training and supervised practice.',
            ]
        );

        Education::query()->updateOrCreate(
            [
                'professional_id' => $professional->id,
                'degree' => 'B.Sc. Psychology',
            ],
            [
                'institution' => 'University of Lausanne',
                'field_of_study' => 'Psychology',
                'year' => now()->year - $professional->experience_years - 5,
            ]
        );

        Experience::query()->updateOrCreate(
            [
                'professional_id' => $professional->id,
                'organization' => 'MindCare Clinic',
                'designation' => 'Senior Clinical Psychologist',
            ],
            [
                'start_date' => now()->subYears(4)->startOfYear(),
                'end_date' => null,
                'currently_working' => true,
                'description' => 'Provides individual consultations and evidence-based treatment plans.',
            ]
        );

        Experience::query()->updateOrCreate(
            [
                'professional_id' => $professional->id,
                'organization' => 'Geneva Wellness Centre',
                'designation' => 'Clinical Psychologist',
            ],
            [
                'start_date' => now()->subYears(8)->startOfYear(),
                'end_date' => now()->subYears(4)->endOfYear(),
                'currently_working' => false,
                'description' => 'Delivered psychological assessments and counseling.',
            ]
        );

        foreach ([
            ['name' => 'Individual Consultation', 'duration' => 50, 'price' => $professional->consultation_fee],
            ['name' => 'Online Counseling', 'duration' => 45, 'price' => $professional->consultation_fee],
            ['name' => 'Initial Assessment', 'duration' => 60, 'price' => (float) $professional->consultation_fee + 20],
        ] as $service) {
            Service::query()->updateOrCreate(
                [
                    'professional_id' => $professional->id,
                    'name' => $service['name'],
                ],
                $service + [
                    'description' => $service['name'].' with a personalized care plan.',
                    'status' => true,
                ]
            );
        }

        foreach ([
            ['day' => 'Monday', 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
            ['day' => 'Tuesday', 'start_time' => '10:00:00', 'end_time' => '18:00:00'],
            ['day' => 'Wednesday', 'start_time' => '09:00:00', 'end_time' => '15:00:00'],
        ] as $availability) {
            Availability::query()->updateOrCreate(
                [
                    'professional_id' => $professional->id,
                    'day' => $availability['day'],
                    'start_time' => $availability['start_time'],
                ],
                $availability + ['is_available' => true]
            );
        }

        foreach ([
            [
                'reviewer_name' => 'Marie L.',
                'reviewer_email' => 'marie.review@example.com',
                'rating' => 5,
                'review' => 'Very supportive, attentive, and easy to talk to. I felt understood from the first session.',
            ],
            [
                'reviewer_name' => 'Daniel R.',
                'reviewer_email' => 'daniel.review@example.com',
                'rating' => 5,
                'review' => 'Professional guidance with practical steps that made a real difference.',
            ],
        ] as $review) {
            Review::query()->updateOrCreate(
                [
                    'professional_id' => $professional->id,
                    'reviewer_email' => $review['reviewer_email'],
                ],
                $review + [
                    'is_approved' => true,
                    'status' => true,
                ]
            );
        }

        $approvedReviews = $professional->reviews()
            ->where('is_approved', true)
            ->where('status', true);

        $professional->update([
            'rating' => round((float) $approvedReviews->average('rating'), 2),
            'total_reviews' => $approvedReviews->count(),
        ]);
    }
}
