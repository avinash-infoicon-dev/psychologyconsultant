<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Patient;
use App\Models\Psychologist;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'psychologist_id' => Psychologist::factory(),
            'status' => 'active',
            'last_message_at' => now(),
        ];
    }
}
