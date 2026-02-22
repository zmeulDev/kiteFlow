# KiteFlow - Enterprise Visitor Management SaaS

A production-ready, multi-tenant visitor management platform built with Laravel 12, Livewire 3, Alpine.js, and MySQL.

## 🚀 Features

### Core Features
- **Multi-tenant Architecture** - Support for businesses, sub-tenants, and complete data isolation
- **Visitor Management** - Track visitors from check-in to check-out with detailed logs
- **Meeting Scheduling** - Book rooms, schedule meetings, invite attendees
- **Self-Service Kiosk Mode** - Touchless check-in with badge printing
- **Facility Management** - Buildings, zones, access points, parking
- **Role-based Access Control** - Super Admin, Admin, Receptionist, User roles

### Technical Features
- **API-First Design** - RESTful API ready for Flutter/React Native apps
- **Laravel Sanctum** - Token-based API authentication
- **Spatie Permission** - Comprehensive RBAC system
- **Livewire 3** - Dynamic, reactive frontend components
- **Alpine.js** - Lightweight JavaScript interactions
- **Full Type-Hinting** - Production-ready, zero placeholder code

## 📋 Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ & NPM

## 🛠 Installation

```bash
# 1. Clone and install dependencies
cd kiteflow
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Update .env with your database credentials
# DB_DATABASE=kiteflow
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# 4. Create MySQL database
mysql -u root -p -e "CREATE DATABASE kiteflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations and seeders
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder

# 6. Create storage link
php artisan storage:link

# 7. Build frontend assets
npm run build

# 8. Start development server
php artisan serve
```

## 📁 Project Structure

```
kiteflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # API Controllers
│   │   ├── Requests/           # Form Requests
│   │   └── Resources/          # API Resources
│   ├── Livewire/               # Livewire Components
│   │   ├── Dashboard.php
│   │   ├── Kiosk/
│   │   ├── Visitors/
│   │   └── Meetings/
│   └── Models/                 # Eloquent Models
│       ├── User.php
│       ├── Tenant.php
│       ├── Visitor.php
│       ├── VisitorVisit.php
│       ├── Meeting.php
│       ├── MeetingRoom.php
│       ├── Building.php
│       ├── AccessPoint.php
│       └── ...
├── database/
│   ├── migrations/             # Database Schema
│   └── seeders/
│       └── RolesAndPermissionsSeeder.php
├── routes/
│   ├── api.php                 # API Routes
│   └── web.php                 # Web Routes
└── resources/views/
    ├── layouts/                # Blade Layouts
    ├── livewire/               # Livewire Views
    └── auth/                   # Auth Views
```

## 🔐 User Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full system access, manage all tenants |
| **Admin** | Full tenant access, manage users & settings |
| **Receptionist** | Check-in/out visitors, manage kiosks |
| **User** | View visitors, create meetings |

## 📡 API Endpoints

### Authentication
```
POST /api/auth/login          # Login
POST /api/auth/register       # Register
POST /api/auth/logout         # Logout
GET  /api/auth/user           # Current user
```

### Tenants
```
GET    /api/admin/tenants           # List tenants
POST   /api/admin/tenants           # Create tenant
GET    /api/tenants/{slug}          # Get tenant
PUT    /api/tenants/{slug}          # Update tenant
DELETE /api/tenants/{slug}          # Delete tenant
```

### Visitors
```
GET    /api/tenants/{slug}/visitors           # List visitors
POST   /api/tenants/{slug}/visitors           # Create visitor
POST   /api/tenants/{slug}/visitors/check-in  # Check-in visitor
POST   /api/tenants/{slug}/visitors/check-out/{visit}  # Check-out
```

### Kiosk Mode
```
POST /api/kiosk/{tenant}/{accessPoint}/check-in
POST /api/kiosk/{tenant}/{accessPoint}/check-out
GET  /api/kiosk/{tenant}/{accessPoint}/lookup
GET  /api/kiosk/{tenant}/{accessPoint}/hosts
```

## 🔧 Configuration

### Multi-Tenant Setup

Each tenant gets:
- Unique slug for URLs (e.g., `acme.kiteflow.test`)
- Isolated data and users
- Custom branding and settings

### Kiosk Setup

1. Create an Access Point with `is_kiosk_mode = true`
2. Navigate to `/kiosk/{tenant-slug}/{access-point-uuid}`
3. Visitors can self-check-in

## 🧪 Testing

```bash
php artisan test
```

## 📝 TODO / Roadmap

- [ ] Laravel Cashier integration for subscriptions
- [ ] Email/SMS notifications
- [ ] Badge printing support
- [ ] QR code check-in
- [ ] Flutter mobile app
- [ ] WebSocket real-time updates
- [ ] Advanced analytics dashboard
- [ ] LDAP/SSO integration
- [ ] Multi-language support

## 📄 License

MIT License

---

Built with ❤️ using Laravel 12