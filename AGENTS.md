# Rules for coding agents

This plugin is built on a shared, framework-free starter. Read `docs/STACK.md`
before larger changes and `docs/MODULES.md` before adding a feature. These
rules are not optional.

## Never

- Never add a framework (Acorn/Laravel, WPPB-style loaders), jQuery, Tailwind,
  Vite or any front-end runtime library.
- Never add a runtime Composer dependency. If one is unavoidable, prefix it
  with Strauss (see `docs/STACK.md`) and log it in `CHANGELOG.md`.
- Never use admin-ajax. Server calls are REST routes.
- Never register a REST route or ability without a real `permission_callback`.
- Never enqueue assets globally. Admin assets load on the plugin's own screen;
  front-end assets load only where their markup renders.
- Never autoload options. Settings live in `wps_settings` (autoload off).
- Never commit `build/`, `vendor/`, `node_modules/` or `dist/`.
- Never use em dashes in copy, comments or docs.

## Always

- WordPress 6.9+, PHP 8.1+. Namespaced classes under `src/` (PSR-4), no global
  functions.
- New features are modules: `src/Modules/<Name>/Module.php` implementing
  `Contracts\Module`, listed in `Plugin::MODULES`. `register()` only adds hooks,
  with class-string callables so classes load when the hook fires.
- Settings fields are declared once in `Settings::fields()` (JSON Schema) and
  mirrored in `assets-src/admin/settings.js`.
- Admin UI: React, `@wordpress/components`, DataViews for lists, DataForm for
  forms, `apiFetch` to REST. Front end: Interactivity API script modules with
  server-rendered markup (`wp_interactivity_process_directives()`).
- Abilities: register on `wp_abilities_api_init` behind
  `function_exists( 'wp_register_ability' )`, same permission rule as the REST
  route.
- Escape output (`esc_html`, `esc_attr`, `esc_url`), sanitize input, check
  capabilities. REST nonces come from core via `apiFetch`.
- Plain CSS scoped under a `.wps-` class, tweakables as custom properties.
- Prefix: namespace `WpPluginStarter`, hooks and options `wps_`, handles and CSS
  classes `wps-`, constants `WPS_`, REST namespace `wps/v1`, text domain
  `wp-plugin-starter`.
- Delete everything the plugin stores in `uninstall.php`.
- Check before every commit: `php -l` on changed PHP, `composer lint`,
  `composer analyse`, `composer test`, `npm run lint:js`, `npm run lint:css`,
  `npm run build`, `npm run test:e2e` for anything visible, and
  `grep -rn $'\u2014'` for em dashes.

## Commits

- Commit after each major change (new module, REST route, admin screen, docs
  section), one logical change per commit with a clear message.
- Push to `origin main` only after the checks pass.
- Never commit half-finished work. This repo is public.
