# Rule 01: The Anti-Slop Manifesto & Deterministic Detector

Synthesized from **Anthropic Frontend Design**, **Paul Bakaus's Impeccable**, and **Julien Thibeaut's Baseline UI**.

LLMs suffer from *distributional convergence*—gravitating toward the safe, bland mathematical average of web training data. This document outlines the **12 Cardinal Sins of AI-Generated UI** and provides deterministic rules to catch and destroy them.

---

## The 12 Cardinal AI-Slop Sins

### 1. The Purple/Indigo Gradient Cliché
* **The Sin:** Spreading `bg-gradient-to-r from-purple-600 to-indigo-600` or blurry radial purple blobs behind hero sections to simulate "modern AI innovation".
* **The Fix:** Use intentional monochrome, rich deep slate/zinc tones, or project-specific brand hues. If using a gradient, make it subtle (e.g. `bg-gradient-to-b from-neutral-50 to-neutral-100 dark:from-neutral-900 dark:to-neutral-950`) or use structured geometric boundaries.

### 2. The Inter/Roboto Monoculture
* **The Sin:** Defaulting to generic unstyled system sans fonts for everything, resulting in sterile templates with zero brand personality.
* **The Fix:** Pair deliberate typography: a distinct display font (e.g. bold condensed grotesque, warm editorial serif, or technical geometric sans) with a high-legibility workhorse body font. Tune letter-spacing: `tracking-tight` on large display headings, `tracking-normal` on body copy, `tracking-wider uppercase` on small sub-labels.

### 3. The Low-Contrast "Design Ghost"
* **The Sin:** Light gray text like `text-gray-400` on white backgrounds or `text-zinc-600` on dark backgrounds. It looks "sleek" in Figma but is completely unreadable in real lighting.
* **The Fix:** Strict WCAG AA compliance ($\ge 4.5:1$ for body text, $\ge 3:1$ for large headings).
  - Primary text: `text-neutral-900 dark:text-neutral-100`
  - Secondary text: `text-neutral-600 dark:text-neutral-400`
  - Muted captions: minimum `text-neutral-500 dark:text-neutral-500` only on high-contrast surfaces.

### 4. Cards-Within-Cards Inception
* **The Sin:** Stacking container cards inside other cards, each with identical borders, radius, and padding (`p-6 bg-white border rounded-xl` inside another `p-6 bg-white border rounded-xl`).
* **The Fix:** Vary depth and hierarchy. Use background surface shifts (e.g. canvas background `bg-neutral-50` with elevated card `bg-white dark:bg-neutral-900`), borderless groups, subtle divider lines, or whitespace separation instead of endless boxed containers.

### 5. The Floating Cloud Shadow
* **The Sin:** Slapping `shadow-2xl` or `shadow-xl` with high blur radius on flat cards with no border, making components look like detached, muddy clouds floating in mid-air.
* **The Fix:** Layered elevation:
  ```html
  <!-- Crisp, modern high-craft surface -->
  <div class="border border-neutral-200/80 bg-white shadow-xs dark:border-white/10 dark:bg-neutral-900 dark:shadow-none">
  ```
  Combine a crisp 1px semi-transparent border with micro-elevation (`shadow-xs` or `shadow-sm`).

### 6. The Bubbly Corner Infection
* **The Sin:** Throwing `rounded-3xl` or `rounded-full` onto square data containers, tables, or complex dashboards just to make them feel "friendly".
* **The Fix:** Establish a strict radius hierarchy:
  - Small chips / badges / pills: `rounded-full` or `rounded-md`
  - Buttons & Inputs: `rounded-lg` (6–8px)
  - Cards & Containers: `rounded-xl` (12–16px)
  - Modals / Sheets: `rounded-2xl` (16–24px)
  *Always ensure nested child radiuses are proportionally smaller than parent radiuses.*

### 7. The Cookie-Cutter 3-Column Feature Grid
* **The Sin:** Three identical cards in a row, each topped by an arbitrary pastel circle containing a random Lucide icon, followed by an obvious title and two lines of filler text.
* **The Fix:** Asymmetric, editorial, or interactive layouts. Group related features with varied column spans (`col-span-2` vs `col-span-1`), showcase real interactive preview widgets, or use a Bento grid with distinct visual hierarchy.

### 8. Stateless "Dead" Elements
* **The Sin:** Buttons, links, and cards that look identical whether hovered, clicked, or focused.
* **The Fix:** Full state coverage:
  - `:hover`: subtle surface shift (`hover:bg-neutral-100 dark:hover:bg-neutral-800`).
  - `:active` (press): tactile micro-compression (`active:scale-[0.98] transition-transform duration-100`).
  - `:focus-visible`: clear outline ring (`focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2`).
  - `:disabled`: clear disabled styling with `cursor-not-allowed opacity-50`.

### 9. Sluggish or Janky Motion
* **The Sin:** Transitions set to `transition: all 500ms ease-in`, animating `width`, `height`, `left`, or `top`, resulting in laggy repaints and sluggish UI responses.
* **The Fix:**
  - Fast durations: $100\text{ms}$–$150\text{ms}$ for micro-interactions, $200\text{ms}$ max for modals.
  - Compositor properties only: `transform`, `opacity`.
  - Snappy ease-out: `cubic-bezier(0.16, 1, 0.3, 1)` or fast ease-out. Never `ease-in` for entering elements.

### 10. Viewport & Mobile Amputation
* **The Sin:** Using `h-screen` or `min-h-screen` on mobile web apps (causing the bottom layout to be cut off by mobile address bars) and ignoring mobile safe areas.
* **The Fix:** Use `min-h-dvh` or `h-dvh` (dynamic viewport height) and add safe-area padding for fixed floating navigation:
  ```html
  <div class="min-h-dvh pb-[calc(env(safe-area-inset-bottom)+1rem)]">
  ```

### 11. Invisible Icon-Only Buttons
* **The Sin:** Creating `<button><svg ...></svg></button>` without any accessible name or text.
* **The Fix:** Always provide an explicit `aria-label` and mark SVG icons as `aria-hidden="true"`:
  ```html
  <button type="button" aria-label="Close dialog" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center ...">
    <svg aria-hidden="true" class="size-5" ...></svg>
  </button>
  ```

### 12. Layout-Jumping Numbers & Blank Spinners
* **The Sin:** Live metrics (counters, prices, stock figures) that cause adjacent text to jitter on change because of proportional fonts, or empty screens that pop suddenly with no placeholder.
* **The Fix:** Always use `tabular-nums` for numeric and financial figures. Use structural skeleton loaders matching the exact card dimensions during loading states.

---

## Deterministic Code Inspection Checklist

Before finalizing any frontend file, scan for these deterministic code flags:

| Pattern to Check | Flagged Code (Slop) | Fixed Code (Craft) |
| :--- | :--- | :--- |
| **Viewport Height** | `h-screen` / `min-h-screen` | `h-dvh` / `min-h-dvh` |
| **Headings Typography** | `<h1 class="text-3xl font-bold">` | `<h1 class="text-balance text-3xl font-bold tracking-tight">` |
| **Paragraph Typography** | `<p class="...">` | `<p class="text-pretty text-neutral-600 dark:text-neutral-400">` |
| **Metrics & Data** | `<span class="font-bold">$12,450.00</span>` | `<span class="tabular-nums font-bold tracking-tight">$12,450.00</span>` |
| **Icon-Only Buttons** | `<button><svg></svg></button>` | `<button aria-label="Action description"><svg aria-hidden="true"></svg></button>` |
| **Mobile Tap Target** | `<button class="p-1">` | `<button class="min-h-[44px] min-w-[44px] p-2.5 ...">` |
| **Button Feedback** | `class="bg-blue-600 text-white"` | `class="... active:scale-[0.98] transition-all duration-150 ease-out"` |
| **Cards Elevation** | `class="bg-white shadow-xl rounded-2xl"` | `class="bg-white dark:bg-neutral-900 border border-neutral-200/80 dark:border-white/10 shadow-xs rounded-xl"` |
| **Animated Properties** | `transition-all duration-300` | `transition-[transform,opacity] duration-150` |
| **Reduced Motion** | (None) | `motion-reduce:transition-none motion-reduce:animate-none` |
