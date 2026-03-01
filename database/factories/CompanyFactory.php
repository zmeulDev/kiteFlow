<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'contact_person' => fake()->name(),
            'contact_person_email' => fake()->safeEmail(),
            'contact_person_phone' => fake()->phoneNumber(),
            'is_active' => true,
            'contract_start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'contract_end_date' => fake()->dateTimeBetween('now', '+1 year'),
            'parent_id' => null,
            'main_contact_user_id' => null,
        ];
    }

    public function subCompany(Company $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
