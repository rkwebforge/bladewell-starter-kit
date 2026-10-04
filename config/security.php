<?php

declare(strict_types=1);

/*
| Every security choice the app makes, in one place. The defaults are the safe ones: change a setting only when your
| app needs it, and read its comment first, as each says what loosening it costs. SECURITY.md lists which apps need
| which change ("if your app embeds Stripe…").
*/

return [

    /*
    | Whether anyone can create an account at /register. Turn it off for invite-only or company-internal apps; the
    | links to it disappear and the page answers 404.
    */
    'registration' => (bool) env('REGISTRATION_ENABLED', true),

    'passwords' => [
        // Length is what makes a password hard to guess; 12 is the floor current guidance (NIST SP 800-63B) suggests
        // for accounts without two-factor sign-in. The register and settings pages show these rules as you type.
        'min' => 12,
        'mixed_case' => false,
        'numbers' => false,
        'symbols' => false,

        // Rejects passwords found in known data breaches, in production only. It asks the Have I Been Pwned API
        // with the first 5 characters of the password's SHA-1 hash, so the password itself never leaves your
        // server. Turn it off if your server can't reach the internet.
        'check_breached' => (bool) env('PASSWORD_CHECK_BREACHED', true),
    ],

    /*
    | Content Security Policy: which places your pages may load scripts, styles, images and so on from. It's the main
    | defence against cross-site scripting (XSS): even if an attacker gets markup into a page, the browser won't run
    | a script that isn't from your own domain or doesn't carry this request's nonce.
    |
    | Add a domain when you add a service, e.g. Stripe:
    |     'script' => ['https://js.stripe.com'], 'frame' => ['https://js.stripe.com'], 'connect' => ['https://api.stripe.com'],
    | Never add 'unsafe-inline' or 'unsafe-eval' to script: they switch the protection off.
    */
    'csp' => [
        'enabled' => (bool) env('CSP_ENABLED', true),

        // Sends the policy as Content-Security-Policy-Report-Only: the browser only logs what it would block, in its
        // console. Useful for a day while you add a service and find out which domains it needs.
        'report_only' => (bool) env('CSP_REPORT_ONLY', false),

        'script' => [],
        'style' => [],
        'img' => [],
        'font' => [],
        'connect' => [],
        'frame' => [],

        // Where forms may submit to. Chrome also checks the redirect after a submit, so a form that sends people on to
        // a payment page elsewhere (Stripe Checkout, PayPal) needs that domain here.
        'form' => [],
    ],

    /*
    | Sites allowed to show your pages inside an <iframe>. Empty means none, which stops clickjacking (a page that
    | loads yours invisibly and tricks people into clicking it). Add origins only if your app is meant to be
    | embedded, e.g. ['https://admin.shopify.com'].
    */
    'frame_ancestors' => [],

    /*
    | HTTP Strict Transport Security: once a browser has seen your site over HTTPS, it refuses plain HTTP to it for
    | max_age seconds, so nobody on the network can downgrade the connection. Sent in production, over HTTPS only.
    | It deliberately leaves out includeSubDomains: add that yourself once every subdomain serves HTTPS.
    */
    'hsts' => [
        'enabled' => (bool) env('HSTS_ENABLED', true),
        'max_age' => 31536000,
    ],

    // How long sign-in history is kept before `php artisan model:prune` (scheduled daily) deletes it.
    'sign_in_history_days' => 90,

];
