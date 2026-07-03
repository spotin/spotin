# Blade & Views Best Practices

## Follow HTML5 Standards and Accessibility

Use semantic tags (`<nav>`, `<main>`, `<article>`, etc.) and follow accessibility best practices (ARIA attributes, proper heading hierarchy, label associations).

## Never Hardcode User-Facing Strings

Use localization files for all text visible to the user to support multiple languages.

```blade
{{-- Bad --}}
<p>Welcome back!</p>

{{-- Good --}}
<p>{{ __('Welcome back!') }}</p>
```

## Prefer Blade Components Over `@include`

Create components for reusable pieces of UI. Unlike `@include`, which shares all parent variables implicitly (hidden coupling), components have explicit props, attribute bags, and slots — promoting reusability and maintainability.

## Use `$attributes->merge()` in Component Templates

Hardcoding classes prevents consumers from adding their own. `merge()` combines class attributes cleanly.

```blade
<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $message }}
</div>
```

## Use Blade Directives to Keep Templates Readable

Use `@if`, `@foreach`, `@forelse`, `@unless`, and other Blade directives instead of raw PHP tags to keep templates clean and consistent.

## Minimize Tailwind Classes — Stay Simple and Consistent

Avoid cluttering templates with long chains of utility classes. Extract repeated patterns into components or use `@apply` in CSS when a combination is reused often.

## Mobile-First Responsive Design

Write base styles for mobile first, then use Tailwind's responsive prefixes (`sm:`, `md:`, `lg:`, `xl:`) to adapt the layout for larger screens.

## Support Dark Mode

Use the `dark:` prefix to provide dark mode variants for colors, backgrounds, and borders.

```blade
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
```

## Use `@pushOnce` for Per-Component Scripts

If a component renders inside a `@foreach`, `@push` inserts the script N times. `@pushOnce` guarantees it's included exactly once.

## Use View Composers for Shared View Data

If every controller rendering a sidebar must pass `$categories`, that's duplicated code. A View Composer centralizes it.

## Use Blade Fragments for Partial Re-Renders (htmx/Turbo)

A single view can return either the full page or just a fragment, keeping routing clean.

```php
return view('dashboard', compact('users'))
    ->fragmentIf($request->hasHeader('HX-Request'), 'user-list');
```

## Use `@aware` for Deeply Nested Component Props

Avoids re-passing parent props through every level of nested components.
