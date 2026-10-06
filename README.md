# Bladewell Starter Kit

A Laravel starter kit with [Bladewell](https://www.bladewellui.com) widgets, Tailwind CSS and AI agents set up, and nothing else: no accounts, no database, no pages but the home page. You build what your app needs, or ask your agent to.

Plain Blade and a small vanilla JS module per widget: no React, Vue, Inertia or Alpine. Works under a strict Content Security Policy.

## Start a new app

```bash
composer create-project bladewell/starter-kit my-app
cd my-app
npm install && npm run build
composer run dev
```

With the Laravel installer, `laravel new my-app --using=bladewell/starter-kit` does the same first step. (No `laravel` command? `composer global require laravel/installer` installs it.)

Sessions and the cache are kept in files, so there's no database to set up. `.env` already points at SQLite for when you add one.

## What's included

- **A home page** with a link to the widget catalogue and two prompts to copy into your agent: a login page and a sign-up page. Each tells the agent to set up the database and the `User` model if the app has none yet, build the page with working sign-in or sign-up, and install the widgets it uses.
- **The widgets the page needs:** button, icon, dropdown (the theme menu) and modal (which the dropdown uses). Add any other with `php artisan bladewell:add`.
- **A Light / Dark / System theme menu**, dark until someone picks another, applied before the page is drawn.
- **Error pages** that say what happened in plain words, with a way home.
- **Browser security headers**, a strict Content Security Policy included.

## Security

Every security setting is in one file, [`config/security.php`](config/security.php), with a comment on what changing it costs. Every page sends a strict Content Security Policy and the other browser security headers.

[SECURITY.md](SECURITY.md) has the full list, a checklist for going live, and what to change when your app adds Stripe, analytics, embedding or Livewire.

## Where things live

```text
routes/web.php                          the home page
resources/views/welcome.blade.php       the home page and its prompts
resources/views/errors/                 the error pages
app/Http/Middleware/SecurityHeaders.php the Content Security Policy and other browser security headers
config/security.php                     every security setting, explained
resources/views/components/layouts/     head (the <head> tags) and guest (centred card, used by the error pages)
resources/views/components/theme-toggle.blade.php   the Light / Dark / System menu
resources/js/theme.js, theme-boot.js    switching the theme, and applying it before the page is drawn
resources/js/copy.js                    the Copy buttons on the prompts
resources/views/components/widget/      Bladewell widgets: yours to edit
resources/js/widget/, resources/css/widget/
bladewell.lock                         which widget files you've edited; commit it
```

## Widgets

The widgets are copied into your app, so you can change them freely. To add more:

```bash
php artisan bladewell:list
php artisan bladewell:add datepicker
```

### Keeping widgets up to date

Your app keeps the widgets exactly as they were when you created it; nothing changes behind your back. New Bladewell releases bring fixes and new widgets, and you take them when you choose:

```bash
composer require --dev bladewell/bladewell:^0.3   # move to the new release (the version from its changelog)
php artisan bladewell:add --installed                # update the widget files you haven't edited
php artisan bladewell:diff                           # see what changed in the ones you have
php artisan bladewell:add new-widget                 # add a widget that's new in that release
```

`bladewell:add --installed` never overwrites a file you've edited: it reports it, and `bladewell:diff` shows the difference so you can merge by hand. `bladewell.lock` is how it tells them apart, so commit it.

While Bladewell is below 1.0, `composer update` alone stays within the minor version you have: `^0.2.1` gets 0.2.x fixes but never 0.3.0, as Composer treats each 0.x minor release as possibly breaking. That's why the first command names the version. Read the [changelog](https://github.com/rkwebforge/bladewell/blob/main/CHANGELOG.md) for the widgets you use before moving.

## AI agents

The kit is ready for AI coding agents (Claude Code, Codex, Cursor, Copilot and others):

- **[Laravel Boost](https://github.com/laravel/boost)** gives them this app's Laravel version, logs, errors, database schema (once it has one) and version-matched Laravel docs, through its MCP server (`php artisan boost:mcp`).
- **`php artisan bladewell:mcp`** lets them look up every widget's props and examples, and install widgets.
- **`CLAUDE.md` and `AGENTS.md`** tell them how to work here: the kit's rules (widgets, the Content Security Policy, `config/security.php`, tests) and a careful working style.

Claude Code finds both MCP servers in `.mcp.json` by itself. For another agent, run `php artisan boost:install` and pick it: Boost writes that agent's MCP settings and instructions file; add `bladewell` (`php artisan bladewell:mcp`) next to `laravel-boost` there. `CLAUDE.md` and `AGENTS.md` are generated, so write your own rules in `.ai/guidelines/`, then run `php artisan boost:update`.

Commit these files: they're the project's instructions, so every agent and every teammate works by the same rules, and changes to them get reviewed like code. Your personal ones stay out of git: `CLAUDE.local.md`, `.claude/settings.local.json`, `.codex/` and `.cursor/` are in `.gitignore`. Never put API keys in `.mcp.json`; use environment variables.

Boost is a development dependency: deploy with `composer install --no-dev` and it isn't on your server.

## Tests

```bash
php artisan test
```

The home page, the error pages and the security headers have feature tests in `tests/Feature`. Add one for each page you build.

## Versions

The kit is versioned by date: `2026.10.0` is the first release of October 2026, and `2026.10.1` a fix to it. You copy a starter kit once rather than upgrade it, so the version says how fresh your copy is. [CHANGELOG.md](CHANGELOG.md) lists what each release changed.

## License

MIT. See [LICENSE](LICENSE).
