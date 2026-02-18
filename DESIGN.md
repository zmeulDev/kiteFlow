# KiteFlow - Visitor Management SaaS

## 1. Overview
A full-featured SaaS application for coworking spaces and office buildings to track and manage visitors.

## 2. Core Personas
- **Super Admin**: Manages global settings, plans, and tenants (offices).
- **Tenant Admin**: Manages their specific office/coworking space, users (employees), and visitor settings.
- **Host (User)**: The employee/member being visited. Receives notifications.
- **Visitor**: Uses the Kiosk (Frontend) to check in.

## 3. Technology Stack
- **Framework**: Laravel 11
- **UI/UX Strategy**: Modern, premium feel using Tailwind CSS. Focus on:
    - **Minimalism**: Clean whitespace and high-contrast typography (Inter/Geist font).
    - **Interactivity**: smooth transitions using **Livewire 3** (SPA-like navigation with `wire:navigate`) + **Alpine.js**.
    - **Independent Component Loading**: Using Livewire's **Lazy Loading** (`#[Lazy]`) and **Wire Elements** pattern for modals and slide-overs to ensure the main dashboard remains fast and responsive.
    - **Real-time State**: Utilizing Livewire's **Events** and **Polling** where necessary for live visitor log updates without full page refreshes.
    - **Accessibility**: ARIA-compliant components.
    - **Kiosk Optimizations**: Large touch-targets for tablet use.
    - **Glassmorphism/Soft Shadows**: Subtle depth for cards and modals.
    - **Auto Dark Mode**: Support for both light and dark environments.
- **Database**: PostgreSQL/MySQL
- **Notifications**: Mail, Database, and potential WhatsApp/Slack integration.

## 4. Key Features
### A. Kiosk Mode (Check-in)
- Company/Office selection.
- Name, Phone, Email capture.
- Purpose of visit.
- Terms & Conditions / NDA checkbox + Digital Signature.
- Photo capture (optional).

### B. SaaS Dashboard
- Real-time visitor log.
- Management of "Expected Guests".
- Analytic charts (Busy hours, Visitor types).
- Export logs (CSV/PDF) for GDPR compliance.

### C. Multi-tenancy
- Each office/coworking space gets a unique subdomain or slug (e.g., `cowork-cluj.visiflow.io`).
- Tenant-specific branding (Logo, Colors).

## 5. Database Schema (Preliminary)
- `tenants`: id, name, slug, settings, logo.
- `users`: id, tenant_id, name, email, role.
- `visitors`: id, tenant_id, first_name, last_name, email, phone.
- `visits`: id, visitor_id, tenant_id, user_id (host), purpose, signed_at, check_in_at, check_out_at.
- `terms`: id, tenant_id, content, version.

## 6. Project Roadmap
1. [ ] Project Initialization & Base Architecture.
2. [ ] Multi-tenancy & Authentication.
3. [ ] Kiosk Interface development.
4. [ ] Host Notification system.
5. [ ] Dashboard & Analytics.
