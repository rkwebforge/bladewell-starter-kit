<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Tests\TestCase;

final class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_sign_in_page_leads_back_where_people_came_from(): void
    {
        $this->assertBackLink($this->get('/login'), route('home'));
        $this->assertBackLink($this->get('/register'), route('home'));
        $this->assertBackLink($this->get('/forgot-password'), route('login'));
        $this->assertBackLink($this->get('/reset-password/a-token?email=ana@example.com'), route('login'));
        $this->assertBackLink($this->actingAs(User::factory()->unverified()->create())->get('/verify-email'), route('home'));
        $this->assertBackLink($this->actingAs(User::factory()->create())->get('/confirm-password'), route('dashboard'));
    }

    public function test_back_never_shows_a_stored_page(): void
    {
        // A page, the redirect a signed-out person gets, and an error page.
        foreach ([$this->get('/login'), $this->get('/dashboard'), $this->get('/no-such-page')] as $response) {
            $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
            $response->assertHeader('X-Frame-Options', 'DENY')->assertHeader('Content-Security-Policy');
        }
    }

    public function test_signing_out_then_going_back_to_the_dashboard_asks_to_sign_in(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk();
        $this->post('/logout');

        $this->get('/dashboard')->assertRedirect(route('login', absolute: false));
    }

    public function test_a_missing_page_says_so_and_leads_home(): void
    {
        $this->assertBackLink($this->get('/no-such-page')->assertNotFound()->assertSee('Page not found'), route('home'));
    }

    public function test_an_expired_email_link_explains_itself(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get("/verify-email/{$user->id}/".sha1($user->email).'?expires=1&signature=old')
            ->assertForbidden()
            ->assertSee('the link has expired');
    }

    public function test_too_many_attempts_says_to_wait(): void
    {
        foreach (range(1, 6) as $attempt) {
            $this->post('/forgot-password', ['email' => 'ana@example.com']);
        }

        $this->post('/forgot-password', ['email' => 'ana@example.com'])->assertTooManyRequests()->assertSee('Too many attempts');
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
