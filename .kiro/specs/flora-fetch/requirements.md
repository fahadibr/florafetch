# Requirements Document

## Introduction

FloraFetch is a web-based marketplace for the online sale and distribution of plant species, including indoor greens, outdoor shrubs, succulents, herbs, and gardening essentials. The platform serves two primary audiences: plant enthusiasts who want to purchase healthy plants with home delivery, and local nurseries that need a digital channel to manage inventory and reach a wider customer base. FloraFetch provides real-time inventory tracking, detailed plant care guides, a streamlined checkout experience with Cash on Delivery, and end-to-end order tracking from confirmation through delivery.

## Glossary

- **System**: The FloraFetch web application as a whole.
- **Guest**: An unauthenticated visitor browsing the platform.
- **Customer**: A registered and authenticated user who can browse, purchase, and review plants.
- **Admin**: A privileged user responsible for managing inventory, orders, users, and reviews.
- **Product**: A plant species or gardening essential listed for sale on the platform.
- **Listing**: A Product entry in the catalog, including all associated metadata and media.
- **Cart**: A temporary collection of Products a Customer intends to purchase.
- **Order**: A confirmed purchase request submitted by a Customer.
- **Order_Status**: The current stage of an Order in the fulfillment pipeline (Order Confirmed, Quality Check, In Transit, Delivered).
- **Review**: A Customer-submitted rating and optional photo of a received Product.
- **Care_Guide**: A structured set of care instructions associated with a Listing.
- **Auth_Service**: The component responsible for authentication and session management.
- **Catalog_Service**: The component responsible for managing and serving Product Listings.
- **Cart_Service**: The component responsible for managing Cart state.
- **Order_Service**: The component responsible for Order creation, tracking, and status updates.
- **Notification_Service**: The component responsible for sending confirmation and status messages to Customers.
- **Review_Service**: The component responsible for managing Customer Reviews.

---

## Requirements

### Requirement 1: User Registration and Authentication

**User Story:** As a Guest, I want to register and log in to FloraFetch, so that I can place orders and manage my account.

#### Acceptance Criteria

1. WHEN a Guest submits a registration form with a valid email address and a password of at least eight characters containing at least one letter and one digit, THE Auth_Service SHALL create a new Customer account and send a verification link valid for twenty-four hours to the provided email address.
2. WHEN a Guest submits a registration form with a valid phone number in E.164 international format and a password meeting the same complexity rules, THE Auth_Service SHALL create a new Customer account and send a one-time verification code valid for ten minutes to the provided phone number.
3. IF a Guest submits a registration form with an email address or phone number that is already associated with an existing account, THEN THE Auth_Service SHALL return an error message indicating that the identifier is already in use and SHALL NOT create a new account.
4. WHEN a Customer submits valid login credentials, THE Auth_Service SHALL create an authenticated session and redirect the Customer to the platform home page.
5. WHEN a Customer is redirected to the home page after successful authentication, THE Auth_Service SHALL clear any previously displayed authentication error messages.
6. IF a Customer submits invalid login credentials, THEN THE Auth_Service SHALL return an error message and increment the failed-attempt counter for that account.
7. IF a Customer's failed-attempt counter reaches five within a fifteen-minute window, THEN THE Auth_Service SHALL lock the account for fifteen minutes and display a message informing the Customer of the lockout duration.
8. WHEN a Customer requests logout, THE Auth_Service SHALL invalidate the current session and redirect the Customer to the platform home page.
9. WHEN a Customer requests a password reset using a registered email address, THE Auth_Service SHALL send a time-limited reset link valid for thirty minutes to that email address.
10. WHEN a Customer requests a password reset using a registered phone number, THE Auth_Service SHALL send a one-time reset code valid for ten minutes to that phone number.

---

### Requirement 2: Customer Profile Management

**User Story:** As a Customer, I want to manage my profile and delivery addresses, so that I can keep my personal details current and speed up checkout.

#### Acceptance Criteria

1. THE System SHALL allow a Customer to store up to ten delivery addresses, each labeled with a name of one to fifty characters (e.g., "Home", "Office").
2. IF a Customer attempts to save an eleventh delivery address when ten are already stored, THEN THE System SHALL reject the request and display an error message indicating the ten-address limit has been reached.
3. WHEN a Customer updates personal details, THE System SHALL validate that the name is between one and one hundred characters, the email address conforms to standard email format, and the phone number conforms to E.164 international format, and SHALL persist valid changes within five seconds.
4. IF a Customer submits a personal details update with an invalid value, THEN THE System SHALL return a validation error identifying each invalid field and SHALL NOT persist any changes.
5. IF a Customer attempts to save a delivery address with a missing required field (street, city, or postal code), THEN THE System SHALL return a validation error identifying each missing field.
6. THE System SHALL display a "Plant History" section on the Customer profile page listing all previously delivered Orders in reverse chronological order.
7. WHEN a Customer deletes a saved delivery address, THE System SHALL remove the address and display a confirmation message stating the address has been deleted.

---

### Requirement 3: Admin Dashboard and User Management

**User Story:** As an Admin, I want a centralized dashboard, so that I can monitor platform activity and manage users, inventory, and sales.

#### Acceptance Criteria

1. THE System SHALL restrict access to the Admin Dashboard to authenticated users with the Admin role.
2. IF an unauthenticated user or a Customer without the Admin role attempts to access the Admin Dashboard, THEN THE System SHALL redirect the user to the login page and display an error message indicating insufficient permissions.
3. THE System SHALL display aggregate sales metrics on the Admin Dashboard, including total orders, total revenue, and orders by Order_Status, updated within five minutes of each transaction.
4. WHEN an Admin searches for Customer accounts by name, email address, or phone number, THE System SHALL return matching accounts; IF no accounts match the query, THE System SHALL display a message indicating no results were found.
5. WHEN an Admin deactivates a Customer account, THE System SHALL invalidate any active sessions for that Customer and cancel all Orders in "Order Confirmed" or "Quality Check" status associated with the account; IF session invalidation fails, THE System SHALL still proceed with Order cancellation and report the session invalidation failure; IF Order cancellation fails for any Order, THE System SHALL report which Orders could not be cancelled.
6. WHEN an Admin deactivates a Customer account and all actions complete, THE System SHALL display a summary indicating which actions succeeded and which failed.
7. THE System SHALL provide an Admin with a paginated list of all Products at twenty items per page, filterable by category and by stock status (In Stock or Out of Stock).

---

### Requirement 4: Product Listing Management (Admin)

**User Story:** As an Admin, I want to create, update, and remove plant Listings, so that the catalog accurately reflects available inventory.

#### Acceptance Criteria

1. WHEN an Admin submits a new Listing with all required fields (common name, botanical name, price greater than zero, size, category, sunlight requirement, watering frequency, and at least one image), THE Catalog_Service SHALL persist the Listing and make it visible in the catalog.
2. IF an Admin submits a new Listing with any required field missing or with a price of zero or less, THEN THE Catalog_Service SHALL return a validation error identifying each invalid field and SHALL NOT persist the Listing.
3. WHEN an Admin updates the price or stock quantity of an existing Listing, THE Catalog_Service SHALL validate that the price is greater than zero and the stock quantity is zero or greater, persist valid changes, and reflect them in the catalog within ten seconds.
4. IF an Admin submits a price update with a value of zero or less, or a stock quantity update with a negative value, THEN THE Catalog_Service SHALL return a validation error and SHALL NOT persist the change.
5. WHEN an Admin deletes a Listing, THE Catalog_Service SHALL remove the Listing from the catalog and apply a "Listing Removed" flag to all Orders in "Order Confirmed", "Quality Check", or "In Transit" status that contain that Listing, making the flag visible in the Admin order management queue.
6. WHEN an Admin uploads a CSV file for bulk import, THE Catalog_Service SHALL persist all rows that conform to the platform's defined import schema, skip non-conforming rows, and return a per-row error report; IF all rows are non-conforming, zero Listings SHALL be persisted.

---

### Requirement 5: Plant Catalog Browsing and Filtering

**User Story:** As a Customer or Guest, I want to browse and filter the plant catalog, so that I can find plants that match my needs and preferences.

#### Acceptance Criteria

1. THE Catalog_Service SHALL organize all Listings into at least the following categories: Indoor, Outdoor, Succulents, Flowering, Medicinal, and Gardening Essentials.
2. WHEN a Customer or Guest applies one or more filters, THE Catalog_Service SHALL return only Listings that satisfy all selected filters simultaneously; the Price Range filter SHALL accept a minimum value of zero and a maximum value up to the highest listed price; the Growth Rate filter SHALL accept only the values "Slow", "Moderate", or "Fast".
3. WHEN a Customer or Guest submits a search query, THE Catalog_Service SHALL return Listings ranked in the following order: exact common name match first, then partial common name or botanical name match, then category match.
4. THE Catalog_Service SHALL display search and filter results within two seconds of the query submission or filter application event for result sets of up to five hundred Listings.
5. WHEN no Listings match the applied filters or search query, THE Catalog_Service SHALL display a message indicating that no results were found and SHALL display each active filter with an individual remove control so the Customer or Guest can remove filters one at a time.

---

### Requirement 6: Plant Care Page (Listing Detail)

**User Story:** As a Customer or Guest, I want to view a detailed care page for each plant, so that I can make an informed purchase decision.

#### Acceptance Criteria

1. THE Catalog_Service SHALL display a Care_Guide for each Listing, including soil recommendations, temperature range in degrees Celsius, sunlight requirement, and watering frequency.
2. THE Catalog_Service SHALL display at least one image of minimum dimensions 800×800 pixels per Listing, with support for multiple images presented in a navigable gallery.
3. THE Catalog_Service SHALL display a "Frequently Bought With" section on each Listing detail page, showing up to five Admin-associated Products (e.g., fertilizers, pots).
4. THE Catalog_Service SHALL display the botanical name, common name, size options, current price, and stock status ("In Stock" or "Out of Stock") on each Listing detail page.
5. WHEN a Listing's stock quantity reaches zero, THE Catalog_Service SHALL display an "Out of Stock" indicator on the Listing detail page.
6. WHEN a Listing's stock quantity reaches zero, THE Catalog_Service SHALL disable the "Add to Cart" control for that Listing.
7. WHEN a Listing's stock quantity increases from zero to one or more, THE Catalog_Service SHALL remove the "Out of Stock" indicator and re-enable the "Add to Cart" control.

---

### Requirement 7: Shopping Cart

**User Story:** As a Customer, I want to manage a shopping cart, so that I can review and adjust my selections before checkout.

#### Acceptance Criteria

1. WHEN a Customer adds a Listing that is not already in the Cart, THE Cart_Service SHALL add the item with a quantity of one and display the updated Cart total.
2. WHEN a Customer adds a Listing that is already in the Cart, THE Cart_Service SHALL increment the existing item's quantity by one and display the updated Cart total.
3. WHEN a Customer changes the quantity of a Cart item to a value greater than zero and not exceeding the available stock for that Listing, THE Cart_Service SHALL update the item quantity and recalculate the Cart total.
4. IF a Customer attempts to set the quantity of a Cart item to a value exceeding the available stock for that Listing, THEN THE Cart_Service SHALL reject the change and display a message indicating the maximum available quantity.
5. WHEN a Customer changes the quantity of a Cart item to zero, THE Cart_Service SHALL remove the item from the Cart and recalculate the Cart total.
6. THE Cart_Service SHALL display a running "Green Total" reflecting the sum of all item prices multiplied by their respective quantities, excluding delivery fees.
7. IF a Customer attempts to add a Listing to the Cart that has zero stock, THEN THE Cart_Service SHALL reject the addition and display a message indicating the item is out of stock.
8. WHILE a Customer is logged in, THE Cart_Service SHALL persist the Cart contents across browser sessions until the Order is placed or the Customer explicitly clears the Cart; IF a persistence error occurs, THE Cart_Service SHALL display an error message and SHALL NOT silently show an empty Cart.
9. WHEN a Customer logs back in after a previous session, THE Cart_Service SHALL restore the Cart contents that were present at the time of logout.

---

### Requirement 8: Checkout Process

**User Story:** As a Customer, I want a streamlined checkout flow, so that I can place an order for plants with specific delivery instructions.

#### Acceptance Criteria

1. WHEN a Customer initiates checkout, THE Order_Service SHALL present a summary of Cart items, the selected delivery address, a delivery date selector offering dates one to fourteen calendar days from the current date, and a field for special handling instructions.
2. IF a Customer initiates checkout with an empty Cart, THEN THE Order_Service SHALL block submission and display a message indicating the Cart is empty.
3. IF a Customer attempts to submit an Order without selecting a delivery address, THEN THE Order_Service SHALL block submission and display a message requiring address selection.
4. THE Order_Service SHALL allow the Customer to enter free-text special handling instructions of up to five hundred characters for delicate living items.
5. WHEN a Customer confirms an Order, THE Order_Service SHALL create the Order record with Order_Status set to "Order Confirmed", decrement the stock quantity of each ordered Listing, and clear the Customer's Cart.
6. IF stock for any Cart item is exhausted between Cart addition and Order confirmation, THEN THE Order_Service SHALL display the name and quantity of each unavailable item, block Order submission, and preserve all remaining Cart items until the Customer removes or adjusts the affected items.
7. IF THE Order_Service fails to persist the Order record, THEN THE Order_Service SHALL display an error message and preserve the Customer's Cart contents unchanged.

---

### Requirement 9: Payment — Cash on Delivery

**User Story:** As a Customer, I want to pay cash upon delivery, so that I can inspect the plants before completing payment.

#### Acceptance Criteria

1. THE Order_Service SHALL support Cash on Delivery as the sole payment method for all Orders.
2. WHEN a Customer confirms an Order, THE Order_Service SHALL display the total amount due, calculated as the sum of all item prices multiplied by their quantities plus the fixed delivery fee defined in platform configuration.
3. THE Order_Service SHALL display the Cash on Delivery amount due on the Order detail page, consistent with the amount shown at Order confirmation.
4. WHEN an Admin updates an Order's Order_Status to "Delivered", THE Order_Service SHALL record the delivery timestamp on the Order record.
5. IF a Customer refuses delivery of an Order, THE Admin SHALL be able to update the Order_Status to "Delivery Refused" and THE Order_Service SHALL record the refusal timestamp on the Order record.

---

### Requirement 10: Order Confirmation Notifications

**User Story:** As a Customer, I want to receive an automated confirmation after placing an order, so that I have a record of my purchase.

#### Acceptance Criteria

1. WHEN an Order is created, THE Notification_Service SHALL send an Order confirmation email to the Customer's registered email address within sixty seconds under normal conditions; the message SHALL be sent even if delivery takes longer than sixty seconds due to network or load conditions.
2. THE Notification_Service SHALL include the Order identifier, itemized list of Products with names and quantities, delivery address, selected delivery date, total amount due, and special handling instructions (or a note indicating none were provided) in the confirmation email.
3. WHERE a Customer has provided a phone number, THE Notification_Service SHALL also send an SMS confirmation message containing the Order identifier and total amount due within sixty seconds under normal conditions.

---

### Requirement 11: Order Tracking

**User Story:** As a Customer, I want to track my order in real time, so that I know when my plants will arrive.

#### Acceptance Criteria

1. THE Order_Service SHALL track each Order through the following sequential statuses: Order Confirmed → Quality Check → In Transit → Delivered.
2. WHEN an Admin updates the Order_Status of an Order, THE Notification_Service SHALL send a status update message to the Customer containing the Order identifier and the new Order_Status via email, and via SMS where a phone number is registered; the message SHALL be sent even if delivery takes longer than sixty seconds due to network or load conditions.
3. THE System SHALL display the current Order_Status and a timestamp for each status transition on the Customer's Order detail page, refreshed within thirty seconds of each status change.
4. WHEN an Order reaches "In Transit" status and an estimated delivery date has been set, THE Order_Service SHALL display the estimated delivery date on the Order detail page.
5. IF an Order reaches "In Transit" status and no estimated delivery date has been set, THEN THE Order_Service SHALL display a message indicating the estimated delivery date is not yet available.
6. THE Order_Service SHALL NOT display an estimated delivery date for Orders in "Order Confirmed" or "Quality Check" status.

---

### Requirement 12: Admin Order Status Management

**User Story:** As an Admin, I want to update order statuses, so that customers receive accurate fulfillment progress information.

#### Acceptance Criteria

1. THE System SHALL allow an Admin to advance the Order_Status of any Order to the next sequential status in the pipeline: Order Confirmed → Quality Check → In Transit → Delivered.
2. IF an Admin attempts to set an Order_Status to a value that is not the immediate next status in the sequence, or attempts to advance an Order already at "Delivered" status, THEN THE System SHALL prevent the update and display an error message stating the valid next status.
3. WHEN an Admin updates an Order_Status, THE System SHALL record the Admin's identifier and the UTC timestamp of the update.
4. THE System SHALL display a paginated list of all Orders on the Admin Dashboard at twenty-five Orders per page, filterable by Order_Status and sorted by Order creation date in descending order by default.

---

### Requirement 13: Customer Reviews

**User Story:** As a Customer, I want to submit a review with a photo of my plant, so that I can share my experience with the community.

#### Acceptance Criteria

1. WHEN a Customer's Order reaches "Delivered" status, THE Review_Service SHALL enable the Customer to submit one Review per Product in that Order.
2. IF a Customer attempts to submit a second Review for the same Product in the same delivered Order, THEN THE Review_Service SHALL reject the submission and display a message indicating a Review has already been submitted for that Product.
3. IF a Customer attempts to submit a Review for a Product not included in one of their delivered Orders, THEN THE Review_Service SHALL reject the submission and display an error message.
4. THE Review_Service SHALL require a whole-number rating between one and five inclusive and allow an optional text comment of up to one thousand characters.
5. THE Review_Service SHALL accept an optional photo upload in JPEG or PNG format with a file size greater than zero bytes and no greater than five megabytes.
6. WHEN a Customer submits a Review, THE Review_Service SHALL persist the Review in a pending state and add it to the Admin moderation queue within sixty seconds.
7. THE Catalog_Service SHALL display all Admin-approved Reviews on Listing detail pages along with the average rating calculated from all approved Reviews rounded to one decimal place; IF no approved Reviews exist for a Listing, THE Catalog_Service SHALL display a message indicating no reviews are available.

---

### Requirement 14: Admin Review Moderation

**User Story:** As an Admin, I want to moderate customer reviews, so that the platform maintains accurate and appropriate content.

#### Acceptance Criteria

1. THE System SHALL present pending Reviews to the Admin in a moderation queue ordered by submission date ascending (oldest first).
2. WHEN an Admin approves a Review, THE Review_Service SHALL change the Review state to "Approved" and make it visible on the associated Listing detail page.
3. WHEN an Admin rejects a Review, THE Review_Service SHALL change the Review state to "Rejected"; the Review SHALL be retained in the system but SHALL NOT be displayed on the Listing detail page or in the moderation queue.
4. WHEN an Admin posts an "Expert Advice" response to an Approved Review, THE Review_Service SHALL persist the response and display it alongside the Review on the Listing detail page.
5. WHEN an Admin posts an Expert Advice response, THE Notification_Service SHALL notify the Customer who submitted the original Review via email within sixty seconds, and via SMS where a phone number is registered.
