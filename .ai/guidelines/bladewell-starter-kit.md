# Bladewell Starter Kit

This app was built from the Bladewell starter kit: Blade pages, Tailwind CSS v4 and Bladewell widgets, with no React, Vue, Inertia or Alpine. Follow these rules on top of the Laravel ones.

## Widgets

- How to find, add and use the widgets is in the Bladewell rules below, from the `bladewell/bladewell` package. In this app the widget files are in `resources/views/components/widget/`, `resources/js/widget/`, `resources/css/widget/` and `app/View/Widget/`.
- Laravel Pint leaves widget files as they are, but don't run Prettier or another formatter over them: a reformatted file counts as edited and stops getting updates.

## Security

- Every security setting lives in `config/security.php`, each with a comment. Change settings there, never by weakening `app/Http/Middleware/SecurityHeaders.php`. `SECURITY.md` lists which setting a given feature needs.
- The Content Security Policy blocks inline scripts, inline event handlers (`onclick=""`) and `style=""` attributes. Put JavaScript in a module under `resources/js/` imported from `app.js`, and styling in classes. To load a script from another site, add its domain to `config/security.php`; never add `'unsafe-inline'` or `'unsafe-eval'`.
- The one inline script is `resources/js/theme-boot.js`, allowed by its hash, which `SecurityHeaders` computes from the file. Keep it tiny; anything that can wait belongs in `resources/js/theme.js`.
- The kit starts with no database, accounts or pages but the home page. When adding accounts, follow "Adding accounts" in `SECURITY.md`.
- Queries for a signed-in person's data go through their relations (`$request->user()->orders()`), so they can only reach their own rows. Records people share need a policy.
- Sort and filter columns from the query string are checked against a fixed list before they reach the query.
- Print with `{{ }}`. Use `{!! !!}` only for markup the app built itself, with a comment saying why.

## Tests

- Every feature and every protection has a test in `tests/Feature`. Run `php artisan test` and `vendor/bin/pint --test` before you say a change is done.
- A security change needs a test that fails without it.
