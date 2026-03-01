Application Arhitecture

Name: Chio
Type: SaaS application
Tech stack: Laravel 12, Livewire 4, Alpine.js 3, Tailwind CSS 4

Description: Chio is a visitor management system that helps companies manage their visitors.

Architecture:
- System Administrator
- Company Administrator
- Receptionist
- Location Administrator
- Employee


Real world example based on a coworking space:

I am Fabian, owner of the SaaS application, i have System Administrator role and i manage / own the application.

Stables - is my client, they have a company, they have a location, they have a reception desk, they have employees, they have visitors.
    - Role: Company Administrator
    - They can add / remove / edit / view / delete / update / etc. their own company, locations, reception desk, employees, visitors.
    - They can have sub-tenants that use same builidng, same rooms, same reception. Stables has access to all of there sub-tenants data and can manage them.

    -- Stables Reception Desk
    Role: Receptionist
    Help Company Administrator with managing the app but cannot change Manage Settings. 
    They can add / remove / edit / view / delete / update / etc. their own company, locations, reception desk, employees, visitors and sub-tenants data

    -- Stables Employee
        Role: Employee
        They can view their own data, visitors, schedule visits for themselves, 

    --- Computa Center
        Role: Location Administrator
        They manage data for there own company, users and visitors tied to there location. 
        They can add / remove / edit / view / delete / update / etc. their own company, users and visitors tied to there location. 

    --- Computa Center Employee
        Role: Employee
        They can view their own data, visitors, schedule visits for themselves, 

The Office - is my client, they have a company, they have a location, they have a reception desk, they have employees, they have visitors.
    - Role: Company Administrator
    - They can add / remove / edit / view / delete / update / etc. their own company, locations, reception desk, employees, visitors.
    - They can have multiple receptions desks set as kiosks or as traditional reception desks.
    - They can have sub-tenants that use same builidng, same rooms, same reception. The Office has access to all of there sub-tenants data and can manage them.

    -- The Office Reception Desk
        Role: Receptionist  
        Help Company Administrator with managing the app but cannot change Manage Settings. 
        They can add / remove / edit / view / delete / update / etc. their own company, locations, reception desk, employees, visitors and sub-tenants data

    -- The Office Employees
        Role: Employee
        They can view their own data, visitors, schedule visits for themselves, 

    --- Yonder
        Role: Location Administrator
        They manage data for there own company, users and visitors tied to there location. 
        They can add / remove / edit / view / delete / update / etc. their own company, users and visitors tied to there location. 

    -- Yonder Employee
        Role: Employee
        They can view their own data, visitors, schedule visits for themselves, 

## Technical Implementation Details

### Data Scoping
- **Global Managers (System Administrator)**: Access to all companies, buildings, users, and global settings.
- **Tenant Managers (Company Administrator, Receptionist)**: Access to their own company and all descendants recursively.
- **Individual Tenants (Location Administrator, Employee)**: Access only to their own company.

### Role & Permission Summary
| Role | Backend Key | Internal Label | Managed Scope | Data Visibility | Can Assign Roles |
| :--- | :--- | :--- | :--- | :--- | :--- |
| System Administrator | `admin` | God Mode | Global | All Data | All Roles |
| Company Administrator | `administrator` | Company Admin | Company + Sub-tenants | Scoped | All except `admin` |
| Receptionist | `receptionist` | Receptionist | Company + Sub-tenants | Scoped (No Settings) | All except `admin` |
| Location Administrator | `tenant` | Location Admin | Own Company Only | Scoped | `tenant`, `viewer` |
| Employee | `viewer` | Employee | Own Data | Private + Company Info | None |

### Security Constraints
- **Role Assignment**: Users can only assign roles that are within their hierarchy (e.g., a Location Administrator can only create other Location Administrators or Employees).
- **Settings Access**: Only `admin` can access global business settings (Business Details, GDPR, NDA).
- **Visit Scheduling**: 
    - `admin` can schedule for anyone.
    - `administrator` / `receptionist` can schedule for their company or sub-tenants.
    - `viewer` is forced to be the host of their own scheduled visits.
- **Form Integrity**: Submit buttons must be within the `<form>` tag in all admin views to ensure data persistence (e.g., Building Status Toggle fix).
