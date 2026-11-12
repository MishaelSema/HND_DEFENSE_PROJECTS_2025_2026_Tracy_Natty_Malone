# ALLO_SERVICE - Quick Start Guide

## What is ALLO_SERVICE?
A marketplace connecting people who need help (clients) with people who provide services (providers like plumbers, electricians, hairdressers).

---

## Quick Navigation

| I Want To... | Go To |
|--------------|-------|
| Visit Website | `/index.php` |
| Browse Services | `/all_services.php` |
| Contact Support | `/contact.php` |
| Client Login | `/client/client_login.php` |
| Provider Login | `/provider/provider_login.php` |
| Admin Login | `/admin/admin_login.php` |

---

## Default Accounts

### Admin Account
- **URL:** `/admin/admin_login.php`
- **Email:** `admin@alloservice.com`
- **Password:** `admin123`
- **Can:** Manage users, categories, contacts, reviews

### Test Accounts (Create via signup)
- **Clients:** Register at `/client/client_signup.php`
- **Providers:** Register at `/provider/provider_signup.php` (requires approval)

---

## Page Reference

### Public Pages
| Page | Purpose |
|------|---------|
| `index.php` | Homepage with hero, features, services |
| `all_services.php` | Browse/search all providers |
| `search.php` | Browse by service category |
| `profile.php?id=X` | View provider profile |
| `contact.php` | Contact form for support |
| `about.php` | About the platform |
| `choose_role.php` | Register as client or provider |

### Client Pages
| Page | Purpose |
|------|---------|
| `client/client_login.php` | Client login |
| `client/client_signup.php` | Client registration |
| `client/client_dashboard.php` | Client dashboard |

### Provider Pages
| Page | Purpose |
|------|---------|
| `provider/provider_login.php` | Provider login |
| `provider/provider_signup.php` | Provider registration |
| `provider/provider_profile.php` | View/edit profile |
| `provider/provider_profile_edit.php` | Edit profile |
| `provider/provider_portfolio_upload.php` | Upload work photos |
| `provider/provider_reviews.php` | View received reviews |

### Admin Pages
| Page | Purpose |
|------|---------|
| `admin/admin_login.php` | Admin login |
| `admin/admin_dashboard.php` | Stats overview |
| `admin/users.php` | Manage all users |
| `admin/admin_categories.php` | Manage services |
| `admin/contacts.php` | View contact messages |
| `admin/admin_reviews.php` | Manage reviews |

---

## Common Tasks

### Add a New Service Category
1. Login to Admin
2. Go to Categories
3. Click "Add New"
4. Enter: Name, Slug, Description
5. Upload icon image
6. Save

### Approve a Provider
1. Login to Admin
2. Go to Users
3. Find provider with "Pending" status
4. Click "Approve" button
5. Optionally add a message

### Reset Password
1. Go to login page
2. Click "Forgot Password"
3. Enter email and select user type
4. Check email for reset link
5. Link expires in 1 hour

---

## Security Features
- ✅ CSRF Protection on all forms
- ✅ Rate Limiting (5 login attempts)
- ✅ Input Validation
- ✅ Password Hashing (Bcrypt)
- ✅ XSS Prevention
- ✅ SQL Injection Prevention
- ✅ CAPTCHA on contact form

---

## File Structure
```
/
├── index.php, all_services.php, profile.php
├── contact.php, about.php, search.php
├── client/          ← Client files
├── provider/        ← Provider files
├── admin/           ← Admin files
├── includes/        ← Shared code
│   ├── db.php       ← Database connection
│   ├── auth_check.php ← Session management
│   ├── csrf.php     ← Security tokens
│   ├── ratelimit.php ← Login protection
│   └── captcha.php  ← Contact form CAPTCHA
├── assets/
│   ├── css/         ← Stylesheets
│   ├── img/         ← Images & icons
│   └── uploads/    ← User uploads
└── setup.sql        ← Database setup
```

---

## Support
For help: `/contact.php`
