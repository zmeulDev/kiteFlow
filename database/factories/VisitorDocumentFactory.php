<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\VisitorDocument;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VisitorDocumentFactory extends Factory
{
    protected $model = VisitorDocument::class;

    public function definition(): array
    {
        $documentTypes = ['id_card', 'passport', 'driver_license', 'nda', 'photo', 'signature', 'insurance', 'other'];
        $mimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'visitor_id' => Visitor::factory(),
            'type' => fake()->randomElement($documentTypes),
            'file_path' => 'visitor_documents/' . Str::random(10) . '.' . fake()->fileExtension(),
            'file_name' => fake()->word() . '.' . fake()->fileExtension(),
            'mime_type' => fake()->randomElement($mimes),
            'file_size' => fake()->numberBetween(1024, 10485760), // 1KB to 10MB
            'metadata' => [
                'uploaded_by' => 'system',
                'uploaded_at' => now()->toIso8601String(),
            ],
        ];
    }

    public function idCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'id_card',
            'file_name' => 'id_card_' . fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function passport(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'passport',
            'file_name' => 'passport_' . fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    public function driverLicense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'driver_license',
            'file_name' => 'license_' . fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function nda(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'nda',
            'file_name' => 'nda_signed_' . fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    public function photo(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'photo',
            'file_name' => 'visitor_photo_' . fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(51200, 524288), // 50KB to 500KB
        ]);
    }

    public function signature(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'signature',
            'file_name' => 'signature_' . fake()->word() . '.png',
            'mime_type' => 'image/png',
            'file_size' => fake()->numberBetween(10240, 102400), // 10KB to 100KB
        ]);
    }

    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => 'application/pdf',
            'file_name' => fake()->word() . '.pdf',
        ]);
    }

    public function image(): static
    {
        $imageMimes = ['image/jpeg', 'image/png', 'image/jpg'];

        return $this->state(fn (array $attributes) => [
            'mime_type' => fake()->randomElement($imageMimes),
            'file_name' => fake()->word() . fake()->randomElement(['.jpg', '.png', '.jpeg']),
        ]);
    }
}