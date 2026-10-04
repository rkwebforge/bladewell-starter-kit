# LarawellUI Starter Kit

A Laravel starter kit with sign in, registration, password reset, email verification and account settings, built from [LarawellUI](https://larawellui.wasmer.app) widgets.

Plain Blade and a small vanilla JS module per widget: no React, Vue, Inertia or Alpine. Works under a strict Content Security Policy.

## Start a new app

```bash
laravel new my-app --using=larawellui/starter-kit
cd my-app
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

To update them after `composer update larawellui/larawellui`:

```bash
php artisan larawell:add --installed   # updates files you haven't edited, reports the ones you have
php artisan larawell:diff              # shows what changed in the ones you have
```

`pint.json` leaves `app/View/Widget` alone: reformatting those files would mark them as edited, and updates would then skip them.

## AI agents

`php artisan larawell:mcp` is an MCP server that lists the widgets, their props and examples, and can install them. For Claude Code:

```bash
claude mcp add larawellui -- php artisan larawell:mcp
```

## Tests

```bash
php artisan test
```

Every flow above has a feature test in `tests/Feature`.

## License

MIT
