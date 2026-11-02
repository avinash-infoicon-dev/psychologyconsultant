<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Professional;
use App\Models\ProfessionalType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $patientUser = User::query()->where('email', 'patient@example.com')->first();
        $psychologistUser = User::query()->where('email', 'psychologist@example.com')->first();

        if (! $patientUser || ! $psychologistUser) {
            return;
        }

        $patient = Patient::query()->firstOrCreate(
            ['user_id' => $patientUser->id],
            [
                'date_of_birth' => '1990-01-15',
                'gender' => 'other',
                'country' => 'CH',
            ]
        );

        // $psychologist = Psychologist::query()->firstOrCreate(
        //     ['user_id' => $psychologistUser->id],
        //     [
        //         'license_number' => 'PSY-SEED-'.$psychologistUser->id,
        //         'specialization' => 'General Psychology',
        //         'years_experience' => 5,
        //         'is_verified' => true,
        //         'is_accepting_patients' => true,
        //         'languages' => ['fr_CH', 'en'],
        //     ]
        // );

        // Professional profiles now replace the old psychologists table.
        $type = ProfessionalType::query()->firstOrCreate(
            ['slug' => 'psychologist'],
            [
                'name' => 'Psychologist',
                'status' => true,
                'sort_order' => 1,
            ]
        );
        $nameParts = explode(' ', trim($psychologistUser->name), 2);
        $psychologist = Professional::query()->updateOrCreate(
            ['user_id' => $psychologistUser->id],
            [
                'professional_type_id' => $type->id,
                'first_name' => $nameParts[0] ?: 'Psychologist',
                'last_name' => $nameParts[1] ?? null,
                'slug' => Str::slug($psychologistUser->name).'-'.$psychologistUser->id,
                'email' => $psychologistUser->email,
                'license_number' => 'PSY-SEED-'.$psychologistUser->id,
                'specialization' => 'General Psychology',
                'experience_years' => 5,
                // 'languages' => ['fr_CH', 'en'],
                'languages' => \App\Support\Locales::codes(),
                'is_verified' => true,
                'verification_status' => Professional::VERIFICATION_VERIFIED,
                'is_accepting_patients' => true,
                'status' => true,
            ]
        );

        $patientUser->update(['email_verified_at' => $patientUser->email_verified_at ?? now()]);
        $patient->update(['psychologist_id' => $psychologist->id]);

        $conversation = Conversation::query()->updateOrCreate(
            [
                'patient_id' => $patient->id,
                'psychologist_id' => $psychologist->id,
            ],
            [
                'status' => 'active',
                'last_message_at' => now(),
            ]
        );

        $dummyMessages = [
            [
                'sender_id' => $patientUser->id,
                'body' => 'Hello, I would like to start my consultation.',
                'is_read' => true,
                'created_at' => now()->subHours(3),
            ],
            [
                'sender_id' => $psychologistUser->id,
                'body' => 'Welcome! I am here to help. How are you feeling today?',
                'is_read' => true,
                'created_at' => now()->subHours(2),
            ],
            [
                'sender_id' => $patientUser->id,
                'body' => 'I have been feeling anxious lately and wanted some guidance.',
                'is_read' => false,
                'created_at' => now()->subHour(),
            ],
            [
                'sender_id' => $psychologistUser->id,
                'body' => 'Thank you for sharing. We can work through this together step by step.',
                'is_read' => false,
                'created_at' => now()->subMinutes(20),
            ],
        ];

        $conversation->messages()->delete();

        foreach ($dummyMessages as $message) {
            Message::query()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $message['sender_id'],
                'type' => 'text',
                'body' => $message['body'],
                'is_read' => $message['is_read'],
                'read_at' => $message['is_read'] ? now() : null,
                'created_at' => $message['created_at'],
                'updated_at' => $message['created_at'],
            ]);
        }

        $conversation->update(['last_message_at' => now()->subMinutes(20)]);
    }
}
