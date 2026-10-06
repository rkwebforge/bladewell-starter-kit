# Changelog

The kit is versioned by date: `YYYY.MM.N`, where `N` counts the releases in that month from 0.

## Unreleased

The kit is now a starting point rather than a finished app: a home page, the widgets it uses, the theme menu, error pages and the security headers, with no accounts or database.

- Sign in, registration, password reset, email verification, the dashboard and the settings page are gone, with their controllers, models, migrations and tests. So are the widgets only they used; `php artisan bladewell:add` brings any of them back.
- Nothing needs a database: sessions and the cache are kept in files, the queue runs straight away, and `composer create-project` no longer runs migrations.
- The home page links to the widget catalogue in a new tab, and has two prompts to copy into your AI agent: a login page and a sign-up page, with working sign-in and sign-up.
- `config/security.php` no longer has the `registration`, `passwords` and `sign_in_history_days` settings. `SECURITY.md` lists what to cover when you add accounts.

## 2026.10.3

Built on Bladewell 0.3.0.

- LarawellUI is now **Bladewell**. The kit is `bladewell/starter-kit` and requires `bladewell/bladewell`. The widget commands are `php artisan bladewell:add | list | diff | mcp`, the lock file is `bladewell.lock`, and the MCP server in `.mcp.json` is `bladewell`. The widgets look and work as before.
- To move an app built from an earlier release: rename `larawellui.lock` to `bladewell.lock` first, then `composer remove --dev larawellui/larawellui && composer require --dev bladewell/bladewell`, and change `larawell:mcp` to `bladewell:mcp` in `.mcp.json`.

## 2026.10.2

Built on LarawellUI 0.2.3.

- On large screens, a sidebar (the accordion widget's menu) runs down the left: the app name, Dashboard, and a Settings group linking to each section of the Settings page. It stays put while the page scrolls, highlights where you are, and on Settings follows the section you click. The header then holds just the theme toggle and the account menu, and the header and page are centred beside it, up to 1536px wide. Smaller screens keep the header as before.
- Jumping to a Settings section shows its whole card, not just its heading.
- LarawellUI 0.2.3: any widget's icon props also take icons from an installed Blade Icons set, such as `icon-start="lucide-rocket"`. Nothing changes until you install one.

## 2026.10.1

Built on LarawellUI 0.2.2.

- Tables change page, sort and filter without the browser reporting Content Security Policy violations for the fetched page's inline styles.
- The widgets' PHP helpers are in Laravel Pint's default style, so Pint can run over the whole app: `pint.json` and its exclude for `app/View/Widget` are gone.
- The theme switch uses the widgets' own `data-theme-changing` rule; the kit's copy of it is gone.
- The back arrow sits closer to its label.
- The quick start builds the frontend, so a new app's first page never fails.

## 2026.10.0

The first release. Built on Laravel 13 and LarawellUI 0.2.1.

### Pages

- Home page linking to every page; pages behind sign in ask for it, then go straight there.
- Sign in, create account, forgot and reset password, email verification, and password confirmation.
- Dashboard with your own sign-in history in a sortable, paginated table that turns into cards on phones, and a warning after recent wrong passwords.
- Settings: profile, password, signing out other devices, and deleting the account.
- Light, dark and system theme, remembered in the browser and applied before the page is drawn.
- A way back from every page: sign-in pages lead to where people came from, and plain error pages (404, expired links, too many attempts, server errors) lead home.
- Every page fits screens from 320px wide.

### Security

- Every setting in `config/security.php`, each explained; `SECURITY.md` has a go-live checklist and what to change for Stripe, analytics, embedding or Livewire.
- Content Security Policy with a fresh nonce on every request, plus clickjacking, MIME-sniffing, referrer and HSTS headers, on every response.
- Pages are never stored by the browser, so Back after signing out can't show the dashboard again.
- Sign-in, sign-up, password reset and confirmation rate limited; registration can be turned off.
- New passwords need 12 characters and, in production, mustn't appear in known data breaches.
- Changing the email asks for the password again; changing the password signs out every other session.
- Encrypted sessions, HTTPS-only cookies and HTTPS links in production.
- The demo account is only ever created on your own machine.
- CI runs the tests, Pint, `composer audit` and `npm audit`; Dependabot keeps dependencies current.

### AI agents

- Laravel Boost and LarawellUI's MCP server set up for Claude Code and Codex, with guidelines for the kit's rules and a careful working style in `.ai/guidelines/`.
