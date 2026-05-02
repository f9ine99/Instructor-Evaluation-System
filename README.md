# HOPE Instructor Evaluation System

A role-based evaluation platform for collecting, processing, and analyzing student feedback on instructors under controlled and auditable conditions.

## Architecture

```
Instructor-Evaluation-System/
├── public/                          # Web-accessible root
│   ├── index.php                    # Entry point → redirects to home
│   └── assets/                      # Static assets (CSS, JS)
│       └── css/                     # Modular CSS (variables, base, layout, etc.)
│   └── pages/                       # PHP page controllers
│       ├── common/home.php          # Landing page
│       ├── student/                 # Student login + evaluation form
│       ├── instructor/              # Instructor login + dashboard
│       ├── dean/                    # Dean login + evaluation management
│       ├── hr/                      # HR login + analytics dashboard
│       └── admin/                   # System admin login + data management
├── src/                             # Core application logic (NOT web-accessible)
│   ├── config/database.php          # PDO connection factory (TiDB Cloud + SSL)
│   ├── middleware/auth.php          # Session auth + RBAC guard
│   ├── services/
│   │   ├── AuthService.php          # Login, logout, session management
│   │   ├── EvaluationService.php    # Lifecycle state machine, submissions
│   │   ├── AnonymizationService.php # HMAC-SHA256 token hashing
│   │   └── AnalyticsService.php     # Aggregation, trends, alerts
│   └── api/                         # AJAX endpoint handlers
│       ├── auth.php                 # POST login/logout
│       ├── evaluations.php          # CRUD evaluation sheets
│       ├── submissions.php          # Submit/fetch evaluations
│       ├── analytics.php            # Aggregated data endpoints
│       └── admin.php                # User/course/dept management
├── database/
│   ├── schema.sql                   # Full DDL + default question templates
│   ├── clear_data.sql               # Clear all data except default_questions
│   └── seed.sql                     # Placeholder (no demo data; see file)
├── .env.example                     # Database config template
├── .htaccess                        # Apache security rules
└── assets/html/                     # Original HTML prototypes (archived)
```

## Roles

| Role | Access |
|------|--------|
| **Student** | Submit evaluations for enrolled courses (one per course, anonymized) |
| **Instructor** | View aggregated scores + student comments. Submit self-reflections |
| **Dean** | Create/manage evaluation sheets, lifecycle transitions, view full anonymized results |
| **HR** | View instructor rankings, trends, performance alerts. Record administrative decisions |
| **System Admin** | Manage users, courses, departments. No evaluation data access |

## Evaluation Lifecycle

```
Draft → Scheduled → Open → Closed → Reviewed → Archived
```

- **Draft**: Evaluation created, questions seeded from defaults
- **Open**: Students can submit (anonymized via HMAC-SHA256 tokens)
- **Closed**: No new submissions; dean reviews results
- **Archived**: Terminal state; data preserved for trends

## Setup

1. **Clone** and set up your `.env`:
   ```bash
   cp .env.example .env
   # Edit .env with your TiDB Cloud credentials
   ```

2. **Initialize the database** (creates `ievaluation`, all tables, and the 13 default evaluation questions):
   ```bash
   mysql -h <host> -P 4000 -u <user> -p --ssl-ca=<cert> < database/schema.sql
   ```
   There is no bundled demo data. Add departments, users, and courses via the Admin portal or your own SQL. To wipe data later while keeping the default question templates, run `database/clear_data.sql`.

3. **Start PHP server**:
   ```bash
   cd public
   php -S localhost:8000
   ```

4. **Access**: Open `http://localhost:8000`

## First admin user

After `schema.sql`, create at least one admin (or use the Admin UI if you already have an admin). Example: generate a bcrypt hash, then insert:

```bash
php -r 'echo password_hash("your-secure-password", PASSWORD_BCRYPT), "\n";'
```

```sql
USE ievaluation;
INSERT INTO users (username, password_hash, full_name, email, role, department_id, status)
VALUES (
  'admin',
  'PASTE_BCRYPT_HASH_HERE',
  'System Administrator',
  NULL,
  'admin',
  NULL,
  'active'
);
```

## Key Security Features

- **Anonymization**: Student identity is decoupled from submissions via HMAC-SHA256 tokens
- **RBAC**: Every page enforces role-based access via `requireAuth()` middleware
- **Session Security**: `session_regenerate_id()`, HTTP-only cookies, strict same-site policy
- **Audit Logging**: All auth events and state transitions are logged
- **Soft Deletes**: Users/courses/departments are deactivated, never hard-deleted
- **SQL Injection Prevention**: All queries use PDO prepared statements

## Tech Stack

- **Backend**: PHP 8+
- **Database**: MySQL/TiDB Cloud
- **Frontend**: Vanilla HTML/CSS/JS with modular CSS design system
- **Auth**: bcrypt password hashing + PHP sessions
