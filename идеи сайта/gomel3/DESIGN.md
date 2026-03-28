# Design System Documentation: The Civic Editorial

## 1. Overview & Creative North Star
The visual identity of a state institution must command respect while remaining profoundly human. For this design system, our **Creative North Star is "The Civic Editorial."** 

We are moving away from the cold, "grid-locked" appearance of traditional government portals. Instead, we embrace a high-end editorial layout that uses intentional asymmetry, overlapping elements, and exaggerated vertical whitespace to guide the user. The goal is to present complex social information with the clarity of a premium broadsheet and the warmth of a modern community center. By prioritizing tonal depth over rigid lines, we create an interface that feels open, accessible, and dignified.

---

## 2. Colors & Surface Philosophy
The palette is rooted in the institution's heritage—using Deep Red and Green—but refined through a Material Design lens to ensure professional polish and WCAG AA accessibility.

### The "No-Line" Rule
To achieve a signature high-end look, **1px solid borders are strictly prohibited for sectioning.** Boundaries must be defined through background color shifts or subtle tonal transitions. For example:
- A `surface-container-low` (#f3f3f3) content block sitting on a `surface` (#f9f9f9) background.
- This creates a "soft edge" that feels integrated into the environment rather than boxed in.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers—stacked sheets of fine paper. 
- **Base Level:** `surface` (#f9f9f9) for the primary background.
- **Sectioning:** Use `surface-container-low` (#f3f3f3) for large content areas.
- **Cards/Focus Elements:** Use `surface-container-lowest` (#ffffff) to provide a "lifted" feel for interactive elements.

### Glass & Gradient Transitions
To prevent a flat, "out-of-the-box" feel:
- **Hero CTAs:** Apply a subtle linear gradient transitioning from `primary` (#990320) to `primary_container` (#bc2635) at a 135-degree angle. This adds "soul" and depth.
- **Floating Navigation:** Use **Glassmorphism**. Apply `surface_container_lowest` at 80% opacity with a `backdrop-blur` of 12px. This ensures the institutional red and green accents bleed through the header, softening the layout.

---

## 3. Typography
Our typography pairing balances institutional authority with high-readability accessibility.

- **Display & Headlines (Public Sans):** Used for large-scale editorial moments. Public Sans provides a geometric, sturdy foundation that feels official. Use `display-lg` (3.5rem) for hero statements with tight letter-spacing (-0.02em) to create a "custom-set" editorial look.
- **Body & Labels (Inter):** Inter is the workhorse of accessibility. Its tall x-height ensures that even at `body-sm` (0.75rem), text remains legible for elderly citizens or those with visual impairments.
- **Intentional Asymmetry:** Avoid centering all text. Align headlines to the left with generous `spacing-12` (4rem) left-margins to create an elegant, non-standard rhythmic flow.

---

## 4. Elevation & Depth
In this system, depth is a function of light and tone, not shadows and lines.

- **The Layering Principle:** Instead of a drop shadow, place a `surface_container_lowest` card inside a `surface_container` section. The contrast between #ffffff and #eeeeee creates a soft, natural lift.
- **Ambient Shadows:** When a card must float (e.g., a critical alert or modal), use a shadow with a 32px blur and 4% opacity, using a tint of `on_surface` (#1a1c1c). This mimics natural, ambient room light.
- **The "Ghost Border" Fallback:** If a border is required for input field clarity, use the `outline_variant` (#e2bebd) at **20% opacity**. Never use a 100% opaque border; it breaks the editorial flow.
- **Glassmorphism:** Use semi-transparent layers for navigation bars to allow background content to remain part of the user's peripheral context, reinforcing a sense of transparency and trust.

---

## 5. Components

### Buttons
- **Primary:** High-contrast `primary` (#990320) background with `on_primary` (#ffffff) text. Use `rounded-md` (0.375rem) for a professional look.
- **Secondary:** `secondary_container` (#a0f399) background with `on_secondary_container` (#217128) text. This provides a welcoming "Green" alternative for "Apply" or "Submit" actions.
- **Interaction:** On hover, shift the background color to one tier higher in the color scale (e.g., `primary` to `on_primary_fixed_variant`) rather than using a shadow.

### Cards & Lists
- **Rule:** **Forbid divider lines.** 
- Separate list items using `spacing-4` (1.4rem) of vertical white space. 
- For cards, use a subtle background shift to `surface_container_low`. If multiple cards are grouped, use a staggered "masonry" layout to break the rigid grid and lean into the editorial aesthetic.

### Input Fields
- Use `surface_container_highest` (#e2e2e2) for the input tray.
- Avoid a bottom line. Instead, use a 2px `primary` (#990320) left-accent bar that appears only on focus to indicate the active state authoritatively.

### Institutional Accents (Signature Component)
- **The "Pattern Strip":** Incorporate a subtle, 40px tall stylized geometric pattern (inspired by the logo's embroidery) using `outline_variant` at 10% opacity. Use this to separate major thematic shifts in a long-scroll page.

---

## 6. Do's and Don'ts

### Do
- **Do** use `spacing-16` (5.5rem) or `spacing-20` (7rem) between major sections to let the content breathe.
- **Do** use "Editorial Overlaps": allow a high-quality image of the community to slightly overlap the edge of a `surface-container` block.
- **Do** prioritize the `secondary` green (#1b6d24) for success messages and growth-related services to feel "welcoming."

### Don't
- **Don't** use pure black (#000000) for text. Use `on_surface` (#1a1c1c) to maintain a premium, softer contrast.
- **Don't** use 1px dividers to separate menu items. Use `spacing-3` (1rem) gaps.
- **Don't** use "Alert Red" for standard branding. Only use `error` (#ba1a1a) for critical system failures; use our institutional `primary` (#bc2635) for all other brand moments.
- **Don't** crowd the layout. If a section feels "full," increase the spacing token by one level.