# Series Manager Design System

Version: 1.0.0  
Last Updated: 2026-05-20  
Plugin: Series Manager  
Scope: WordPress Admin UI + Gutenberg Components

---
## 1. Overview & Creative North Star
This design system is built for high-stakes editorial environments where clarity, authority, and sophistication are paramount. Moving beyond the "generic SaaS" aesthetic, we embrace a **Creative North Star: The Digital Curator.** 

The system treats the interface not as a collection of boxes, but as a series of curated layers. By utilizing intentional asymmetry, breathing room (negative space), and a sophisticated typographic scale, we create a signature experience that feels as intentional as a high-end print magazine. We break the "template" look by prioritizing tonal depth over structural lines, allowing the content to dictate the rhythm of the UI.

---

## 2. Colors
Our palette is a study in tonal restraint. We use a base of "Cool Grays" and "Crisp Whites" to create an expansive, airy feel, punctuated by a deep, authoritative Blue for functional intent.

```css
:root {
  --sm-color-primary: #0062a2;
  --sm-color-primary-dim: #00568e;

  --sm-surface: #f9f9fa;
  --sm-surface-low: #f2f4f5;
  --sm-surface-lowest: #ffffff;

  --sm-text-primary: #2f3335;
  --sm-text-secondary: #5b6062;

  --sm-outline-ghost: rgba(175,178,181,0.15);

  --sm-radius-md: 0.375rem;

  --sm-shadow-ambient:
    0px 12px 32px rgba(47, 51, 53, 0.06);
}
```

---

## 3. Typography
Typography is the voice of this system. We use a dual-typeface strategy to balance character with utility.

*   **The Display Voice (Manrope):** All `display` and `headline` tokens utilize **Manrope**. Its geometric yet friendly proportions provide a modern, high-end editorial feel. Use `display-lg` (3.5rem) with tighter letter-spacing (-0.02em) for hero moments to create an authoritative presence.
*   **The Utility Voice (Inter):** All `title`, `body`, and `label` tokens utilize **Inter**. Inter is chosen for its supreme readability in dense content management contexts.
*   **Hierarchy as Identity:** By pairing a large `headline-lg` in Manrope with a precise `body-md` in Inter, we create a high-contrast visual tension that signals "Premium Editorial" rather than "Standard Data Entry."

---

## 4. Elevation & Depth
In this system, depth is a result of **Tonal Layering**, not artificial shadows.

*   **The Layering Principle:** Stacking determines hierarchy. Place a `surface-container-lowest` (#ffffff) card on a `surface-container-low` (#f2f4f5) section. The slight shift in brightness creates a "natural lift."
*   **Ambient Shadows:** When a floating effect is mandatory (e.g., a Modal), shadows must be extra-diffused. 
    *   *Shadow Setting:* `0px 12px 32px rgba(47, 51, 53, 0.06)`. The use of the `on-surface` color (#2f3335) at a very low opacity (6%) mimics natural light.
*   **The "Ghost Border" Fallback:** If accessibility requires a container boundary, use a **Ghost Border**. This is a 1px stroke using `outline_variant` (#afb2b5) at **15% opacity**. It should be felt, not seen.
*   **Glassmorphism & Depth:** For overlaying elements, use `surface` at 80% opacity with a `blur` effect. This allows the underlying content's tonal warmth to bleed through, preventing the UI from feeling "pasted on."

---

## 5. Components

### Buttons
*   **Primary:** Uses the Signature Texture gradient. Roundedness: `md` (0.375rem). Text: `title-sm` (Inter).
*   **Secondary:** `surface-container-highest` (#dfe3e5) background with `on-surface` text. No border.
*   **Tertiary:** No background. Uses `primary` (#0062a2) text. For low-emphasis actions.

### Cards & Sections
*   **Rule:** **Forbid the use of divider lines.**
*   Content within cards is separated by vertical whitespace (referencing our 8px spacing grid) or by nesting a `surface-container-high` element within a `surface-container` base.

### Input Fields
*   **Default:** `surface-container-lowest` background with a Ghost Border.
*   **Focus:** Border transitions to 100% opacity `primary` (#0062a2) with a 2px "glow" (using the primary color at 10% opacity).
*   **Editorial Note:** Labels use `label-md` in `on-surface-variant` (#5b6062) for a muted, professional look.

### Chips (Meta-tags)
*   Used for Categories or Tags. Background: `secondary-container` (#dee3eb). Text: `label-md`. Roundedness: `full`. They should feel like soft, rounded pebbles that don't distract from the main text.

### The Editorial List
*   List items must not use borders. An "Active" state is indicated by a background shift to `primary-container` (#75b8fd) at 20% opacity and a 4px `primary` accent bar on the left edge.

---

## 6. Do's and Don'ts

### Do:
*   **Do** use extreme whitespace. If you think there is enough margin, add 8px more.
*   **Do** use `surface-container` shifts to define a "Sidebar" vs. a "Workspace."
*   **Do** prioritize Manrope for any text larger than 24px to maintain the high-end brand voice.
*   **Do** use `surface-bright` (#f9f9fa) for the main work area to reduce eye strain during long editorial sessions.

### Don't:
*   **Don't** use pure black (#000000). Always use `on-surface` (#2f3335) for text to maintain a soft, premium "ink-on-paper" feel.
*   **Don't** use standard "drop shadows" with high opacity. They look "cheap" and dated.
*   **Don't** use borders to separate items in a list. Use vertical spacing or a subtle `surface-container-low` hover state.
*   **Don't** use the `error` color (#a83836) for anything other than critical destructive actions. It is a high-alert color that should not be used for "style."

---
*Director's Final Note: Every pixel must have a reason. If a design element doesn't serve the content or the clarity of the hierarchy, remove it. Elegance is subtraction.*