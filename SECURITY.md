# Security

What this starter kit protects you from, what you need to do before going live, and which settings to change when your app needs something the safe defaults don't allow. Every setting mentioned here is in [`config/security.php`](config/security.php), each with a comment explaining it.

## Already done for you

| Protection | Where |
| --- | --- |
| Passwords hashed with bcrypt; never shown back in forms | `User` model, the password widget |
| New passwords: 12+ characters, checked against known breaches in production | `config/security.php`, `AppServiceProvider` |
| Sign-in locked for an email + IP after 5 wrong passwords; one IP capped at 30 tries a minute | `LoginRequest`, `routes/auth.php` |
| Sign-ups capped at 10 an hour per IP; registration can be turned off | `AppServiceProvider`, `config/security.php` |
| Password reset says the same thing whether or not the email has an account | `PasswordResetLinkController` |
| New session ID on sign-in; session destroyed on sign-out | `AuthenticatedSessionController` |
| Changing the password signs out every other session | `auth.session` in `routes/web.php` |
| "Sign out other devices" button | Settings, `OtherDevicesController` |
| Password asked again before changing the email (the usual first step of an account takeover) | `password.confirm` in `routes/web.php` |
| Password asked to change the password or delete the account | `PasswordController`, `ProfileController` |
| Sign-in history, with a warning about recent wrong passwords | Dashboard, `RecordSignIn` |
| Each person only ever sees their own data | `DashboardController` queries through `$user->signIns()` |
| Signed, expiring email verification links | `routes/auth.php` |
| CSRF tokens on every form | `@csrf`, Laravel's middleware |
| Output escaped; widgets never print raw input | Blade `{{ }}` |
| Sort columns checked against a fixed list before they reach SQL | `DashboardController` |
| Session data encrypted; cookies HTTP-only, `SameSite=Lax`, HTTPS-only in production | `config/session.php` |
| Content Security Policy with a fresh nonce per request (stops injected scripts running) | `SecurityHeaders` middleware |
| Pages never kept by the browser: Back after signing out asks the server again, so the next person at a shared computer can't see your pages | `SecurityHeaders` middleware |
| Headers on every response, redirects and error pages included | Global middleware in `bootstrap/app.php` |
| Error pages that never show details, each with a way home | `resources/views/errors/` |
| No framing by other sites (clickjacking), no MIME sniffing, limited Referer, HSTS in production | `SecurityHeaders` middleware |
| HTTPS links in production; strict Eloquent while you build; no `db:wipe` in production | `AppServiceProvider` |
| Dependencies checked for known vulnerabilities on every push | `.github/workflows/ci.yml` |

## Before you go live

- [ ] `APP_ENV=production` and `APP_DEBUG=false`. Debug mode shows your code, settings and secrets to anyone who triggers an error.
- [ ] A fresh `APP_KEY` (`php artisan key:generate`), different from the one you developed with. Never commit `.env`.
- [ ] No `test@example.com` account on the live database. `db:seed` refuses to create it outside `APP_ENV=local`, but check if you copied a local database across.
- [ ] HTTPS on your domain. `APP_URL` starts with `https://`.
- [ ] Behind a load balancer or proxy (Forge with a load balancer, Cloudflare, AWS ELB, Heroku…)? Tell Laravel to trust it, or it can't see that requests are HTTPS or who sent them, and the rate limits treat everyone as one IP. In `bootstrap/app.php`: `$middleware->trustProxies(at: ['10.0.0.0/8'])`, with your proxy's addresses, not `'*'` unless the proxy is the only way in.
- [ ] A real mail driver (`MAIL_MAILER`), so verification and reset emails arrive.
- [ ] The scheduler running: one cron entry, `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`. It deletes old sign-in history.
- [ ] `php artisan config:cache route:cache view:cache` on each deploy.
- [ ] Only `public/` is reachable from the web. `.env`, `storage/` and `vendor/` must not be.
- [ ] Database user with only the rights the app needs, and backups you have restored at least once.
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
| Is invite-only or company-internal | Set `REGISTRATION_ENABLED=false`. |
| Runs where the server can't reach the internet | Set `PASSWORD_CHECK_BREACHED=false`. |
| Holds money, health or other sensitive data | Shorten `SESSION_LIFETIME` (minutes) and `AUTH_PASSWORD_TIMEOUT` (seconds) in `.env`, and add two-factor sign-in. |
| Has subdomains, all on HTTPS | Add `includeSubDomains` to HSTS in `SecurityHeaders`. |

Not sure which domains a service needs? Set `CSP_REPORT_ONLY=true`, use the feature, and read the browser console: it lists everything the policy would have blocked. Then turn it back off. Never add `'unsafe-inline'` or `'unsafe-eval'` to `csp.script`: they switch the protection off.

## What the kit can't do for you

Security depends on what you build next. In particular:

- **Who can see and change what.** The kit only has "your own account". Once your app has records people share (orders, teams, projects), check ownership on every request with [policies](https://laravel.com/docs/authorization#creating-policies), and test that one user can't reach another's records by changing an ID in the URL.
- **File uploads.** Validate type and size on the server (`mimes:`, `max:`), store uploads outside `public/` or under random names, and never trust the file name.
- **APIs.** Use [Sanctum](https://laravel.com/docs/sanctum) tokens with the narrowest abilities, and rate limit every endpoint.
- **Raw SQL.** Use bindings (`DB::select('... where id = ?', [$id])`), never string concatenation.
- **Printing HTML.** `{!! !!}` prints without escaping. Only use it for HTML your own code built, never for anything a user typed.
- **Whether an email has an account.** Sign in and password reset don't reveal it, but the register form does ("already taken"), as most sites' do. Rate limiting keeps anyone from checking addresses in bulk. If even that matters for your app (a clinic, a dating site), switch to a sign-up that emails a link first and answers the same either way.
- **Two-factor sign-in, passkeys, single sign-on.** Not included yet. Add them for accounts that hold anything valuable.
- **Compliance** (GDPR, HIPAA, PCI DSS). The kit keeps sign-in IPs and browsers for 90 days; say so in your privacy policy, and change `sign_in_history_days` to suit it.

Before launch, have someone who didn't build the app try to break it.

## Reporting a vulnerability

Found a security problem in the starter kit itself? Please don't open a public issue. Report it privately with the **Report a vulnerability** button on the repository's **Security** tab on GitHub, with steps to reproduce it.
