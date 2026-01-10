<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // protected $model = Invitation::class;
    public function forProject($id): InvitationFactory
    {
        return $this->state(function ()use ($id) {
            return [
                'invitable_id' => $id,
                'invitable_type' => Project::class,
            ];
        });
    }

    public function forTask()
    {
        return $this->state(function () {
            return [
                'invitable_id' => Task::factory(),
                'invitable_type' => Task::class
            ];
        });
    }

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'sender_id' => User::factory(),
        ];
    }
}
