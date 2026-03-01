# RBAC & Multi-Tenant Architecture Implementation Plan

Based on the `Specs.md`, the current implementation has a few discrepancies that need to be addressed to perfectly match the defined architecture.

## Identified Issues
1. **Receptionist Scope Bug**: Currently, the `Receptionist` role is grouped with `System Administrator` in the `canManageAllTenants()` method. This gives them access to ALL companies across the entire SaaS, which is incorrect. They should only see their own company and its sub-tenants, identical to the `Company Administrator`.
2. **Receptionist Permissions**: `Receptionist` should not be able to change "Manage Settings". We need to ensure the default RBAC permissions reflect this restriction.
3. **Data Isolation Validations**: Ensure that UI components correctly enforce the scopes defined in `User->getManagedCompanyIds()` and restrict actions appropriately based on the user's role.

## TODO List

### 1. Fix User Model Scopes
- [x] Edit `app/Models/User.php`.
- [x] Update `canManageAllTenants()` to ONLY return true for the `admin` (System Administrator) role.
- [x] Update `getManagedCompanyIds()` so that the `receptionist` role evaluates the same way as the `administrator` role (returning their own company ID + all child company IDs).

### 2. Update Role Permissions (RBAC defaults)
- [x] Verify or update the default `rbac_permissions` matrix (potentially in `DatabaseSeeder.php` or a dedicated migration/command).
- [x] Ensure `administrator` (Company Administrator) has necessary management permissions (including `manage_settings` if applicable to their scope).
- [x] Ensure `receptionist` has the same data management permissions as `administrator` BUT explicitly lacks the `manage_settings` permission.
- [x] Ensure `tenant` (Location Administrator) has management permissions but scoped only to their own company level.
- [x] Ensure `viewer` (Employee) only has basic permissions (e.g., viewing own visitors, scheduling own visits) and lacks user/company management permissions.

### 3. Review & Update Policies / Middleware
- [x] Ensure `abort_if(!auth()->user()->can('manageSettings'), 403);` or equivalent exists on settings controllers/Livewire components.
- [x] Verify that `viewer` role is properly restricted from accessing `UserList` and `CompanyList` by default through the `manageUsers` and `manageCompanies` permissions.

### 4. Update Tests
- [x] Update `tests/Unit/UserTest.php` to reflect that `receptionist` no longer manages all company IDs, but instead manages their own + children.
- [x] Add explicit tests ensuring `receptionist` does not have `manage_settings` permission.
- [x] Run the test suite to ensure no other features break from the `canManageAllTenants` scope change.

### 5. Frontend & UI Fixes
- [x] Fix Building Status Toggle (wrapped Save Changes button in the `<form>` tag).
- [x] Update Building details view to allow Location Administrators (Tenants) to load the page in read-only mode.
- [x] Restrict Role selection for Location Administrators to only allow assigning `tenant` or `viewer` roles.
- [x] Update `Specs.md` to reflect all technical implementation details and security constraints.
