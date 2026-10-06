<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Routing\Route as RouteDefinition;
use Illuminate\Routing\ViewController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class RoutesTest extends TestCase
{
    // Every page the app defines: none may fail, send people somewhere that doesn't exist, or bounce them around.
    // New routes are picked up by themselves; once the app has accounts, open them signed in as well.
    public function test_every_page_ends_somewhere_sensible_for_everyone(): void
    {
        $this->assertContains('/', $this->pages());

        foreach ($this->pages() as $url) {
            $this->assertEndsWell($url, 'a guest');
        }
    }

    /**
     * @return list<string>
     */
    private function pages(): array
    {
        $examples = ['token' => 'a-token', 'id' => '1', 'hash' => 'a-hash'];

        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn (RouteDefinition $route): bool => in_array('GET', $route->methods(), true))
            // The app's own: its controllers and Route::view(), not the framework's or a package's.
            ->filter(fn (RouteDefinition $route): bool => str_starts_with($route->getActionName(), 'App\\')
                || ltrim($route->getActionName(), '\\') === ViewController::class)
            ->map(fn (RouteDefinition $route): string => '/'.ltrim((string) preg_replace_callback(
                '/\{(\w+)\??\}/',
                fn (array $parameter): string => $examples[$parameter[1]] ?? 'example',
                $route->uri(),
            ), '/'))
            ->values()
            ->all();
    }

    private function assertEndsWell(string $url, string $who): void
    {
        $visited = [];

        while (count($visited) < 5) {
            $visited[] = $url;
            $response = $this->get($url);
            $status = $response->getStatusCode();

            $this->assertLessThan(500, $status, "{$url} fails for {$who}");

            if (! $response->isRedirect()) {
                $this->assertContains($status, [200, 403, 404], "{$url} ends in {$status} for {$who}");

                return;
            }

            $url = (string) parse_url((string) $response->headers->get('Location'), PHP_URL_PATH);
            $this->assertNotContains($url, $visited, 'Redirect loop for '.$who.': '.implode(' → ', [...$visited, $url]));
        }

        $this->fail("More than 4 redirects for {$who}: ".implode(' → ', $visited));
    }
}
