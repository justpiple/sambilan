# Rule 03: Micro-Interactions, Spring Physics & Motion Performance

Synthesized from **Emil Kowalski's Design Engineering** (`emilkowalski/skills`) and **Julien Thibeaut's Baseline UI & Motion Performance** (`ui-skills.com`).

Interfaces should feel tactile, responsive, physical, and alive. Motion should communicate state changes and reinforce spatial relationships—never delay user intent.

---

## The 4 Laws of Interaction Motion

1. **Durations are strictly budgeted:** Never make a user wait for an animation to interact with UI. Maximum interaction duration is **$200\text{ms}$**.
2. **Compositor properties only:** ONLY animate `transform` and `opacity`. Never animate layout properties like `width`, `height`, `margin`, `padding`, `top`, or `left`.
3. **Never `transition: all`:** Always declare the exact transitioning properties (e.g., `transition-[transform,opacity]` or `transition-colors`) to prevent layout thrashing and unintended property animations.
4. **Always respect `prefers-reduced-motion`:** Provide instant cuts for users who have requested reduced motion.

---

## Duration Budget

| Interaction Type | Target Duration | Easing Curve | Allowed Properties |
| :--- | :--- | :--- | :--- |
| **Button press / micro-feedback** | $100\text{ms}$–$150\text{ms}$ | Snappy ease-out | `transform`, `background-color` |
| **Toggle / Switch** | $150\text{ms}$ | Spring or snappy ease-out | `transform`, `background-color` |
| **Dropdown / Popover / Tooltip** | $150\text{ms}$–$200\text{ms}$ | `cubic-bezier(0.16, 1, 0.3, 1)` | `transform`, `opacity` |
| **Modal / Dialog / Drawer** | $200\text{ms}$–$250\text{ms}$ | `cubic-bezier(0.16, 1, 0.3, 1)` | `transform`, `opacity` |
| **High-frequency utility (e.g. Command Palette)** | $0\text{ms}$–$100\text{ms}$ | Instant or ultra-fast | `transform`, `opacity` |

> [!WARNING]
> Any interaction animation exceeding $250\text{ms}$ feels sluggish, unresponsive, and causes user frustration. High-frequency actions like typing in a command palette should open almost instantly ($\le 100\text{ms}$).

---

## Emil Kowalski Tactile Principles

### 1. Tactile Compression on Press (`active:scale-[0.98]`)
Every interactive button, tile, or card should feel physical when clicked or tapped. When pressed, compress it slightly and recover on release:

```html
<!-- High-craft interactive button -->
<button
  type="button"
  class="inline-flex items-center justify-center gap-2 rounded-lg border border-neutral-200/90 bg-white px-3.5 py-2 text-sm font-medium text-neutral-900 shadow-xs transition-all duration-150 ease-out hover:bg-neutral-50 hover:border-neutral-300 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2 motion-reduce:transition-none dark:border-white/10 dark:bg-neutral-900 dark:text-neutral-100 dark:hover:bg-neutral-800"
>
  Deploy Changes
</button>
```

### 2. Never Animate From `scale(0)`
When opening modals, tooltips, or popovers, **never animate from `scale(0)`**. An element scaling up from zero looks like it is popping out of a pinhole.
- **Correct entering scale:** start from `scale-[0.96]` or `scale-[0.95]` and animate to `scale-100`.
- **Exit scale:** animate from `scale-100` down to `scale-[0.97]` while fading to `opacity-0`.

### 3. Origin-Aware Transitions
Popovers and context menus must expand from their anchor position:
- Dropdown below a trigger: `origin-top`
- Dropdown top-right trigger: `origin-top-right`
- Dropdown above a trigger: `origin-bottom`

```html
<!-- Origin-aware dropdown menu container -->
<div class="origin-top scale-100 opacity-100 transition-all duration-150 ease-out data-[state=closed]:scale-95 data-[state=closed]:opacity-0">
  <!-- Menu Items -->
</div>
```

### 4. Asymmetric Enter & Exit Timing
Enter animations and exit animations should never have identical timing:
- **Enter (Opening):** Slightly longer ($180\text{ms}$–$200\text{ms}$) with a soft decelerating ease-out curve (`cubic-bezier(0.16, 1, 0.3, 1)`).
- **Exit (Dismissing):** Shorter ($100\text{ms}$–$150\text{ms}$) with an accelerating ease-in curve (`cubic-bezier(0.7, 0, 0.84, 0)`), letting the element exit quickly so the user can get back to their workflow.

---

## Modern CSS `@starting-style` for Dialogs & Popovers

Modern browsers support `@starting-style` to enable smooth entrance animations for native HTML `<dialog>` and `[popover]` elements without requiring JavaScript animation libraries:

```css
/* Smooth native dialog transition */
dialog[open] {
  opacity: 1;
  transform: scale(1);
  transition: opacity 180ms ease-out, transform 180ms cubic-bezier(0.16, 1, 0.3, 1), display 180ms allow-discrete, overlay 180ms allow-discrete;
}

@starting-style {
  dialog[open] {
    opacity: 0;
    transform: scale(0.96);
  }
}
```

---

## Accessibility Guarantee: `prefers-reduced-motion`

Always ensure all transitions and animations are neutralized when reduced motion is requested:

```html
<div class="transition-transform duration-150 motion-reduce:transition-none motion-reduce:animate-none">
  <!-- Content -->
</div>
```
Or in global CSS:
```css
@media (prefers-reduced-motion: reduce) {
  *, ::before, ::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```
