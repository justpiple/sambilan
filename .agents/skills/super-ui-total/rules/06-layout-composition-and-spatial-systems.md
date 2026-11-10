# Rule 06: Layout Composition, Spatial Hierarchy & Grid Systems

Synthesized from **Anthropic Frontend Design**, **Paul Bakaus's Impeccable (`/layout`)**, and **Modern Design Engineering**.

Layout is the visual syntax of user experience. A poor layout causes cognitive friction, while an intentional spatial architecture guides the eye effortlessly through hierarchy, rhythm, and clear relationships.

---

## The Spatial Hierarchy: 8pt Grid & Proximity Law

### 1. The Law of Proximity (Gestalt)
Items that belong together must be visibly closer to each other than to unrelated elements:
- **Atom Gap (Icon + Label):** $6\text{px}$–$8\text{px}$ (`gap-1.5` or `gap-2`).
- **Molecule Gap (Title + Description inside a card):** $8\text{px}$–$12\text{px}$ (`gap-2` or `gap-3`).
- **Organism Internal Padding (Card interior):** $16\text{px}$–$24\text{px}$ (`p-4` to `p-6`).
- **Card-to-Card Grid Gap:** $16\text{px}$–$24\text{px}$ (`gap-4` to `gap-6`).
- **Section-to-Section Macro Rhythm:** $48\text{px}$–$80\text{px}$ (`py-12` to `py-20`).

> [!CAUTION]
> **Equal Spacing Trap:** Never use the same margin/padding everywhere (e.g. `mb-4` on title, `mb-4` on paragraph, `mb-4` between sections). Without varying spatial intervals, hierarchy collapses into visual static.

---

## The 6 Master Layout Archetypes

### 1. Modern SaaS App Shell (Collapsible Sidebar + Fluid Body)
The standard for web applications, dashboards, and portals:

```html
<div class="min-h-dvh flex bg-neutral-50 dark:bg-zinc-950 text-neutral-900 dark:text-neutral-100">
  <!-- Desktop Sidebar -->
  <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:border-r border-neutral-200/80 dark:border-white/10 bg-white dark:bg-zinc-900/50 p-4">
    <!-- Workspace Brand / Selector -->
    <div class="h-10 flex items-center px-2 font-semibold tracking-tight">Workspace</div>
    <!-- Navigation Links -->
    <nav class="mt-4 flex-1 space-y-1">
      <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium bg-neutral-100 dark:bg-zinc-800 text-neutral-900 dark:text-white">Dashboard</a>
      <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-zinc-800/60">Projects</a>
    </nav>
  </aside>

  <!-- Main Canvas -->
  <div class="flex-1 flex flex-col min-w-0">
    <!-- Sticky Topbar -->
    <header class="sticky top-0 z-20 h-14 border-b border-neutral-200/80 dark:border-white/10 bg-white/80 dark:bg-zinc-900/80 backdrop-blur px-4 sm:px-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <!-- Breadcrumbs or Title -->
        <h1 class="text-sm font-semibold tracking-tight">Analytics Overview</h1>
      </div>
      <!-- Actions / User Menu -->
      <div class="flex items-center gap-2">...</div>
    </header>

    <!-- Scrollable Body with Max Containment -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
      <!-- Dashboard Content -->
    </main>
  </div>
</div>
```

---

### 2. The Asymmetric Bento Grid
Breaks the monotony of generic 3-column cards by giving dominant weight to key features:

```html
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-12 gap-4 sm:gap-6">
  <!-- Primary Focal Card (Takes 8 of 12 columns, spans 2 rows) -->
  <div class="md:col-span-3 lg:col-span-8 lg:row-span-2 rounded-xl border border-neutral-200/80 bg-white p-6 shadow-xs dark:border-white/10 dark:bg-zinc-900 flex flex-col justify-between">
    <div>
      <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Key Metric</span>
      <h3 class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white mt-1 text-balance">Real-time Performance Metrics</h3>
      <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2 text-pretty">Monitor request latency, memory spikes, and error rates across microservices.</p>
    </div>
    <!-- Rich graphic / chart placeholder -->
    <div class="mt-6 h-48 rounded-lg bg-neutral-100 dark:bg-zinc-800/60 flex items-center justify-center">
      <span class="text-xs text-neutral-400 font-mono">Chart Visualizer</span>
    </div>
  </div>

  <!-- Secondary Tile 1 (Takes 4 of 12 columns) -->
  <div class="md:col-span-3 lg:col-span-4 rounded-xl border border-neutral-200/80 bg-white p-6 shadow-xs dark:border-white/10 dark:bg-zinc-900">
    <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Throughput</span>
    <p class="text-3xl font-bold tracking-tight tabular-nums text-neutral-900 dark:text-white mt-2">14,200 req/s</p>
    <p class="text-xs text-emerald-600 font-medium mt-1">+8.4% from yesterday</p>
  </div>

  <!-- Secondary Tile 2 (Takes 4 of 12 columns) -->
  <div class="md:col-span-3 lg:col-span-4 rounded-xl border border-neutral-200/80 bg-white p-6 shadow-xs dark:border-white/10 dark:bg-zinc-900">
    <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Error Budget</span>
    <p class="text-3xl font-bold tracking-tight tabular-nums text-neutral-900 dark:text-white mt-2">99.98%</p>
    <p class="text-xs text-neutral-500 mt-1">Within SLA targets</p>
  </div>
</div>
```

---

### 3. Editorial Split Section (60/40 or 50/50 Hero)
Ideal for landing pages, authentication views, or featured showcases:
- **Left Column (Context):** Display typography, value proposition, bullet proof-points, primary CTA.
- **Right Column (Showcase):** High-craft interactive product mock, live code snippet, or visual card.

```html
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
    <!-- Text Column -->
    <div class="lg:col-span-7 space-y-6">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-neutral-200/80 dark:border-white/10 bg-white dark:bg-zinc-900 text-xs font-medium text-neutral-700 dark:text-neutral-300 shadow-xs">
        <span class="size-2 rounded-full bg-emerald-500"></span>
        Production Ready v2.0
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-neutral-900 dark:text-white text-balance">
        Deploy applications with unprecedented speed and certainty.
      </h1>
      <p class="text-lg text-neutral-600 dark:text-neutral-400 max-w-2xl text-pretty">
        Automate database provisioning, SSL termination, and horizontal scale across global edge nodes in seconds.
      </p>
      <div class="flex flex-wrap items-center gap-3 pt-2">
        <button class="min-h-[44px] px-5 py-2.5 rounded-lg bg-neutral-900 text-white font-medium text-sm hover:bg-neutral-800 active:scale-[0.98] transition-all shadow-xs dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100">
          Get Started
        </button>
        <button class="min-h-[44px] px-5 py-2.5 rounded-lg border border-neutral-200/90 bg-white text-neutral-800 font-medium text-sm hover:bg-neutral-50 active:scale-[0.98] transition-all shadow-xs dark:border-white/10 dark:bg-zinc-900 dark:text-neutral-200">
          Documentation
        </button>
      </div>
    </div>

    <!-- Product Showcase Column -->
    <div class="lg:col-span-5">
      <div class="rounded-2xl border border-neutral-200/80 bg-white dark:border-white/10 dark:bg-zinc-900 p-6 shadow-sm">
        <!-- Interactive Preview Mockup -->
      </div>
    </div>
  </div>
</section>
```

---

### 4. Master-Detail (Split Pane Canvas)
Used for inbox, support tickets, file explorers, and logs:
- Left pane: list with search and filter controls (`w-full md:w-80 lg:w-96 flex-shrink-0 border-r`).
- Right pane: full detail inspector canvas (`flex-1 min-w-0 overflow-y-auto`).

---

## 4 Critical Layout Engineering Rules

### 1. Action Button Alignment in Grids (`mt-auto`)
When cards in a grid have variable text lengths, action buttons look sloppy if they sit at different heights. Use `flex flex-col` and `mt-auto` on the footer/button to pin them flush across all cards:

```html
<div class="rounded-xl border border-neutral-200/80 bg-white p-6 flex flex-col justify-between">
  <div>
    <h3>Plan Title</h3>
    <p class="text-sm text-neutral-600 mt-2">Variable length description...</p>
  </div>
  <!-- Pinned flush to bottom -->
  <div class="mt-6 pt-4 border-t border-neutral-100 dark:border-white/5">
    <button class="w-full ...">Select Plan</button>
  </div>
</div>
```

### 2. Prevent Layout Breakage with `min-w-0`
In flex containers with long text, truncating text (`truncate`) fails unless the flex item has `min-w-0`:

```html
<!-- Without min-w-0, the flex child will overflow the viewport! -->
<div class="flex items-center gap-3">
  <div class="size-8 rounded-full bg-neutral-200 flex-shrink-0"></div>
  <div class="min-w-0 flex-1">
    <p class="text-sm font-medium truncate">Very-long-user-email-address-that-might-break-layout@example.com</p>
  </div>
</div>
```

### 3. Container Boundaries (`max-w-* mx-auto`)
Never let content stretch across an ultra-wide monitor without bounds:
- Marketing / Landing page text: `max-w-4xl` to `max-w-5xl`.
- Dashboard grids: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`.
- Reading / Article / Prose: `max-w-prose` (approx 65–75 characters per line for optimal reading ergonomics).

### 4. Responsive Wrapping & Stacking
Always specify how items wrap when viewport contracts:
- Use `flex-wrap gap-3` on button groups and filter bars.
- Use `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4` on metric cards.
