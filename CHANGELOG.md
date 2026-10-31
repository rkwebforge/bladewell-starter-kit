# Changelog

> [!IMPORTANT]
> This is the Bladewell starter kit's changelog, not your app's. It came with the kit so you can tell which release your app started from: the first entry below. Don't add your app's changes here; start your own changelog or replace this file when you like.

The kit is versioned by date: `YYYY.MM.N`, where `N` counts the releases in that month from 0.

## 2026.10.18

Built on Bladewell 1.4.6.

- Tooltips work on a touch screen: a long press on a button or link shows its tooltip without pressing it, a tap still presses, and a tooltip opened by a finger shows above it. A chart's cursor tooltip can be read bar by bar by holding a bar and sliding.
- No widget in the kit changed in this release, so the ones already in your app stay as they are.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell`, then `php artisan bladewell:add --installed` if it uses `tooltip` or `tooltip-cursor`.

## 2026.10.17

Built on Bladewell 1.4.5.

- Kanban cards drag by touch too: hold a finger still on a card for a moment and it comes up to drag, as with a mouse, while a swipe still scrolls the board and the page. Moves are saved the same way as before, so your route or `wire:kanban-move` needs no change.
- No widget in the kit changed in this release, so the ones already in your app stay as they are.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell`, then `php artisan bladewell:add --installed` if it uses the kanban board.

## 2026.10.16

Built on Bladewell 1.4.4.

- A kanban board is one command away: `php artisan bladewell:add kanban`. People move its cards by a menu, the keyboard, a tap or a drag, and each move is saved to your route or, in a Livewire component, with `wire:kanban-move`.
- A Turnstile, reCAPTCHA or hCaptcha captcha binds with `wire:model` in a Livewire component, and a form that fails validation gets a new token, so it can be sent again.
- No widget in the kit changed in this release, so the ones already in your app stay as they are.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell`, then `php artisan bladewell:add --installed` if it uses the captcha.

## 2026.10.15

Built on Bladewell 1.4.3.

- The rules for your AI agent (`CLAUDE.md`, `AGENTS.md`) now say not to wrap widgets in `wire:ignore` in a Livewire component, which would stop a field showing values set in PHP and its errors, and to set a date picker's, select's or similar field's value from the server or Livewire rather than from a script or Alpine, where it wouldn't show.
- No widget changed in this release, so the ones already in your app stay as they are.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell` and `php artisan boost:update`.

## 2026.10.14

Built on Bladewell 1.4.2.

- Escape closes only the layer on top: a dropdown open in a modal closes first, even in a modal that can't be dismissed, and focus goes back to what opened it. A dropdown stays on screen after the window is resized, and pressing the same letter again moves to the next item starting with it.
- In your own Livewire tests, calling an action after `Livewire::test()` no longer throws "Duplicate element id" for a widget with its own `id`.
- Widgets you add from now on, such as `tags` (with a new `layout="below"`), `chat`, `tree`, `toast` and `otp`, come with this release's fixes; Bladewell's changelog lists them.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell` and `php artisan bladewell:add --installed`.

## 2026.10.13

Built on Bladewell 1.4.1.

- With one modal open over another, Escape closes the one on top, and `reset-on-close` resets any form, whatever its fields are called.
- `php artisan bladewell:add --force` overwrites only the widgets you name, so your edits to `theme.css` and `base.css` stay. The Bladewell MCP server answers agents that send several requests at once.
- Widgets you add from now on, such as `select`, `datepicker`, `table`, `number` and `phone`, come with this release's fixes; Bladewell's changelog lists them.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell` and `php artisan bladewell:add --installed`.

## 2026.10.12

Built on Bladewell 1.4.0.

- Three new widgets you can add when you need them: `chat`, for an AI assistant or people messaging, sent without reloading the page; `sidebar-layout`, an app frame whose sidebar collapses to icons and slides in on phones, with an optional chat panel; and `auth-layout`, for sign-in, sign-up and password pages. Add one with `php artisan bladewell:add chat`.
- The icon set has a `panel-left` icon. Nothing in the kit's own pages changes.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell` and `php artisan bladewell:add --installed`.

## 2026.10.11

Built on Bladewell 1.3.3.

- Input backgrounds are a shade darker, so fields stand out from the card behind them on dim or washed-out screens. Placeholders and hints darken with them to stay readable.
- An app made from an earlier kit gets this with `composer update bladewell/bladewell` and `php artisan bladewell:add --installed`. If you've edited `resources/css/widget/theme.css`, that leaves it alone: set `--color-field` to `#ebedf1` and `--color-muted` to `#5d6679` there yourself.

## 2026.10.10

Built on Bladewell 1.3.2.

- The Bladewell MCP server (`php artisan bladewell:mcp`) sends its answers as compact JSON, so they use fewer of your agent's tokens. Your agent gets the same information.
- The widgets and your agent's rules don't change. An app made from an earlier kit gets this with `composer update bladewell/bladewell`.

## 2026.10.9

Built on Bladewell 1.3.1.

- `php artisan bladewell:add` with no names shows a list to select widgets from: type to narrow it, Space to select, Enter to install. A mistyped name suggests the closest widget instead of listing them all.
- `php artisan bladewell:list` fits your terminal: each widget and what it also installs, without the long descriptions.
- Your agent's rules (`CLAUDE.md`, `AGENTS.md`) point it to `php artisan bladewell:list --json` to find a widget, which says what each one does.
- Your agent's Boost skills (`.claude/skills`, `.agents/skills`) are refreshed to `laravel/boost` 2.10.3's, which the kit already required: best practices, conventions, Tailwind and testing.
- The widgets don't change. An app made from an earlier kit gets all of this with `composer update bladewell/bladewell` and `php artisan boost:update`.

## 2026.10.8

Built on Bladewell 1.2.2.

- Widgets you add from now on come with Bladewell 1.2.2's security fixes: search and the command palette open only page, email and phone links from your app's results, and a tree loads `children-url` only from your own site. The widgets the kit ships with don't change.
- Your agent's rules (`CLAUDE.md`, `AGENTS.md`) say that the URLs a widget calls (`suggest-url`, `search-url`, `children-url`, `upload-url`) are named routes in `routes/web.php`, not `routes/api.php`, so they share the page's sign-in and CSRF protection.
- An app made from an earlier kit gets both with `composer update bladewell/bladewell`, `php artisan bladewell:add --installed` and `php artisan boost:update`.

## 2026.10.7

Built on Bladewell 1.2.1.

- Text the browser autofills in a form field follows a switch between light and dark at once. It kept the colour it had when it was filled, so after switching it could be light text on a light field until the page was reloaded. The fix is in `resources/css/widget/base.css`.
- An app made from an earlier kit gets it with `composer update bladewell/bladewell`, then `php artisan bladewell:add --installed`.

## 2026.10.6

Built on Bladewell 1.2.0.

- Your agent's rules (`CLAUDE.md`, `AGENTS.md`) now take Bladewell's widget rules from the package itself: `boost.json` lists `bladewell/bladewell` under `packages`, so `php artisan boost:update` brings each release's rules. Besides what the kit said before, they cover form errors, toasts, the shared row menu for long tables, the widgets' script APIs and dates.
- The kit's own guideline keeps only what is particular to the kit: where the widget files are, the Content Security Policy, `config/security.php` and tests.
- An app made from an earlier kit gets the same with `composer require --dev bladewell/bladewell:^1.2`, then `php artisan boost:update`, ticking `bladewell/bladewell` when asked.

## 2026.10.5

Built on Bladewell 1.1.0.

- The kit requires `bladewell/bladewell:^1.1`. It required `^0.3`, which Composer never moves past 0.3, so a new app's `php artisan bladewell:add` brought widgets from 0.3.0. It now brings 1.1's: a colour picker, tags, a slider, trees, popovers and a command palette among the new ones, and `readonly` on the form controls, `confirm` on buttons and a shared row menu for long tables. See the [Bladewell changelog](https://github.com/rkwebforge/bladewell/blob/main/CHANGELOG.md).
- The button, dropdown, modal and icon widgets are updated to 1.1.0. The theme menu and the buttons look and work as before.
- The agent guidelines say to use `readonly` rather than `disabled` for a value that can't be changed, and `confirm="…"` on a button for "Are you sure?".
- The README's update steps are for 1.x: `composer update bladewell/bladewell` takes every 1.x release. An app made from an earlier kit moves with `composer require --dev bladewell/bladewell:^1.1`.

## 2026.10.4

Built on Bladewell 0.3.0.

The kit is now a starting point rather than a finished app: a home page, the widgets it uses, the theme menu, error pages and the security headers, with no accounts or database.

- Sign in, registration, password reset, email verification, the dashboard and the settings page are gone, with their controllers, models, migrations and tests. So are the widgets only they used; `php artisan bladewell:add` brings any of them back.
- Nothing needs a database: sessions and the cache are kept in files, the queue runs straight away, and `composer create-project` no longer runs migrations.
- The home page has two prompts to copy into your AI agent, a login page and a sign-up page, with working sign-in and sign-up. Each sits in a card with its Copy button on top, and the routes, commands and code in it are marked as code. "Browse the widgets" in the header opens the widget catalogue in a new tab.
- The theme starts dark until someone picks Light or System.
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
