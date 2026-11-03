<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

/**
 * Laravel builds absolute URLs from the request's Host header. Without a list of trusted hosts, a request sent with
 * someone else's Host gets links to that site: once the app has accounts, a password reset asked for with a forged
 * Host emails the victim a real message whose link hands their token to the attacker.
 */
final class TrustedHostsTest extends TestCase
{
    protected function tearDown(): void
    {
        // Symfony keeps the trusted hosts on Request itself, across every test in the run.
        Request::setTrustedHosts([]);

        parent::tearDown();
    }

    public function test_production_answers_only_for_its_own_host_and_subdomains(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://example.com']);

        $this->get('https://example.com/')->assertOk();
        $this->get('https://www.example.com/')->assertOk();

        // Turned away as a bad request, on the kit's own error page, which never repeats the forged host.
        $forged = $this->get('https://evil.example/');
        $forged->assertStatus(400);
        $this->assertStringNotContainsString('evil.example', (string) $forged->getContent());

        // Not one of its subdomains, though it ends the same way.
        $this->get('https://notexample.com/')->assertStatus(400);
    }

    public function test_local_development_answers_for_any_host(): void
    {
        $this->app['env'] = 'local';
        config(['app.url' => 'https://example.com']);

        // php artisan serve, Herd's .test domains, a phone on the local network: whatever the developer opens it as.
        $this->get('http://192.168.1.20:8000/')->assertOk();
    }
}
