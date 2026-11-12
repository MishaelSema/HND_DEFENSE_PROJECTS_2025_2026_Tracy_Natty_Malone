# ALLO_SERVICE - Complete Project Documentation

> **Version:** 1.0.0  
> **Last Updated:** March 2026  
> **Status:** Production Ready

---

## Table of Contents

1. [What is ALLO_SERVICE?](#what-is-allo_service)
2. [The Problem We're Solving](#the-problem-were-solving)
3. [Tech Stack](#tech-stack)
4. [User Roles & Permissions](#user-roles--permissions)
5. [How Each Page Works](#how-each-page-works)
6. [Database Schema](#database-schema)
7. [Security Features](#security-features)
8. [Key Features](#key-features)
9. [Setup Instructions](#setup-instructions)
10. [Workflows](#workflows)
11. [Future Improvements](#future-improvements)
12. [FAQ](#faq)

---

## What is ALLO_SERVICE?

**ALLO_SERVICE** is a **multi-sided online marketplace** that connects people who need help with everyday services to local professionals who can provide those services. Think of it like a "Uber for home services" - but specifically designed for the Cameroon market.

### Simple Explanation:
Imagine you need your plumbing fixed, your house painted, or your hair styled. Instead of:
- Asking friends for recommendations
- Walking around looking for workers
- Worrying if they're reliable
- Negotiating prices blindly

**You go to ALLO_SERVICE, find verified professionals near you, see their ratings and reviews, and contact them directly.**

### For Professionals:
If you're a plumber, electrician, hairdresser, or any skilled worker, ALLO_SERVICE gives you:
- A platform to showcase your skills
- Access to customers actively looking for your services
- A way to build your reputation through reviews
- A chance to grow your business

---

## The Problem We're Solving

### For Customers:
| Problem | Solution |
|---------|----------|
| Don't know where to find reliable workers | Verified professionals with reviews |
| No way to compare prices | Transparent profiles with service info |
| Worried about quality | Rating system and reviews |
| Hard to contact providers | Direct WhatsApp integration |
| Language barriers | Bilingual platform (English/French) |

### For Professionals:
| Problem | Solution |
|---------|----------|
| Hard to find customers | Platform visibility to all users |
| No way to showcase work | Portfolio/posts feature |
| Building reputation takes time | Reviews from real clients |
| Unfair pricing competition | Professional profile with credentials |

---

## Tech Stack

### What Makes Up the Website:

| Component | Technology | Why It Was Chosen |
|-----------|------------|------------------|
| **Backend** | PHP 7.4+ | Reliable, widely supported, easy to deploy |
| **Database** | MySQL | Free, powerful, perfect for structured data |
| **Frontend** | HTML5, CSS3, JavaScript | Standard web technologies |
| **Styling** | Custom CSS | No framework dependencies |
| **Icons** | Font Awesome | Professional icons |
| **Charts** | Chart.js | Beautiful data visualization |
| **Server** | Apache (WAMP/XAMPP/LAMP) | Easy local development |
| **Security** | Custom PHP modules | CSRF, Rate limiting, Validation |

### How It Works Together:

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   User Browser  │ ←→  │   Apache Server │ ←→  │     MySQL       │
│   (HTML/CSS/JS)│     │   (PHP Logic)   │     │   (Database)    │
└─────────────────┘     └─────────────────┘     └─────────────────┘
        ↑                        ↑                        ↑
        └────────────────────────┴────────────────────────┘
                         HTTP/HTTPS
```

---

## User Roles & Permissions

ALLO_SERVICE has **3 main user roles**, each with different access levels:

---

### 1. **CLIENT (Customer)**

**Who they are:** Regular people looking for services.

**What they can do:**

| Feature | Access Level |
|---------|-------------|
| Browse all services | ✅ Yes |
| View provider profiles | ✅ Yes |
| Search for professionals | ✅ Yes |
| Contact providers (WhatsApp) | ✅ Yes |
| Write reviews | ✅ Yes (must be logged in) |
| Contact support | ✅ Yes |
| Book services | ❌ Not yet implemented |
| Manage providers | ❌ No |
| Access admin panel | ❌ No |

**How to become one:**
1. Go to the website
2. Click "Get Started"
3. Select "Client"
4. Fill out signup form
5. Login to your dashboard

---

### 2. **PROVIDER (Service Professional)**

**Who they are:** Plumbers, electricians, hairdressers, etc.

**What they can do:**

| Feature | Access Level |
|---------|-------------|
| Create professional profile | ✅ Yes |
| Edit profile information | ✅ Yes |
| Upload profile photo | ✅ Yes |
| Upload portfolio/work photos | ✅ Yes |
| View their reviews | ✅ Yes |
| Receive notifications | ✅ Yes |
| Get listed in marketplace | ✅ Yes (after approval) |
| Approve/suspend users | ❌ No |
| Access admin panel | ❌ No |

**Approval Process:**
1. Provider signs up → Status: **PENDING**
2. Admin reviews application
3. Admin **APPROVES** → Status: **APPROVED** → Listed in marketplace
4. OR Admin **SUSPENDS** → Status: **SUSPENDED** → Not listed

**How to become one:**
1. Go to the website
2. Click "Get Started"
3. Select "Service Provider"
4. Fill out registration form
5. Wait for admin approval
6. Login after approval

---

### 3. **ADMIN (Platform Manager)**

**Who they are:** The ALLO_SERVICE team managing the platform.

**What they can do:**

| Feature | Access Level |
|---------|-------------|
| View dashboard with statistics | ✅ Yes |
| Manage all users (clients & providers) | ✅ Yes |
| Approve/suspend providers | ✅ Yes |
| Add providers manually | ✅ Yes |
| Delete user accounts | ✅ Yes |
| View contact messages | ✅ Yes |
| Reply to contacts | ✅ Yes |
| Mark contacts as read | ✅ Yes |
| Manage service categories | ✅ Yes |
| View all reviews | ✅ Yes |
| Delete inappropriate reviews | ✅ Yes |

**Default Login:**
- Email: `admin@alloservice.com`
- Password: `admin123` (should be changed immediately)

---

## How Each Page Works

### Public Pages (No Login Required)

---

#### **index.php** - Homepage

**What you see:**
- Hero section with main headline
- "Why Choose Us" benefits section
- "How It Works" 4-step process
- Service category cards
- "Become a Provider" promotional section
- Footer with links

**What it does:**
- Showcases the platform to visitors
- Displays all available service categories
- Links to browse all services
- Directs new users to register

**User Flow:**
```
Visitor arrives → Sees homepage → Can browse services OR register
```

---

#### **all_services.php** - Service Marketplace

**What you see:**
- Search bar at the top
- Category filter pills
- Grid of provider cards
- Each card shows: Photo, Name, Service, Location, Rating, Availability
- Portfolio posts feed at the bottom

**What it does:**
- Lists all APPROVED providers
- Allows filtering by category
- Shows real-time ratings and reviews
- Links to individual provider profiles

**Filters Available:**
- Single category (`?cat=plumbing`)
- Multiple categories (`?cats=plumbing,cleaning`)
- Text search (search bar on page)

---

#### **search.php** - Category Browser

**What you see:**
- Grid of service categories
- Each category shows job type and number of professionals

**What it does:**
- Organized view of all services
- Clicking a category searches for providers in that field
- Shows results in a list with WhatsApp contact buttons

---

#### **profile.php** - Public Provider Profile

**What you see:**
- Provider's photo and name
- Service type and location
- About section
- Skills/competencies
- Availability status
- Reviews section
- Contact/WhatsApp button
- Portfolio posts

**What it does:**
- Gives detailed info about a provider
- Shows their work through portfolio
- Displays client reviews and ratings
- Allows direct contact via WhatsApp

---

#### **contact.php** - Contact Form

**What you see:**
- Contact form with fields: Name, Email, Subject, Message
- Math CAPTCHA challenge (security)
- Success/error messages

**What it does:**
- Allows visitors to send messages to the admin team
- Validates all input
- Prevents spam with CAPTCHA
- Stores message in database
- Sends email notification to admin

---

#### **about.php** - About Page

**What you see:**
- Information about the platform
- Mission and values
- Team/company info

**What it does:**
- Builds trust with potential users
- Explains the platform's purpose

---

#### **choose_role.php** - Registration Hub

**What you see:**
- Two big buttons: "I'm a Client" and "I'm a Provider"
- Brief description of each role

**What it does:**
- Directs users to the correct registration form
- Prevents confusion about which form to fill

---

#### **forgot_password.php** - Password Recovery

**What you see:**
- Form asking for email and user type
- Success message after submission

**What it does:**
- Generates a secure reset link
- Sends email with instructions
- Link expires after 1 hour

---

### Client Pages (Login Required)

---

#### **client/client_login.php** - Client Login

**What you see:**
- Email and password form
- "Forgot Password" link
- Link to signup

**Security Features:**
- Rate limiting (5 attempts, then 15-minute lockout)
- CSRF token protection
- Session regeneration on success

---

#### **client/client_signup.php** - Client Registration

**What you see:**
- Registration form: Name, Email, Password
- Password strength requirements displayed

**What it does:**
- Creates new client account
- Validates email uniqueness
- Requires strong passwords (8+ chars, mixed case, number)

---

#### **client/client_dashboard.php** - Client Dashboard

**What you see:**
- Welcome message with client name
- Recent activity
- Quick links to services

**Note:** This is a simplified dashboard - future versions may include booking history, saved providers, etc.

---

### Provider Pages (Login Required)

---

#### **provider/provider_login.php** - Provider Login

**What you see:**
- Email and password form
- Pending approval message (if account is pending)

**Security Features:**
- Same as client login (rate limiting, CSRF)

---

#### **provider/provider_signup.php** - Provider Registration

**What you see:**
- Comprehensive registration form:
  - Full Name
  - Email
  - Phone Number
  - Service/Job Category (dropdown)
  - Profile Photo (upload)
  - About Services (textarea)
  - Location
  - Availability (Full-time/Part-time/On Demand)
  - Password

**What it does:**
- Creates provider account with PENDING status
- Uploads profile photo
- Validates phone number format
- Sends to admin for approval

---

#### **provider/provider_profile.php** - Provider Dashboard

**What you see:**
- Profile overview
- Stats (views, reviews, rating)
- Recent reviews
- Portfolio posts
- Edit profile button

**What providers can do:**
- View their public profile
- See all received reviews
- Upload portfolio images
- Edit their information

---

#### **provider/provider_profile_edit.php** - Edit Profile

**What you see:**
- Form pre-filled with current data
- Fields for all profile information

**What it does:**
- Allows providers to update their information
- Changes go live immediately (if approved)

---

#### **provider/provider_portfolio_upload.php** - Portfolio Upload

**What you see:**
- File upload interface
- Caption input
- Gallery of existing posts

**What it does:**
- Allows providers to showcase their work
- Multiple images per post supported
- Posts appear on their public profile

---

#### **provider/provider_ajax.php** - Backend API

**What it does:**
- Handles AJAX requests from provider pages
- Process portfolio uploads
- Handle notifications

---

#### **provider/provider_notifications.php** - Notifications

**What you see:**
- List of notifications
- Mark as read functionality

**What it does:**
- Shows important alerts
- Keeps providers updated

---

#### **provider/provider_reviews.php** - Reviews View

**What you see:**
- All reviews received
- Rating breakdown
- Individual review cards

---

### Admin Pages (Admin Login Required)

---

#### **admin/admin_login.php** - Admin Login

**What you see:**
- Simple email/password form
- Link back to main site

**Security:**
- Same protections as other logins
- Separate rate limiting

---

#### **admin/admin_dashboard.php** - Main Dashboard

**What you see:**
- Statistics cards:
  - Total Professionals
  - Pending Approvals
  - New Inquiries
  - Customer Reviews
  - Average Rating
- Bar chart showing user distribution

**What it does:**
- Overview of platform health
- Quick view of pending items
- User growth visualization

---

#### **admin/users.php** - User Management

**What you see:**
- "Add New Professional" form
- Tabbed/filtered view of:
  - All providers (with status badges)
  - All clients

**Provider Actions:**
- Approve (green button)
- Suspend (yellow button)
- Remove (red button)
- Modal for providing reason

**Client Actions:**
- Remove account

**What it does:**
- Complete user lifecycle management
- Sends email notifications for actions
- Token-protected delete actions

---

#### **admin/admin_categories.php** - Category Management

**What you see:**
- List of service categories
- Add new category form
- Edit/Delete options

**What it does:**
- CRUD operations on service types
- Categories appear in provider signup and filters

---

#### **admin/admin_edit_category.php** - Edit Category

**What you see:**
- Form to edit category details:
  - Name
  - Slug (URL-friendly version)
  - Description
  - Image

---

#### **admin/admin_edit_provider.php** - Edit Provider

**What you see:**
- Full provider profile editor
- All fields from signup form
- Status dropdown

**What admins can do:**
- Edit any provider information
- Change approval status
- Update all details

---

#### **admin/contacts.php** - Contact Messages

**What you see:**
- Table of all contact submissions
- Columns: Date, Sender, Subject, Message, Status, Actions

**Actions:**
- Reply (opens modal)
- Mark as Read
- Delete
- Status badges (NEW, READ, REPLIED)

---

#### **admin/admin_reviews.php** - Review Management

**What you see:**
- All reviews on the platform
- Associated provider and client
- Rating and content
- Date

**Actions:**
- Delete inappropriate reviews

---

#### **admin/admin_reports.php** - Analytics

**What you see:**
- Various statistics
- Charts and graphs
- User activity data

---

### Utility Pages

---

#### **save_review.php** - Review Submission (API)

**What it does:**
- AJAX endpoint for submitting reviews
- Validates:
  - User is logged in as client
  - Rating is 1-5
  - Comment length is reasonable
- Stores in database

---

#### **logout.php** - Logout Handler

**What it does:**
- Destroys session
- Redirects to homepage

---

#### **404.php** - Page Not Found

**What it does:**
- Displays when a URL doesn't exist
- Friendly error message
- Link back to homepage

---

#### **500.php** - Server Error

**What it does:**
- Displays when something breaks
- Technical error logging
- User-friendly message

---

## Database Schema

The database stores all information about users, services, and interactions.

### Tables Overview:

```
┌─────────────────────┐
│     clients         │  ← Regular users seeking services
├─────────────────────┤
│   prestataires      │  ← Service providers
├─────────────────────┤
│     admin           │  ← Platform administrators
├─────────────────────┤
│     services        │  ← Service categories
├─────────────────────┤
│     contacts        │  ← Contact form messages
├─────────────────────┤
│     reviews         │  ← Client reviews for providers
├─────────────────────┤
│  signalements      │  ← User reports/block requests
├─────────────────────┤
│  provider_posts    │  ← Provider portfolio posts
├─────────────────────┤
│   post_images       │  ← Images for portfolio posts
├─────────────────────┤
│ password_reset_tokens│  ← Secure password reset links
└─────────────────────┘
```

### Table Details:

#### **clients**
| Field | Type | Description |
|-------|------|-------------|
| Id_client | INT (PK) | Unique identifier |
| NOM | VARCHAR | Client's full name |
| Email | VARCHAR | Unique email address |
| Mot_de_passe | VARCHAR | Hashed password |
| created_at | TIMESTAMP | Registration date |

#### **prestataires (Providers)**
| Field | Type | Description |
|-------|------|-------------|
| Id_prestataire | INT (PK) | Unique identifier |
| nom | VARCHAR | Full name |
| email | VARCHAR | Unique email |
| mot_de_passe | VARCHAR | Hashed password |
| telephone | VARCHAR | Phone number |
| metier | VARCHAR | Service type |
| profile | VARCHAR | Photo path |
| description | TEXT | About the provider |
| competence | TEXT | Skills list |
| localisation | VARCHAR | Location |
| disponibilite | VARCHAR | Availability |
| status | ENUM | pending/approved/suspended |
| created_at | TIMESTAMP | Registration date |

#### **services**
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK) | Unique identifier |
| name | VARCHAR | Service name |
| slug | VARCHAR | URL-friendly name |
| description | TEXT | Service description |
| image_path | VARCHAR | Icon/photo path |

#### **reviews**
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK) | Unique identifier |
| id_client | INT (FK) | Who wrote it |
| id_prestataire | INT (FK) | Who it's for |
| note | INT | Rating (1-5) |
| contenu | TEXT | Review text |
| date_commentaire | TIMESTAMP | When written |

#### **contacts**
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK) | Unique identifier |
| name | VARCHAR | Sender's name |
| email | VARCHAR | Sender's email |
| subject | VARCHAR | Message subject |
| message | TEXT | Message content |
| status | ENUM | new/read/replied |
| created_at | TIMESTAMP | When sent |

---

## Security Features

ALLO_SERVICE implements multiple layers of security:

### 1. **CSRF Protection**
- Every form has a unique token
- Tokens expire after use
- Prevents unauthorized form submissions

### 2. **Rate Limiting**
- Login forms: 5 attempts maximum
- After 5 failed attempts: 15-minute lockout
- Prevents brute-force attacks

### 3. **Input Validation**
- Email format validation
- Phone number format validation (Cameroon format)
- Password strength requirements
- SQL injection prevention via prepared statements

### 4. **XSS Prevention**
- All user input is sanitized
- HTML special characters are escaped
- Prevents malicious script injection

### 5. **Session Security**
- Session regeneration on login
- HTTP-only cookies configuration
- Cache control headers
- Automatic session timeout

### 6. **File Upload Security**
- File type validation (images only)
- Size limits (5MB max)
- Random filename generation
- Extension validation

### 7. **Password Security**
- Bcrypt hashing (industry standard)
- No plain-text passwords ever stored
- Password reset tokens are single-use

### 8. **Error Handling**
- Custom error pages (404, 500)
- No technical details shown to users
- Errors logged to file

---

## Key Features

### For Users (Clients):

1. **Service Discovery**
   - Browse by category
   - Search functionality
   - Filter by location
   - View ratings and reviews

2. **Provider Profiles**
   - Professional photos
   - Service descriptions
   - Skills and competencies
   - Portfolio of work
   - Availability status

3. **Direct Contact**
   - WhatsApp integration
   - One-click calling
   - Email support

4. **Reviews & Ratings**
   - 5-star rating system
   - Written reviews
   - Real-time updates

### For Service Providers:

1. **Professional Profile**
   - Customizable information
   - Photo gallery
   - Service descriptions

2. **Portfolio System**
   - Upload work photos
   - Add captions
   - Organize by date

3. **Review Management**
   - View all reviews
   - Response to feedback
   - Build reputation

4. **Approval System**
   - Admin verification
   - Trust badge
   - Quality assurance

### For Administrators:

1. **Dashboard**
   - Real-time statistics
   - Visual charts
   - Quick actions

2. **User Management**
   - View all users
   - Approve/suspend providers
   - Delete accounts

3. **Content Management**
   - Add/edit service categories
   - Manage reviews
   - Handle contacts

4. **Analytics**
   - User growth tracking
   - Popular services
   - Engagement metrics

---

## Setup Instructions

### Prerequisites:

- **Web Server:** Apache (with mod_rewrite) or WAMP/XAMPP
- **PHP Version:** 7.4 or higher
- **MySQL Version:** 5.7 or higher

### Step 1: Clone/Download Files

```bash
git clone <repository-url>
cd Allo_Service.com
```

### Step 2: Setup Database

1. Open phpMyAdmin or MySQL CLI
2. Create database:
```sql
CREATE DATABASE allo_service_database;
```

3. Import the schema:
```bash
mysql -u root -p allo_service_database < setup.sql
```

### Step 3: Configure Connection

Edit `includes/db.php`:
```php
$host = "localhost";
$user = "root";
$pass = "";           // Your MySQL password
$db   = "allo_service_database";
```

### Step 4: Configure Email (Optional)

Edit `includes/mailer.php` with your SMTP settings for password reset and contact form emails to work.

### Step 5: Set Permissions

```bash
chmod 755 -R .          # Linux/Mac
# Windows: Right-click → Properties → Uncheck Read-only
```

### Step 6: Access the Site

- Website: `http://localhost/Allo_Service.com/`
- Admin Panel: `http://localhost/Allo_Service.com/admin/`

### Step 7: Login to Admin

- URL: `http://localhost/Allo_Service.com/admin/admin_login.php`
- Email: `admin@alloservice.com`
- Password: `admin123`

**⚠️ IMPORTANT:** Change the admin password immediately!

---

## Workflows

### User Registration Flow:

```
CLIENT SIGNUP                    PROVIDER SIGNUP
      │                               │
      ▼                               ▼
  Fill Form                      Fill Form (more info)
      │                               │
      ▼                               ▼
 Email Check                  Email Check
      │                               │
      ▼                               ▼
Account Created              Status: PENDING
      │                               │
      ▼                               ▼
Login Enabled               Email to Admin
      │                               │
      ▼                               ▼
Redirect to Dashboard      Admin Reviews Application
                                    │
                        ┌───────────┴───────────┐
                        ▼                       ▼
                   APPROVED                REJECTED
                        │                       │
                        ▼                       ▼
                Status: APPROVED        Email Sent
                        │              (with reason)
                        ▼
                Visible in Marketplace
```

### Contact Form Flow:

```
User Fills Form
       │
       ▼
  CAPTCHA Check
       │
  ┌────┴────┐
  │         │
Fail      Pass
  │         │
  │         ▼
  │    Validate Input
  │         │
  │    ┌────┴────┐
  │    │         │
  │   Fail     Pass
  │    │         │
  │    │         ▼
  │    │    Save to Database
  │    │         │
  │    │         ▼
  │    │    Send Email to Admin
  │    │         │
  │    │    ┌────┴────┐
  │    │    │         │
  │    │   Success  Fail
  │    │    │         │
  │    │    ▼         ▼
  │    │ Success   Success
  │    │ Message   (saved)
  │    │         │
  ▼    ▼         ▼
Error  ────────►│
```

### Review Submission Flow:

```
Client Views Provider
         │
         ▼
  Click "Write Review"
         │
         ▼
  Must be Logged In
         │
    ┌────┴────┐
    │         │
   No        Yes
    │         │
    │         ▼
    │    Fill Rating (1-5)
    │         │
    │         ▼
    │    Write Comment
    │         │
    │         ▼
    │    Submit
    │         │
    │         ▼
    │    AJAX Request
    │         │
    │         ▼
    │    Validate
    │         │
    │         ▼
    │    Save Review
    │         │
    │         ▼
    │    Update Avg Rating
    │         │
    │         ▼
    │    Show Success
    │         │
    ▼         ▼
Login Prompt  Done
```

---

## Future Improvements

### Phase 1: Enhanced User Experience
- [ ] Mobile responsive design improvements
- [ ] Push notifications
- [ ] In-app messaging system
- [ ] Service booking calendar

### Phase 2: Payment Integration
- [ ] Online payment gateway
- [ ] Escrow system for payments
- [ ] Provider payouts system
- [ ] Service pricing options

### Phase 3: Advanced Features
- [ ] Provider verification badges
- [ ] Background check integration
- [ ] Insurance verification
- [ ] Emergency services mode

### Phase 4: Analytics & AI
- [ ] Smart recommendations
- [ ] Price estimation tools
- [ ] Demand forecasting
- [ ] Provider matching algorithm

### Phase 5: Scale
- [ ] Multiple cities/regions
- [ ] Multi-language support
- [ ] Mobile apps (iOS/Android)
- [ ] API for third-party integration

---

## FAQ

### General Questions

**Q: What is ALLO_SERVICE?**
A: ALLO_SERVICE is an online marketplace connecting people who need home services with local professionals who provide them.

**Q: Is ALLO_SERVICE free to use?**
A: Yes, clients can browse and contact providers for free. Providers pay a small fee (future feature) to be listed.

**Q: How do I find a service provider?**
A: Go to the "All Services" page, browse categories, or use the search bar. Click on a provider to view their profile and contact them.

**Q: How do I become a service provider?**
A: Register as a provider, fill out your profile, and wait for admin approval. Once approved, you'll appear in the marketplace.

### For Clients

**Q: Is there a fee to contact providers?**
A: No, contacting providers is free. You pay directly to the provider for services rendered.

**Q: How do I know if a provider is reliable?**
A: Check their reviews and ratings. Providers must be verified by our admin team before appearing in the marketplace.

**Q: Can I leave a review?**
A: Yes, after using a service, you can log in and leave a 1-5 star rating with a comment.

### For Providers

**Q: How long does approval take?**
A: Typically 24-48 hours. You'll receive an email when your application is reviewed.

**Q: Can I edit my profile after approval?**
A: Yes, you can edit your profile at any time from your dashboard.

**Q: What happens if I'm suspended?**
A: You'll be notified via email with the reason. Contact support to resolve the issue.

### For Administrators

**Q: How do I approve a provider?**
A: Log into admin panel → Users → Find provider → Click "Approve". They'll be notified by email.

**Q: How do I add a new service category?**
A: Admin Panel → Categories → Add New Category. Fill in name, slug, description, and upload an icon.

**Q: How do I handle inappropriate content?**
A: You can delete reviews, suspend providers, or remove content from any section of the admin panel.

---

## Support & Contact

For technical support or questions:
- Email: admin@alloservice.com
- Contact Form: /contact.php

---

## Credits

**ALLO_SERVICE** was developed to empower local professionals and connect them with people who need their services. 

Built with ❤️ for the Cameroon community.

---

*This documentation was last updated on March 2026. For the most current information, refer to the source code comments or contact the development team.*
