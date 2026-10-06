<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends the browser security headers on every web page. What they allow is set in config/security.php.
 */
final class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Before the page renders, so @vite can put this request's nonce on its script and style tags.
        Vite::useCspNonce();

        $response = $next($request);

        // Once the app has accounts, a page depends on who's signed in, so Back must ask the server again rather than
        // show a stored copy: a private page after signing out, a form whose CSRF token has expired, or an old redirect.
        $response->headers->set('Cache-Control', 'no-store, private');
        // Stops the browser guessing a file's type, e.g. running an uploaded "image" as a script.
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Other sites see only your domain in the Referer header, never full URLs (which can hold tokens).
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Turns off browser features the app doesn't use, so injected code can't use them either.
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), browsing-topics=()');
        // Pages opened from yours, or that open yours, can't reach into it through window.opener.
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if (config('security.frame_ancestors') === []) {
            // For older browsers; newer ones follow frame-ancestors in the CSP below.
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        if (config('security.hsts.enabled') && app()->isProduction() && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age='.(int) config('security.hsts.max_age'));
        }

        if ($this->sendsPolicy($response)) {
            $header = config('security.csp.report_only') ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
            $response->headers->set($header, $this->policy());
        }

        return $response;
    }

    private function sendsPolicy(Response $response): bool
    {
        // Laravel's debug error page relies on inline scripts. It's only shown while APP_DEBUG is on, never in production.
        return config('security.csp.enabled') && ! (config('app.debug') && $response->isServerError());
    }

    private function policy(): string
    {
        $nonce = "'nonce-".Vite::cspNonce()."'";
        // The theme script inlined in <head>, allowed by its hash: it's the same on every page, so pages the table
        // fetches in place match too, which a nonce (new each request) wouldn't.
        $theme = "'sha256-".base64_encode((string) hash_file('sha256', resource_path('js/theme-boot.js'), true))."'";
        $dev = $this->viteDevServer();

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", $nonce, $theme, ...$dev, ...config('security.csp.script')],
            // While `npm run dev` runs, Vite injects CSS through <style> tags it can't put a nonce on. The built CSS
            // that production serves is a file, so this never reaches it.
            'style-src' => ["'self'", $nonce, ...$dev, ...($dev === [] ? [] : ["'unsafe-inline'"]), ...config('security.csp.style')],
            // data: and blob: for icons drawn in CSS and previews of files picked for upload.
            'img-src' => ["'self'", 'data:', 'blob:', ...$dev, ...config('security.csp.img')],
            'font-src' => ["'self'", ...$dev, ...config('security.csp.font')],
            'connect-src' => ["'self'", ...$dev, ...$this->websockets($dev), ...config('security.csp.connect')],
            'frame-src' => ["'self'", ...config('security.csp.frame')],
            'form-action' => ["'self'", ...config('security.csp.form')],
            'frame-ancestors' => config('security.frame_ancestors') === [] ? ["'none'"] : config('security.frame_ancestors'),
            // A <base> tag injected into a page would otherwise send every relative link somewhere else.
            'base-uri' => ["'self'"],
            'object-src' => ["'none'"],
        ];

        $policy = collect($directives)
            ->map(fn (array $sources, string $name): string => $name.' '.implode(' ', array_unique($sources)))
            ->implode('; ');

        return app()->isProduction() ? $policy.'; upgrade-insecure-requests' : $policy;
    }

    /**
     * The Vite dev server's origin while `npm run dev` runs, which serves scripts and styles in development.
     *
     * @return list<string>
     */
    private function viteDevServer(): array
    {
        if (app()->isProduction() || ! Vite::isRunningHot()) {
            return [];
        }

        $url = trim((string) file_get_contents(Vite::hotFile()));

        // A policy can't name an IPv6 address such as http://[::1]:5173, which is where Vite listens on many Macs,
        // so it allows that port on any host instead. Development only.
        return $url === '' ? [] : [(string) preg_replace('#^(https?://)\[[^\]]+\](:\d+)?$#', '$1*$2', $url)];
    }

    /**
     * Its hot-reload connection: the same address over ws:// or wss://.
     *
     * @param  list<string>  $origins
     * @return list<string>
     */
    private function websockets(array $origins): array
    {
        return array_map(fn (string $origin): string => (string) preg_replace('#^http#', 'ws', $origin), $origins);
    }
}
