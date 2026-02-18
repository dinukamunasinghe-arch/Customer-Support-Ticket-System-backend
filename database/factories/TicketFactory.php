<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $categories = ['technical', 'billing', 'account', 'general', 'feature_request', 'bug_report'];

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'category' => $this->faker->randomElement($categories),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'customer_phone' => $this->faker->optional()->phoneNumber(),
            'assigned_to' => User::where('role', 'agent')->inRandomOrder()->first()->id ?? null,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    public function open()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'open',
            ];
        });
    }

    public function highPriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'high',
            ];
        });
    }
}