<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use Livewire\Livewire;
use App\Livewire\Kiosk\CheckIn;
use App\Notifications\SecurityAlert;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class SecurityAlertTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_a_security_alert_when_a_flagged_visitor_checks_in()
    {
        Notification::fake();

        $tenant = Tenant::create(['name' => 'Secure Hub', 'slug' => 'secure']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Security Officer',
            'email' => 'security@secure.com',
            'password' => bcrypt('password')
        ]);

        $flaggedVisitor = Visitor::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Malicious',
            'last_name' => 'User',
            'email' => 'malicious.user@example.com',
            'is_flagged' => true,
            'internal_notes' => 'Known trespasser.'
        ]);

        Livewire::test(CheckIn::class)
            ->set('selected_company', $tenant->id)
            ->set('first_name', 'Malicious')
            ->set('last_name', 'User')
            ->set('purpose', 'To cause trouble')
            ->call('submit');

        Notification::assertSentTo(
            [$user], SecurityAlert::class
        );
    }
}
