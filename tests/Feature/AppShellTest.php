<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AppShellTest extends TestCase
{
    use RefreshDatabase;

    private const array SECTIONS = ['profile-heading', 'password-heading', 'devices-heading', 'delete-heading'];

    public function test_the_sidebar_menu_marks_the_dashboard_as_current_there(): void
    {
        $html = $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk()->getContent();

        $this->assertStringContainsString('data-accordion-menu', (string) $html);
        $this->assertSame([route('dashboard')], $this->current((string) $html));
    }

    public function test_the_settings_group_links_to_each_section_of_the_settings_page(): void
    {
        $html = (string) $this->actingAs(User::factory()->create())->get('/dashboard')->getContent();

        foreach (self::SECTIONS as $section) {
            $this->assertStringContainsString('href="'.route('profile.edit').'#'.$section.'"', $html);
        }
    }

    public function test_on_settings_the_first_section_is_current_until_another_is_clicked(): void
    {
        $html = (string) $this->actingAs(User::factory()->create())
            ->withSession(['auth.password_confirmed_at' => time()])
            ->get('/settings/profile')->assertOk()->getContent();

        $this->assertSame([route('profile.edit').'#profile-heading'], $this->current($html));

        // Each link lands on its heading, which leaves room above the card when it scrolls there.
        foreach (self::SECTIONS as $section) {
            $this->assertMatchesRegularExpression('/<h2 id="'.$section.'" class="[^"]*\bscroll-mt-12\b/', $html);
        }
    }

    public function test_the_header_leaves_its_page_links_to_the_sidebar_on_large_screens(): void
    {
        $html = (string) $this->actingAs(User::factory()->create())->get('/dashboard')->getContent();

        $this->assertMatchesRegularExpression('/<nav aria-label="Main" class="[^"]*\blg:hidden\b/', $html);
        $this->assertMatchesRegularExpression('/<aside class="[^"]*\bhidden\b[^"]*\blg:flex\b/', $html);
    }

    /**
     * The menu links marked as the current page.
     *
     * @return list<string>
     */
    private function current(string $html): array
    {
        preg_match('#<nav data-accordion-menu.*?</nav>#s', $html, $menu);
        preg_match_all('#<a\b(?=[^>]*aria-current="page")[^>]*href="([^"]+)"#', $menu[0] ?? '', $links);

        return $links[1];
    }
}
