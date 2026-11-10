# Rule 04: Accessibility (a11y) & Ergonomics

Synthesized from **Julien Thibeaut's Fixing Accessibility** (`ui-skills.com`), **WCAG 2.1 AA Standards**, and **Modern Web Ergonomics**.

Design craft and accessibility are indivisible. A component that looks beautiful but cannot be navigated by keyboard or read by a screen reader is broken code.

---

## The 6 Non-Negotiable Accessibility Rules

### 1. Contrast Ratios (WCAG AA)
* **Standard Body Text:** Minimum **$4.5:1$** contrast ratio against its surface.
* **Large Text ($\ge 24\text{px}$ or $\ge 18.5\text{px}$ bold):** Minimum **$3:1$** contrast ratio.
* **UI Controls & Borders:** Focus indicators, checkmarks, and active borders must have at least **$3:1$** contrast against adjacent backgrounds.
* *Anti-Pattern:* `text-gray-400` on white ($2.1:1$) or `text-zinc-500` on black ($3.4:1$) for body text.

### 2. The 44px Mobile Tap Target Law
* On touch devices, human thumbs require an active target area of at least **$44 \times 44\text{px}$**.
* Even if an icon is $16\text{px}$ or $20\text{px}$, its bounding button container or hit-box must meet the minimum size:
  ```html
  <!-- Accessible icon button -->
  <button
    type="button"
    aria-label="Filter items"
    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg p-2.5 text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white"
  >
    <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
    </svg>
  </button>
  ```

### 3. Clear Visible Focus Indicators
* **Never use `outline-none` alone.** Stripping outlines without a replacement leaves keyboard users stranded.
* Enforce this universal focus-visible pattern:
  ```html
  focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2 dark:focus-visible:ring-white dark:focus-visible:ring-offset-neutral-950
  ```

### 4. Icon-Only Buttons Require `aria-label`
* When a button has no visible text node, screen readers cannot inform users of its function.
* **Mandate:** All icon-only buttons must carry an `aria-label` describing the action, and child SVGs must include `aria-hidden="true"`:
  ```html
  <!-- CORRECT -->
  <button type="button" aria-label="Delete project">
    <svg aria-hidden="true" class="size-5" ...></svg>
  </button>
  ```

### 5. Destructive Actions Require `AlertDialog`
* Any irreversible or destructive action (deleting data, cancelling subscriptions, revoking API keys) must require explicit confirmation.
* The confirm button must use an explicit descriptive verb:
  - **Correct:** "Delete Database", "Revoke Token", "Remove Member"
  - **Prohibited:** "OK", "Yes", "Submit"

### 6. Never Block User Clipboard Operations
* **Strictly Prohibited:** `onpaste="return false"`, blocking autocomplete, or disabling text selection on fields.
* Password fields, OTP inputs, and token forms must allow paste operations for password managers and accessibility software.

---

## Semantic HTML Hierarchy

Use proper semantic elements rather than generic `<div>` soup:

| Component Role | Semantic Element | Accessibility Benefit |
| :--- | :--- | :--- |
| **Top Navigation** | `<nav aria-label="Main Navigation">` | Screen reader landmark navigation |
| **Main Content** | `<main>` | Allows users to skip navigation straight to content |
| **Side Panel / Context** | `<aside>` | Denotes secondary complementary content |
| **Clickable Actions** | `<button type="button">` | Native keyboard activation (Enter & Space) and focusability |
| **Page Navigation** | `<a href="...">` | Informs browser of URL routing |
| **Modal Dialog** | `<dialog>` | Native accessibility tree, inert background trapping, ESC to close |
| **Data Lists** | `<ul>`, `<ol>`, `<li>` | Announces item counts and positional index to assistive tech |
| **Tabular Data** | `<table>`, `<th>`, `<td>` | Enables column/row coordinate navigation |

---

## Form Validation & Error Placement

Place validation error messages immediately beneath the related field, and connect them with `aria-describedby` and `aria-invalid="true"`:

```html
<div>
  <label for="project-name" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">
    Project Name
  </label>
  <input
    id="project-name"
    name="name"
    type="text"
    aria-invalid="true"
    aria-describedby="project-name-error"
    class="mt-1.5 block w-full rounded-lg border border-red-500 bg-white px-3 py-2 text-sm text-neutral-900 shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:bg-neutral-900 dark:text-white"
  />
  <p id="project-name-error" class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
    Project name must be at least 3 characters.
  </p>
</div>
```
