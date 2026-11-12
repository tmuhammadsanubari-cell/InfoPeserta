---
name: Portal Peserta Ringkas
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#3e4947'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#6e7977'
  outline-variant: '#bdc9c6'
  surface-tint: '#006a63'
  primary: '#005c55'
  on-primary: '#ffffff'
  primary-container: '#0f766e'
  on-primary-container: '#a3faef'
  inverse-primary: '#80d5cb'
  secondary: '#006a61'
  on-secondary: '#ffffff'
  secondary-container: '#86f2e4'
  on-secondary-container: '#006f66'
  tertiary: '#485452'
  on-tertiary: '#ffffff'
  tertiary-container: '#606c6a'
  on-tertiary-container: '#e0edea'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9cf2e8'
  primary-fixed-dim: '#80d5cb'
  on-primary-fixed: '#00201d'
  on-primary-fixed-variant: '#00504a'
  secondary-fixed: '#89f5e7'
  secondary-fixed-dim: '#6bd8cb'
  on-secondary-fixed: '#00201d'
  on-secondary-fixed-variant: '#005049'
  tertiary-fixed: '#d8e5e2'
  tertiary-fixed-dim: '#bcc9c6'
  on-tertiary-fixed: '#121e1c'
  on-tertiary-fixed-variant: '#3d4947'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  headline-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.025em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-desktop: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

This design system powers a high-utility, mobile-first participant portal tailored for on-the-ground event attendees across Indonesia. Built for fast loading on low-spec Android devices and clear outdoor viewing under direct sunlight, the visual language prioritizes utility, rapid orientation, and zero cognitive friction.

### Aesthetics & Design Movement
The style is **Functional Minimalism** combined with **Accessible Utility**:
- **Zero unnecessary assets:** Free of large raster imagery, heavy gradients, or decorative micro-illustrations. 
- **High legibility & contrast:** Sharp slate typography against crisp surfaces ensures complete clarity under varied outdoor lighting.
- **Physical confidence:** Generous, finger-friendly touch targets with instant visual feedback prevent mis-taps during active event movement.
- **Tone & Voice:** Authoritative yet welcoming, structured, institutional, and purely informative, using concise Indonesian phrasing (*Bahasa Indonesia yang lugas dan komunikatif*).

## Colors

The palette is engineered for high outdoor legibility and crisp contrast ratios that exceed WCAG AAA standards for critical informational elements.

- **Primary (`#0F766E` - Teal 700):** Primary actions, active navigation anchors, primary badges, and top-level brand markers.
- **Secondary (`#0D9488` - Teal 600):** Interactive hover/press states, accents, and prominent borders for active card selections.
- **Tertiary / Tint (`#F0FDFA` - Teal 50):** Soft highlight surfaces, subtle pill backgrounds, and active selection fills.
- **Canvas Neutral (`#F8FAFC` - Slate 50):** App-wide background providing glare-reducing warmth compared to pure white.
- **Surface Neutral (`#FFFFFF` - Pure White):** Clean container fill for content cards and modular panels.
- **Border / Divider (`#E2E8F0` - Slate 200):** Non-intrusive container delineation.
- **Text Primary (`#0F172A` - Slate 900):** Primary headings, critical data, labels, and core values.
- **Text Secondary (`#475569` - Slate 600):** Supporting metadata, timestamps, locations, and sub-labels.
- **Status Accents:**
  - Success: `#047857` (Surface `#ECFDF5`)
  - Warning: `#B45309` (Surface `#FFFBEB`)
  - Critical/Error: `#B91C1C` (Surface `#FEF2F2`)

## Typography

The type system is powered by **Inter**, chosen for its tall x-height, clear numeric tabular spacing, and exceptional rendering performance across varied mobile screens. 

### Rules & Hierarchy
- **Base body sizing:** Never scale base reading copy below `16px` on core participant instructions, ensuring legible reading while walking outdoors.
- **Tabular figures:** Use numeric font-variant settings (`font-feature-settings: "tnum"`) for dates, seat assignments, QR codes, and schedules to maintain vertical alignment.
- **Pacing:** Pair `headline-md` or `headline-sm` directly with `body-sm` in metadata rows (e.g., "Lokasi: Ruang Cendrawasih") to deliver instant scanning.

## Layout & Spacing

A mobile-constrained, single-column fluid grid system keeps content centered, legible, and predictable.

### Grid & Breakpoints
- **Mobile (< 640px):** Single-column layout. Margin: `1rem` (16px), Column gutter: `1rem`. Max-width content boundary is constrained to `480px` on mobile phones to ensure comfortable thumb navigation.
- **Tablet & Desktop (≥ 640px):** Single-column container with a maximum width of `560px` centered on the viewport. This retains the ergonomic efficiency of the mobile interface while preventing sprawling information density on laptops or kiosks.

### Spacing Philosophy
- Form fields, buttons, and action panels are strictly separated using vertical spacing of `space-md` (16px) or `space-lg` (24px).
- Internal card padding is standardized to `space-md` (16px) on mobile and `space-lg` (24px) for prominent hero widgets (e.g., Participant QR Badge).

## Elevation & Depth

Visual hierarchy relies on structural clarity and tactile lightness rather than artificial, GPU-heavy shadow stacks.

### Elevation Levels
- **Base Layer (0dp):** Canvas `#F8FAFC`.
- **Card / Surface Layer (1dp):** Pure `#FFFFFF` background with a subtle crisp outline (`1px solid #E2E8F0`) and an ultra-soft ambient shadow: `box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04)`.
- **Raised / Interactive Layer (2dp):** Used for sticky bottom action bars and active modals: `1px solid #E2E8F0` with `box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08)`.
- **Highlight Boundary:** Critical sections (such as check-in QR codes or schedule changes) apply an accent border (`1px solid #0D9488`) combined with a soft background tint of `#F0FDFA`.

## Shapes

The design system standardizes on a balanced, approachable curvature that feels modern and structural without becoming excessively playful.

- **Standard Containers & Cards:** `12px` (`0.75rem`), balancing structured alignment with a soft, friendly appearance.
- **Buttons & Form Inputs:** `8px` (`0.5rem`) for compact visual discipline and unambiguous tap targets.
- **Badges, Chips & Status Pills:** `9999px` (fully rounded pill) to contrast against rectangular content blocks.
- **Borders:** Consistent `1px` structural borders (`#E2E8F0`) across all elevated surfaces.

## Components

### 1. Buttons
- **Touch Targets:** Minimum height of `48px` with horizontal padding of `16px` (`space-md`).
- **Primary Button:** `#0F766E` background, pure white text (`#FFFFFF`), `8px` radius, `label-lg` font weight. Active/Pressed: `#115E59`.
- **Secondary / Outline Button:** Transparent background, `1.5px solid #0F766E`, `#0F766E` text. Active/Pressed: `#F0FDFA`.
- **Tertiary Button:** Transparent background, `#0F766E` text, underline on active. Used for secondary navigation ("Kembali ke Jadwal").

### 2. Cards (Kartu Informasi)
- **Base Card:** `#FFFFFF` background, `1px solid #E2E8F0`, `12px` border radius, padding `16px`. Soft drop shadow (`0 2px 8px rgba(0,0,0,0.04)`).
- **Featured Card (e.g., ID Peserta / QR Masuk):** Highlighted with `1px solid #0D9488` border, top highlight bar in `#0F766E`, and internal highlight container with `#F0FDFA` background.

### 3. Chips & Status Badges (Lencana Status)
- **Geometry:** Height of `28px` to `32px`, pill radius (`9999px`), padding `4px 12px`.
- **Info/Schedule Active:** `#F0FDFA` fill, `#0F766E` label, `#0D9488` border (`1px`).
- **Registration Confirmed:** `#ECFDF5` fill, `#047857` label (`"Terverifikasi"`).
- **Pending/Waitlist:** `#FFFBEB` fill, `#B45309` label (`"Menunggu"`).

### 4. Input Fields (Kolom Input & Pencarian)
- **Geometry:** Minimum height `48px`, `8px` border radius, padding `12px 16px`.
- **Styling:** `#FFFFFF` surface, `1px solid #CBD5E1` border, `#0F172A` text, `#94A3B8` placeholder.
- **Focus State:** `1.5px solid #0F766E` border with an accessible subtle outline (`#0F766E` at 15% opacity).

### 5. Checkboxes & Radio Buttons
- **Touch Target:** The interactive container must span at least `48px × 48px` even if the visual element is `20px × 20px`.
- **Visuals:** `#0F766E` checked fill, `#CBD5E1` unchecked border.

### 6. Lists (Daftar Acara & Sesi)
- **Item Rhythm:** Minimum vertical height of `56px` per item. Divided by `1px solid #F1F5F9`.
- **Content Structure:** Left-aligned bold time anchor (`label-md`), vertical divider, followed by session title (`body-lg`) and room label (`body-sm`, `#475569`).

### 7. Domain-Specific Components
- **QR Pass Container:** Centered white card with clean padding (`24px`), rendering a crisp, high-contrast monochrome QR code with high error-correction, framed by attendee name (`headline-md`) and Registration ID (`label-md`, `#475569`).
- **Live Agenda Banner:** Fixed bottom or floating notice bar (`#0F766E` background, `#FFFFFF` text) indicating the ongoing session with a single-tap jump action.