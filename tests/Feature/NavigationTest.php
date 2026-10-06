<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Tests\TestCase;

final class NavigationTest extends TestCase
{
    public function test_back_never_shows_a_stored_page(): void
    {
        // A page and an error page.
        foreach ([$this->get('/'), $this->get('/no-such-page')] as $response) {
            $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
            $response->assertHeader('X-Frame-Options', 'DENY')->assertHeader('Content-Security-Policy');
        }
    }

    public function test_a_missing_page_says_so_and_leads_home(): void
    {
        $this->assertBackLink($this->get('/no-such-page')->assertNotFound()->assertSee('Page not found'), route('home'));
    }

    public function test_a_server_error_shows_no_details_and_leads_home(): void
    {
        config(['app.debug' => false]);
        Route::get('/broken', fn () => throw new RuntimeException('database password is hunter2'));

        $this->get('/broken')
            ->assertServerError()
            ->assertSee('Something went wrong on our side')
            ->assertSee('Go to the home page')
            ->assertDontSee('hunter2');
    }

    // The back arrow is the first link on the page, in the top corner.
    private function assertBackLink(TestResponse $response, string $href): void
    {
        preg_match('#<body[^>]*>.*?<a\s+href="([^"]+)"#s', (string) $response->getContent(), $link);

        $this->assertSame($href, $link[1] ?? null);
    }
}
