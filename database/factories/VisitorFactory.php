<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitor>
 */
class VisitorFactory extends Factory
{
    protected $model = Visitor::class;

    public function definition(): array
    {
        return [
            'company_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'id_document_type' => null,
            'id_document_number' => null,
            'photo_path' => null,
            'signature_path' => null,
        ];
    }

    public function withCompany(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => Company::factory(),
        ]);
    }
}
