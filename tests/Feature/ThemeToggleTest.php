<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_layout_offers_light_dark_and_system(): void
    {
        foreach (['/', '/login'] as $url) {
            $this->get($url)
                ->assertSee('data-theme-choice="light"', false)
                ->assertSee('data-theme-choice="dark"', false)
                ->assertSee('data-theme-choice="system"', false);
        }

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertSee('data-theme-choice="system"', false);
    }

    public function test_the_inline_theme_script_is_allowed_by_its_hash_and_nothing_else_inline_is(): void
    {
        $response = $this->get('/login');
        $html = (string) $response->getContent();
        $policy = (string) $response->headers->get('Content-Security-Policy');

        // Every inline script without a nonce must be the theme script, byte for byte, or the browser blocks it.
        preg_match_all('#<script(?![^>]*\bsrc=)(?![^>]*\bnonce=)(?![^>]*type="application/json")[^>]*>(.*?)</script>#s', $html, $inline);
        $this->assertCount(1, $inline[1]);

        $hash = base64_encode(hash('sha256', $inline[1][0], true));
        $this->assertStringContainsString("'sha256-{$hash}'", $policy);
    }
}
