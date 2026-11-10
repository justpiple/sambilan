# Rule 02: Visual Craft, Archetypes & Token Architecture

Synthesized from **Anthropic Frontend Design**, **Paul Bakaus's Impeccable**, and **Julien Thibeaut's Baseline UI**.

High-craft interface design is intentional, systematic, and restrained. This guide establishes the 5 Aesthetic Archetypes, the 4–6 Named Token Architecture, layered elevation math, and typography hierarchy.

---

## The 5 Aesthetic Archetypes

Never design an "unspecified modern interface". Choose one of these 5 distinct archetypes and execute it with 100% conviction:

### 1. Linear / Raycast Precision (Modern Utilitarian Dark/Light)
* **Personality:** Technical, keyboard-first, ultra-crisp, high-density, professional.
* **Colors:** Deep monochromatic canvas (`zinc-950` / `#09090b`), elevated surfaces (`zinc-900` / `#18181b`), subtle 1px borders (`border-white/10` or `border-zinc-200`), bright electric or crisp white accents.
* **Typography:** Clean geometric sans (e.g. Geist, Inter, SF Pro) with monospace accents for shortcuts (`font-mono text-xs`).
* **Signature Details:** `kbd` badges (`border border-white/15 bg-white/5 px-1.5 py-0.5 rounded text-[10px]`), inner top highlights, 100ms snappy animations.

### 2. Swiss Modern / Editorial
* **Personality:** Sophisticated, authoritative, content-driven, magazine-grade.
* **Colors:** Pure stark ivory/cream canvas (`#faf9f5` or `neutral-50`), deep charcoal ink text (`#1a1a1a`), single rich editorial accent (e.g. Bordeaux wine `#800020`, Forest `#1b4332`, or Cobalt `#0047ab`).
* **Typography:** Bold serif display headings (e.g. Newsreader, Instrument Serif, Playfair) paired with a clean Grotesque body font.
* **Signature Details:** Asymmetric 2-column or 3-column layouts, generous white space, thin hairline rule dividers (`border-t border-neutral-300`), large pull-quotes.

### 3. Warm Crafted / Humanist
* **Personality:** Inviting, artisanal, thoughtful, approachable yet premium.
* **Colors:** Warm stone/sand background (`stone-50` / `stone-100`), warm surface cards (`white`), warm slate text, warm amber or terracotta primary accents.
* **Typography:** Rounded or humanist sans (e.g. Plus Jakarta Sans, Outfit, DM Sans) with friendly proportions.
* **Signature Details:** Pill badges, soft tactile buttons with warm subtle borders (`border-stone-200/80`), gentle micro-elevations.

### 4. Fintech / High-Density Data
* **Personality:** Fast, trustworthy, dense, information-rich, audit-ready.
* **Colors:** Neutral slate canvas (`slate-50` / `slate-950`), white or `slate-900` cards, tint-based status pills (Emerald for positive, Rose for negative, Amber for pending).
* **Typography:** Compact sans with strict `tabular-nums` on all values, currencies, timestamps, and percentages.
* **Signature Details:** Border-separated compact table rows, sticky filter bars, zero unnecessary vertical padding, clear metric delta indicators (`+12.4%`).

### 5. Neo-Brutalist / Industrial
* **Personality:** Bold, raw, energetic, high-contrast, playful.
* **Colors:** High-saturation primary punch (Electric Yellow, Cyan, or International Klein Blue), stark black borders (`border-2 border-black dark:border-white`), pure white or pitch black canvas.
* **Typography:** Heavy grotesque display sans in uppercase, bold weight everywhere (`font-extrabold`).
* **Signature Details:** Hard drop shadows with zero blur (`shadow-[3px_3px_0_0_#000]`), tactile offset buttons that press down flush into their shadow on active:
  ```html
  <button class="border-2 border-black bg-yellow-400 px-4 py-2 font-bold shadow-[3px_3px_0_0_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
    Confirm Action
  </button>
  ```

---

## The 4–6 Named Token Architecture

Every view or component must map to explicit semantic tokens. Do not introduce one-off, arbitrary color hexes.

```css
/* Example Token Schema in Tailwind v4 CSS @theme */
@theme {
  --color-canvas: oklch(0.985 0.002 247);       /* Page background */
  --color-surface: oklch(1 0 0);                /* Cards, containers, modals */
  --color-surface-hover: oklch(0.96 0.005 247); /* Hover states */
  --color-border-subtle: oklch(0.91 0.005 247); /* Card & divider borders */
  --color-primary: oklch(0.20 0.02 260);        /* Key CTA actions */
  --color-primary-hover: oklch(0.15 0.02 260);  /* Primary CTA hover */
  --color-text-main: oklch(0.15 0.01 260);      /* Headings & body (High contrast) */
  --color-text-muted: oklch(0.48 0.02 260);     /* Captions & subtext (>= 4.5:1) */
  --color-danger: oklch(0.55 0.22 27);          /* Destructive actions */
}
```

### Contrast Tier Standards
- **Primary / Main Text:** $\ge 7:1$ contrast ratio against background (e.g. `text-neutral-900 dark:text-neutral-50`).
- **Secondary / Supporting Text:** $\ge 4.5:1$ contrast ratio (e.g. `text-neutral-600 dark:text-neutral-400`).
- **Muted / Inactive Text:** Only for non-critical hints or metadata where contrast is $\ge 4.5:1$ against surface (e.g. `text-neutral-500`).

---

## Layered Elevation Math: Crisp Borders vs. Fuzzy Shadows

Modern UI craft achieves depth through **containment borders + subtle micro-shadows**, never through giant blurry drop-shadows.

### The Light Mode Elevation Formula
Combine:
1. **1px subtle border:** `border border-neutral-200/80`
2. **Micro downward shadow:** `shadow-xs` or `shadow-[0_1px_2px_rgba(0,0,0,0.05)]`
3. **Optional inner light ring:** for primary buttons or cards to simulate light hitting the top edge:
   ```html
   <div class="rounded-xl border border-neutral-200/90 bg-white p-5 shadow-xs">
     <!-- Content -->
   </div>
   ```

### The Dark Mode Elevation Formula
Drop shadows are invisible against pure black canvases. Modern dark mode relies on **luminance layering**:
1. Canvas: deep background `bg-zinc-950`
2. Elevated surface: 5–8% lighter `bg-zinc-900`
3. Crisp boundary: 1px semi-transparent border `border border-white/10`
4. Top highlight: `shadow-[inset_0_1px_0_0_rgba(255,255,255,0.08)]`

```html
<!-- Dark mode high-craft card -->
<div class="rounded-xl border border-white/10 bg-zinc-900/90 p-5 shadow-[inset_0_1px_0_0_rgba(255,255,255,0.08)]">
  <!-- Content -->
</div>
```

---

## Typography Standards

Always enforce modern CSS typography utilities:

1. **`text-balance` for all titles and headings:** Prevents orphan words on second lines.
   ```html
   <h2 class="text-balance text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
     Accelerate your workflow with automated pipelines
   </h2>
   ```

2. **`text-pretty` for body paragraphs:** Enforces modern browser hyphenation and paragraph balancing:
   ```html
   <p class="text-pretty text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
     Connect your repositories and begin running automated test suites on every pull request.
   </p>
   ```

3. **`tabular-nums` for all metrics, dates, and tables:** Prevents UI jitter when numbers count up or update dynamically:
   ```html
   <span class="tabular-nums font-mono text-xl font-bold tracking-tight">
     $48,290.50
   </span>
   ```

4. **Tracking Rules:**
   - Display Headings ($\ge 24\text{px}$): `tracking-tight` or `tracking-tighter`.
   - Normal Body ($14\text{px}$–$16\text{px}$): `tracking-normal`.
   - Small Caps / Badges ($\le 12\text{px}$): `text-[11px] font-semibold uppercase tracking-wider`.
