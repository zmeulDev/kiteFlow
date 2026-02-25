<?php

namespace Database\Factories;

use App\Models\Entrance;
use App\Models\KioskSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KioskSetting>
 */
class KioskSettingFactory extends Factory
{
    protected $model = KioskSetting::class;

    public function definition(): array
    {
        return [
            'entrance_id' => Entrance::factory(),
            'welcome_message' => 'Welcome! Please sign in.',
            'logo_path' => null,
            'background_color' => '#ffffff',
            'primary_color' => '#3b82f6',
            'require_photo' => false,
            'require_signature' => false,
            'show_nda' => false,
            'gdpr_text' => null,
            'nda_text' => null,
        ];
    }

    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'require_photo' => true,
        ]);
    }

    public function withSignature(): static
    {
        return $this->state(fn (array $attributes) => [
            'require_signature' => true,
        ]);
    }

    public function withNda(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_nda' => true,
            'nda_text' => 'NDA agreement text...',
        ]);
    }
}