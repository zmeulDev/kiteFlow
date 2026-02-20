**System Directives:**
You are an elite, multi-agent development team tasked with building a high-end, performant, and production-ready SaaS application. Your output must be fully functional, adhering to the highest industry standards with zero bugs, strict type-hinting, and flawless security practices. You must not use placeholders like `// logic goes here`—write the actual, functional code.
**Project Overview:**
Build a multi-tenant Visitor Management SaaS in **Laravel + Livewire + Alpine.js**. The system must utilize an MCP architecture and an API-first design to support a future Flutter mobile application. Businesses (tenants/sub-tenants) use this system to track visitors, schedule meetings, and manage facility logistics.
**Multi-Agent Workflow:**
Divide your reasoning and code output among the following three sub-agents. Label the output of each agent clearly.

### Packages to use for laravel
Use bellow packages and any other useful package that can improve KiteFlow Application
- Laravel 12
- Livewire 
- Alpine.js
- Laravel Cashier
- Laravel Spatie for permissions on Super Admin, Admin, Tenants, Users and other parts of the application.
- Laravel Boost - https://laravel.com/docs/12.x/boost

### 1. Backend Architect Agent


**Focus:** API design, database schema, deep multi-tenancy, background jobs, and business logic.
**Tasks:**
* **Hierarchy & Multi-Tenancy:** Implement a schema supporting Super Admins, Tenants, Sub-Tenants, and Users. Super Admins manage all entities. When a user is created, assign them a strict role and a specific tenant/sub-tenant.
* **Facility Management:** Create models for Meeting Rooms with amenities. Rooms must be assignable to cross-building locations/office spaces and linked to tenants.
* **Visit Lifecycle & Pre-Registration:** Build endpoints for tenants to schedule visits (date, room/building, purpose, guest name/email, designated for tenant or sub-tenant). Generate predefined codes and QR codes for these reservations.
* **Automated Jobs & Notifications:**     * Implement Email/WhatsApp notification triggers for: pre-registration invites (with QR code), host alerts upon guest arrival, and guest post-check-in emails containing GDPR/NDA details.
* Create an automated end-of-day CRON job to auto-checkout any guests who forgot to manually check out.
* Create a GDPR compliance job that automatically purges tenant visitor data after a customizable threshold (default 6 months).




### 2. Frontend Engineer Agent


**Focus:** API consumption, Kiosk UI, Admin Dashboards.
**Design Constraints:** The UI/UX must be strictly minimal, functional, and modern. **Absolutely no shadows, drop-shadows, or glassmorphism effects.** Use flat design principles.
**Tasks:**
* **Kiosk Mode:** Build a tablet-optimized, locked-down public route for the front desk.
*
* Include options for: Manual Check-in, Predefined Code Entry, or QR Code Scanner.
* During check-in, display the meeting room/building, time, and waiting host. Capture digital signatures for the tenant's specific NDA/Terms.


* **Tenant Admin Dashboard:** Build interfaces for Tenant Admins to manage meeting rooms, upload business logos, edit addresses/contacts, customize NDA/GDPR text, and configure their custom data retention threshold. Include an Analytics view tracking peak hours and visitor frequency.
* **Visitor Profiles:** Implement reusable visitor profiles allowing returning guests a 1-click check-in experience.


### 3. QA Specialist Agent


**Focus:** Application stability, API contract testing, and automated security testing.
**Tasks:**
* Write comprehensive tests (Pest or PHPUnit) for the entire Laravel 12 API.
* Include strict smoke tests for **all** generated routes to ensure 200 OK responses.
* Write specific feature tests for the automatic end-of-day checkout job, the GDPR data deletion job, and strict tenant/sub-tenant data isolation.


**Execution Request:**
Begin by having the Backend Architect outline the database schema and API structure. Then, provide the complete, step-by-step code implementation across all three agents, culminating in the comprehensive test suite from the QA agent.

