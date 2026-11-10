# Rule 05: Modern Tailwind CSS v4 & Blade Component Patterns

Tailwind CSS v4 introduces a CSS-first architecture, dropping `tailwind.config.js` in favor of `@theme` directives in CSS. Paired with Laravel 13 Blade components, this provides an ultra-clean, component-driven design workflow.

---

## Tailwind CSS v4 Essentials

### 1. CSS-First Token Configuration
Configure design tokens directly in `resources/css/app.css` using the `@theme` block:

```css
@import "tailwindcss";

@theme {
  /* Brand Tokens */
  --color-brand-50: oklch(0.97 0.02 245);
  --color-brand-500: oklch(0.55 0.20 245);
  --color-brand-600: oklch(0.48 0.22 245);
  --color-brand-700: oklch(0.40 0.20 245);

  /* Radius Scales */
  --radius-xs: 4px;
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;

  /* Font Families */
  --font-display: "Plus Jakarta Sans", system-ui, sans-serif;
  --font-sans: "Inter", system-ui, sans-serif;
  --font-mono: "Geist Mono", monospace;
}
```

### 2. Container Queries (`@container`)
Build components that adapt to their container's width, not just the global viewport:

```html
<div class="@container">
  <div class="grid grid-cols-1 @md:grid-cols-2 @xl:grid-cols-3 gap-4">
    <!-- Cards adapt automatically whether in sidebar or full page -->
  </div>
</div>
```

### 3. Viewports & Mobile Safe Areas
Always use dynamic viewport units (`dvh`) and safe area insets:
```html
<div class="min-h-dvh flex flex-col justify-between pb-[calc(env(safe-area-inset-bottom)+1rem)]">
  <!-- Layout body -->
</div>
```

---

## Laravel Blade Component Standards

Reusable Blade components encapsulate high-craft styling, state variations, and accessibility attributes.

### 1. High-Craft Button Component (`<x-button>`)
Create `resources/views/components/button.blade.php`:

```blade
@props([
    'variant' => 'primary', // primary, secondary, ghost, danger
    'size' => 'md',         // sm, md, lg
    'type' => 'button',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 ease-out active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 motion-reduce:transition-none disabled:opacity-50 disabled:pointer-events-none cursor-pointer';

$sizeClasses = [
    'sm' => 'px-2.5 py-1.5 text-xs gap-1.5 min-h-[36px]',
    'md' => 'px-3.5 py-2 text-sm gap-2 min-h-[44px]',
    'lg' => 'px-5 py-2.5 text-base gap-2.5 min-h-[48px]',
][$size];

$variantClasses = [
    'primary' => 'bg-neutral-900 text-white hover:bg-neutral-800 shadow-xs border border-neutral-900 focus-visible:ring-neutral-900 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100 dark:border-white',
    'secondary' => 'bg-white text-neutral-800 border border-neutral-200/90 shadow-xs hover:bg-neutral-50 hover:border-neutral-300 focus-visible:ring-neutral-900 dark:bg-neutral-900 dark:text-neutral-200 dark:border-white/10 dark:hover:bg-neutral-800',
    'ghost' => 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 shadow-xs border border-red-600 focus-visible:ring-red-600 dark:bg-red-500 dark:hover:bg-red-600',
][$variant];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->class([$baseClasses, $sizeClasses, $variantClasses]) }}
>
    {{ $slot }}
</button>
```

### 2. High-Craft Card Component (`<x-card>`)
Create `resources/views/components/card.blade.php`:

```blade
@props([
    'padding' => 'md', // sm, md, lg, none
])

@php
$paddingClasses = [
    'none' => '',
    'sm' => 'p-4',
    'md' => 'p-5 sm:p-6',
    'lg' => 'p-6 sm:p-8',
][$padding];
@endphp

<div {{ $attributes->class([
    'rounded-xl border border-neutral-200/80 bg-white shadow-xs dark:border-white/10 dark:bg-neutral-900/90 dark:shadow-[inset_0_1px_0_0_rgba(255,255,255,0.06)]',
    $paddingClasses
]) }}>
    {{ $slot }}
</div>
```

### 3. High-Craft Status Badge (`<x-badge>`)
Create `resources/views/components/badge.blade.php`:

```blade
@props([
    'variant' => 'neutral', // neutral, success, warning, danger, brand
])

@php
$variantClasses = [
    'neutral' => 'bg-neutral-100 text-neutral-700 border-neutral-200/60 dark:bg-neutral-800 dark:text-neutral-300 dark:border-white/10',
    'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/40',
    'warning' => 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/40',
    'danger' => 'bg-red-50 text-red-700 border-red-200/80 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800/40',
    'brand' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80 dark:bg-indigo-950/40 dark:text-indigo-400 dark:border-indigo-800/40',
][$variant];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium tracking-wide',
    $variantClasses
]) }}>
    {{ $slot }}
</span>
```

---

## Tailwind Class Ordering Principle

To keep markup readable and maintainable, order utility classes consistently:

```text
Layout / Display  -> flex, grid, block, hidden
Positioning       -> relative, absolute, top-0, z-10
Sizing            -> w-full, max-w-lg, min-h-[44px]
Spacing           -> p-4, px-3, py-2, gap-3, space-y-4
Typography        -> text-sm, font-medium, text-balance, tabular-nums
Surfaces & Colors -> bg-white, text-neutral-900, border, border-neutral-200
Radius & Shadow   -> rounded-lg, shadow-xs
Transforms/Motion -> transition-all, duration-150, active:scale-[0.98]
Focus & a11y      -> focus-visible:ring-2, focus-visible:outline-none
Dark Mode         -> dark:bg-neutral-900, dark:text-white
```
