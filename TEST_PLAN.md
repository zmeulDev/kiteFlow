# KiteFlow - Test Plan

## 1. Testing Objectives
Validate that KiteFlow visitor management system works according to business logic requirements for all user roles:
- Super-Admin
- Tenant Admin
- Sub-tenant Admin  
- User (Tenant/Sub-tenant)
- Visitor

## 2. Testing Scope

### Features to Test
1. **Authentication** - Login, logout, 2FA (if enabled)
2. **Dashboard** - Stats display correctly
3. **Visitor Management** - CRUD operations, check-in/out, blacklist
4. **Meeting Management** - CRUD, room booking, code generation
5. **Meeting Rooms** - CRUD, amenities, availability
6. **Buildings & Access Points** - CRUD
7. **Super-Admin Features**:
   - Tenants management (CRUD, sub-tenants, contracts)
   - User management with RBAC
   - Audit logs
   - Kiosk settings
   - System settings
   - Integrations
   - Billing
   - Support tickets

## 3. Test Cases

### TC-001: Login Flow
- [ ] Navigate to login page
- [ ] Enter valid credentials
- [ ] Verify successful login and redirect to dashboard

### TC-002: Super-Admin - Tenant Management
- [ ] Navigate to Settings > Tenants
- [ ] Create new tenant with all fields
- [ ] Edit existing tenant
- [ ] View tenant details (users, sub-tenants, contract)
- [ ] Add/edit/delete sub-tenants

### TC-003: Super-Admin - User Management
- [ ] Navigate to Settings > Users
- [ ] Create new user with role assignment
- [ ] Edit user details and roles
- [ ] Deactivate user

### TC-004: Visitor Check-in (Kiosk Flow)
- [ ] Navigate to visitor list
- [ ] Click "Add Visitor"
- [ ] Fill required fields
- [ ] Submit and verify visitor created

### TC-005: Visitor Check-in with Code
- [ ] Create meeting with visitor
- [ ] Get check-in code
- [ ] Use code to check in visitor

### TC-006: Meeting Room Booking
- [ ] Navigate to Meetings > Create
- [ ] Select tenant/company
- [ ] Select host
- [ ] Choose time slot
- [ ] Verify available rooms shown
- [ ] Create meeting

### TC-007: Building & Access Points
- [ ] Create building
- [ ] Add access points to building
- [ ] Verify location appears in meeting room dropdown

### TC-008: Kiosk Settings
- [ ] Navigate to Settings > Kiosk
- [ ] Configure required fields
- [ ] Enable/disable GDPR consent
- [ ] Save settings

### TC-009: Audit Logs
- [ ] Navigate to Settings > Audit Logs
- [ ] Verify logs display
- [ ] Test filtering by action type
- [ ] Test search functionality

## 4. Test Environment
- URL: http://localhost:8000
- Browser: Chrome (headless)
- Test User: admin@kiteflow.com / password

## 5. Execution Results

| TC ID | Test Case | Status | Notes |
|-------|-----------|--------|-------|
| TC-001 | Login Flow | - | - |
| TC-002 | Tenant Management | - | - |
| TC-003 | User Management | - | - |
| TC-004 | Visitor Check-in | - | - |
| TC-005 | Meeting Booking | - | - |
| TC-006 | Kiosk Settings | - | - |
| TC-007 | Audit Logs | - | - |

## 6. Bug Report

| # | Description | Severity | Status |
|---|-------------|----------|--------|
| 1 | Sidebar layout issues | High | Fixed |
