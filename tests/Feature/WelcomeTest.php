<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class WelcomeTest extends TestCase
{
    public function test_the_widget_catalogue_opens_in_a_new_tab(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="https://www.bladewellui.com/components"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener"', false);
    }

    public function test_the_home_page_has_a_login_and_a_sign_up_prompt_to_copy(): void
    {
        $this->get('/')
            ->assertSeeInOrder(['Login page', 'data-copy="prompt-login"', 'id="prompt-login"', 'Sign-up page', 'data-copy="prompt-sign-up"', 'id="prompt-sign-up"'], false)
            // As copied: the code highlighting adds tags, never characters.
            ->assertSeeText('Build a login page at /login: email and password fields')
            ->assertSeeText('Build a sign-up page at /register: name, email')
            ->assertSeeText('with php artisan bladewell:add, and add feature tests.')
            ->assertSee('https://www.bladewellui.com/llms.txt')
            ->assertSee('role="status"', false);
    }

    public function test_the_app_needs_no_database(): void
    {
        $this->get('/')->assertOk();

        $this->assertSame([], $this->app['db']->getConnections(), 'The home page connected to a database.');
    }
}
