# 🚀 HK CRM - Lead Management System

A professional CRM Lead Management Admin Panel built with **Laravel 12**, **MySQL**, **Firebase Authentication (Google Sign-In)**, and **Blade Templates**.

> 🎓 **Demo Project** — Built a CRM Lead Management System

> 🛡️ **Author:** [Harsshh (@Soulcynics404)](https://github.com/Soulcynics404) | *"Breaking systems to make them secure."*

---

## ✨ Features

### 📋 Lead Management
- **Add / Edit / Delete Leads** with full CRUD operations
- **Lead List** with pagination (15 per page)
- **Search & Filter** — search by name, email, mobile; filter by status & source
- **Soft Delete** support — recoverable data
- **Lead Fields:** Name, Mobile Number, Email, Source, Status
- **Lead Statuses:** New, Contacted, Interested, Follow Up, Won, Lost

### 📥 Import / Export
- **Import Leads** from CSV files (drag & drop upload)
- **Export Leads** to CSV (respects current filters)
- **Smart Column Matching** — auto-detects headers like "Phone", "Email Address", "Lead Source"
- **Sample CSV Template** — downloadable template for easy bulk upload
- **Demo Leads** included — pre-made CSV for quick testing

### 📅 Follow-up Management
- Add Follow-ups inside each lead detail page
- Set Follow-up Date & Time
- Add Notes for context
- Toggle between Pending / Completed status
- Mark as Completed with one click
- Delete follow-ups

### 📊 Dashboard
- **Total Leads** count
- **Today's Follow-ups** count
- **Pending Follow-ups** count
- **Won Leads** count
- **Lead Status Overview** — visual bar chart breakdown
- **Today's Follow-up List** — time-sorted with lead names
- **Recent Leads** — latest 5 leads at a glance

### 🔐 Authentication
- **Google Sign-In** via Firebase Authentication
- Secure session-based auth for web
- Firebase token-based auth for API
- User avatar & profile display in sidebar

### 🎨 UI/UX
- Clean, modern design with **Inter** font
- Professional navy blue sidebar with HK logo
- Responsive layout — works on desktop, tablet, & mobile
- Status-colored badges for quick identification
- Modal dialogs for follow-up & import management
- Flash messages with auto-dismiss
- Smooth CSS animations & transitions

---

## 🏗️ Architecture

```
Frontend:  Laravel Blade + Vanilla CSS + JavaScript
Backend:   Laravel 12 (PHP 8.2+)
Database:  MySQL / MariaDB
Auth:      Firebase Authentication (Google Sign-In)
API:       RESTful JSON API with Firebase Token Auth
```

### Database Schema

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│    users     │      │    leads     │      │  follow_ups  │
├──────────────┤      ├──────────────┤      ├──────────────┤
│ id           │─────<│ user_id (FK) │      │ id           │
│ name         │      │ id           │─────<│ lead_id (FK) │
│ email        │      │ name         │      │ follow_up_date│
│ firebase_uid │      │ mobile_number│      │ follow_up_time│
│ avatar       │      │ email        │      │ notes        │
│ password     │      │ source       │      │ status       │
│ timestamps   │      │ status       │      │ timestamps   │
└──────────────┘      │ soft_deletes │      └──────────────┘
                      │ timestamps   │
                      └──────────────┘
```

---

## 🧪 Quick Demo (Try It Instantly)

After setting up, you can **test the app in 30 seconds**:

1. **Login** with your Google account
2. Go to **Leads** → Click **Import**
3. Upload the included demo file: `public/demo/demo_leads.csv`
4. ✅ 5 sample leads will be imported instantly
5. Try searching, filtering, editing, and adding follow-ups!

The demo CSV includes leads with different statuses (New, Contacted, Interested, Follow Up, Won) and sources (Website, Referral, Social Media, Google Ads, Cold Call).

---

## 📦 Prerequisites

Before you begin, ensure you have:

- **PHP 8.2+** with extensions: `dom`, `xml`, `mbstring`, `curl`, `mysql`, `zip`
- **Composer 2.x**
- **MySQL 5.7+** or **MariaDB 10.x+**
- **Node.js 18+** & **npm** (for Vite assets, optional)
- **Firebase Project** with Google Sign-In enabled
- **Git** (for version control)

> ⚠️ **IMPORTANT:** You **must** start the MariaDB/MySQL service before running the application. This is required every time after a system reboot (unless enabled on boot):
>
> ```bash
> sudo systemctl start mariadb    # Start the database service
> sudo systemctl enable mariadb   # (Optional) Auto-start on boot
> ```

---

## ⚙️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/Soulcynics404/crm-lead-management.git
cd crm-lead-management
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install PHP Extensions (if missing)

```bash
# Ubuntu/Debian/Kali
sudo apt-get install -y php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-mysql

# Note: Kali Linux ships PHP 8.4 — install php8.4-xml etc. instead
```

### 4. Start MariaDB/MySQL & Create Database

```bash
# Start the database service (required!)
sudo systemctl start mariadb

# Enable auto-start on boot (recommended)
sudo systemctl enable mariadb
```

Then create the database and user:

```bash
sudo mysql
```
```sql
CREATE DATABASE crm_lead CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'crm_password';
CREATE USER 'crm_user'@'127.0.0.1' IDENTIFIED BY 'crm_password';
GRANT ALL PRIVILEGES ON crm_lead.* TO 'crm_user'@'localhost';
GRANT ALL PRIVILEGES ON crm_lead.* TO 'crm_user'@'127.0.0.1';
FLUSH PRIVILEGES;
EXIT;
```

> **Tip:** You can also run `./setup.sh` to automate all of the above steps.

### 5. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and update:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_lead
DB_USERNAME=crm_user
DB_PASSWORD=crm_password

# Firebase (from Firebase Console > Project Settings > General > Your apps)
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_API_KEY=your-api-key
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
```

### 6. Generate App Key

```bash
php artisan key:generate
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Start the Development Server

```bash
php artisan serve
```

### 9. Open in Browser

Visit: **http://localhost:8000**

Login with your Google account and start managing leads! 🎉

---

## 🔥 Firebase Setup

### Step 1: Create Firebase Project
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Click **"Add Project"** → follow the wizard
3. Enable **Google Analytics** (optional)

### Step 2: Enable Google Sign-In
1. In Firebase Console, go to **Authentication** → **Sign-in method**
2. Enable **Google** provider
3. Set your support email
4. Click **Save**

### Step 3: Get Config Values
1. Go to **Project Settings** → **General**
2. Scroll to **"Your apps"** → Click **Web** icon (`</>`)
3. Register your app → Copy the config values:
   - `apiKey` → `FIREBASE_API_KEY`
   - `authDomain` → `FIREBASE_AUTH_DOMAIN`
   - `projectId` → `FIREBASE_PROJECT_ID`

### Step 4: Add Authorized Domains
1. In **Authentication** → **Settings** → **Authorized domains**
2. Add `localhost` (usually auto-added)
3. Add your production domain when deploying

---

## 📋 Demo Leads Generation

A custom script is included to generate random demo leads in both CSV and HTML formats for testing purposes.

### Included Files
The following demo files have been generated:
- `demo_leads_50.csv` / `demo_leads_50.html` (50 leads)
- `demo_leads_100.csv` / `demo_leads_100.html` (100 leads)
- `demo_leads_500.csv` / `demo_leads_500.html` (500 leads)

### How to Generate More
You can modify and run the `generate_demo_leads.py` script to generate custom datasets:

```bash
python3 generate_demo_leads.py
```

This will automatically create new CSV and HTML files in your root directory.

---

## 📡 API Documentation

See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) for complete REST API documentation.

**Quick Overview:**

| Method | Endpoint                          | Description           |
|--------|-----------------------------------|-----------------------|
| GET    | `/api/leads`                      | List all leads        |
| POST   | `/api/leads`                      | Create a lead         |
| GET    | `/api/leads/{id}`                 | Get lead details      |
| PUT    | `/api/leads/{id}`                 | Update a lead         |
| DELETE | `/api/leads/{id}`                 | Delete a lead         |
| GET    | `/api/leads/{id}/follow-ups`      | List follow-ups       |
| POST   | `/api/leads/{id}/follow-ups`      | Create follow-up      |
| PUT    | `/api/follow-ups/{id}`            | Update follow-up      |
| DELETE | `/api/follow-ups/{id}`            | Delete follow-up      |

---

## 📁 Project Structure

```
CRM LEAD/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Firebase Google Sign-In
│   │   │   ├── DashboardController.php   # Dashboard stats & view
│   │   │   ├── LeadController.php        # Lead CRUD + Import/Export
│   │   │   └── FollowUpController.php    # Follow-up CRUD
│   │   └── Middleware/
│   │       └── VerifyFirebaseToken.php   # API token verification
│   ├── Models/
│   │   ├── User.php                      # User model with Firebase UID
│   │   ├── Lead.php                      # Lead model with statuses
│   │   └── FollowUp.php                  # Follow-up model
│   └── Services/
│       └── FirebaseService.php           # Firebase token verification
├── database/
│   └── migrations/                       # All table migrations
├── public/
│   ├── css/app.css                       # Main stylesheet
│   ├── demo/demo_leads.csv              # Demo leads for testing
│   └── images/logo.png                   # HK Logo
├── resources/views/
│   ├── layouts/app.blade.php             # Main layout (sidebar, header)
│   ├── auth/login.blade.php              # Login page with Firebase
│   ├── dashboard.blade.php               # Dashboard with stats
│   └── leads/
│       ├── index.blade.php               # Lead list + Import/Export
│       ├── create.blade.php              # Create lead form
│       ├── edit.blade.php                # Edit lead form
│       └── show.blade.php                # Lead detail + follow-ups
├── routes/
│   ├── web.php                           # Web routes (session auth)
│   └── api.php                           # API routes (token auth)
├── setup.sh                              # Auto setup & health check script
├── .env.example                          # Environment template
├── .gitignore                            # Git ignore rules
├── API_DOCUMENTATION.md                  # REST API docs
├── memory.md                             # Dev progress tracker
└── README.md                             # This file
```

---

## 📐 Scalability & Security

### Scalability
- **Database Indexing** — Status and date columns indexed for fast queries
- **Pagination** — All lists paginated (15 per page)
- **Soft Deletes** — Leads use soft deletes for data recovery
- **Query Optimization** — Eager loading prevents N+1 query issues
- **Scoped Queries** — All queries scoped to authenticated user
- **RESTful API** — Enables future mobile app or SPA integration

### Security
- **Firebase Auth** — Secure Google Sign-In with token verification
- **CSRF Protection** — All web forms include CSRF tokens
- **Authorization** — Users can only access their own leads
- **Input Validation** — Server-side validation on all inputs
- **SQL Injection Prevention** — Eloquent ORM with parameterized queries
- **XSS Protection** — Blade's `{{ }}` auto-escapes output

---

## 🛠️ Troubleshooting

### Database Connection Refused (`SQLSTATE[HY000] [2002]`)

This error means MariaDB/MySQL is not running. Fix it by starting the service:

```bash
sudo systemctl start mariadb
sudo systemctl enable mariadb   # Prevent this after reboot
```

### Database Access Denied (`SQLSTATE[HY000] [1045]`)

The database user credentials in `.env` don't match. Re-create the user:

```bash
sudo mysql -e "CREATE USER IF NOT EXISTS 'crm_user'@'127.0.0.1' IDENTIFIED BY 'crm_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON crm_lead.* TO 'crm_user'@'127.0.0.1';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

### Database Not Found (`SQLSTATE[HY000] [1049]`)

The `crm_lead` database doesn't exist. Create it:

```bash
sudo mysql -e "CREATE DATABASE crm_lead CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

### Quick Fix — Run Setup Script

The included `setup.sh` script automatically diagnoses and fixes all database issues:

```bash
chmod +x setup.sh
./setup.sh
```

---

## 🌐 Access Online (Temporary)

To share the app temporarily (e.g., for demo/presentation):

```bash
# Option 1: Using localhost.run (no install needed)
ssh -R 80:localhost:8000 nokey@localhost.run

# Option 2: Using ngrok
ngrok http 8000
```

This creates a public URL you can share with anyone!

---

## 📝 License & Author

© 2026 **Harsshh** ([@Soulcynics404](https://github.com/Soulcynics404))

> *"Breaking systems to make them secure."*

This project is created as a college assignment for CRM Lead Management.

⚠️ **Do not copy, redistribute, or strip authorship without proper attribution.**
See [LICENSE](./LICENSE) for details.

**Built with ❤️ by Harsshh using Laravel 12 + Firebase + MySQL**
