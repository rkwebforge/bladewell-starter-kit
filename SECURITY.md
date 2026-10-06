# Security

What this starter kit protects you from, what you need to do before going live, and which settings to change when your app needs something the safe defaults don't allow. Every setting mentioned here is in [`config/security.php`](config/security.php), each with a comment explaining it.

## Already done for you

| Protection | Where |
| --- | --- |
| CSRF tokens on every form | `@csrf`, Laravel's middleware |
| Output escaped; widgets never print raw input | Blade `{{ }}` |
| Session data encrypted; cookies HTTP-only, `SameSite=Lax`, HTTPS-only in production | `config/session.php` |
| Content Security Policy with a fresh nonce per request (stops injected scripts running) | `SecurityHeaders` middleware |
| Pages never kept by the browser: Back after signing out asks the server again, so the next person at a shared computer can't see your pages | `SecurityHeaders` middleware |
| Headers on every response, redirects and error pages included | Global middleware in `bootstrap/app.php` |
| Error pages that never show details, each with a way home | `resources/views/errors/` |
| No framing by other sites (clickjacking), no MIME sniffing, limited Referer, HSTS in production | `SecurityHeaders` middleware |
| HTTPS links in production; strict Eloquent while you build; no `db:wipe` in production | `AppServiceProvider` |
| Dependencies checked for known vulnerabilities on every push | `.github/workflows/ci.yml` |

## Adding accounts

The kit has no accounts. When you add them, yourself or with the prompts on the home page, cover at least:

- Passwords hashed (Laravel's `hashed` cast) and never shown back in forms; new ones with 12+ characters, set once in `Password::defaults()`.
- Sign-in rate limited per email and IP, and sign-ups per IP.
- A new session ID on sign-in, and the session destroyed on sign-out.
- The same answer from a password reset whether or not the email has an account.
- The password asked again before changing the email or the password, or deleting the account.
- Queries for a person's data through their relations (`$request->user()->orders()`), so they can only reach their own rows.
- A feature test for each of these that fails without it.

## Before you go live

- [ ] `APP_ENV=production` and `APP_DEBUG=false`. Debug mode shows your code, settings and secrets to anyone who triggers an error.
- [ ] A fresh `APP_KEY` (`php artisan key:generate`), different from the one you developed with. Never commit `.env`.
- [ ] HTTPS on your domain. `APP_URL` starts with `https://`.
- [ ] Behind a load balancer or proxy (Forge with a load balancer, Cloudflare, AWS ELB, Heroku…)? Tell Laravel to trust it, or it can't see that requests are HTTPS or who sent them, and rate limits treat everyone as one IP. In `bootstrap/app.php`: `$middleware->trustProxies(at: ['10.0.0.0/8'])`, with your proxy's addresses, not `'*'` unless the proxy is the only way in.
- [ ] Sessions and the cache are kept in files under `storage/`. Running more than one server? Move them to the database or Redis (`SESSION_DRIVER`, `CACHE_STORE`).
- [ ] `php artisan config:cache route:cache view:cache` on each deploy.
- [ ] Only `public/` is reachable from the web. `.env`, `storage/` and `vendor/` must not be.
- [ ] Once there's a database: a database user with only the rights the app needs, and backups you have restored at least once.
- [ ] `composer audit` and `npm audit` clean (CI runs both).

## When your app needs something the defaults block

| If your app… | Change, in `config/security.php` |
| --- | --- |
| Loads a script from another site (Stripe, Google Analytics, Intercom, Plausible…) | Add its domain to `csp.script`, and usually `csp.connect`. The service's docs list the domains. |
| Shows an iframe from another site (YouTube, Stripe card fields, maps) | Add its domain to `csp.frame`. |
| Uses the captcha widget with reCAPTCHA, hCaptcha or Turnstile | Add the provider to `csp.script` and `csp.frame`. |
| Shows images from another domain or a CDN | Add it to `csp.img`. |
| Sends a form on to a payment page elsewhere (Stripe Checkout, PayPal) | Add that domain to `csp.form`. |
| Is meant to be embedded in another site (Shopify app, partner portal) | Add the embedding site's origin to `frame_ancestors`. |
| Uses Livewire | Livewire needs a few CSP changes of its own; see its docs on Content Security Policy. Turn on `csp.report_only` while you find them. |
| Signs in with a popup window from another site | Popups may need `Cross-Origin-Opener-Policy: same-origin-allow-popups` in `SecurityHeaders`. Redirect-based sign-in (Socialite) works as is. |
| Has subdomains, all on HTTPS | Add `includeSubDomains` to HSTS in `SecurityHeaders`. |

Not sure which domains a service needs? Set `CSP_REPORT_ONLY=true`, use the feature, and read the browser console: it lists everything the policy would have blocked. Then turn it back off. Never add `'unsafe-inline'` or `'unsafe-eval'` to `csp.script`: they switch the protection off.

## What the kit can't do for you

Security depends on what you build next. In particular:

- **Who can see and change what.** Once your app has records people share (orders, teams, projects), check ownership on every request with [policies](https://laravel.com/docs/authorization#creating-policies), and test that one user can't reach another's records by changing an ID in the URL.
- **File uploads.** Validate type and size on the server (`mimes:`, `max:`), store uploads outside `public/` or under random names, and never trust the file name.
- **APIs.** Use [Sanctum](https://laravel.com/docs/sanctum) tokens with the narrowest abilities, and rate limit every endpoint.
- **Raw SQL.** Use bindings (`DB::select('... where id = ?', [$id])`), never string concatenation.
- **Printing HTML.** `{!! !!}` prints without escaping. Only use it for HTML your own code built, never for anything a user typed.
- **Two-factor sign-in, passkeys, single sign-on.** Add them for accounts that hold anything valuable.
- **Compliance** (GDPR, HIPAA, PCI DSS). Say in your privacy policy what you keep about people, and for how long.

Before launch, have someone who didn't build the app try to break it.

## Reporting a vulnerability

Found a security problem in the starter kit itself? Please don't open a public issue. Report it privately with the **Report a vulnerability** button on the repository's **Security** tab on GitHub, with steps to reproduce it.
