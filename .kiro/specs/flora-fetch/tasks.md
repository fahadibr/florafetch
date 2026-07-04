# Implementation Tasks — FloraFetch

## Task 1: Project Scaffolding & Configuration
- [ ] Create a new Laravel 11 project in the workspace root
- [ ] Configure `.env` with MySQL credentials, mail (Mailtrap), Vonage keys, queue driver (database), and `FLORAFETCH_DELIVERY_FEE`
- [ ] Install required packages: `laravel/sanctum`, `laravel-notification-channels/vonage`, `league/csv`
- [ ] Register `EnsureUserIsAdmin` and `EnsureAccountNotLocked` middleware in `bootstrap/app.php`
- [ ] Create `routes/admin.php` and include it in `bootstrap/app.php` with the `admin` middleware group
- [ ] Publish and run initial migrations (`php artisan migrate`)

## Task 2: Database Migrations
- [ ] `create_users_table` — add `phone`, `role`, `is_active`, `failed_login_attempts`, `locked_until` columns to the default Laravel users migration
- [ ] `create_addresses_table`
- [ ] `create_categories_table`
- [ ] `create_products_table`
- [ ] `create_product_images_table`
- [ ] `create_product_related_table`
- [ ] `create_cart_items_table`
- [ ] `create_orders_table`
- [ ] `create_order_items_table`
- [ ] `create_order_status_history_table`
- [ ] `create_reviews_table`
- [ ] Extend `password_reset_tokens` migration with `expires_at` column
- [ ] Run `php artisan migrate` and verify all tables are created

## Task 3: Eloquent Models & Relationships
- [ ] `User` — hasMany addresses, cartItems, orders, reviews; role helpers (`isAdmin()`, `isCustomer()`)
- [ ] `Address` — belongsTo user
- [ ] `Category` — hasMany products
- [ ] `Product` — belongsTo category; hasMany productImages, orderItems, reviews; belongsToMany relatedProducts (self-referential via `product_related`)
- [ ] `ProductImage` — belongsTo product
- [ ] `CartItem` — belongsTo user, product
- [ ] `Order` — belongsTo user, address; hasMany orderItems, statusHistory; status pipeline constants
- [ ] `OrderItem` — belongsTo order, product (nullable)
- [ ] `OrderStatusHistory` — belongsTo order, updatedBy (user)
- [ ] `Review` — belongsTo order, product, user

## Task 4: Authentication (Requirement 1)
- [ ] `AuthService` — `register()`, `login()`, `logout()`, `incrementFailedAttempts()`, `lockAccountIfThresholdReached()`, `isLocked()`
- [ ] `AuthController` — register, login, logout actions using `AuthService`
- [ ] `EnsureAccountNotLocked` middleware — checks `locked_until` before processing login
- [ ] `PasswordResetController` — send reset link/code, reset password; set `expires_at` on token
- [ ] `VerifyEmailController` — handle email verification link
- [ ] Blade views: `auth/register.blade.php`, `auth/login.blade.php`, `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`
- [ ] Apply `throttle:login` middleware to POST `/login` route

## Task 5: Customer Profile & Addresses (Requirement 2)
- [ ] `ProfileController` — show and update profile; validate name, email, phone per spec
- [ ] `AddressController` — index, store, update, destroy; enforce 10-address limit
- [ ] Blade views: `profile/show.blade.php` (includes Plant History section), `profile/addresses.blade.php`

## Task 6: Categories & Seeder
- [ ] `CategorySeeder` — seed Indoor, Outdoor, Succulents, Flowering, Medicinal, Gardening Essentials
- [ ] Run seeder as part of `DatabaseSeeder`

## Task 7: Product Catalog — Public (Requirements 5 & 6)
- [ ] `CatalogService` — `search()` with ranked results (exact name → partial name/botanical → category), `filter()` with Low Maintenance / Pet Friendly / Price Range / Growth Rate, `getRelated()`
- [ ] `ProductController` — `index()` (catalog with filters), `show()` (listing detail), `search()`
- [ ] Blade views: `catalog/index.blade.php` (grid + filter sidebar), `catalog/show.blade.php` (care page, gallery, Frequently Bought With, reviews, Add to Cart)
- [ ] JS: filter panel interactions, image gallery navigation, live search (debounced fetch or form submit)

## Task 8: Admin Product Management (Requirement 4)
- [ ] `AdminProductController` — index, create, store, edit, update, destroy, import (CSV)
- [ ] `CsvImportService` — parse CSV using `league/csv`, validate each row, return per-row error report
- [ ] Blade views: `admin/products/index.blade.php`, `admin/products/create.blade.php`, `admin/products/edit.blade.php`
- [ ] File upload handling for product images via `Storage::disk('public')`
- [ ] On product delete: set `is_active = false`, apply `has_removed_listing = true` flag to affected open orders

## Task 9: Shopping Cart (Requirement 7)
- [ ] `CartService` — `add()`, `increment()`, `updateQuantity()`, `remove()`, `clear()`, `getTotal()`, `restore()` (on login)
- [ ] `CartController` — add, update, remove actions; return updated cart partial or redirect
- [ ] Blade view: `cart/index.blade.php` — item list, quantity controls, Green Total
- [ ] JS: quantity +/- buttons submit PATCH via fetch; update total without full page reload

## Task 10: Checkout & Order Creation (Requirements 8 & 9)
- [ ] `CheckoutController` — `index()` (show checkout page), `store()` (create order)
- [ ] `OrderService` — `createOrder()`: validate stock, snapshot address + prices, decrement stock, clear cart, dispatch `OrderPlaced` event; `getStatusPipeline()`, `advanceStatus()`
- [ ] Blade view: `checkout/index.blade.php` — cart summary, address selector, delivery date picker, special instructions, COD total
- [ ] Delivery fee read from `config('florafetch.delivery_fee')`

## Task 11: Order Tracking — Customer (Requirements 11)
- [ ] `OrderController` — `index()` (order list), `show()` (order detail with status timeline)
- [ ] Blade views: `orders/index.blade.php`, `orders/show.blade.php` — status stepper, timestamps, estimated delivery date (only when In Transit)

## Task 12: Notifications (Requirements 10 & 11)
- [ ] `OrderNotification` Laravel Notification class — mail channel (order confirmation email with all required fields) + Vonage SMS channel (order ID + total)
- [ ] `SendOrderConfirmationJob` — dispatched on `OrderPlaced` event
- [ ] `SendStatusUpdateJob` — dispatched on `OrderStatusUpdated` event; email + SMS with order ID and new status
- [ ] `SendExpertAdviceNotificationJob` — dispatched on `ExpertAdvicePosted` event
- [ ] Run `php artisan queue:work` (or configure supervisor for production)

## Task 13: Admin Dashboard (Requirement 3)
- [ ] `AdminDashboardController` — aggregate metrics: total orders, total revenue, orders by status (cached for 5 minutes using Laravel Cache)
- [ ] `AdminUserController` — search customers, deactivate (invalidate sessions via `Auth::logoutOtherDevices()` equivalent, cancel eligible orders, return summary)
- [ ] Blade views: `admin/dashboard/index.blade.php` (metrics cards, recent orders), `admin/users/index.blade.php`
- [ ] Admin layout: `layouts/admin.blade.php` with sidebar navigation

## Task 14: Admin Order Management (Requirement 12)
- [ ] `AdminOrderController` — `index()` (paginated, filterable, sortable), `show()`, `updateStatus()`
- [ ] `OrderService::advanceStatus()` — enforce pipeline, record `OrderStatusHistory`, dispatch `OrderStatusUpdated` event
- [ ] Blade views: `admin/orders/index.blade.php`, `admin/orders/show.blade.php`

## Task 15: Reviews — Customer Submission (Requirement 13)
- [ ] `ReviewService` — `canReview()` (order delivered, product in order, no duplicate), `store()`, `getAverageRating()`
- [ ] `ReviewController` — `store()` with photo upload validation (JPEG/PNG, 1 byte – 5 MB)
- [ ] Blade view: review form embedded in `orders/show.blade.php` (shown only for delivered orders)
- [ ] Display approved reviews + average rating on `catalog/show.blade.php`

## Task 16: Admin Review Moderation (Requirement 14)
- [ ] `AdminReviewController` — `index()` (moderation queue, oldest first), `approve()`, `reject()`, `postAdvice()`
- [ ] On `postAdvice()`: persist `expert_advice`, set `expert_advice_posted_at`, dispatch `ExpertAdvicePosted` event
- [ ] Blade views: `admin/reviews/index.blade.php`, review detail modal or page

## Task 17: Policies & Authorization
- [ ] `OrderPolicy` — `view()`: customer can only view their own orders
- [ ] `ReviewPolicy` — `create()`: customer must have a delivered order containing the product; `store()`: no duplicate
- [ ] Register policies in `AuthServiceProvider`

## Task 18: Frontend Polish
- [ ] Customer layout `layouts/app.blade.php` — navbar with cart item count badge, login/logout links
- [ ] Admin layout `layouts/admin.blade.php` — sidebar with links to dashboard, products, orders, users, reviews
- [ ] Responsive Bootstrap 5 grid across all pages
- [ ] Flash message component (success / error alerts) included in both layouts
- [ ] Accessibility: all form inputs have associated `<label>`, images have `alt` text, buttons have descriptive text

## Task 19: Seeders & Demo Data
- [ ] `AdminSeeder` — create one admin user
- [ ] `ProductSeeder` — seed 20 sample products across all categories with images
- [ ] `DatabaseSeeder` — call CategorySeeder, AdminSeeder, ProductSeeder

## Task 20: Final Verification
- [ ] Run `php artisan migrate:fresh --seed` and confirm all tables and seed data are correct
- [ ] Manually walk through: register → browse catalog → add to cart → checkout → admin advances status → customer sees update → customer submits review → admin moderates
- [ ] Confirm queued notifications are dispatched (`php artisan queue:work`)
- [ ] Check all admin-only routes return 403/redirect for non-admin users
