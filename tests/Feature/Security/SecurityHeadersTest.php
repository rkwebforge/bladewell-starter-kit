<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Tests\TestCase;

final class SecurityHeadersTest extends TestCase
{
    public function test_pages_send_the_security_headers(): void
    {
        $this->get('/login')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Permissions-Policy');
    }

    public function test_the_policy_only_runs_scripts_from_the_site_or_with_this_requests_nonce(): void
    {
        $response = $this->get('/login');
        $policy = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("base-uri 'self'", $policy);
        $this->assertStringNotContainsString('unsafe-inline', $policy);
        $this->assertStringNotContainsString('unsafe-eval', $policy);

        preg_match("/'nonce-([^']+)'/", $policy, $nonce);
        $this->assertNotEmpty($nonce[1] ?? null);
        $this->assertStringContainsString('nonce="'.$nonce[1].'"', (string) $response->getContent());
    }

    public function test_the_nonce_changes_on_every_request(): void
    {
        $first = $this->get('/login')->headers->get('Content-Security-Policy');
        $second = $this->get('/login')->headers->get('Content-Security-Policy');

        $this->assertNotSame($first, $second);
    }

    public function test_added_sources_and_embedding_origins_come_from_the_config(): void
    {
        config([
            'security.csp.script' => ['https://js.stripe.com'],
            'security.frame_ancestors' => ['https://admin.shopify.com'],
        ]);

        $response = $this->get('/login')->assertHeaderMissing('X-Frame-Options');
        $policy = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('https://js.stripe.com', $policy);
        $this->assertStringContainsString('frame-ancestors https://admin.shopify.com', $policy);
    }

    public function test_report_only_mode_reports_without_blocking(): void
    {
        config(['security.csp.report_only' => true]);

        $this->get('/login')
            ->assertHeaderMissing('Content-Security-Policy')
            ->assertHeader('Content-Security-Policy-Report-Only');
    }

    public function test_hsts_is_sent_only_in_production_over_https(): void
    {
        $this->get('https://localhost/login')->assertHeaderMissing('Strict-Transport-Security');

        $this->app['env'] = 'production';

        $this->get('http://localhost/login')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/login')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }
}
