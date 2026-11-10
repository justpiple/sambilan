# Project Design System & Visual Guidelines (DESIGN.md)

> This document establishes the durable visual tokens, aesthetic archetype, and component states for this application. All AI agents and developers must adhere to these tokens to ensure consistent, production-grade craft.

---

## 1. Aesthetic Archetype & Personality

* **Selected Archetype:** Linear Utilitarian / Modern Precision (or Swiss Editorial / Warm Crafted / Fintech)
* **Tone & Voice:** Fast, crisp, high-density, accessible, tactile.
* **Core Tenet:** 1px semi-transparent borders with subtle micro-elevations over heavy blurry drop shadows.

---

## 2. Token Architecture

### Color Palette (OKLCH / Hex)

| Token Name | Light Value | Dark Value | Purpose |
| :--- | :--- | :--- | :--- |
| `--bg-canvas` | `#fcfcfc` (`neutral-50`) | `#09090b` (`zinc-950`) | Page root background |
| `--surface-card` | `#ffffff` | `#18181b` (`zinc-900`) | Containers, cards, modals |
| `--surface-hover`| `#f4f4f5` (`neutral-100`)| `#27272a` (`zinc-800`) | List & row hover states |
| `--border-subtle`| `rgba(0, 0, 0, 0.08)` | `rgba(255, 255, 255, 0.10)` | Card borders & dividers |
| `--primary` | `#18181b` (`neutral-900`) | `#fafafa` (`neutral-50`) | Primary CTA buttons & badges |
| `--primary-hover`| `#27272a` (`neutral-800`)| `#e4e4e7` (`neutral-200`)| Primary button hover |
| `--text-main` | `#09090b` (Ratio > 12:1) | `#f4f4f5` (Ratio > 12:1) | Primary headers & body text |
| `--text-muted` | `#71717a` (Ratio $\ge$ 4.5:1)| `#a1a1aa` (Ratio $\ge$ 4.5:1)| Captions & secondary copy |
| `--danger` | `#dc2626` (`red-600`) | `#ef4444` (`red-500`) | Destructive actions |

---

## 3. Typography Standards

* **Display Font:** Variable Sans / Grotesque (`font-semibold tracking-tight text-balance`)
* **Body Font:** High-legibility Sans (`leading-relaxed text-pretty`)
* **Mono Font:** Data & Code (`font-mono tabular-nums text-xs`)

### Scale Hierarchy
* **Page Title (H1):** `text-2xl sm:text-3xl font-bold tracking-tight text-balance`
* **Section Header (H2):** `text-lg sm:text-xl font-semibold tracking-tight text-balance`
* **Card Title (H3):** `text-base font-medium tracking-tight`
* **Body Text:** `text-sm leading-relaxed text-pretty text-neutral-600 dark:text-neutral-400`
* **Caption / Label:** `text-xs font-medium text-neutral-500 uppercase tracking-wider`
* **Numeric Data:** `tabular-nums font-mono font-semibold`

---

## 4. Elevation, Radii & Depth

### Radius Scale
* **Badges & Pills:** `rounded-full`
* **Buttons & Inputs:** `rounded-lg` ($8\text{px}$)
* **Cards & Containers:** `rounded-xl` ($12\text{px}$)
* **Modals & Dialogs:** `rounded-2xl` ($16\text{px}$)

### Elevation Formula
* **Light Mode:** `border border-neutral-200/80 bg-white shadow-xs`
* **Dark Mode:** `border border-white/10 bg-zinc-900 shadow-[inset_0_1px_0_0_rgba(255,255,255,0.06)]`

---

## 5. Interaction & Motion Rules

* **Duration Budget:** Max $200\text{ms}$ for micro-interactions; $150\text{ms}$ for dropdowns.
* **Easing Curve:** `cubic-bezier(0.16, 1, 0.3, 1)` (snappy ease-out).
* **Press State:** All buttons and interactive cards must include `active:scale-[0.98] transition-transform duration-100 ease-out`.
* **Focus Ring:** `focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2 dark:focus-visible:ring-white dark:focus-visible:ring-offset-neutral-950`.
* **Reduced Motion:** Neutralize all animations with `motion-reduce:transition-none motion-reduce:animate-none`.

---

## 6. Accessibility & Ergonomics Matrix

* **Tap Targets:** Mobile buttons $\ge 44 \times 44\text{px}$ (`min-h-[44px] min-w-[44px]`).
* **Contrast:** Body copy strictly $\ge 4.5:1$ against surface; large text $\ge 3:1$.
* **Icon Buttons:** Must include explicit `aria-label` and child SVG `aria-hidden="true"`.
* **Dialogs:** Esc closes modals; destructive actions require confirmation dialogs.
* **Form Inputs:** Labels explicitly linked via `for`/`id`; errors placed directly below inputs.
