<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $conversation = Conversation::factory()->create();

        return [
            'conversation_id' => $conversation->id,
            'sender_id' => $conversation->patient->user_id,
            'type' => 'text',
            'body' => fake()->paragraph(),
            'is_read' => false,
        ];
    }

    public function fromPsychologist(): static
    {
        return $this->state(function (array $attributes) {
            $conversation = Conversation::find($attributes['conversation_id']);

            return ['sender_id' => $conversation->psychologist->user_id];
        });
    }
}
