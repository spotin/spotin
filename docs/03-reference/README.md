# Reference

This document provides a comprehensive reference for the project to assist you
in understanding its components, configurations, and functionalities.

> [!WARNING]
>
> This is a work in progress.

## Table of contents

- [Table of contents](#table-of-contents)
- [Architecture](#architecture)
  - [Conventions](#conventions)
  - [Dependencies](#dependencies)
  - [Development dependencies](#development-dependencies)
- [Infrastructure](#infrastructure)
  - [GitHub and GitHub Actions](#github-and-github-actions)
  - [Infomaniak](#infomaniak)
- [Features](#features)
- [API](#api)

## Architecture

The application is built with [Laravel](https://laravel.com/docs). The primary
dependencies, as declared in `composer.json` (PHP) and `package.json`
(JavaScript), are listed below.

### Conventions

- **Domains.** The code is grouped by domain (`Organizations`, `Spots`,
  `Invitations`, `Settings`) in the controllers, requests, actions, components
  and tests. A controller sits in the domain of the URL it serves: the actions
  on the spots of an organization are in `Organizations\SpotController`, the
  ones on `/spots/{uuid}` in `Spots\SpotController`. The API has the same layout
  under `Api\V1`.
- **Routes.** Pages (`GET`) are Folio pages in `resources/views/pages`, and name
  themselves with `name()`. Everything that changes data (`POST`, `PUT`,
  `PATCH`, `DELETE`) is a route of `routes/web.php`, and `routes/api.php` for
  the API. The pages of a public domain (organizations, spots) need no login. A
  page that is not under a dynamic segment is `create.blade.php`, unless a
  dynamic sibling would take its URL: then it is `create/index.blade.php`.
- **Authorization.** Routes check abilities with the `can:` middleware, pages
  with `Gate::authorize()` in their render function, and controllers never
  authorize. Public pages have no ability to check.
- **Lists.** The filters (`query`, `mine`) are validated by `ListFiltersRequest`
  on the pages, and by its `ApiListFiltersRequest` subclass in the API. The
  query is in the `listed()` scope of the model, and the page is an
  `OrganizationListing` or `SpotListing` class with an `x-organizations.list` or
  `x-spots.list` component.
- **Feedback.** Controllers flash the translated message with
  `->with('success', __('ui…'))`, and the layout shows it once. Only the pages
  of Fortify use its own `status` values.
- **Steps.** A long form is split with `x-steps` and one `x-steps.step` per
  step, inside a single `<form>`. Each step has a title, also shown in the
  indicator, and can hold any content, such as several fieldsets. Its `actions`
  slot holds the buttons next to Previous and Continue, such as the submit
  button of the last step. `resources/js/steps.js` shows one step at a time,
  checks its fields before the next one, marks the steps done or with errors in
  the indicator, records the step in the URL for the back button of the browser,
  and warns before leaving the page with what was typed. The form is submitted
  at once to a single route. After a failed submit, the page reopens on the
  first step with an error. Without JavaScript, all the steps are shown. The
  last step can show `x-steps.recap`, which lists the fields of the earlier
  steps that have a `data-recap="Label"` attribute, with a button to change each
  step.
- **Scripts.** `resources/js/app.js` holds the scripts used on many pages. The
  heavier ones are imported only when their element is on the page, so Vite puts
  them in their own files: `map.js` (MapLibre, for `x-map` and `x-map.picker`,
  loaded once a map is about to be seen) and `markdown-editor.js` (Tiptap, for
  `x-markdown-editor`). Without JavaScript, their fields stay plain inputs.
- **Markdown.** Texts written by users in Markdown, such as the description of a
  spot, are stored as Markdown and rendered on the server with
  `Str::markdown()`, without raw HTML or unsafe links.
- **Breadcrumbs.** Pages build their trail from `App\Support\Breadcrumbs` and
  add their own last crumb.
- **Translations.** `lang/{en,fr}/ui.php` have the same keys, in `snake_case`. A
  domain has `common`, `feedback` and one key per page (`index`, `create`,
  `show`, `edit`, `delete`). The texts of a confirmation are with the action
  that opens it, and `x-confirm-modal` shows them.
- **Identifiers.** Organizations and spots are identified by a UUID in the URLs.
  The other rows (users, invitations, tokens) use their numeric id, and are
  always reached through a route that checks who is asking.

### Dependencies

- [Laravel Framework](https://laravel.com/docs): the application framework.
- [Laravel Folio](https://laravel.com/docs/folio): page-based routing from Blade
  files in `resources/views`.
- [Laravel Fortify](https://laravel.com/docs/fortify): backend authentication
  (login, registration, passwords).
- [Laravel Head](https://github.com/laravel/head): management of the page title,
  description and meta tags.
- [Laravel Sanctum](https://laravel.com/docs/sanctum): personal access tokens
  for the API.
- [Scramble](https://scramble.dedoc.co): OpenAPI documentation generated from
  the API code.
- [Blade Icons](https://github.com/blade-ui-kit/blade-icons): base package for
  using SVG icons in Blade.
- [Blade Lucide Icons](https://github.com/mallardduck/blade-lucide-icons): the
  Lucide icon set for Blade Icons.
- [Blade Simple Icons](https://github.com/ublabs/blade-simple-icons): the Simple
  Icons brand icon set for Blade Icons.
- [CommonMark](https://commonmark.thephpleague.com): rendering of the Markdown
  written by users, through `Str::markdown()` (installed with Laravel).

### Development dependencies

- [Pest](https://pestphp.com/docs): testing framework.
- [Laravel Pint](https://laravel.com/docs/pint): code style fixer.
- [Laravel Lang](https://laravel-lang.com): translations for Laravel's built-in
  messages.
- [Tailwind CSS](https://tailwindcss.com/docs): utility-first CSS framework.
- [daisyUI](https://daisyui.com/docs/intro/): component library for Tailwind
  CSS.
- [Tailwind CSS Typography](https://github.com/tailwindlabs/tailwindcss-typography):
  `prose` classes for styling rich text.
- [MapLibre GL JS](https://maplibre.org/maplibre-gl-js/docs/): the maps of the
  spots, drawn with the Liberty style of [OpenFreeMap](https://openfreemap.org/)
  by default (`MAP_STYLE_URL`), from
  [OpenStreetMap](https://www.openstreetmap.org/copyright) data, whose credits
  must stay visible.
- [Tiptap](https://tiptap.dev/docs): the WYSIWYG Markdown editor of the
  descriptions, with its Markdown extension.
- [Prettier](https://prettier.io/docs/): code formatter.
- [Prettier Plugin Blade](https://github.com/shufo/prettier-plugin-blade):
  formatting of Blade templates.
- [Prettier Plugin Tailwind CSS](https://github.com/tailwindlabs/prettier-plugin-tailwindcss):
  sorting of Tailwind CSS classes.

## Infrastructure

_Coming soon._

### GitHub and GitHub Actions

_Coming soon._

### Infomaniak

_Coming soon._

## Features

_Coming soon._

## API

The application exposes a REST API under `/api` to read and manage the
organizations and the spots. The responses follow
[JSON:API](https://jsonapi.org).

Every request needs a personal access token, sent as a bearer token. Tokens are
created in **Settings > Developer settings** of the application, and their owner
needs a verified email address.

The API is documented with Scramble:

- `/api`: the interactive documentation, where requests can be tried with a
  token.
- `/api.json`: the OpenAPI document.

The documentation is generated from the controllers in
`app/Http/Controllers/Api` and from the description in `config/scramble.php`.
