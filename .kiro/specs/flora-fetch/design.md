# Technical Design Document — FloraFetch

## Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel 11 (PHP 8.2) |
| Frontend | Blade templates, Bootstrap 5, Vanilla JS (ES6+) |
| Database | MySQL 8.0 |
| Auth | Laravel Sanctum (session-based for web) |
| Email | Laravel Mail + SMTP (Mailtrap for dev, configurable for prod) |
| SMS | Vonage (Nexmo) SMS API via Laravel Notification channel |
| File storage | Laravel Storage (local disk in dev, S3-compatible in prod) |
| Queue | Laravel Queue with database driver (jobs for notifications) |
| Web server | Apache / Laravel Artisan serve (dev) |

---

## High-Level Architecture

```
Browser
  │
  ▼
Apache / Laravel Router
  │
  ├── Web Routes (Blade SSR)
  │     ├── Guest pages  (catalog, listing detail, search)
  │     ├── Customer pages (cart, checkout, orders, profile, reviews)
  │     └── Admin pages  (dashboard, products, orders, users, reviews)
  │
  └── Middleware Stack
        ├── auth          (Laravel built-in)
        ├── role:admin    (custom middleware)
        └── throttle:login (rate-limit login attempts)

Laravel Application
  ├── Controllers  (thin — delegate to Services)
  ├── Services     (Auth, Catalog, Cart, Order, Notification, Review)
  ├── Models       (Eloquent ORM)
  ├── Jobs         (SendOrderConfirmation, SendStatusUpdate, SendExpertAdviceNotification)
  ├── Policies     (OrderPolicy, ReviewPolicy)
  └── Events / Listeners (OrderPlaced, OrderStatusUpdated)

MySQL 8.0
  └── (schema described below)
```

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(100) | |
| email | VARCHAR(255) UNIQUE NULLABLE | |
| phone | VARCHAR(20) UNIQUE NULLABLE | E.164 format |
| password | VARCHAR(255) | bcrypt |
| role | ENUM('customer','admin') | default 'customer' |
| is_active | BOOLEAN | default true |
| email_verified_at | TIMESTAMP NULLABLE | |
| phone_verified_at | TIMESTAMP NULLABLE | |
| failed_login_attempts | TINYINT UNSIGNED | default 0 |
| locked_until | TIMESTAMP NULLABLE | |
| created_at / updated_at | TIMESTAMPS | |

### `addresses`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| user_id | FK → users | |
| label | VARCHAR(50) | e.g. "Home" |
| street | VARCHAR(255) | |
| city | VARCHAR(100) | |
| postal_code | VARCHAR(20) | |
| created_at / updated_at | TIMESTAMPS | |

### `categories`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(100) UNIQUE | Indoor, Outdoor, etc. |
| slug | VARCHAR(100) UNIQUE | |

### `products`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| category_id | FK → categories | |
| common_name | VARCHAR(255) | |
| botanical_name | VARCHAR(255) | |
| description | TEXT NULLABLE | |
| size | ENUM('small','medium','large') | |
| price | DECIMAL(10,2) | > 0 |
| stock_quantity | INT UNSIGNED | default 0 |
| sunlight_requirement | VARCHAR(100) | |
| watering_frequency | VARCHAR(100) | |
| soil_recommendation | TEXT NULLABLE | |
| temperature_min_c | DECIMAL(5,1) NULLABLE | |
| temperature_max_c | DECIMAL(5,1) NULLABLE | |
| is_low_maintenance | BOOLEAN | default false |
| is_pet_friendly | BOOLEAN | default false |
| growth_rate | ENUM('Slow','Moderate','Fast') NULLABLE | |
| is_active | BOOLEAN | default true |
| created_at / updated_at | TIMESTAMPS | |

### `product_images`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| product_id | FK → products | |
| path | VARCHAR(500) | storage path |
| sort_order | TINYINT UNSIGNED | default 0 |

### `product_related` (Frequently Bought With)
| Column | Type | Notes |
|---|---|---|
| product_id | FK → products | |
| related_product_id | FK → products | |
| PRIMARY KEY (product_id, related_product_id) | | |

### `cart_items`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| user_id | FK → users | |
| product_id | FK → products | |
| quantity | INT UNSIGNED | |
| created_at / updated_at | TIMESTAMPS | |
| UNIQUE (user_id, product_id) | | |

### `orders`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| user_id | FK → users | |
| address_id | FK → addresses | snapshot copied to order |
| delivery_address_snapshot | JSON | street/city/postal at time of order |
| delivery_date | DATE | |
| special_instructions | VARCHAR(500) NULLABLE | |
| status | ENUM('order_confirmed','quality_check','in_transit','delivered','delivery_refused') | |
| delivery_fee | DECIMAL(10,2) | from config at time of order |
| total_amount | DECIMAL(10,2) | items + delivery fee |
| estimated_delivery_date | DATE NULLABLE | set when status → in_transit |
| delivered_at | TIMESTAMP NULLABLE | |
| refused_at | TIMESTAMP NULLABLE | |
| has_removed_listing | BOOLEAN | default false |
| created_at / updated_at | TIMESTAMPS | |

### `order_items`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| order_id | FK → orders | |
| product_id | FK → products NULLABLE | null if product deleted |
| product_name_snapshot | VARCHAR(255) | name at time of order |
| unit_price_snapshot | DECIMAL(10,2) | price at time of order |
| quantity | INT UNSIGNED | |

### `order_status_history`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| order_id | FK → orders | |
| status | ENUM (same as orders.status) | |
| updated_by | FK → users (admin) | |
| created_at | TIMESTAMP | UTC |

### `reviews`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| order_id | FK → orders | |
| product_id | FK → products | |
| user_id | FK → users | |
| rating | TINYINT UNSIGNED | 1–5 |
| comment | TEXT NULLABLE | max 1000 chars |
| photo_path | VARCHAR(500) NULLABLE | |
| status | ENUM('pending','approved','rejected') | default 'pending' |
| expert_advice | TEXT NULLABLE | admin response |
| expert_advice_posted_at | TIMESTAMP NULLABLE | |
| created_at / updated_at | TIMESTAMPS | |
| UNIQUE (order_id, product_id, user_id) | | prevents duplicate reviews |

### `password_reset_tokens`
Standard Laravel table — extended with `expires_at` column.

### `personal_access_tokens`
Standard Laravel Sanctum table (used for any future API access).

---

## Application Modules & Key Classes

### Auth Module
- `AuthController` — register, login, logout, password reset
- `AuthService` — account creation, session management, lockout logic
- `VerifyEmailController` / `VerifyPhoneController`
- Middleware: `ThrottleLogins`, `EnsureAccountNotLocked`

### Catalog Module
- `ProductController` — public listing, detail page, search, filter
- `AdminProductController` — CRUD, bulk CSV import
- `CatalogService` — search ranking, filter logic, related products
- `CsvImportService` — validates and imports product CSV

### Cart Module
- `CartController` — add, update quantity, remove, clear
- `CartService` — stock validation, total calculation, session persistence

### Order Module
- `CheckoutController` — checkout page, order confirmation
- `OrderController` — customer order list, order detail
- `AdminOrderController` — order list, status update
- `OrderService` — order creation, stock decrement, status pipeline enforcement

### Notification Module
- `OrderPlaced` event → `SendOrderConfirmationJob` (queued)
- `OrderStatusUpdated` event → `SendStatusUpdateJob` (queued)
- `ExpertAdvicePosted` event → `SendExpertAdviceNotificationJob` (queued)
- `OrderNotification` (Laravel Notification class — mail + Vonage SMS channels)

### Review Module
- `ReviewController` — submit review, upload photo
- `AdminReviewController` — moderation queue, approve, reject, expert advice
- `ReviewService` — eligibility check, duplicate prevention, average rating

### Admin Dashboard
- `AdminDashboardController` — metrics aggregation
- `AdminUserController` — search, deactivate

---

## Key Design Decisions

**Server-side rendering with Blade** — keeps the stack simple and avoids a separate SPA build pipeline. JS is used only for progressive enhancements (cart quantity updates, filter interactions, image gallery).

**Cart stored in database** — `cart_items` table keyed by `user_id + product_id`. This gives cross-device persistence without relying on cookies or localStorage. Guest browsing is read-only; adding to cart requires login.

**Order address snapshot** — the delivery address is copied as JSON into `orders.delivery_address_snapshot` at order creation time. This means editing or deleting an address later doesn't corrupt historical orders.

**Price and name snapshots in `order_items`** — same principle: unit price and product name are snapshotted so order history is accurate even if the product is later edited or deleted.

**Status pipeline enforced in `OrderService`** — a `PIPELINE` constant defines the allowed transitions. Any attempt to skip a step or go backwards throws a `InvalidStatusTransitionException` caught by the controller.

**Notifications are queued jobs** — email and SMS are dispatched as queued jobs so the HTTP response is not blocked. The database queue driver is used (no Redis required for initial deployment).

**File uploads** — review photos and product images are stored via Laravel's `Storage` facade. In development, the `local` disk is used. The storage path is configurable via `.env` for S3 in production.

**Role-based access** — a single `role` column on `users` (customer / admin). A custom `EnsureUserIsAdmin` middleware protects all `/admin/*` routes. Laravel Policies handle fine-grained authorization (e.g., a customer can only review their own delivered orders).

---

## Route Structure

```
# Public
GET  /                          → HomeController@index
GET  /catalog                   → ProductController@index
GET  /catalog/{product}         → ProductController@show
GET  /search                    → ProductController@search

# Auth
GET  /register                  → AuthController@showRegister
POST /register                  → AuthController@register
GET  /login                     → AuthController@showLogin
POST /login                     → AuthController@login
POST /logout                    → AuthController@logout
GET  /forgot-password           → PasswordResetController@showRequest
POST /forgot-password           → PasswordResetController@sendLink
GET  /reset-password/{token}    → PasswordResetController@showReset
POST /reset-password            → PasswordResetController@reset

# Customer (auth required)
GET  /cart                      → CartController@index
POST /cart                      → CartController@add
PATCH /cart/{item}              → CartController@update
DELETE /cart/{item}             → CartController@remove
GET  /checkout                  → CheckoutController@index
POST /checkout                  → CheckoutController@store
GET  /orders                    → OrderController@index
GET  /orders/{order}            → OrderController@show
GET  /profile                   → ProfileController@show
PUT  /profile                   → ProfileController@update
GET  /profile/addresses         → AddressController@index
POST /profile/addresses         → AddressController@store
PUT  /profile/addresses/{addr}  → AddressController@update
DELETE /profile/addresses/{addr}→ AddressController@destroy
POST /orders/{order}/reviews    → ReviewController@store

# Admin (auth + admin role required)
GET  /admin                     → AdminDashboardController@index
GET  /admin/products            → AdminProductController@index
GET  /admin/products/create     → AdminProductController@create
POST /admin/products            → AdminProductController@store
GET  /admin/products/{p}/edit   → AdminProductController@edit
PUT  /admin/products/{p}        → AdminProductController@update
DELETE /admin/products/{p}      → AdminProductController@destroy
POST /admin/products/import     → AdminProductController@import
GET  /admin/orders              → AdminOrderController@index
GET  /admin/orders/{order}      → AdminOrderController@show
PATCH /admin/orders/{order}/status → AdminOrderController@updateStatus
GET  /admin/users               → AdminUserController@index
PATCH /admin/users/{user}/deactivate → AdminUserController@deactivate
GET  /admin/reviews             → AdminReviewController@index
PATCH /admin/reviews/{review}/approve → AdminReviewController@approve
PATCH /admin/reviews/{review}/reject  → AdminReviewController@reject
POST /admin/reviews/{review}/advice   → AdminReviewController@postAdvice
```

---

## Directory Structure

```
florafetch/
├── app/
│   ├── Console/
│   ├── Events/
│   │   ├── OrderPlaced.php
│   │   ├── OrderStatusUpdated.php
│   │   └── ExpertAdvicePosted.php
│   ├── Exceptions/
│   │   └── InvalidStatusTransitionException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Admin/
│   │   │   └── Customer/
│   │   └── Middleware/
│   │       ├── EnsureUserIsAdmin.php
│   │       └── EnsureAccountNotLocked.php
│   ├── Jobs/
│   │   ├── SendOrderConfirmationJob.php
│   │   ├── SendStatusUpdateJob.php
│   │   └── SendExpertAdviceNotificationJob.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Address.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── ProductImage.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderStatusHistory.php
│   │   └── Review.php
│   ├── Notifications/
│   │   └── OrderNotification.php
│   ├── Policies/
│   │   ├── OrderPolicy.php
│   │   └── ReviewPolicy.php
│   └── Services/
│       ├── AuthService.php
│       ├── CatalogService.php
│       ├── CartService.php
│       ├── CsvImportService.php
│       ├── OrderService.php
│       └── ReviewService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php       (customer layout)
│       │   └── admin.blade.php     (admin layout)
│       ├── auth/
│       ├── catalog/
│       ├── cart/
│       ├── checkout/
│       ├── orders/
│       ├── profile/
│       ├── reviews/
│       └── admin/
│           ├── dashboard/
│           ├── products/
│           ├── orders/
│           ├── users/
│           └── reviews/
├── routes/
│   ├── web.php
│   └── admin.php
├── public/
│   ├── css/
│   └── js/
└── storage/
    └── app/public/
        ├── products/
        └── reviews/
```

---

## Configuration

Key `.env` values:

```
APP_NAME=FloraFetch
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=florafetch
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525

VONAGE_KEY=
VONAGE_SECRET=
VONAGE_SMS_FROM=FloraFetch

QUEUE_CONNECTION=database

FLORAFETCH_DELIVERY_FEE=150   # fixed delivery fee in smallest currency unit or decimal
FLORAFETCH_MAX_ADDRESSES=10
```
