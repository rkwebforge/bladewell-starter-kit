# LarawellUI Starter Kit

A Laravel starter kit with sign in, registration, password reset, email verification and account settings, built from [LarawellUI](https://larawellui.wasmer.app) widgets.

Plain Blade and a small vanilla JS module per widget: no React, Vue, Inertia or Alpine. Works under a strict Content Security Policy.

## Start a new app

```bash
laravel new my-app --using=larawellui/starter-kit
cd my-app
npm install && npm run build
composer run dev
```

Or without the Laravel installer:

```bash
composer create-project larawellui/starter-kit my-app
cd my-app
npm install && npm run build
composer run dev
```

It uses SQLite by default. For MySQL, set the `DB_*` values in `.env`, then run `php artisan migrate`.

To try it with data, run `php artisan db:seed`: it creates `test@example.com` (password `password`) with a few pages of sign-in history for the dashboard table. The home page links to every page; the ones behind sign in ask for it first, then go straight there.

## What's included

| Page | Route | Widgets |
| --- | --- | --- |
| Sign in | `/login` | text-input, password, checkbox, button, alert |
| Create account | `/register` | text-input, password (with its rule checklist), button |
| Forgot / reset password | `/forgot-password`, `/reset-password/{token}` | text-input, password, button, alert |
| Verify email | `/verify-email` | button, alert |
| Confirm password | `/confirm-password` | password, button |
| Dashboard: your sign-in history | `/dashboard` | table (sortable, paginated, cards on phones), alert, dropdown account menu |
| Settings | `/settings/profile` | text-input, password, button, breadcrumbs, modal (other devices, delete account), toast |

New accounts verify their email before reaching the dashboard; in development the link is written to `storage/logs/laravel.log` (`MAIL_MAILER=log`). To skip verification, remove `implements MustVerifyEmail` from `app/Models/User.php`.

## Security

Safe by default, and every choice is in one file, [`config/security.php`](config/security.php), with a comment on what changing it costs. Sign-in is rate limited, passwords need 12+ characters (and are checked against known breaches in production), changing your email asks for your password, a password change signs out your other sessions, and every page sends a strict Content Security Policy.

[SECURITY.md](SECURITY.md) has the full list, a checklist for going live, and what to change when your app adds Stripe, analytics, embedding or Livewire.

## Where things live

```text
routes/auth.php                         sign in, registration, password reset, verification
routes/web.php                          dashboard and settings
app/Http/Controllers/Auth/              the auth controllers
app/Http/Controllers/Settings/          profile, password, other devices
app/Http/Middleware/SecurityHeaders.php the Content Security Policy and other browser security headers
app/Listeners/RecordSignIn.php          keeps the sign-in history
config/security.php                     every security setting, explained
resources/views/components/layouts/     app (header and account menu) and guest (centred card)
resources/views/auth/                   the auth pages
resources/views/components/theme-toggle.blade.php   the Light / Dark / System menu
resources/js/theme.js, theme-boot.js    switching the theme, and applying it before the page is drawn
resources/views/components/widget/      LarawellUI widgets: yours to edit
resources/js/widget/, resources/css/widget/
larawellui.lock                         which widget files you've edited; commit it
```

## Widgets

The widgets are copied into your app, so you can change them freely. To add more:

```bash
php artisan larawell:list
php artisan larawell:add datepicker
```

### Keeping widgets up to date

Your app keeps the widgets exactly as they were when you created it; nothing changes behind your back. New LarawellUI releases bring fixes and new widgets, and you take them when you choose:

```bash
composer require --dev larawellui/larawellui:^0.3   # move to the new release (the version from its changelog)
php artisan larawell:add --installed                # update the widget files you haven't edited
php artisan larawell:diff                           # see what changed in the ones you have
php artisan larawell:add new-widget                 # add a widget that's new in that release
```

`larawell:add --installed` never overwrites a file you've edited: it reports it, and `larawell:diff` shows the difference so you can merge by hand. `larawellui.lock` is how it tells them apart, so commit it.

While LarawellUI is below 1.0, `composer update` alone stays within the minor version you have: `^0.2.1` gets 0.2.x fixes but never 0.3.0, as Composer treats each 0.x minor release as possibly breaking. That's why the first command names the version. Read the [changelog](https://github.com/rkwebforge/larawellui/blob/main/CHANGELOG.md) for the widgets you use before moving.

## AI agents

The kit is ready for AI coding agents (Claude Code, Codex, Cursor, Copilot and others):

- **[Laravel Boost](https://github.com/laravel/boost)** gives them this app's Laravel version, database schema, logs, errors and version-matched Laravel docs, through its MCP server (`php artisan boost:mcp`).
- **`php artisan larawell:mcp`** lets them look up every widget's props and examples, and install widgets.
- **`CLAUDE.md` and `AGENTS.md`** tell them how to work here: the kit's rules (widgets, the Content Security Policy, `config/security.php`, tests) and a careful working style.

Claude Code finds both MCP servers in `.mcp.json` by itself. For another agent, run `php artisan boost:install` and pick it: Boost writes that agent's MCP settings and instructions file; add `larawellui` (`php artisan larawell:mcp`) next to `laravel-boost` there. `CLAUDE.md` and `AGENTS.md` are generated, so write your own rules in `.ai/guidelines/`, then run `php artisan boost:update`.

Commit these files: they're the project's instructions, so every agent and every teammate works by the same rules, and changes to them get reviewed like code. Your personal ones stay out of git: `CLAUDE.local.md`, `.claude/settings.local.json`, `.codex/` and `.cursor/` are in `.gitignore`. Never put API keys in `.mcp.json`; use environment variables.

Boost is a development dependency: deploy with `composer install --no-dev` and it isn't on your server.

## Tests

```bash
php artisan test
```

Every flow above has a feature test in `tests/Feature`.

## Versions

The kit is versioned by date: `2026.10.0` is the first release of October 2026, and `2026.10.1` a fix to it. You copy a starter kit once rather than upgrade it, so the version says how fresh your copy is. [CHANGELOG.md](CHANGELOG.md) lists what each release changed.

## License

MIT. See [LICENSE](LICENSE).
